/**
 * Paragraph functionalities
 * @package zork
 * @subpackage paragraph
 * @author Sipos Zoltán <sipos.zoltan@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";
    
    if ( typeof js.paragraph.vote !== "undefined" )
    {
        return;
    }
        
    /**
     * Vote
     * 
     * @param {HTMLElement|$} element
     */
    global.Zork.Paragraph.prototype.vote = function ( element )
    {
        element = $( element );
        
        var ajaxLoad = function(url)
            {
                js.core.loadRawElement( {
                    url: url,
                    target: element.parent()
                 // replace: true,
//                    success: onLoad
                } );
            },
            viewButtonClick = function()
            {
                ajaxLoad( $(this).parents('form').attr('action') + '?op=view' );
            },
            backButtonClick = function()
            {
                ajaxLoad( $(this).parents('form').attr('action') );
            },
            onSubmit = function()
            {
                js.core.loadRawElement( {
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serializeArray(),
                    target: element.parent()
                 // replace: true,
//                    success: onLoad
                } );

                return false;
            },
            onLoad = function()
            {
                element.find('button[name="back"]').click(backButtonClick);
                element.find('button[name="view"]').click(viewButtonClick);                
                element.find('form').submit(onSubmit);
            };
        
        onLoad();
        
    };
        
    global.Zork.Paragraph.prototype.vote.isElementConstructor = true;
    
} ( window, jQuery, zork ) );
