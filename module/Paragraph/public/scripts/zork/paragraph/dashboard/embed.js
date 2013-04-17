/**
 * Embed interface functionalities
 * @package zork
 * @subpackage embed
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";
    
    if ( typeof js.paragraph.dashboard.embed !== "undefined" )
    {
        return;
    }
    
    js.require( "js.embed" );
    
    /**
     * @class Embed dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.embed = function ( form, element )
    {
        form    = $( form );
        element = $( element );

        var 
            params = {},
            container = $( element.find('.paragraph-children') ),
            before = null
        ;//var
        
        params.onReadStart = function()
        {
            if ( before === null )
            {
                before = container.children().detach();
            }
            return true;
        };
        params.onReadEnd = function(response)
        {
            if( response.error )
            {
                container.html( '<p>'
                                +'<strong>'
                                +js.core.translate("default.error"
                                                    ,js.core.userLocale)
                                +':</strong> '
                                +response.error+'</p>' );
            }
            else
            {
                container.html( 
                    $(form.find('input[name="paragraph-embed[embedHtml]"]')).val()
                );
            }
            
        }
  
        js.embed.initReader(form,true,params);
        
        return {
            "update": function () {
                before = null;
            },
            "restore": function () {
                if ( before !== null )
                {
                    container.children()
                           .remove();

                    container.append( before );
                }
            }
        };
    };
    
} ( window, jQuery, zork ) );
