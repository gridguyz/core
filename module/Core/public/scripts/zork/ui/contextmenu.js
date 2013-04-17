/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.contextMenu !== "undefined" )
    {
        return;
    }

    js.require( "jQuery.fn.contextmenu" );

    /**
     * Progress element
     *
     * @memberOf Zork.Ui
     */
    global.Zork.Ui.prototype.contextMenu = function ( element )
    {
        element = $( element );
        var menu = $( "#" + ( element.attr( "contextmenu" ) ||
            element.data( "jsContextmenu" ) ) );

        menu.hide();

        element.contextui( function ()
        {
            return menu;
        } );
    };

    global.Zork.Ui.prototype.contextMenu.isElementConstructor = true;

} ( window, jQuery, zork ) );
