/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.carousel !== "undefined" )
    {
        return;
    }

    /**
     * Carousel form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.carousel = function ( element )
    {
        element = $( element );

        var iconBase    = element.data( "jsCarouselIconBase" ) || js.core.uploadsUrl,
            iconPrefix  = element.data( "jsCarouselIconPrefix" ),
            iconPostfix = element.data( "jsCarouselIconPostfix" );

        if ( iconBase )
        {
            iconBase = iconBase.replace( /\/+$/, "" ) + "/";
        }

        js.require( "jQuery.fn.carousel", function () {
            element.find( "label:has(:radio), label:has(:checkbox)" ).each(
                function () {
                    var self = $( this );
                    self.append(
                        $( "<div />" ).addClass( "ui-carousel-caption" ).
                            append( self.contents() )
                    );

                    var input   = self.find( ":radio, :checkbox" ),
                        title   = input.attr( "title" ),
                        icon    = input.data( "jsCarouselIcon" ),
                        iconSrc = icon
                            ? ( icon[0] == "/" ? icon : iconBase + icon )
                            : iconBase + iconPrefix +
                              input.attr( "value" ) + iconPostfix;

                    if ( title )
                    {
                        self.prepend( title );
                    }

                    self.prepend(
                        $( "<img alt='icon' />" )
                            .attr( "src", iconSrc )
                    );
                }
            );

            element.carousel( {
                "captions": ".ui-carousel-caption",
                "items": "label:has(:radio), label:has(:checkbox)",
                "rollWidth": element.data( "jsCarouselRollWidth" ) || null,
                "rollSpeed": element.data( "jsCarouselRollSpeed" ) || null,
                "rollEasing": element.data( "jsCarouselRollEasing" ) || null
            } );

            var update = function () {
                element.carousel( "widget" ).each(
                    function () {
                        var self = $( this ),
                            input = self.find( ":radio, :checkbox" );

                        if ( input.is( ":checked" ) )
                        {
                            self.addClass( "ui-state-active" );
                        }
                        else
                        {
                            self.removeClass( "ui-state-active" );
                        }
                    }
                );
            };

            element.carousel( "widget" ).on( "click change keyup", update );
            update();
        } );
    };

    global.Zork.Form.Element.prototype.carousel.isElementConstructor = true;

} ( window, jQuery, zork ) );
