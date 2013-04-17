( function ( global, $, js )
{
    "use strict";

    js.style( "/styles/scripts/carousel.css" );

    $.widget( "ui.carousel",
    {
        "options":
        {
            "items": "p, div, figure, fieldset",
            "captions": "label, figcaption, legend",
            "refreshRate": 1000,
            "rollWidth": null,
            "rollSpeed": null,
            "rollEasing": null,
            "disabled": null
        },
        "_create": function()
        {
            if ( typeof this.options.disabled !== "boolean" )
            {
                this.options.disabled = this.element.attr( "disabled" );
            }

            var self = this,
                options = this.options,
                baseClasses = "ui-carousel ui-widget " +
                    "ui-widget-content ui-corner-all",
                itemClasses = "ui-carousel-item ui-corner-all ui-state-default",
                hoverClass = "ui-state-hover";
            
            if ( Object.isUndefined( options.rollSpeed ) )
            {
                options.rollSpeed = 400;
            }
            
            if ( Object.isUndefined( options.rollEasing ) )
            {
                options.rollEasing = "linear";
            }
            
            if ( this.element.is( ":disabled" ) )
            {
                options.disabled = true;
            }
            
            this.element.addClass( baseClasses );
            this.element.children().not( options.items ).hide();
            this.items = this.element.children( options.items );
            this.itemsContainer = $( "<div />" ).
                addClass( "ui-carousel-items" );
            this.itemsContainer.append( this.items );
            this.element.append( this.itemsContainer );
            this.items.find( options.captions ).
                addClass( "ui-carousel-caption" );
            this.items.
                addClass( itemClasses ).
                hover(
                    function () { $( this ).addClass( hoverClass ); },
                    function () { $( this ).removeClass( hoverClass ); }
                );

            this.itemsContainer.css( "margin-left", "0px" );

            this.prev = $( "<button type='button'></button>" );
            this.prev.addClass( "ui-carousel-button-prev" ).
                button( {
                    "icons": { "primary": "ui-icon-circle-arrow-w" },
                    "text": false
                } ).
                prependTo( this.element ).
                click( function ()
                {
                    var margin = - parseInt( self.itemsContainer.
                            css( "margin-left" ), 10 ),
                        rollWidth = self.options.rollWidth ||
                            parseInt( self.element.width() / 2, 10 ),
                        roll = false;

                    if ( margin > rollWidth ) { roll = "+=" + rollWidth; }
                    else if ( margin > 0 ) { roll = "0px"; }

                    if ( roll )
                    {
                        self.itemsContainer.animate(
                            { "margin-left": roll },
                            self.options.rollSpeed,
                            self.options.rollEasing
                        );
                    }
                } );

            this.next = $( "<button type='button'></button>" );
            this.next.addClass( "ui-carousel-button-next" ).
                button( {
                    "icons": { "primary": "ui-icon-circle-arrow-e" },
                    "text": false
                } ).
                appendTo( this.element ).
                click( function ()
                {
                    var margin = - parseInt( self.itemsContainer.
                            css( "margin-left" ), 10 ),
                        max = self.itemsContainer.width() -
                            self.element.width(),
                        rollWidth = self.options.rollWidth ||
                            parseInt( self.element.width() / 2, 10 ),
                        roll = false;

                    if ( margin < ( max - rollWidth ) ) { roll = "-=" + rollWidth; }
                    else if ( margin < max ) { roll = "-" + max + "px"; }

                    if ( roll )
                    {
                        self.itemsContainer.animate(
                            { "margin-left": roll },
                            self.options.rollSpeed,
                            self.options.rollEasing
                        );
                    }
                } );
            
            this.interval = options.refreshRate > 0
                ? setInterval( $.proxy( this.refresh, this ), options.refreshRate )
                : null;
            
            // TODO: pull out $.Widget's handling for the disabled option into
            // $.Widget.prototype._setOptionDisabled so it's easy to proxy and can
            // be overridden by individual plugins
            this._setOption( "disabled", options.disabled );
        },
        "widget": function ()
        {
            return this.items;
        },
        "destroy": function ()
        {
            this.prev.remove();
            this.next.remove();
            this.items.find( ".ui-carousel-caption" ).
                removeClass( "ui-carousel-caption" );
            this.items.removeClass( "ui-carousel-item ui-state-default " +
                "ui-state-hover ui-state-active" );
            this.element.append( this.items );
            this.itemsContainer.remove();
            this.element.children().not( this.options.items ).show();
            this.element.removeClass( "ui-carousel ui-widget " +
                "ui-widget-content ui-corner-all" );
            
            if ( null !== this.interval )
            {
                clearInterval( this.interval );
            }
            
            $.Widget.prototype.destroy.call( this );
        },
        "_setOption": function ( key, value )
        {
            $.Widget.prototype._setOption.apply( this, arguments );

            if ( key === "disabled" )
            {
                if ( value )
                {
                    this.element.attr( "disabled", true );
                }
                else
                {
                    this.element.removeAttr( "disabled" );
                }
            }
            else if ( key === "refreshRate" )
            {
                if ( null !== this.interval )
                {
                    clearInterval( this.interval );
                }
                
                this.interval = value > 0
                    ? setInterval( $.proxy( this.refresh, this ), value )
                    : null;
            }
        },
        "refresh": function ()
        {
            var maxHeight = 16, sumWidth = 0;
            this.items.each( function ()
            {
                var self = $( this ),
                    width = self.outerWidth( true ),
                    height = self.outerHeight( true );

                if ( height > maxHeight ) { maxHeight = height; }
                sumWidth += width;
            } );
            this.element.height( maxHeight );
            this.prev.height( maxHeight - 6 );
            this.next.height( maxHeight - 6 );
            this.itemsContainer.width( sumWidth );

            var isDisabled = this.element.is( ":disabled" );

            if ( isDisabled !== this.options.disabled )
            {
                this._setOption( "disabled", isDisabled );
            }
        }
    } );

} ( window, jQuery, zork ) );
