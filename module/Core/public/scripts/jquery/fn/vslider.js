/**
 * Vertical slider
 *
 * @package jQuery
 * @subpackage fn
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */

( function ( global, $, js )
{
    "use strict";

    js.style( "/styles/scripts/vslider.css" );

    $.widget( "ui.vslider",
    {
        "options":
        {
            "items": "p, div, figure, fieldset, label",
            "rollHeight": null,
            "rollSpeed": 300,
            "rollEasing": "linear",
            "disabled": null,
            "itemheight": null
        },

        "classes":
        {
            "mainContainer": "ui-vslider ui-widget",
            "contentContainer": "ui-vslider-content",
            "itemsContainer": "ui-vslider-items",
            "itemContainer": "ui-vslider-item",
            "buttonNext": "ui-vslider-button next",
            "buttonPrev": "ui-vslider-button prev"
        },

        "_create": function()
        {
            var self = this,
            options = this.options;

            if ( this.element.is( ":disabled" ) )
            {
                options.disabled = true;
            }

            if( this.element.data( "jsVsliderItemheight" ) )
            {
                this.options.itemheight = this.element.data("jsVsliderItemheight");
            }

            this.element.addClass( self.classes.mainContainer );

            this.element.children().not( options.items ).hide();

            this.items = this.element.children( options.items )
                        .addClass( self.classes.itemContainer );

            this.contentContainer = $( "<div />" )
                                    .addClass(self.classes.contentContainer);

            this.itemsContainer = $( "<div />" )
                                  .addClass(self.classes.itemsContainer)
                                  .css( "margin-top", "0px" )
                                  .appendTo(this.contentContainer )
                                  .append( this.items );

            this.element.append( this.contentContainer );

            this.prev = $( '<button type="button" />' );
            this.prev.addClass( self.classes.buttonPrev  )
                    .prependTo( this.element )
                    .click( function(){ self._buttonClick("prev") } );

            this.next = $( '<button type="button" />' );
            this.next.addClass( self.classes.buttonNext   )
                    .appendTo( this.element )
                    .click( function(){ self._buttonClick("next") } );

            this.refreshButtons();
        },
        "widget": function ()
        {
            return this.items;
        },
        "destroy": function ()
        {
            this.prev.remove();
            this.next.remove();
            this.items.removeClass( this.classes.itemsContainer );
            this.element.append( this.items );
            this.contentContainer.remove();
            this.element.children().not( this.options.items ).show();
            this.element.removeClass( this.classes.mainContainer  );

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

            this.refresh();
        },
        "refresh": function ()
        {
            if( isNaN(parseInt(this.options.itemheight)) || parseInt(this.options.itemheight)<1 )
            {
                var maxHeight = 0
                this.items.each( function (idx,eleItem)
                {
                    var height = $(eleItem).height();
                    if ( height > maxHeight ) { maxHeight = height; }
                } );
                this.options.itemheight = maxHeight;
            }
            this.contentContainer.height( this.options.itemheight );


            var isDisabled = this.element.is( ":disabled" );

            if ( isDisabled !== this.options.disabled )
            {
                this._setOption( "disabled", isDisabled );
            }
            this.refreshButtons();
        },
        "currentItemIdx": 0,
        "_buttonClick": function(button)
        {
            var showItemIdx = this.currentItemIdx + (button == 'prev' ? -1 : 1);
            if( showItemIdx<0 ){ showItemIdx = 0; }
            if( showItemIdx>this.items.length-1 ){ showItemIdx = this.items.length-1; }
            this.slideToItem(showItemIdx);

        },
        "slideToItem": function(itemIndex)
        {
            if( itemIndex == this.currentItemIdx ){ return; }
            var item = $(this.items[itemIndex]);
            var pos = item.offset().top-this.itemsContainer.offset().top;

            this.itemsContainer.animate(
                            { "margin-top": "-"+pos+"px" },
                            this.options.rollSpeed,
                            this.options.rollEasing
                        );

            this.currentItemIdx = itemIndex;
            this.refreshButtons();
        },
        "refreshButtons": function()
        {
            if( this.currentItemIdx == 0 )
            {
                this.prev.attr('disabled', 'disabled');
            }
            else
            {
                this.prev.removeAttr('disabled');
            }
            if( this.currentItemIdx == this.items.length-1 )
            {
                this.next.attr('disabled', 'disabled');
            }
            else
            {
                this.next.removeAttr('disabled');
            }
        }
    } );

} ( window, jQuery, zork ) );