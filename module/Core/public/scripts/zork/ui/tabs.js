/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.tabs !== "undefined" )
    {
        return;
    }

    /**
     * Progress element
     *
     * @memberOf Zork.Ui
     */
    global.Zork.Ui.prototype.tabs = function ( element )
    {
        js.style( "/styles/scripts/tabs.css" );
        element = $( element );

        var first = 0,
            index = 0,
            placement = element.data( "jsTabsPlacement" ) || "top",
            selected = element.data( "jsTabsActive" ) || global.location.hash;

        if ( selected )
        {
            element.find( "> ul:first > li > a" )
                   .each( function () {
                        var href = String( this.href );

                        if ( selected == href.substr( href.indexOf( "#" ) ) )
                        {
                            first = index;
                        }

                        index++;
                    } );
        }

        element.tabs( {
            "active": first,
            "event": element.data( "jsTabsEvent" ) || "click",
            "collapsible": !! element.data( "jsTabsCollapsible" )
        } );

        switch ( placement )
        {
            case 'bottom':
                element.addClass( "js-tabs-horizontal js-tabs-bottom" );
                $( "<div />" ).addClass( "js-tabs-spacer" )
                              .insertAfter( element.find( "> ul:first" ) );
                element.find( ".ui-tabs-nav, .ui-tabs-nav > *" )
                       .removeClass( "ui-corner-all ui-corner-top" )
                       .addClass( "ui-corner-bottom" );
                break;

            case 'right':
                element.addClass( "js-tabs-vertical js-tabs-right" );
                element.append( $( "<div />" ).addClass( "js-tabs-clear" ) );
                element.find( ".ui-tabs-nav, .ui-tabs-nav > *" )
                       .removeClass( "ui-corner-all ui-corner-top" )
                       .addClass( "ui-corner-right" );
                break;

            case 'left':
                element.addClass( "js-tabs-vertical js-tabs-left" );
                element.append( $( "<div />" ).addClass( "js-tabs-clear" ) );
                element.find( ".ui-tabs-nav, .ui-tabs-nav > *" )
                       .removeClass( "ui-corner-all ui-corner-top" )
                       .addClass( "ui-corner-left" );
                break;

            case 'top':
            default:
                element.addClass( "js-tabs-horizontal js-tabs-top" );
                break;
        }
    };

    global.Zork.Ui.prototype.tabs.isElementConstructor = true;

} ( window, jQuery, zork ) );
