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
                         .animate( { "opacity": 1.0 }, "fast" );
            }, function() {
                $( this ).children()
                         .stop()
                         .animate( { "opacity": 0.0 }, "fast" );
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
                                 .animate( { "opacity": 1.0 }, "fast" );
                    }, function() {
                        $( this ).find( ".lightbox-viewer-button" )
                                 .stop()
                                 .animate( { "opacity": 0.0 }, "fast" );
                    } );
        }

        // click event
        var self            = ! element.prop( "href" ),
            bgColor         = $( "body" ).css( "backgroundColor" ),
            bgTransparent   = /^(transparent|(rgba|hsla)\(.*,\s*(0(\.0+)?|\.0+)\))$/.test( bgColor );

        js.require( "js.ui.lightbox", function () {
            js.ui.lightbox( element, {
                "background": bgTransparent ? "#ffffff" : bgColor,
                "handle": self ? null : ".lightbox-viewer-button"
            } );
        } );
    };

    global.Zork.Paragraph.prototype.image.removeLightboxEvent = function ( element )
    {
        element = $( element );

        element.css( "cursor", "" )
               .find( ".lightbox-viewer-button" )
               .remove();

        js.require( "js.ui.lightbox", function () {
            js.ui.lightbox.remove( element );
        } );
    };

    global.Zork.Paragraph.prototype.image.isElementConstructor = true;

} ( window, jQuery, zork ) );
