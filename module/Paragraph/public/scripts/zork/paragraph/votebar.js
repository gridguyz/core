/**
 * Paragraph functionalities
 * @package zork
 * @subpackage paragraph
 * @author Sipos Zoltán <sipos.zoltan@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.votebar !== "undefined" )
    {
        return;
    }

    /**
     * Vote
     *
     * @param {HTMLElement|$} element
     */
    global.Zork.Paragraph.prototype.votebar = function ( element )
    {
        element = $( element );

        element.find('td'.each(function()
        {
            if($(this).find('span span').length>0)
            {
                $(this).find('span span').css({height: 0}).animate({ height: $(this).data('jsPercentage')+'%' },500);
            }
        }));
    };

    global.Zork.Paragraph.prototype.votebar.isElementConstructor = true;

} ( window, jQuery, zork ) );
