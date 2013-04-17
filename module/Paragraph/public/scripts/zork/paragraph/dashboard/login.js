/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.login !== "undefined" )
    {
        return;
    }

    /**
     * @class Login dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.login = function ( form, element )
    {
        form    = $( form );
        element = $( element );

        var linkToUser = element.find( ".linkToUser" ),
            before = !! linkToUser.nextAll( ".linkToAdminUi" ).length,
            remove = function () {
                linkToUser.nextAll( ".linkToAdminUi" ).remove();
            },
            add = function () {
                if ( ! linkToUser.nextAll( ".linkToAdminUi" ).length )
                {
                    linkToUser.after(
                        $( "<a />" )
                            .addClass( "linkToAdminUi" )
                            .attr( "href", "/app/" + js.core.defaultLoacle + "/admin" )
                            .html( js.core.translate( "user.form.logout.toAdminUI" ) )
                    );
                }
            };

        form.find( ":input[name='paragraph-login[displayAdminUiLink]']" )
            .on( "click change", function () {
                this.checked ? add() : remove();
            } );

        return {
            "update": function () {
                before = form.find( ":input[name='paragraph-login[displayAdminUiLink]']" ).is( ":checked" );
            },
            "restore": function () {
                before ? add() : remove();
            }
        };
    };

} ( window, jQuery, zork ) );
