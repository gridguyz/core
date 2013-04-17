/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.datalist !== "undefined" )
    {
        return;
    }

    /**
     * DataList form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.datalist = function ( element )
    {
        js.require( "jQuery.fn.autocompletegroup" );
        element = $( element );

        var toggle      = !! element.data( "jsDatalistToggle" ),
            minLength   = element.data( "jsDatalistMinLength" ) || 0,
            selected    = element.find( ":selected" ),
            input       = $( "<input type='text' />" ),
            opened      = false;

        if ( selected.length )
        {
            input.val( selected.text() );
        }

        element.removeAttr( "multiple" )
               .addClass( "ui-helper-hidden" )
               .prop( "multiple", false )
               .after( input );

        input.autocompletegroup( {
            "minLength": minLength,
            "source": function ( request, response ) {
                var result = [],
                    term = request.term
                                  .toLowerCase()
                                  .replace( /^\s+/, "" )
                                  .replace( /\s+$/, "" )
                                  .replace( /\s+/, " " );

                element.find( "option" ).each( function () {
                    var self    = $( this ),
                        val     = self.val(),
                        text    = self.text(),
                        par     = self.parent(),
                        group   = par.is( "optgroup" )
                                ? par.attr( "label" )
                                : "",
                        search  = String( group + " " + text )
                                    .toLowerCase()
                                    .replace( /^\s+/, "" )
                                    .replace( /\s+$/, "" )
                                    .replace( /\s+/, " " );

                    if ( ~search.indexOf( term ) )
                    {
                        par = self.parent();

                        result.push( {
                            "val"   : val,
                            "label" : text,
                            "group" : group
                        } );
                    }
                } );

                response( result );
            },
            "change": function ( _, ui ) {
                var val = "", f;

                if ( ui.item && ui.item.val )
                {
                    val = ui.item.val;
                }
                else if ( element.attr( "required" ) )
                {
                    f = element.find( "option:first" );
                    val = f.val();
                    input.val( f.text() );
                }

                element.val( val )
                       .trigger( "change" );
            },
            "search": function () {
                opened = true;
            },
            "close": function () {
                opened = false;
            }
        } );

        if ( toggle )
        {
            input.after(
                $( '<button type="button" />' )
                    .button( {
                        "text": false,
                        "icons": {
                            "primary": "ui-icon-triangle-1-s"
                        }
                    } )
                    .click( function () {
                        if ( minLength )
                        {
                            input.autocompletegroup( "option", "minLength", 0 );
                        }

                        if ( opened )
                        {
                            input.autocompletegroup( "close" );
                        }
                        else
                        {
                            input.focus()
                                 .autocompletegroup( "search", "" );
                        }

                        if ( minLength )
                        {
                            input.autocompletegroup( "option", "minLength", minLength );
                        }
                    } )
            );

            element.parent()
                   .inputset();
        }
    };

    global.Zork.Form.Element.prototype.datalist.isElementConstructor = true;

} ( window, jQuery, zork ) );
