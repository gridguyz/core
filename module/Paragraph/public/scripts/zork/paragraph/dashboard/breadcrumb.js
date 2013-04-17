/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author Pozsár Dávid
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.breadcrumb !== "undefined" )
    {
        return;
    }

    /**
     * @class breadcrumb dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.breadcrumb = function ( form, element )
    {
        form    = $( form );
        element = $( element );

        var before = element.find( ".paragraph .separator:first" ).text();

        form.find( ":input[name='paragraph-breadcrumb[separator]']" )
            .on( "change click", function () {
                element.find( ".paragraph .separator" ).text(
                    $( this ).val()
                );
            } );

        return {
            "update": function () {
                before = form.find( ":input[name='paragraph-breadcrumb[separator]']" ).val();
            },
            "restore": function () {
                element.find( ".paragraph .separator" ).text( before );
            }
        };
    };

} ( window, jQuery, zork ) );
