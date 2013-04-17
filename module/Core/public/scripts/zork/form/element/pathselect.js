/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.pathselect !== "undefined" )
    {
        return;
    }

    /**
     * File selector element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.pathselect = function ( element )
    {
        element = $( element );

        var width    = 0,
            button   = element.data( "jsPathselectButton" ),
            file     = element.data( "jsPathselectFile" ),
            startDir = String( element.data( "jsPathselectStartDir" ) || "" )
                .replace( /^\/+/, "" )
                .replace( /\/+$/, "" ),
            selector = function () {
                this.blur();

                js.caller.js.require( "js.core.pathselect", function ( pathselect ) {
                    pathselect( {
                        "directory" : file ? startDir : element.val() || startDir,
                        "file"      : file ? element.val() : false,
                        "select"    : function ( val ) {
                            element.val( val )
                                   .trigger( "change" );
                        }
                    } );
                } );

                return false;
            };

        if ( Object.isUndefined( file ) )
        {
            file = true;
        }

        if ( ! element.attr( "required" ) )
        {
            element.addClass( "ui-controls-after" )
                   .after(
                       $( '<input class="ui-fileselect-clear" type="button" value="&empty;" />' )
                           .click( function () {
                               element.val( "" )
                                      .trigger( "change" );
                           } )
                           .each( function () {
                               var $this = $( this );
                               width += $this.outerWidth();
                               width += $this.css( "margin-left" );
                           } )
                   );
        }

        if ( button )
        {
            button = js.core.translate( button );

            element.addClass( "ui-controls-after" )
                   .after(
                       $( '<input class="ui-fileselect-trigger" type="button" />' )
                           .val( button )
                           .click( selector )
                           .each( function () {
                               var $this = $( this );
                               width += $this.outerWidth();
                               width += $this.css( "margin-left" );
                           } )
                   );
        }
        else
        {
            element
                .click( selector )
                .keydown( selector );
        }

        if ( width > 0 )
        {
            var inputs  = [];

            element.parent()
                   .inputset()
                   .find( ":input" )
                   .each( function () {
                       if ( ! $( this ).is( ".ui-fileselect-trigger" ) )
                       {
                           inputs.push( this );
                       }
                   } );

            if ( inputs.length && width )
            {
                width = Math.ceil( width / inputs.length );

                $.each( inputs, function () {
                    var input = $( this );
                    input.width( input.width() - width );
                } );
            }
        }
    };

    global.Zork.Form.Element.prototype.pathselect.isElementConstructor = true;

} ( window, jQuery, zork ) );
