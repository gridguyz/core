/**
 * zork.ui.fsviewer 
 *
 * @package zork
 * @subpackage ui
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */

/**
 * 
 * @param {window} global
 * @param {jQuery} $
 * @param {Zork} js
 * @param {document} document
 * @param {Math} Math
 * @returns {void}
 */
( function ( global, $, js, document, Math )
{
    "use strict";

    if ( typeof js.ui.fsviewer !== "undefined" )
    {
        return;
    }

    js.style( "/styles/scripts/fsviewer.css" );

    var FsViewer = function(element,params)
        {
            this.init(element,params);
        };     
        
        FsViewer.prototype.element   = null;
        FsViewer.prototype.items     = [];
        FsViewer.prototype.settings  = null;
        FsViewer.prototype.featureSupport  = null;
        FsViewer.prototype.container = null;  
        FsViewer.prototype.itemsConatiner = null;  
        FsViewer.prototype.currentSlideIndex = null;
        FsViewer.prototype.restoreScrollPos = null;
        FsViewer.prototype.defaults = {
            itemSelector: '.fsviewer-item',
            itemImageAttr: 'href',
            itemTitleAttr: 'title',
            showTitle: false,
            jsSlideSpeed: 400,
            jsSlideEasing: 'linear', 
            swipeThreshold: 50,
            /**
             * @param {FsViewer} fsviewerInstance
             * @returns {void}
             */
            init:  function(fsviewerInstance){ },
            /**
             * @param {FsViewer} fsviewerInstance
             * @returns {void}
             */
            open:  function(fsviewerInstance){ },
            /**
             * @param {FsViewer} fsviewerInstance
             * @returns {void}
             */
            close: function(fsviewerInstance){ }
        };
        
        /**
         * 
         * @param {element} element
         * @param {object} params
         * @returns {void}
         */
        FsViewer.prototype.init = function(element,params)
        {
            var Me = this;
            Me.element  = $(element);
            Me.settings = $.extend({},Me.defaults,params);
            Me.items = [];
            Me.element.find(Me.settings.itemSelector).each(function(idx,item)
            {
                Me.items.push( {
                    src: $(item).attr(Me.settings.itemImageAttr), 
                    title: $(item).attr(Me.settings.itemTitleAttr), 
                    type: 'image'
                });
                $(item).on('click',function(e)
                {
                    e.preventDefault();
                    e.stopPropagation();
                    Me.open(idx);
                });
            });
            if( js.ui.fsviewer.featureSupport.checked === false )
            {
                js.ui.fsviewer.featureSupport.check();
            }
            Me.featureSupport = js.ui.fsviewer.featureSupport;
            if( Function.isFunction(Me.settings.init) ){ Me.settings.init(Me); }
        };
        
        /**
         * 
         * @param {integer} itemIndex
         * @returns {void}
         */
        FsViewer.prototype.open = function(itemIndex)
        {           
            var Me = this;
            Me.restoreScrollPos = 0 + $(global).scrollTop();
            global.scrollTo(0,1);
            Me.currentSlideIndex = null;
            Me.container = $('<div id="fsviewer-container">'
                             +'<div id="fsviewer-items"></div>'
                             +'<div id="fsviewer-nav"></div>'
                             +'<a id="fsviewer-close"></a>'
                             +'<a id="fsviewer-fullscreen"></a>'
                             +'</div>'
                            ).appendTo('body');
                    
            Me.itemsContainer = $('#fsviewer-items').css({opacity: 0});
            $.each( Me.items, function(idx,item)
            { 
                var textNode = Me.settings.showTitle && item.title
                               ? '<div class="text"><span>'+item.title+'</span></div>'
                               : '';
                Me.itemsContainer.append(
                    $('<div class="fsviewer-item '+item.type+'">'
                        + '<img src="'+item.src+'" alt="'+item.title+'">'
                        + textNode
                        +'</div>')
                );
            });
            if( Me.items.length>1 )
            {
                $('#fsviewer-nav').append( $('<a class="prev"></a><a class="next"></a>') );
                $('#fsviewer-nav a.prev').on('click',function(e)
                {
                    Me.slidePrev();
                });
                $('#fsviewer-nav a.next').on('click',function(e)
                {
                    Me.slideNext();
                });
            }
            
            if( Me.featureSupport.fullscreen!==true ){ $('#fsviewer-fullscreen').remove(); }
            
            $('#fsviewer-close').on('click',function(){ Me.close(); });
            Me.container.on('click',function(e)
            { 
                if( $(e.target).hasClass('fsviewer-item')){ Me.close(); };  
            });
            
            $(global).on('resize.fsviewer',function(){
                Me.resize();
            });
            
            Me.initKeyPress();
            Me.initTouch();
            Me.slideTo(itemIndex, true);
            
            if( Function.isFunction(Me.settings.open) ){ Me.settings.open(Me); }
            
            Me.itemsContainer.animate(
                {opacity: 1},
                Me.settings.jsSlideSpeed, 
                Me.settings.jsLideEasing
            );
        };
        
        /**
         * 
         * @returns {void}
         */
        FsViewer.prototype.slideNext = function()
        {
            var Me = this;
            Me.slideTo( Me.currentSlideIndex+1 );
        };
        
        /**
         * 
         * @returns {void}
         */
        FsViewer.prototype.slidePrev = function()
        {
            var Me = this;
            Me.slideTo( Me.currentSlideIndex-1 );
        };
        
        /**
         * 
         * @param {integer} itemIndex
         * @param {boolean} isOpen optional
         * @returns {void}
         */
        FsViewer.prototype.slideTo = function(itemIndex,isOpen)
        {
            var Me = this;
            if( itemIndex >= Me.items.length-1 ){ itemIndex = Me.items.length-1; }    
            if( itemIndex <= 0 ){ itemIndex = 0; }

            if( isOpen )
            {
                Me.itemsContainer.css({left: (-itemIndex*100)+'%'});
            }
            else
            {
                Me.itemsContainer.animate(
                    {left: (-itemIndex*100)+'%','margin-left':0},
                    Me.settings.jsSlideSpeed, 
                    Me.settings.jsLideEasing
                );
            }
            
            Me.currentSlideIndex = itemIndex;

            Me.updateNav();
        };        
        
        /**
         * 
         * @returns {$}
         */
        FsViewer.prototype.activeItem = function()
        {
            var Me = this;
            return $( Me.itemsContainer.find('.fsviewer-item').get(Me.currentSlideIndex) );
        };
        
        /**
         * 
         * @returns {void}
         */
        FsViewer.prototype.updateNav = function()
        {
            var Me = this;
            if( Me.items.length>1 )
            {
                $('#fsviewer-nav a').removeClass('disabled');
                if( Me.currentSlideIndex===0 ){ $('#fsviewer-nav a.prev').addClass('disabled'); }
                if( Me.currentSlideIndex===Me.items.length-1 ){ $('#fsviewer-nav a.next').addClass('disabled'); }
            }
        };
        
        /**
         * 
         * @returns {void}
         */
        FsViewer.prototype.resize = function()
        {
            global.scrollTo(0,1);
        };       
        
        /**
         * 
         * @returns {void}
         */
        FsViewer.prototype.close = function()
        {
            var Me = this;
            $(global).off('keyup.fsviewer');
            $(global).off('keypress.fsviewer');
            $(global).off('resize.fsviewer');
            $('body').off('touchstart.fsviewer');
            $('body').off('touchmove.fsviewer');
            $('body').off('touchend.fsviewer');
            $(global).scrollTop(Me.restoreScrollPos);
            Me.container.remove();            
            if( Function.isFunction(Me.settings.close) ){ Me.settings.close(Me); }
        };
        
        /**
         * 
         * @returns {void}
         */
        FsViewer.prototype.initKeyPress = function() 
        {
            var Me = this;
            $(global).on('keyup.fsviewer', function(e) 
            {
                e.preventDefault();
                e.stopPropagation();
                if( e.keyCode === 37 )//[arrowLeft]
                {
                    Me.slidePrev();
                }
                else if( e.keyCode === 39 || e.keyCode === 32 )//[arrowRight][Space]
                {
                    Me.slideNext();
                }
                else if( e.keyCode === 27 )//[Esc]
                {
                    Me.close();
                }
                else if( e.keyCode === 36 )//[Home]
                {
                    Me.slideTo(0);;
                }
                else if( e.keyCode === 35 )//[End]
                {
                    Me.slideTo(Me.items.length-1);;
                }
            });
            $(global).on('keypress.fsviewer', function(e) 
            {
                if( e.keyCode === 36 || e.keyCode === 35 )//[Home][End]
                {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        };
        
        /**
         * 
         * @returns {void}
         */
        FsViewer.prototype.initTouch = function()
        {
            var Me = this,            
                touchStartCoords = {},
                touchEndCoords = {};
        
            $('body').on('touchstart.fsviewer', function(e) 
            {
                touchEndCoords = e.originalEvent.targetTouches[0];
                touchStartCoords.pageX = e.originalEvent.targetTouches[0].pageX;
                touchStartCoords.pageY = e.originalEvent.targetTouches[0].pageY;
                
            });
            $('body').on('touchmove.fsviewer', function(e) 
            {
                var orig = e.originalEvent;
                touchEndCoords = orig.targetTouches[0];
                e.preventDefault();
                var distance = Math.round((touchEndCoords.pageX - touchStartCoords.pageX)/3);
                Me.itemsContainer.css({'margin-left':(0+distance)+'px'});
            });
            $('body').on('touchend.fsviewer', function(e) 
            {
                var distance = touchEndCoords.pageX - touchStartCoords.pageX,
                    swipeThreshold = Me.settings.swipeThreshold;
                if (distance >= swipeThreshold) 
                {
                    Me.slidePrev();
                } 
                else if (distance <= -swipeThreshold) 
                {
                    Me.slideNext();
                }
                else
                {
                    Me.slideTo(Me.currentSlideIndex);
                }
            });
                
            
        };

    /**
     * 
     * @param {element} element
     * @param {obhect} params
     * @returns {FsViewer}
     */
    global.Zork.Ui.prototype.fsviewer = function ( element, params )
    {
        return new FsViewer(element, params);
    };

    /**
     * js.ui.fsviewer() is enabled for element constructing
     */
    global.Zork.Ui.prototype.fsviewer.isElementConstructor = true;
    

    global.Zork.Ui.prototype.fsviewer.featureSupport = {
        checked:  false,
        csstrans: false,
        /**
         * @returns {boolean}
         */
        check: function()
        {
            if( this.checked ){ return false; }
            var 
                checkCssTrans = function() 
                {
                    var transition = ['transition', 'MozTransition', 'WebkitTransition', 'OTransition', 'msTransition', 'KhtmlTransition'],
                        ele        = document.documentElement
                    ;
                    for (var i = 0; i < transition.length; i++) 
                    {
                        if (transition[i] in ele.style){ return true; }
                    }
                    return false;
                }
            ;
            this.csstrans = checkCssTrans();             
            this.checked = true;
            return true;
        }
    };
    
} ( window, jQuery, zork, document, Math ) );
