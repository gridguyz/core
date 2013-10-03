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
     *
     */
    var createButton = function (className, width, height)
        {
            return $('<span></span>')
                .attr('class',className + '-button')
                .css( {
                    display: 'block',
                    position: 'absolute',
                    cursor: 'pointer',
                    background: 'url("/images/scripts/lightbox/' + className + '-passive.png")'
                } )
                .width(width)
                .height(height)
                .hover(
                    function()
                    {
                        $(this).children().stop().animate({opacity: 1.0},500);
                    },
                    function()
                    {
                        $(this).children().stop().animate({opacity: 0.0},500);
                    }
                )
                .append(
                    $('<span></span>')
                        .css( {
                            display: 'block',
                            position: 'absolute',
                            width: '100%',
                            height: '100%',
                            background: 'url("/images/scripts/lightbox/' + className + '-active.png")',
                            opacity: 0.0
                        } )
                );

        },

        createLightboxContentOfImage = function ( )
        {
            return createButton( "lightbox-viewer", 24, 24 )
                               .css( {
                                   right: "5px",
                                   bottom: "5px",
                                   opacity: 0.0
                               } );
        };

    global.Zork.Paragraph.prototype.image = function ( element )
    {
        var loaded = false;

        element = $( element );

        element
            .css( {
                position:'relative',
                cursor: 'pointer'
            } )
            .append(
                createLightboxContentOfImage()
            )
            // Create hover animation
            .hover(
                function()
                {
                    $(this).find('span.lightbox-viewer-button').stop().animate({opacity: 1.0},500);
                },
                function()
                {
                    $(this).find('span.lightbox-viewer-button').stop().animate({opacity: 0.0},500);
                }
            );

        // click event
        var activeItem = typeof(element.attr('href')) == 'undefined' || element.attr('href') == ''
            ? element
            : element.find('span.lightbox-viewer-button');

        var bg = $('body').css('backgroundColor');
        element.data('jsColor', bg=='transparent'||/^rgba\(.*, ?0\)$/.test(bg)?'#fff':bg);

        activeItem.click( function( event ) {
            if(loaded)
            {
                js.ui.lightboxOpen( element );
            }
            event.stopPropagation();
            event.preventDefault();
            return false;
        } );

        js.require('js.ui.lightbox', function() {
            loaded = true;
        } );
        // Creating dom elements
    };

    global.Zork.Paragraph.prototype.image.removeLightboxEvent = function ( element )
    {
        element = $( element );
        element.off('click');
        element.find('span.lightbox-viewer-button').remove();
    };

    global.Zork.Paragraph.prototype.image.isElementConstructor = true;

} ( window, jQuery, zork ) );
