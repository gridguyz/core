/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.preview !== "undefined" )
    {
        return;
    }

    /**
     * Preview element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.prototype.preview = function ( element )
    {
        element = $( element );

        var // form = $( element[0].form || element.parents( "form:first" ) ),
            _for    = element.attr( "for" ) || element.data( "jsPreviewFor" ),
            format  = String( element.data( "jsPreviewFormat" ) || element.html()
                        .replace( /^\s+/, "" ).replace( /\s+$/, "" ) || "%s" ),
            empty   = element.data( "jsPreviewEmpty" ) ||
                      js.core.translate( "default.empty" ),
            update  = function () {
                var val = String( _for.val() )
                            .replace( /^\s+/, "" )
                            .replace( /\s+$/, "" );

                if ( val )
                {
                    val = "<ins>" + val + "</ins>";
                }
                else
                {
                    val = "<i>" + empty + "</i>";
                }

                element.html( format.format( val ) );
            };

        if ( ! _for || 1 > ( _for = $( "#" + _for ) ).length )
        {
            _for    = element.parents()
                             .find( "> :input:first" )
                             .slice( 0, 1 );
        }

        if ( _for.length )
        {
            _for.on( "change keyup click", update );
            update();
        }
    };

    global.Zork.Form.prototype.preview.isElementConstructor = true;

} ( window, jQuery, zork ) );
