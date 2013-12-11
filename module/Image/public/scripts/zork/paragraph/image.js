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

    var createLightboxViewer = function ( ) {
        return $( "<span />" )
            .addClass( "lightbox-viewer-button" )
            .css( {
                "display": "block",
                "cursor": "pointer",
                "position": "absolute",
                "background": 'url("/images/scripts/lightbox/lightbox-viewer-passive.png")'
            } )
            .width( 24 )
            .height( 24 )
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
                    "background": 'url("/images/scripts/lightbox/lightbox-viewer-active.png")'
                } )
            )
            .css( {
                "opacity": 0.0,
                "right": "5px",
                "bottom": "5px"
            } );
    };

    global.Zork.Paragraph.prototype.image = function ( element )
    {
        element = $( element ).css( {
            "cursor": "pointer",
            "position":"relative"
        } );

        if ( ! element.find( ".lightbox-viewer-button" ).length )
        {
            element.append( createLightboxViewer() )
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
        var self            = ! element.prop( "href" ),
            activeItem      = self ? element : element.find( ".lightbox-viewer-button" ),
            bgColor         = $( "body" ).css( "backgroundColor" ),
            bgTransparent   = /^(transparent|(rgba|hsla)\(.*,\s*0(\.0+)?\s*\))$/.test( bgColor );

        element.data( "jsColor", bgTransparent ? "#ffffff" : bgColor );

        js.require( "jQuery.fn.fancybox", function () {
            activeItem.fancybox( {
                "type": "image",
                "onStart": function () {
                    var img = element.find( "img" ).first();

                    return {
                        "href": element.data( "jsHref" )
                            || element.attr( "href" )
                            || img.attr( "src" ),
                        "title": element.data( "jsHtml" )
                            || element.attr( "title" )
                            || img.attr( "title" )
                            || img.attr( "alt" )
                    };
                }
            } );
        } );
    };

    global.Zork.Paragraph.prototype.image.removeLightboxEvent = function ( element )
    {
        element = $( element );

        element.css( "cursor", "" )
               .unbind( ".fb" )
               .find( ".lightbox-viewer-button" )
               .remove();
    };

    global.Zork.Paragraph.prototype.image.isElementConstructor = true;

} ( window, jQuery, zork ) );
