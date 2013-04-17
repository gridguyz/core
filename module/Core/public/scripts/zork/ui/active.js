/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.active !== "undefined" )
    {
        return;
    }

    /**
     * Activization element
     *
     * @memberOf Zork.Ui
     */
    global.Zork.Ui.prototype.active = function ( element )
    {
        js.style( "/styles/scripts/active.css" );
        element = $( element );

        var params = {
                "parent"    : element.data( "jsActiveParent" ) || "parent",
                "children"  : element.data( "jsActiveChildren" ) || "self",
                "handler"   : element.data( "jsActiveHandler" ) || "self"
            },
            parent   = params.parent == "parent"
                     ? element.parent()
                     : element.parents( params.parent + ":first" ),
            children = params.children == "self"
                     ? element.prop( "tagName" ).toLowerCase()
                     : params.children,
            handler  = params.handler == "self"
                     ? element
                     : element.find( params.handler );

        parent.addClass( "ui-active-parent" );
        element.addClass( "ui-active ui-active-hidden" );
        handler.addClass( "ui-active-handler" )
               .click( function () {
                    parent.find( children )
                          .addClass( "ui-active-hidden" )
                          .removeClass( "ui-active-active" );

                    element.addClass( "ui-active-active" )
                           .removeClass( "ui-active-hidden" );
                } );
    };

    global.Zork.Ui.prototype.active.isElementConstructor = true;

} ( window, jQuery, zork ) );
