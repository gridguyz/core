/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.share !== "undefined" )
    {
        return;
    }

    /**
     * @class Share dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.share = function ( form, element )
    {
        form    = $( form );
        element = $( element ).children();
        var formElements = {
                "services" : form.find( ":input[type=checkbox]")
            },
            before = null,
            ajaxRefresh = function() {
                if ( before === null )
                {
                    before = element.children().detach();
                }

                js.core.loadRawElement( {
                    url: '/app/' + js.core.defaultLocale + '/share?services='
                        + encodeURIComponent(
                            form.find( ":input[type=checkbox]:checked")
                              .map( function( index, element ) {
                                      return $( element ).val();
                                } )
                              .get()
                              .join( '|' )
                          )
                        + '&url='
                        + encodeURIComponent( global.location ),
                    target: element
                } );
            };

        formElements.services.on( "click", ajaxRefresh );

        form.find('[data-js-type="js.share.sortableCheckboxGroup"]').sortable({ "update": ajaxRefresh });

        return {
            "update": function () {
                before = null;
            },
            "restore": function () {
                if ( before !== null )
                {
                    element.children()
                           .remove();

                    element.append( before );
                }
            }
        };
    };

} ( window, jQuery, zork ) );
