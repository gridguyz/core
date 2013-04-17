/**
 * Lightbox effect
 *
 * @package zork
 * @subpackage ui
 * @author Sipi
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.lightbox !== "undefined" )
    {
        return;
    }

    var lightboxIsActive = false,
        stylesheetArray = new Array(),
        /**
         *  load configurations from element
         */
        defaultValues = {
            type: 'image',
            width: null,
            height: null,
            color: '#fff',
            backgroundColor: '#000',
            stylesheet: null,
            javascript: null,
            gallery: null,
            html: null,
            closeButton: true,
            closeOnOverlay: true,
            form: null,
            formData: null,
            callback: null
        },

        getConfigurationsFromElement = function(element)
        {
            var configs = {element: element};

            configs.href = element.data('jsHref')
                ? element.data('jsHref')
                : ( element.attr('href')
                    ? element.attr('href')
                    : ( element.attr('src')
                        ? element.attr('src')
                        : ( (element[0].tagName.toLowerCase()=='form' && element.attr('action'))
                            ? element.attr('action')
                            : ( element.parents('form').attr('action')
                                ? element.parents('form').attr('action')
                                :  global.location
                              )
                          )
                      )
                  );

            configs.type = element.data('jsLinkType')
                ? element.data('jsLinkType')
                : ( /\.(?:jpg|jpeg|gif|png)$/i.test(configs.href)
                    ? 'image'
                    : defaultValues.type
                  );

            for(var property in defaultValues)
            {
                if(property=='type')
                {
                    continue;
                }

                configs[property] = element.data('js' + property.charAt(0).toUpperCase() + property.slice(1)) || defaultValues[property];
            }

            if(configs.form!==null && typeof(configs.form).toLowerCase()=='string')
            {
                configs.form = (configs.form=='_parent')
                    ? ( element.parents('form').first() )
                    : ( $('#'+configs.form) );
            }
            if(configs.form === null && element[0].tagName.toLowerCase()=='form')
            {
                configs.form = element;
            }
            return configs;

        },

        getConfigurationsFromDefaults = function(configs)
        {
            for(var property in defaultValues)
            {
                if(property=='type')
                {
                    continue;
                }

                configs[property] = configs[property]
                    ? configs[property]
                    : defaultValues[property];
            }

            return configs;

        },
        /**
         *   Loader methods
         */
        loadImage = function( url, callback )
        {
            var img = new Image();
            $(img).load(function()
            {
                callback(img);
            });
            img.src = url;
            return img;
        },

        urlIsImage = function(url){
            return /\.(?:jpg|jpeg|png|gif)$/i.test(url);
        },

        getImageUrlsOfContent = function(content){
            var imageArray = new Array();
            content.find('*').each(function(){
                var item = $(this);
                if( urlIsImage(item.attr('src')) )
                {
                    imageArray.push(this.src);
                }
                if( urlIsImage(item.attr('href')) )
                {
                    imageArray.push(this.href);
                }
                var cssImage = item.css('backgroundImage').replace(/^\s*url\("?/,'').replace(/"?\)$/,'');
                if( urlIsImage(cssImage))
                {
                    imageArray.push(cssImage);
                }
            });
            return imageArray;
        },
        loadImagesOfContent = function( content, callback )
        {
            var imageArray = getImageUrlsOfContent(content);
            var length = imageArray.length;
            if( length == 0)
            {
                callback();
                return;
            }
            var counter = 0;
            for(var i in imageArray)
            {
                loadImage(imageArray[i],function(img)
                {
                    if(++counter==length)
                    {
                        callback();
                    }
                });
            }
        },
        loadHtml = function( content, callback )
        {
            if(content)
            {
                loadImagesOfContent( $(content) , callback );
            }
            else
            {
                callback()
            }
        },
        loadStylesheet = function( url, callback )
        {
            if( url && stylesheetArray.indexOf(url)==-1 )
            {
                $.ajax({
                    url: url,
                    success: function(data)
                    {
                        $('head').append
                        (
                            $('<style type="text/css"></style>').html(data)
                        );

                        callback();
                    }
                });
            }
            else
            {
                callback();
            }
        },
        loadJavascript = function( url, callback )
        {
            if(url)
            {
                js.core.script( url, callback);
            }
            else
            {
                callback();
            }
        },
        loadContent = function( configs , callback )
        {
            switch(configs.type)
            {
                case 'callback':
                    js.require(configs.callback,callback);
                    break;
                case 'ajax':
                    var settings =
                    {
                        url: configs.href,
                        success: callback
                    };
                    var method = 'GET';

                    if(configs.form)
                    {
                        configs.formData = configs.form.serializeArray();
                        method = configs.form.attr('method')
                            ? configs.form.attr('method').toUpperCase()
                            :'GET';
                    }

                    if(configs.formData)
                    {
                        settings.type = method;
                        settings.data = configs.formData;
                    }

                    $.ajax( settings );
                    break;
                case 'image':
                    loadImage(configs.href, callback);
                    break;
                case 'iframe':

                    var iframe = $('<iframe />').css(
                    {
                        display: 'block',
                        width: '100%',
                        position: 'absolute',
                        overflow: 'auto',
                        height: 0
                    });

                    callback(iframe);

                    break;
            }
        },

        loadAllContents = function( configs , callback )
        {
            loadStylesheet(configs.stylesheet, function()
            {
                loadJavascript(configs.javascript, function()
                {
                    loadHtml(configs.html, function()
                    {
                        loadContent(configs, function(data){
                            callback(data);
                        });
                    });
                });
            });
        },
        /**
         *    Manage window
         */

        changeContentToEmpty = function( container , callback )
        {
            if(container.children().length>0)
            {
                container.children().animate({opacity: 0.0}, 500, function()
                {
                    container.html('');
                    callback();
                });
            }
            else
            {
                callback();
            }
        },

        attachContentToContainer = function(content, configs, callback)
        {
            var container = configs.container,
                htmlContainer = null,
                innerContainer = $('<div></div>'),
                overlay = configs.container.parent().parent();

            container.append(innerContainer).css('position','relative');

            innerContainer.css(
            {
                position: 'relative',
                overflow: 'hidden'
            });

            if(configs.html)
            {
                htmlContainer = $('<div></div>').css(
                                {
                                    position: 'absolute',
                                    bottom: 0,
                                    width: '100%'
                                })
                                .html(configs.html);
                innerContainer.append(htmlContainer);
            }

            switch( configs.type )
            {
                case 'callback':
                    var div = $('<div></div>')
                                    .data('jsType',configs.callback)
                                    .css('position','absolute');

                    innerContainer.prepend(div);

                    var parent, callparts = configs.callback.split('.');
                    callparts.pop();
                    eval('parent = '+callparts.join('.')+';');
                    content.call(parent, div);

                    innerContainer.width(
                        configs.width
                            ? configs.width
                            : div.width()
                    );

                    innerContainer.height(
                        configs.height
                            ? configs.height
                            : (div.height()+(htmlContainer?htmlContainer.height():0))
                    );

                    callback();

                    break;
                case 'ajax':
                    var div = $('<div></div>')
                                    .css('position','absolute')
                                    .append($(content));

                    innerContainer.prepend(div);

                    innerContainer.width(
                        configs.width
                            ? configs.width
                            : div.width()
                    );

                    innerContainer.height(
                        configs.height
                            ? configs.height
                            : (div.height()+(htmlContainer?htmlContainer.height():0))
                    );

                    js.core.parseDocument( div );
                    callback();

                    break;
                case 'image':
                    var originalWidth = content.width,
                        originalHeight = content.height;
                    $( content )
                        .css('position','absolute')
                        .css('width','100%');
                    innerContainer.prepend( content );

                    if(configs.width && configs.height)
                    {
                        innerContainer.width(configs.width);
                        innerContainer.height(configs.height);
                    }
                    else if(configs.width)
                    {
                        innerContainer.width(configs.width);
                        innerContainer.height(
                            (configs.width * originalHeight / originalWidth) +
                                (htmlContainer?htmlContainer.height():0)
                        );
                    }
                    else if(configs.height)
                    {
                        var limitWidth = overlay.width() - 40,
                            limitHeight = overlay.height() - 40;

                        innerContainer.height(configs.height);
                        innerContainer.width(
                            Math.floor( configs.height * originalWidth / originalHeight )
//                                (htmlContainer?htmlContainer.height():0)
                        );
                        while(
                            htmlContainer &&
                            htmlContainer.height() + content.height > innerContainer.height()
//                            htmlContainer.height() < innerContainer.height() / 3
                        )
                        {
                            var newHeight = innerContainer.height() - htmlContainer.height();
                            var newWidth  =  Math.floor( newHeight * originalWidth / originalHeight );
                            innerContainer.width(newWidth);
                            if(htmlContainer.height() < innerContainer.height() / 3 )
                            {
                                htmlContainer.height(innerContainer.height()-content.height);
                            }
                        }
                    }
                    else
                    {
                        var limitWidth = overlay.width() - 40,
                            limitHeight = overlay.height() - 40;

                        innerContainer.width(Math.min(
                            limitWidth,
                            originalWidth
                        ));

                        while(
                            limitHeight < content.height + ((htmlContainer)?(htmlContainer.height()):0) &&
                            ( !htmlContainer || htmlContainer.height() < limitHeight / 3 )
                        )
                        {
                            var newHeight = limitHeight - ((htmlContainer)?(htmlContainer.height()):0);
                            var newWidth = Math.floor( newHeight * originalWidth / originalHeight );
                            innerContainer.width( newWidth );
                        }

                        if(limitHeight < content.height + ((htmlContainer)?(htmlContainer.height()):0))
                        {
                            htmlContainer.height(limitHeight-content.height);
                        }
                        innerContainer.height( content.height + ((htmlContainer)?(htmlContainer.height()):0) );
                    }
                    $(content)
                        .height(content.height)
                        .width(content.width);

                    callback();

                    break;
                case 'iframe':
                    content.load(function()
                    {
                        callback();
                    })

                    innerContainer.append(
                        content
                    );

                    content[0].src = configs.href;

                    var width = configs.width
                            ? configs.width
                            : overlay.width()-40,
                        height = configs.height
                            ? configs.height
                            : overlay.height()-40;

                    innerContainer.width(width);

                    innerContainer.height(height);

                    content.height(height-(htmlContainer?htmlContainer.height():0) );

                    break;
            }

        },

        changeWindowSize = function( configs, callback ){
            var container = configs.container;
            var oldSize = {width: container.parent().width(),height: container.parent().height()},
                newSize = {width: container.children().width(),height: container.children().height()};

            var time = Math.max(
                Math.abs( newSize.height - oldSize.height ),
                Math.abs( newSize.width - oldSize.width )
            );

            $(container)
                .parent()
                .animate(
                    {
                        width: '' + newSize.width + 'px',
                        height: '' + newSize.height + 'px',
                        marginLeft: '-' + Math.floor(newSize.width/2) + 'px',
                        marginTop: '-' + Math.floor(newSize.height/2) + 'px'
                    },
                    time
                )
                .parent()
                .children('.ui-widget-shadow')
                .animate(
                    {
                        width: '' + newSize.width + 'px',
                        height: '' + newSize.height + 'px',
                        marginLeft: '-' + (Math.floor(newSize.width/2)+10) + 'px',
                        marginTop: '-' + (Math.floor(newSize.height/2)+10) + 'px'
                    },
                    time,
                    callback
                );
        },

        createGalleryButtons = function( container )
        {
            var properties =
                {
                    position: 'absolute',
                    marginTop: '-16px',
                    top: '50%',
                    width: '32px',
                    height: '32px',
                    opacity: 0.0
                },
                createArrowButton = function(direction)
                {
                    return $('<a href="#"></a>')
                            .css(properties)
                            .css(direction, '16px')
                            .css('background','url(/images/common/lightbox/passive/'+direction+'-button.png)')
                            .hover(function()
                            {
                                $(this).children().stop().animate({opacity: 1.0},500);
                            },function()
                            {
                                $(this).children().stop().animate({opacity: 0.0},500);
                            });
                };
            container
                .append(createArrowButton('left'))
                .append(createArrowButton('right'))
                .hover(
                    function()
                    {
                        $(this).children('a').stop().animate({opacity: 1.0},500);
                    },
                    function()
                    {
                        $(this).children('a').stop().animate({opacity: 0.0},500);
                    }
                );
            container
                .children('a').each(function()
                {
                    $(this).append(
                        $('<span></span>').css(
                        {
                            display: 'block',
                            width: '32px',
                            height: '32px',
                            opacity: 0.0,
                            background: $(this).css('background').replace('/passive/','/active/')
                        })
                    )
                });

            return container;
        },

        setGallery = function( configs )
        {
            var arrows = configs.container.parent().children('a');
            var gallery = $('*[data-js-type="js.ui.lightbox"][data-js-gallery="' + configs.gallery + '"]');
            var index = gallery.index(configs.element);
            arrows.first().css('display',(index==0) ? 'none' : 'block');
            arrows.last().css('display',(index == gallery.length - 1) ? 'none' : 'block');
            arrows.first().off('click').click(function() {
                index--;
                global.Zork.Ui.prototype.lightboxChange(gallery[index])
            });
            arrows.last().off('click').click(function() {
                index++;
                global.Zork.Ui.prototype.lightboxChange(gallery[index])
            });
        },

        lightboxProcess = function( configs )
        {

            var loadCount = 0,
                data,
                loaderContainer = configs.container.parent().children('div:last').css(
                {
                    display: 'block'
                }),
                changeContent = function(){
                    loadCount++;
                    if( loadCount == 2 )
                    {
                        changeContentToEmpty( configs.container, function()
                        {
                            attachContentToContainer( data, configs, function()
                            {
                                loaderContainer.css('backgroundImage','none');

                                changeWindowSize( configs, function()
                                {
                                    loaderContainer.animate({opacity: 0.0},500,function()
                                    {
                                        loaderContainer.css('display','none');
                                        configs.container.parent().children('span').css('display','block').animate({opacity: 1.0},500);
                                    });
                                });
                            });
                        });

                        if(configs.gallery)
                        {
                            setGallery(configs);
                        }
                    }
                };

            configs.container.parent().children('span').animate({opacity: 0.0},500, function(){
                $(this).css('display','none');
            });
            loaderContainer.animate
            (
                {opacity: 1.0},
                (loaderContainer.css('opacity') == 1.0) ? 0 : 500,
                function()
                {
                    loaderContainer.css('backgroundImage','url(/images/scripts/loading.gif)');
                    changeContent()
                }
            );

            loadAllContents( configs, function(content)
            {
                data = content;
                changeContent()
            });
        },

        createCloserButton = function(container, closerMethod){
            $(container).append(
                $('<span></span>')
                    .css( {
                        display: 'none',
                        position: 'absolute',
                        zIndex: 999999,
                        cursor: 'pointer',
                        background: 'url("/images/scripts/lightbox/close-passive.png")',
                        top: '-18px',
                        right: '-18px',
                        opacity: 0.0
                    } )
                    .width(36)
                    .height(36)
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
                    .click(closerMethod)
                    .append(
                        $('<span></span>')
                            .css( {
                                display: 'block',
                                position: 'absolute',
                                width: '100%',
                                height: '100%',
                                background: 'url("/images/scripts/lightbox/close-active.png")',
                                opacity: 0.0
                            } )
                    )
            );
        },

        layerMethod = function(content, callback)
        {
//            if ( Function.isFunction( content ) )
//            {
//                resume = content;
//                content = false;
//            }
            if(!callback)
            {
                callback = function(){};
            }

            var layer = $( '<div class="ui-overlay ui-overlay-lightbox" />' ),
                overlay = $( '<div class="ui-widget-overlay" />' ),
                shadow = $( '<div class="ui-widget-shadow" />' ),
                intervalFunc = function ()
                {
                    var width   = ( content.width() / 2 ) +
                                    parseInt( shadow.css( "borderLeftWidth" ), 10 ),
                        height  = ( content.height() / 2 ) +
                                    parseInt( shadow.css( "borderTopWidth" ), 10 ),
                        stop    = parseInt( shadow.css( "borderTopWidth" ), 10 ) +
                                  parseInt( shadow.css( "paddingTop" ), 10 ),
                        sleft   = parseInt( shadow.css( "borderLeftWidth" ), 10 ) +
                                  parseInt( shadow.css( "paddingLeft" ), 10 );

                    shadow.css( {
                        "width"         : ( width * 2 ) + "px",
                        "height"        : ( height * 2 ) + "px",
                        "margin-top"    : "-" + ( height + stop ) + "px",
                        "margin-left"   : "-" + ( width + sleft ) + "px"
                    } );

                    content.css( {
                        "margin-top": "-" + height + "px",
                        "margin-left": "-" + width + "px"
                    } );
                };

            layer.css( {
                "position"  : "fixed",
                "top"       : "0px",
                "left"      : "0px",
                "width"     : "100%",
                "height"    : "100%",
                "z-index"   : 100
            } );

            content = $( content );

            layer.append( overlay )
                 .append( shadow )
                 .append( content );

            shadow.css( {
                "position"  : "absolute",
                "top"       : "50%",
                "left"      : "50%"
            } );

            content.css( {
                "position"  : "absolute",
                "top"       : "50%",
                "left"      : "50%"
            } );

            $( "body" ).append( layer.animate({opacity: 500}, 500, callback) );

            intervalFunc();

            return function ()
            {
                lightboxIsActive = false;

                layer.fadeOut( "fast", function ()
                {
                    layer.remove();
                } );
            };

        };

//
//  Publikus metódusok
//

    global.Zork.Ui.prototype.lightboxChangeByConfiguration = function ( configs )
    {
        configs = getConfigurationsFromDefaults( configs );

        configs.container = $('.ui-overlay').children('div:last').children('div:first');

        lightboxProcess(configs);
    };

    global.Zork.Ui.prototype.lightboxChange = function ( element )
    {

        element = $(element);
        global.Zork.Ui.prototype.lightboxChangeByConfiguration(
            getConfigurationsFromElement( element )
        )
    };

    global.Zork.Ui.prototype.lightboxOpenByConfiguration = function ( configs )
    {
        lightboxIsActive = true;

        js.style("/styles/scripts/lightbox.css");

        configs = getConfigurationsFromDefaults( configs );

        var container = $('<div></div>')
            .css('background',configs.color)
            .width( 16 )
            .height( 11 )
            .append(
                $('<div></div>')
                    .css({
                        position: 'absolute',
                        overflow: 'hidden',
                        width: '100%',
                        height: '100%'
                    })
            )
        if(configs.gallery)
        {
            container = createGalleryButtons(container);
        }
        container
            .append(
                $('<div></div>')
                    .css({
                        position: 'absolute',
                        overflow: 'hidden',
                        left: 0,
                        top: 0,
                        width: '100%',
                        height: '100%',
                        background: configs.color + ' url(/images/scripts/loading.gif) no-repeat center center',
                        opacity: 1.0
                    })

            );

        configs.closer = layerMethod(container, function()
        {
            lightboxProcess(configs);
        });

        global.Zork.Ui.prototype.closeLightbox = configs.closer;

        container.parent().children('.ui-widget-overlay').css({background: configs.backgroundColor});
        container.parent().children('.ui-widget-shadow').css({background: configs.color})

        configs.container = container.children().first();

        if(configs.closeOnOverlay)
        {
            container.parent().children('.ui-widget-overlay').on('click',configs.closer);
        }

        if(configs.closeButton)
        {
            createCloserButton(container, configs.closer);
        }
    };

    global.Zork.Ui.prototype.lightboxOpen = function ( element )
    {

        element = $(element);

        global.Zork.Ui.prototype.lightboxOpenByConfiguration(
            getConfigurationsFromElement( element )
        )

    };

    global.Zork.Ui.prototype.lightbox = function ( element )
    {
        element = $(element);

        element.on(
            (element[0].tagName.toLowerCase()=='form')?'submit':'click',
            function()
            {
                lightboxIsActive
                    ?global.Zork.Ui.prototype.lightboxChange(element)
                    :global.Zork.Ui.prototype.lightboxOpen(element);
                return false;
            }
        );

    };

    global.Zork.Ui.prototype.removeLightbox = function ( element )
    {
        element = $( element );

        if(element[0].tagName.toLowerCase()=='form')
        {
            element.off('submit');
        }
        else
        {
            element.off('click');
        }

    };

    global.Zork.Ui.prototype.lightbox.isElementConstructor = true;

} ( window, jQuery, zork ) );
