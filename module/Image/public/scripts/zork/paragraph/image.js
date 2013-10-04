/**
 * Lightbox effect
 *
 * @package zork
 * @subpackage ui
 * @author Sipi
 */
( function ( global, $, js, undefined )
{
    "use strict";

    if ( typeof js.paragraph.image !== "undefined" )
    {
        return;
    }

    /**
     * Create dom elements of lightbox window and return it's content as a jQuery object
     */
    var createButton = function ( className, width, height ) {
        return $( "<span />" ).addClass( className + "-button" )
                .css( {
                    "display": "block",
                    "cursor": "pointer",
                    "position": "absolute",
                    "background": 'url("/images/scripts/lightbox/' + className + '-passive.png")'
                } )
                .width( width )
                .height( height )
                .hover( function() {
                    $( this ).children()
                             .stop()
                             .animate( { "opacity": 1.0 }, 500 );
                }, function() {
                    $( this ).children()
                             .stop()
                             .animate( { "opacity": 0.0 }, 500 );
                } )
                .append(
                    $( "<span />" ).css( {
                        "opacity": 0.0,
                        "width": "100%",
                        "height": "100%",
                        "display": "block",
                        "position": "absolute",
                        "background": 'url("/images/scripts/lightbox/' + className + '-active.png")'
                    } )
                );
    },
    createLightboxContentOfImage = function ( ) {
        return createButton( "lightbox-viewer", 24, 24 ).css( {
            "opacity": 0.0,
            "right": "5px",
            "bottom": "5px"
        } );
    };

    global.Zork.Paragraph.prototype.image = function ( element )
    {
        var loaded = false;

        element = $( element ).css( {
            "cursor": "pointer",
            "position":"relative"
        } );

        if ( ! element.find( ".lightbox-viewer-button" ).length )
        {
            element.append( createLightboxContentOfImage() )
                   .hover( function() {
                        $( this ).find( ".lightbox-viewer-button" )
                                 .stop()
                                 .animate( { "opacity": 1.0 }, 500 );
                    }, function() {
                        $( this ).find( ".lightbox-viewer-button" )
                                 .stop()
                                 .animate( { "opacity": 0.0 }, 500 );
                    } );
        }

        // click event
        var activeItem      = element.attr( "href" )
                            ? element
                            : element.find( ".lightbox-viewer-button" ),
            bgColor         = $( "body" ).css( "backgroundColor" ),
            bgTransparent   = /^(transparent|(rgba|hsla)\(.*, ?0\))$/.test( bgColor );

        element.data( "jsColor", bgTransparent ? "#ffffff" : bgColor );

        activeItem.on( "click.lighbox", function ( event ) {
            js.require( "js.ui.lightbox", function () {
                js.ui.lightboxOpen( element );
            } );

            event.stopPropagation();
            event.preventDefault();
            return false;
        } );

        // Creating dom elements
    };

    global.Zork.Paragraph.prototype.image.removeLightboxEvent = function ( element )
    {
        element = $( element );

        element.off( "click.lighbox" )
               .find( ".lightbox-viewer-button" )
               .remove();
    };

    global.Zork.Paragraph.prototype.image.isElementConstructor = true;

} ( window, jQuery, zork ) );
