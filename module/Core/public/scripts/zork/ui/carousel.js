/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.carousel !== "undefined" )
    {
        return;
    }

    /**
     * Carousel element
     *
     * @memberOf Zork.Ui
     */
    global.Zork.Ui.prototype.carousel = function ( element )
    {
        js.require( "jQuery.fn.carousel", function () {
            element = $( element );
            element.carousel( {
                "rollWidth": element.data( "jsCarouselRollWidth" ) || null,
                "rollSpeed": element.data( "jsCarouselRollSpeed" ) || null,
                "rollEasing": element.data( "jsCarouselRollEasing" ) || null
            } );
        } );
    };

    global.Zork.Ui.prototype.carousel.isElementConstructor = true;

} ( window, jQuery, zork ) );
