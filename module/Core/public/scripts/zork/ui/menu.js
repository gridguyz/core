/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.menu !== "undefined" )
    {
        return;
    }

    var menuSelector = "ul";

    /**
     * Progress element
     *
     * @memberOf Zork.Ui
     */
    global.Zork.Ui.prototype.menu = function ( element )
    {
        element = $( element );
        var corners = element.data( "jsMenuCorners" ),
            orient = element.data( "jsMenuOrientation" ),
            menu = element.is( menuSelector ) ?
                element : element.find( menuSelector ).first();

        if ( orient === "horizontal" )
        {
            js.style( "/styles/scripts/menubar.css" );
            js.require( "jQuery.ui.menubar" );
            menu.menubar();
        }
        else
        {
            menu.menu();
        }

        if ( corners )
        {
            menu.removeClass( "ui-corner-all" );

            if ( corners !== "false" && corners !== "none" )
            {
                menu.addClass( "ui-corner-" + corners );
            }
        }
    };

    global.Zork.Ui.prototype.menu.isElementConstructor = true;

} ( window, jQuery, zork ) );
