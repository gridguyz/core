/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.metaContent !== "undefined" )
    {
        return;
    }

    /**
     * @class Content dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.metaContent = function ( form, element )
    {
        form    = $( form );
        element = $( element );

        var titleTag    = $( "head title" ),
            title       = form.find( ":input[name='paragraph-metaContent[title]']" ),
            before      = {
                "title": titleTag.text().replace( /^\s+/, "" ).replace( /\s+$/, "" ) || "",
            },
            base        = before.title.substr( 0, before.title.length - ( title.val() || "" ).length ),
            titles      = $( ".paragraph.paragraph-title" );

        title.on( "keyup change", function () {
            var val = title.val();
            titleTag.text( base + val );
            titles.find( "h1" ).text( val || base );
        } );

        titles.each( function () {
            var self = $( this );
            self.data( "original-title", self.find( "h1" ).text() );
        } );

        return {
            "update": function () {
                before.title = base + title.val();

                titles.each( function () {
                    var self = $( this );
                    self.data( "original-title", self.find( "h1" ).text() );
                } );
            },
            "restore": function () {
                titleTag.text( before.title );

                titles.each( function () {
                    var self = $( this );
                    self.find( "h1" ).text( self.data( "original-title" ) );
                } );
            }
        };
    };

} ( window, jQuery, zork ) );
