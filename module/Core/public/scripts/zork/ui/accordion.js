/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.accordion !== "undefined" )
    {
        return;
    }

    /**
     * Progress element
     *
     * @memberOf Zork.Ui
     */
    global.Zork.Ui.prototype.accordion = function ( element )
    {
        js.style( "/styles/scripts/accordion.css" );

        element = $( element );
        element.accordion( {
            "navigation": true,
            "active": ".active",
            "header": "> fieldset > legend",
            "event": element.data( "jsAccorditionEvent" ) || "click",
            "collapsible": !! element.data( "jsAccorditionCollapsible" ),
            "icons": {
                "header": "ui-icon-plus",
                "headerSelected": "ui-icon-minus"
            }
        } );
    };

    global.Zork.Ui.prototype.accordion.isElementConstructor = true;

} ( window, jQuery, zork ) );
