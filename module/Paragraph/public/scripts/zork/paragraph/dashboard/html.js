/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.html !== "undefined" )
    {
        return;
    }

    /**
     * @class Html dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.html = function ( form, element )
    {
        form    = $( form );
        element = $( element );

        var before = element.find( ".paragraph .paragraph-content-open" ).html();

        form.find( ":input[name='paragraph-html[html]']" )
            .on( "keyup change", function () {
                element.find( ".paragraph .paragraph-content-open" ).html(
                    $( this ).val()
                             .replace( /<script(\s[^>]*)?>.*?<\/script>/g, "" )
                );
            } );

        return {
            "update": function () {
                before = form.find( ":input[name='paragraph-html[html]']" ).val();
            },
            "restore": function () {
                element.find( ".paragraph .paragraph-content-open" ).html( before );
            }
        };
    };

} ( window, jQuery, zork ) );
