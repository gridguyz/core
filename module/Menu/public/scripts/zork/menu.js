/**
 * Menu functionalities
 * @package zork
 * @subpackage menu
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.menu !== "undefined" )
    {
        return;
    }

    /**
     * @class Menu module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Menu = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "menu" ];
    };

    global.Zork.prototype.menu = new global.Zork.Menu();

    var trans    = null,
        gettrans = function ()
        {
            if ( null === trans )
            {
                trans = [
                    js.core.translate( "menu.select.main" ),
                    js.core.translate( "menu.select.sub" )
                ];
            }

            return trans;
        };

    /**
     * Menu select form-element
     * @param {HTMLElement} element
     * @type undefined
     */
    global.Zork.Menu.prototype.select = function ( element )
    {
        js.require( "jQuery.fn.autocompletetitle" );
        element = $( element );

        var minLength   = element.data( "jsUserselectMinLength" ) || 1,
            placeholder = element.data( "jsAutocompletePlaceholder" ) ||
                          js.core.translate( "default.autoCompletePlaceholder" ),
            selected    = element.find( ":selected" ),
            input       = $( "<input type='text' />" ),
            change      = function ( _, ui ) {
                var val, lab;

                if ( ui.item )
                {
                    val = ui.item.value;
                    lab = ui.item.label;
                }
                else
                {
                    val = "";
                    lab = "";
                }

                input.val( lab );
                element.val( val )
                       .trigger( "change" );
            };

        if ( selected.length )
        {
            input.val( selected.text() );
        }

        input.attr( "placeholder", placeholder );

        element.removeAttr( "multiple" )
               .addClass( "ui-helper-hidden" )
               .prop( "multiple", false )
               .after( input );

        input.autocompletetitle( {
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
                        level   = Number( self.data( "level" ) || 0 ),
                        search  = text.toLowerCase()
                                      .replace( /^\s+/, "" )
                                      .replace( /\s+$/, "" )
                                      .replace( /\s+/, " " );

                    if ( ~search.indexOf( term ) )
                    {
                        var trans = gettrans();

                        result.push( {
                            "value": val,
                            "label": text,
                            "description": level ? trans[1].format( level ) : trans[0]
                        } );
                    }
                } );

                response( result );
            },
            "change": change,
            "select": function ( event, ui ) {
                event.preventDefault();
                change.call( this, event, ui );
            }
        } );
    };

    global.Zork.Menu.prototype.select.isElementConstructor = true;

} ( window, jQuery, zork ) );
