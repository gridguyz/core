/*!
 * jQuery UI Sortabletree
 *
 * Depends:
 *	jquery.ui.core.js
 *	jquery.ui.widget.js
 *	jquery.ui.mouse.js
 */
(function( $, undefined ) {

var bounding = function ( nodeset ) {
    var box = {
        top: 0,
        left: 0,
        right: 0,
        bottom: 0
    };

    if ( 0 in nodeset ) {
        var node = nodeset[0];

        if ( "getBoundingClientRect" in node ) {
            var rect = node.getBoundingClientRect(),
                doc  = $( node.ownerDocument || document ),
                top  = doc.scrollTop(),
                left = doc.scrollLeft();

            box.top    = top  + rect.top;
            box.left   = left + rect.left;
            box.right  = left + rect.right;
            box.bottom = top  + rect.bottom;
        } else if ( node.nodeType == 1 ) {
            box        = nodeset.offset();
            box.right  = box.left + nodeset.width();
            box.bottom = box.top  + nodeset.height();

            $( node ).contents().each( function () {
                var sub = bounding( $( this ) );

                if ( sub.width <= 0 || sub.height <= 0 ) {
                    return;
                }

                box.top    = Math.min( box.top,    sub.top    );
                box.left   = Math.min( box.left,   sub.left   );
                box.right  = Math.max( box.right,  sub.right  );
                box.bottom = Math.max( box.bottom, sub.bottom );
            } );
        }
    }

    box.width  = box.right  - box.left;
    box.height = box.bottom - box.top;
    return box;
};

$.widget( "ui.sortabletree", $.ui.mouse, {
    widgetEventPrefix: "sortabletree",
    options: {
        handle: false,
        containers: "> ul",
        items: "> li",
        leaves: false,
        cursor: "move",
        scroll: true,
        scrollSensitivity: 30,
        scrollSpeed: 5,
        currentClass: "ui-sortabletree-sorting",

        // callbacks
        start: null,
        stop: null,
        over: null,
        sort: null,
        change: null,
        accept: function ( parent, child ) { return true; },
        horizontal: function ( item ) {
            return ( /left|right/ ).test( item.css( "float" ) ) ||
                   ( /inline(-block)?|table-cell/ ).test( item.css( "display" ) );
        }
    },
    _create: function() {
        this.containerCache = {};
        this.element.addClass( "ui-sortabletree" );

        //Get the items & containers
        this.refresh();

        //Initialize mouse events for interaction
        this._mouseInit();
    },
    _destroy: function() {
        this.element
            .removeClass( "ui-sortabletree ui-sortabletree-disabled" );
        this._mouseDestroy();

        this.containers.removeData( this.widgetName + "-container-of" );
        this.items.removeData( this.widgetName + "-container" )
                  .removeData( this.widgetName + "-item-of" );

        return this;
    },
    _setOption: function(key, value){
        if ( key === "disabled" ) {
            this.options[ key ] = value;

            this.widget()
                .toggleClass( "ui-sortabletree-disabled", !! value );
        } else {
            // Don't call widget base _setOption for disable as it adds ui-state-disabled class
            $.Widget.prototype._setOption.apply( this, arguments );
        }
    },
    refresh: function() {
        this._refreshItems();
     // this.refreshPositions();
        return this;
    },
    _findContainersAndChildren: function ( node, containers, items ) {
        var o = this.options,
            t = this;
        node  = $( node );
        node.find( o.containers )
            .each( function () {
                var c = this;
                containers.push( this );
                $( this ).data( t.widgetName + "-container-of", t )
                         .find( o.items )
                         .each( function () {
                             var i = $( this ).data( t.widgetName + "-container", c )
                                              .data( t.widgetName + "-item-of", t );

                             items.push( this );
                             if ( o.leaves && i.is( o.leaves ) ) {
                                 return;
                             }

                             t._findContainersAndChildren(
                                 i,
                                 containers,
                                 items
                             );
                         } );
            } );
    },
    _refreshItems: function ( event ) {
        var containers  = [],
            items       = [];

        this._findContainersAndChildren( this.element, containers, items );
        this.containers = $( containers );
        this.items      = $( items );
    },
    _mouseCapture: function ( event, overrideHandle ) {
        var currentItem         = null,
            currentContainer    = null,
            currentAfter        = null,
            currentBefore       = null,
            validHandle         = false,
            that                = this;

        if ( this.options.disabled || this.options.type === "static" ) {
            return false;
        }

        //We have to refresh the items data once first
        this._refreshItems( event );

        //Find out if the clicked node (or one of its parents) is a actual item in this.items
        if ( $.data( event.target, that.widgetName + "-item" ) === that ) {
            currentItem = $( event.target );
        }

        $( event.target ).parents().each( function() {
            if ( ! currentItem && $.data( this, that.widgetName + "-item-of" ) === that ) {
                currentItem = $( this );
            }
            if ( currentItem && $.data( this, that.widgetName + "-container-of" ) === that ) {
                currentContainer = $( this );
                return false;
            }
        } );

        if ( ! currentItem || ! currentContainer ) {
            return false;
        }

        if ( this.options.handle && ! overrideHandle ) {
            $( this.options.handle, currentItem )
                   .find( "*" )
                   .addBack()
                   .each( function () {
                        if ( this === event.target ) {
                            validHandle = true;
                        }
                    } );

            if ( ! validHandle ) {
                return false;
            }
        }

        currentAfter    = currentItem;
        currentBefore   = currentItem;

        while ( ( currentAfter = currentAfter.prev() ) && currentAfter.lenght ) {
            if ( ~ $.inArray( currentAfter[0], this.items ) ) {
                break;
            }
        }

        while ( ( currentBefore = currentBefore.next() ) && currentBefore.lenght ) {
            if ( ~ $.inArray( currentBefore[0], this.items ) ) {
                break;
            }
        }

        this.currentItem        = currentItem;
        this.currentAfter       = currentAfter;
        this.currentBefore      = currentBefore;
        this.currentContainer   = currentContainer;
     // this._removeCurrentsFromItems();
        return true;
    },
    _mouseStart: function ( event, overrideHandle, noActivation ) {
        var that    = this,
            accept  = this.options.accept,
            current = this.currentItem[0],
            possibleContainers = [],
            possibleItems = [];

        this.currentItem.addClass( this.options.currentClass );

        this.containers.each( function () {
            if ( accept( this, current ) ) {
                possibleContainers.push( this );
            }
        } );

        this.items.each( function () {
            if ( ~ $.inArray( $( this ).data( that.widgetName + "-container" ), possibleContainers ) ) {
                possibleItems.push( this );
            }
        } );

        this.possibleContainers = $( possibleContainers );
        this.possibleItems      = $( possibleItems );

        if ( this.options.cursor && this.options.cursor !== "auto" ) {
            var body = this.document.find( "body" );

            // support: IE
            this.storedCursor = body.css( "cursor" );
            body.css( "cursor", this.options.cursor );

            this.storedCursorStylesheet = $(
                "<style> * { cursor: " +
                this.options.cursor +
                " !important; } </style>"
            ).appendTo( body );
        }

        this._trigger( "start", event, this._uiHash() );
        return true;
    },
    _mouseDrag: function ( event ) {
        var i,
            item,
            contr,
            box,
            relatedNode,
            position,
            contrDelta  = 5,
            options     = this.options,
            currbox     = bounding( this.currentItem );

        if ( ! ( event.pageX >= currbox.left  &&
                 event.pageY >= currbox.top   &&
                 event.pageX <= currbox.right &&
                 event.pageY <= currbox.bottom ) )
        {
            for ( i = this.possibleItems.length; ~ i; --i ) {
                if ( this.possibleItems[i] === this.currentItem[0] ) {
                    continue;
                }

                item    = $( this.possibleItems[i] );
                box     = bounding( item );

                if ( event.pageX >= box.left   &&
                     event.pageY >= box.top    &&
                     event.pageX <= box.right  &&
                     event.pageY <= box.bottom )
                {
                    relatedNode = item;
                    position    = options.horizontal( item )
                        ? ( event.pageX < box.left + box.width  / 2 ? "before" : "after" )
                        : ( event.pageY < box.top  + box.height / 2 ? "before" : "after" );
                    break;
                }
            }

            for ( i = this.possibleContainers.length; ~ i; --i ) {
                contr   = $( this.possibleContainers[i] );
                box     = bounding( contr );

                if ( event.pageX >= box.left   - contrDelta &&
                     event.pageY >= box.top    - contrDelta &&
                     event.pageX <= box.right  + contrDelta &&
                     event.pageY <= box.bottom + contrDelta &&
                     ( ! relatedNode ||
                       ~ $.inArray( relatedNode[0], contr.parents() ) ) )
                {
                    relatedNode = null;

                    contr.find( options.items ).each( function () {
                        var item = $( this ),
                            box  = bounding( item );

                        if ( options.horizontal( item )
                             ? event.pageX > box.right
                             : event.pageY > box.bottom )
                        {
                            relatedNode = item;
                            position    = "after";
                        } else if ( ! relatedNode ) {
                            relatedNode = item;
                            position    = "before";
                        }
                    } );

                    if ( ! relatedNode ) {
                        relatedNode = contr;
                        position    = "append";
                    }

                    break;
                }
            }

            if ( relatedNode ) {
                relatedNode[position]( this.currentItem );
            }
        }

        this._trigger( "sort", event, this._uiHash() );

        if ( options.scroll ) {
            var top,
                left,
                right,
                bottom,
                doc = this.currentItem[0].ownerDocument || document,
                win = doc.defaultView || doc.parentWindow || window;

            doc     = $( doc );
            win     = $( win );
            top     = doc.scrollTop();
            left    = doc.scrollLeft();
            right   = left + win.width();
            bottom  = top + win.height();

            if ( event.pageX < Math.min( left + options.scrollSensitivity, ( left + right ) / 2 ) ) {
                doc.scrollLeft( left - options.scrollSpeed );
            } else if ( event.pageX > Math.max( right - options.scrollSensitivity, ( left + right ) / 2 ) ) {
                doc.scrollLeft( left + options.scrollSpeed );
            }

            if ( event.pageY < Math.min( top + options.scrollSensitivity, ( top + bottom ) / 2 ) ) {
                doc.scrollTop( top - options.scrollSpeed );
            } else if ( event.pageY > Math.max( bottom - options.scrollSensitivity, ( top + bottom ) / 2 ) ) {
                doc.scrollTop( top + options.scrollSpeed );
            }
        }

        return true;
    },
    _mouseStop: function ( event, noPropagation ) {
        if ( this.storedCursor ) {
            this.document.find( "body" ).css( "cursor", this.storedCursor );
            this.storedCursorStylesheet.remove();
        }

        this.currentItem.removeClass( this.options.currentClass );

        var ui,
            change,
            currentAfter,
            currentBefore,
            currentContainer,
            containers = this.containers;

        this.currentItem.parents().each( function () {
            if ( ~ $.inArray( this, containers ) ) {
                currentContainer = $( this );
                return false;
            }
        } );

        currentAfter    = this.currentItem;
        currentBefore   = this.currentItem;

        while ( ( currentAfter = currentAfter.prev() ) && currentAfter.lenght ) {
            if ( ~ $.inArray( currentAfter[0], this.items ) ) {
                break;
            }
        }

        while ( ( currentBefore = currentBefore.next() ) && currentBefore.lenght ) {
            if ( ~ $.inArray( currentBefore[0], this.items ) ) {
                break;
            }
        }

        if ( this.currentAfter[0]     != currentAfter[0]    ||
             this.currentBefore[0]    != currentBefore[0]   ||
             this.currentContainer[0] != currentContainer[0] )
        {
            change = true;
        }

        this.currentAfter       = currentAfter;
        this.currentBefore      = currentBefore;
        this.currentContainer   = currentContainer;
        ui                      = this._uiHash();

        if ( change ) {
            this._trigger( "change", event, ui );
        }

        this._trigger( "stop", event, ui );
        return true;
    },
    _uiHash: function () {
        return {
            item: this.currentItem,
            after: this.currentAfter,
            before: this.currentBefore,
            container: this.currentContainer
        };
    }
} );

}( jQuery ));
