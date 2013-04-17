/**
 * Form functionalities for multiMarker form element
 * @package zork
 * @subpackage form
 * @author Sipi
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.multiMarker !== "undefined" )
    {
        return;
    }

    /**
     * Multi Marker
     *
     * @memberOf Zork.Form.Element.multiMarker
     */
    global.Zork.Form.Element.prototype.multiMarker = function ( element )
    {
        element = $( element );
        js.style('/styles/modules/googleMap/multiMarker.css');

        var optionCount = element.find('.multi-marker-box-edit > dt.blue').length,
            template = element.find('.multi-marker-box-edit > span').data('template'),
            createClick = function()
            {
                var item;
                element.find('.multi-marker-box-edit').append
                (
                    item = $(
                        template.replace(/__INDEX__/ig, optionCount )
                    )
                );
                item.first('label').html( item.first('label').html().replace(/\d+/,optionCount+1) );
                js.core.parseDocument(item);
                item = item.find('a').click(deleteClick);
                element.find('.multi-marker-box-empty').css({display:'none'});
                element.find('.multi-marker-box-edit').css({display:'block'});

                orderMethod(element.find('.multi-marker-box-edit dt.blue'));

                optionCount++;

                return false;
            },
            orderMethod = function(fields){
                for(var i = 0; i < fields.length; i++)
                {
                    var actual = $(fields[i]);
                    actual.children('input').attr('name', optionName + '[' + i + ']' + '[id]' );
                    actual.children('label').html( optionMask.replace('%d',''+(i+1)) );
                    actual = actual.next().next().next();
                    actual.children('input').attr('name', optionName + '[' + i + ']' + '[address]' );
                    actual = actual.next().next();
                    actual.children('input').attr('name', optionName + '[' + i + ']' + '[latitude]' );
                    actual = actual.next().next();
                    actual.children('input').attr('name', optionName + '[' + i + ']' + '[longitude]' );
                }
            },
            deleteClick = function()
            {
                var editBox = $(this).parent().parent();
                var items = new Array();

                items[1] = $(this).parent();
                items[0] = items[1].prev();
                for(var i = 2; i<8; i++)
                {
                    items[i] = items[i-1].next();
                }

                for(var i in items)
                {
                    items[i].remove();
                }

                var fields = editBox.children('dt.blue');

                orderMethod(fields);

                optionCount--;

                if(optionCount==0)
                {
                    element.find('.multi-marker-box-empty').css({display: 'block'});
                    element.find('.multi-marker-box-edit').css({display: 'none'});
                }

                return false;

            };
        element.find('.multi-marker-box-edit > span').remove();

        if(optionCount==0)
        {
            element.find('.multi-marker-box-empty').css({display: 'block'});
            element.find('.multi-marker-box-edit').css({display: 'none'});
        }
        else
        {
            element.find('.multi-marker-box-empty').css({display: 'none'});
            element.find('.multi-marker-box-edit').css({display: 'block'});
        }

        element.find('.multi-marker-box-edit dd a.delete').click(deleteClick);
//        paragraph-icon-delete-hover.png
        element.find('.multi-marker-box-create dd a').click(createClick);

    };

    global.Zork.Form.Element.prototype.multiMarker.isElementConstructor = true;

} ( window, jQuery, zork ) );
