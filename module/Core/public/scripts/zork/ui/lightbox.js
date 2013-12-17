/**
 * User interface functionalities: lightbox
 *
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.lightbox !== "undefined" )
    {
        return;
    }

    js.style( "/styles/scripts/lightbox.css" );

    var opened  = false,
        close   = null;

    /**
     * Attach lightbox functionality to an element
     *
     * @function
     * @memberOf Zork.Ui
     * @param {Object|HTMLElement|$} element
     * @param {Object} params
     * @param {HTMLElement|$} params.element
     * @param {String} params.title Title of the dialog window.
     * @type String
     */
    global.Zork.Ui.prototype.lightbox = function ( element, params )
    {
        params = $.extend( {}, js.ui.lightbox.defaults, params || {} );

        if ( $.isPlainObject( element ) )
        {
            params  = $.extend( {}, element, params );
            element = params.element = $( params.element );
        }
        else
        {
            element = params.element = $( element );
        }

        if ( ! params.image )
        {
            params.image = element.data( "jsLightboxImage" )
                    || element.attr( "href" )
                    || element.attr( "src" );
        }

        if ( ! params.title )
        {
            params.title = element.data( "jsLightboxTitle" )
                    || element.attr( "title" )
                    || element.find( "img[title]" ).attr( "title" )
                    || element.find( "img[alt]" ).attr( "alt" );
        }

        if ( ! params.handle )
        {
            params.handle = element.data( "jsLightboxHandle" ) || null;
        }

        var open = function ( event ) {
            js.ui.lightbox.open( params );
            event.stopPropagation();
            event.preventDefault();
            return false;
        };

        element.off( "click.lightbox" );

        if ( params.handle )
        {
            element.on( "click.lightbox", String( params.handle ), open );
        }
        else
        {
            element.on( "click.lightbox", open );
        }
    };

    /**
     * js.ui.lightbox() is enabled for element constructing
     */
    global.Zork.Ui.prototype.lightbox.isElementConstructor = true;

    /**
     * Default params for js.ui.lightbox()
     */
    global.Zork.Ui.prototype.lightbox.defaults = {
        "padding": 0,
        "margin": 20,
        "image": null,
        "handle": null,
        "title": null,
        "titlePadding": 10,
        "easing": "swing",
        "duration": "fast",
        "appendTo": "body",
        "element": 'a.lightbox[href$=".jpg"],a.lightbox[href$=".jpeg"],'
                 + 'a.lightbox[href$=".png"],a.lightbox[href$=".gif"]'
    };

    /**
     * Remove the lightbox events from an element
     *
     * @param {HTMLElement|$} element
     * @returns {undefined}
     */
    global.Zork.Ui.prototype.lightbox.remove = function ( element )
    {
        element = $( element );
        element.off( "click.lightbox" );
    };

    /**
     * Open a lightbox
     *
     * @param {Object} element
     * @returns {Boolean}
     */
    global.Zork.Ui.prototype.lightbox.open = function ( params )
    {
        params = $.extend( {}, js.ui.lightbox.defaults, params || {} );

        if ( opened || ! params.image )
        {
            return true;
        }

        opened = true;
        params.padding = Math.max( 0, parseInt( params.padding, 10 ) );

        var img     = $( "<img>" ),
            layer   = $( "<div>" ).addClass( "ui-overlay" ),
            shadow  = $( "<div>" ).addClass( "ui-widget-shadow ui-lightbox-shadow" ),
            overlay = $( "<div>" ).addClass( "ui-widget-overlay ui-lightbox-overlay" ),
            closebc = $( '<a href="#">' ).addClass( "ui-lightbox-close" ),
            closeba = $( "<img>" ).appendTo( closebc ),
            closebp = $( "<img>" ).appendTo( closebc ),
            content = $( "<div>" ).addClass( "ui-lightbox-container" ).append(
                '<img src="/images/scripts/loading.gif" />'
            );

        layer.css( {
            "top"       : "0px",
            "left"      : "0px",
            "width"     : "100%",
            "height"    : "100%",
            "position"  : "fixed",
            "z-index"   : 100
        } );

        shadow.css( {
            "top"       : "50%",
            "left"      : "50%",
            "width"     : "20px",
            "height"    : "20px",
            "margin"    : ( - params.padding - 10 ) + "px",
            "padding"   : "0px",
            "border"    : "0px none",
            "position"  : "absolute"
        } );

        content.css( {
            "top"               : "50%",
            "left"              : "50%",
            "width"             : "20px",
            "height"            : "20px",
            "line-height"       : "20px",
            "margin"            : "-10px",
            "text-align"        : "center",
            "vertical-align"    : "middle",
            "position"          : "absolute"
        } );

        var cbntop = parseInt( -18 - params.padding, 10 );

        closebc.css( {
            "top"        : cbntop + "px",
            "right"      : cbntop + "px",
            "margin"     : "0px",
            "padding"    : "0px",
            "width"      : "36px",
            "height"     : "36px",
            "position"   : "absolute"
        } );

        closeba.attr( "src", "/images/scripts/lightbox/close-active.png" )
               .css( {
                    "opacity": 0,
                    "top": "0px",
                    "left": "0px",
                    "position": "absolute"
                } );

        closebp.attr( "src", "/images/scripts/lightbox/close-passive.png" )
               .css( {
                    "opacity": 1,
                    "top": "0px",
                    "left": "0px",
                    "position": "absolute"
                } );

        closebc.hover( function () {
            closeba.animate( { "opacity": 1 }, "fast" );
            closebp.animate( { "opacity": 0 }, "fast" );
        }, function () {
            closeba.animate( { "opacity": 0 }, "fast" );
            closebp.animate( { "opacity": 1 }, "fast" );
        } );

        layer.append( overlay )
             .append( shadow )
             .append( content );

        $( params.appendTo ).append( layer.fadeIn( params.duration ) );

        close = function () {
            layer.fadeOut( params.duration, function () {
                layer.remove();
                layer   = null;
                shadow  = null;
                overlay = null;
                content = null;
                params  = null;
            } );
        };

        img.load( function () {
            var titleNode   = null,
                imgNode     = img.get( 0 ),
                imgWidth    = Math.max( imgNode.width, 1 ),
                imgHeight   = Math.max( imgNode.height, 1 ),
                resize      = function () {
                    shadow.stop( true, true );
                    content.stop( true, true );
                    img.stop( true, true );

                    if ( titleNode )
                    {
                        titleNode.stop( true, true );
                    }

                    var margin2     = params.margin * 2,
                        padding2    = params.padding * 2,
                        titlePad2   = params.titlePadding * 2,
                        availWidth  = Math.max( 1, layer.width() - margin2 ),
                        availHeight = Math.max( 1, layer.height() - margin2 ),
                        titleHeight = 0,
                        allWidth,
                        allHeight,
                        toWidth,
                        toHeight,
                        marginTop,
                        marginLeft,
                        animate = {
                            "easing": params.easing,
                            "duration": params.duration
                        },
                        calcHeights = function ( width ) {
                            if ( titleNode )
                            {
                                titleNode.css( {
                                    "width": "auto",
                                    "height": "auto"
                                } );

                                titleNode.width( width - titlePad2 );
                                titleHeight = titleNode.height() + titlePad2;

                                titleNode.css( {
                                    "width": "1px",
                                    "height": "1px"
                                } );
                            }

                            toWidth     = width;
                            allWidth    = width + padding2;
                            toHeight    = width * imgHeight / imgWidth;
                            allHeight   = titleHeight + padding2 + toHeight;

                            if ( allHeight > availHeight )
                            {
                                allHeight = availHeight;
                                toHeight  = allHeight - titleHeight - padding2;
                                toWidth   = toHeight * imgWidth / imgHeight;

                                if ( titleNode )
                                {
                                    calcHeights( toWidth );
                                }

                                allWidth = toWidth + padding2;
                            }
                        };

                    allWidth = Math.min(
                        availWidth,
                        padding2 + imgWidth
                    );

                    calcHeights( allWidth - padding2 );

                    marginTop = allHeight / 2;
                    marginLeft = allWidth / 2;

                    shadow.animate( {
                        "width": allWidth,
                        "height": allHeight,
                        "margin-top": - marginTop,
                        "margin-left": - marginLeft
                    }, {
                        "easing": params.easing,
                        "duration": params.duration
                    } );

                    content.animate( {
                        "width": allWidth - padding2,
                        "height": allHeight - padding2,
                        "margin-top": - marginTop + params.padding,
                        "margin-left": - marginLeft + params.padding
                    }, {
                        "easing": params.easing,
                        "duration": params.duration
                    } );

                    img.animate( {
                        "width": toWidth,
                        "height": toHeight
                    }, {
                        "easing": params.easing,
                        "duration": params.duration,
                        "complete": function () {
                            img.css( {
                                "visibility": "visible"
                            } ).animate( {
                                "opacity": 1
                            }, {
                                "easing": params.easing,
                                "duration": params.duration
                            } );
                        }
                    } );

                    if ( titleNode )
                    {
                        titleNode.animate( {
                                     "width": toWidth - titlePad2,
                                     "height": titleHeight - titlePad2
                                 }, {
                                     "easing": params.easing,
                                     "duration": params.duration,
                                     "complete": function () {
                                         titleNode.css( {
                                             "width": "",
                                             "height": "",
                                             "visibility": "visible"
                                         } ).animate( {
                                             "opacity": 1
                                         }, {
                                             "easing": params.easing,
                                             "duration": params.duration
                                         } );
                                     }
                                 } );
                    }
                };

            img.css( {
                "width": "1px",
                "height": "1px",
                "visibility": "hidden",
                "opacity": 0
            } );

            content.empty()
                   .css( {
                       "width": "",
                       "height": "",
                       "line-height": "1.2em"
                   } )
                   .append( closebc )
                   .append( img );

            if ( params.title )
            {
                content.append(
                    titleNode = $( "<div>" )
                        .html( params.title )
                        .css( {
                            "margin": "0px",
                            "padding": params.titlePadding,
                            "visibility": "hidden",
                            "opacity": 0
                        } )
                );
            }

            $( global ).on( "resize.lightbox", resize );
            overlay.on( "click.lightbox", js.ui.lightbox.close );
            closebc.on( "click.lightbox", js.ui.lightbox.close );
            setTimeout( resize, 50 );
        } );

        img.prop( "src", params.image );
        return false;
    };

    /**
     * Close the current active lightbox
     *
     * @param {Object} element
     * @returns {Boolean}
     */
    global.Zork.Ui.prototype.lightbox.close = function ()
    {
        if ( ! opened )
        {
            return true;
        }

        opened = false;
        close();
        $( global ).off( "resize.lightbox" );

        return false;
    };

} ( window, jQuery, zork ) );
