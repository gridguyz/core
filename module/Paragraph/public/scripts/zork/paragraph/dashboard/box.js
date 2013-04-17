/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.box !== "undefined" )
    {
        return;
    }

    /**
     * @class Box dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.box = function ( form, element )
    {
        form    = $( form );
        element = $( element );

        var before = element.find( ".paragraph > h1" ).text();

        form.find( ":input[name='paragraph-box[title]']" )
            .on( "keyup change", function () {
                element.find( ".paragraph > h1" ).text( $( this ).val() );
            } );

        return {
            "update": function () {
                before = form.find( ":input[name='paragraph-box[title]']" ).val();
            },
            "restore": function () {
                element.find( ".paragraph > h1" ).text( before );
            }
        };
    };

} ( window, jQuery, zork ) );
