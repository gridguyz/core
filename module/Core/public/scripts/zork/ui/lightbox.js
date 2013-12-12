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
        "padding": 10,
        "image": null,
        "handle": null,
        "title": null,
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
            return false;
        }

        opened = true;
        params.padding = Math.max( 0, parseInt( params.padding, 10 ) );

        var img     = $( "<img>" ),
            layer   = $( "<div>" ).addClass( "ui-overlay" ),
            shadow  = $( "<div>" ).addClass( "ui-widget-shadow" ),
            overlay = $( "<div>" ).addClass( "ui-widget-overlay" ),
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
                    var padding2    = params.padding * 2,
                        availWidth  = layer.width(),
                        availHeight = layer.height(),
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
                        };

                    allWidth = Math.min(
                        availWidth,
                        padding2 + imgWidth
                    );

                    toWidth = allWidth - padding2;

                    if ( titleNode )
                    {
                        titleNode.width( toWidth );
                        titleHeight = titleNode.height() + params.padding;
                    }

                    toHeight    = toWidth * imgHeight / imgWidth;
                    allHeight   = titleHeight + padding2 + toHeight;

                    if ( allHeight > availHeight )
                    {
                        allHeight = availHeight;
                        toHeight  = allHeight - titleHeight - padding2;
                        toWidth   = toHeight * imgWidth / imgHeight;
                    }

                    marginTop = allHeight / 2;
                    marginLeft = allWidth / 2;

                    shadow.animate( {
                        "width": allWidth,
                        "height": allHeight,
                        "margin-top": - marginTop - params.padding,
                        "margin-left": - marginLeft - params.padding
                    }, animate );

                    content.animate( {
                        "width": allWidth - padding2,
                        "height": allHeight - padding2,
                        "margin-top": - marginTop,
                        "margin-left": - marginLeft
                    }, animate );

                    img.animate( {
                        "width": toWidth,
                        "height": toHeight
                    }, animate );
                };

            img.css( {
                "width": "1px",
                "height": "1px"
            } );

            content.empty()
                   .css( {
                       "width": "",
                       "height": "",
                       "line-height": "1.2em"
                   } )
                   .append( img );

            if ( params.title )
            {
                content.append(
                    titleNode = $( "<div>" ).html( params.title )
                                            .css( "padding-top",
                                                  params.padding + "px" )
                );
            }

            $( global ).on( "resize.lightbox", resize );
            overlay.on( "click.lightbox", js.ui.lightbox.close );
            setTimeout( resize, 50 );
        } );

        img.prop( "src", params.image );
        return true;
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
            return false;
        }

        opened = false;
        close();
        $( global ).off( "resize.lightbox" );

        return true;
    };

} ( window, jQuery, zork ) );
