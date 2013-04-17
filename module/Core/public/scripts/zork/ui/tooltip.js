/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.toolTip !== "undefined" )
    {
        return;
    }

    /**
     * Progress element
     *
     * @memberOf Zork.Ui
     */
    global.Zork.Ui.prototype.toolTip = function ( element, params )
    {
        params = params || {};
        element = $( element );
        element.tooltip( {
            "items": "*",
            "position": {
                "my" : element.data( "jsTooltipPositionMy" ) || params.positionMy || "left+15 center",
                "at" : element.data( "jsTooltipPositionAt" ) || params.positionAt || "right center",
                "collision" : "flipfit"
            },
            "content": typeof params.message !== "undefined"
                ? ( Function.isFunction( params.message ) ?
                    params.message : String( params.message ) )
                : function () {
                        return element.data( "jsTooltipTitle" ) ||
                               element.attr( "title" );
                    }
        } );

        if ( params.event === "manual" ||
             element.data( "jsTooltipEvent" ) === "manual" )
        {
            setTimeout( function () {
                var m = "";

                if ( typeof params.message !== "undefined" )
                {
                    m = ( Function.isFunction( params.message ) ) ?
                        m = params.message() : m = params.message;
                }
                else
                {
                    m = element.data( "jsTooltipTitle" ) ||
                        element.attr( "original-title" );
                }

                element.tooltip( Object.isEmpty( m ) ? "hide" : "show" );
            }, 333 );
        }
    };

    global.Zork.Ui.prototype.toolTip.isElementConstructor = true;

} ( window, jQuery, zork ) );
