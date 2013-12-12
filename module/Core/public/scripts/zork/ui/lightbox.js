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
        "element": 'a.lightbox[href$=".jpg"],a.lightbox[href$=".jpeg"],'+
                   'a.lightbox[href$=".png"],a.lightbox[href$=".gif"]'
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

        opened  = true;

        var img     = $( "<img>" ),
            content = $( "<div>" )
                        .addClass( "ui-lightbox-container" )
                        .append( '<img src="/images/scripts/loading.gif" />' )
                        .width( 20 )
                        .height( 20 )
                        .css( {
                            "line-height": "20px",
                            "text-align": "center",
                            "vertical-align": "middle"
                        } );

        close = js.core.layer( content );

        img.load( function () {
            var titleNode   = null,
                imgWidth    = Math.max( img.width(), 1 ),
                imgHeight   = Math.max( img.height(), 1 ),
                layer       = $( '.ui-overlay' ).first(),
                resize      = function () {
                    var padding     = params.padding,
                        availWidth  = layer.width(),
                        availHeight = layer.height(),
                        titleHeight = 0,
                        allWidth,
                        allHeight,
                        toWidth,
                        toHeight,
                        animate = {
                            "easing": params.easing,
                            "duration": params.duration,
                            "complete": function () {

                            }
                        };

                    allWidth = Math.min(
                        availWidth,
                        2 * padding + imgWidth
                    );

                    toWidth = allWidth - 2 * padding;

                    if ( titleNode )
                    {
                        titleNode.width( toWidth );
                        titleHeight = titleNode.height();
                    }

                    toHeight    = toWidth * imgHeight / imgWidth;
                    allHeight   = titleHeight + 3 * padding + toHeight;

                    if ( allHeight > availHeight )
                    {
                        allHeight = availHeight;
                        toHeight  = allHeight - titleHeight - 3 * padding;
                        toWidth   = toHeight * imgWidth / imgHeight;
                    }

                    content.animate( {
                        "width": allWidth,
                        "height": allHeight
                    }, animate );

                    img.animate( {
                        "width": toWidth,
                        "height": toHeight
                    }, animate );
                };

            img.css( {
                "width": "1px",
                "height": "1px",
                "padding": params.padding + "px"
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
                                            .css( {
                                                "padding": params.padding + "px",
                                                "padding-top": "0px"
                                            } )
                );
            }

            $( global ).on( "resize.lightbox", resize );
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
