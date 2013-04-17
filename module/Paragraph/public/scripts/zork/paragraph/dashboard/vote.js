/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author Sipos Zoltán
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.vote !== "undefined" )
    {
        return;
    }

    /**
     * @class vote dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.vote = function ( form, element )
    {
        form    = $( form );
        element = $( element ).children().children();

        var before = null,
            ajaxRefresh = function( url ) {
                js.core.loadRawElement( {
                    url: url,
                    success: function(content)
                    {
                        before = element.children('div').detach();
                        element.append(content);
                        js.core.parseDocument(element);
                    }
                } );
            },
            ajaxRefreshByForm = function() {
                var i, params = form.serializeArray();

                for ( i in params )
                {
                    params[i].name = params[i].name.replace( 'paragraph-vote[','' )
                                                   .replace( ']','' );
                }

                ajaxRefresh(
                    element.children( 'div' )
                           .data( 'jsUrl' ) + '?op=view&' + $.param( params )
                );
            },
            ajaxRefreshByUrl = function()
            {
//              global.console.log('element');
//              global.console.log(element);
                ajaxRefresh(
                    element.children( 'div' )
                           .data( 'jsUrl' ) + '?op=view'
                );
            };

        form.find( ":input[name='paragraph-vote[chartType]']")
            .on( "change", function () {
                js.style('/styles/modules/featuresVote/vote' + $( this ).val() + '.css' , 'text/css' );
                ajaxRefreshByForm();
            } );

        form.find( ":input[name='paragraph-vote[voteId]'], :input[name='paragraph-vote[sorted]']")
            .on( "change", ajaxRefreshByForm );

//      global.console.log('element');
//      global.console.log(element);
        ajaxRefreshByUrl();

        return {
            "update": function () {
                before = null;
            },
            "restore": function () {
                if(before!==null)
                {
                    element.html('');
                    element.append( before );
                }
            }
        };
    };

} ( window, jQuery, zork ) );
