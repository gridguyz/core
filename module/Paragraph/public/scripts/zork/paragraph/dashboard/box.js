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

        var titleNode   = element.find( ".paragraph > .paragraph-content-open > .box-title" ),
            titleInput  = form.find( ":input[name='paragraph-box[title]']" ),
            before      = titleNode.text();

        titleInput.on( "keyup change", function () {
            titleNode.text( titleInput.val() );
        } );

        return {
            "update": function () {
                before = titleInput.val();
            },
            "restore": function () {
                titleNode.text( before );
            }
        };
    };

} ( window, jQuery, zork ) );
