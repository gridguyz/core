/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.title !== "undefined" )
    {
        return;
    }

    /**
     * @class Title dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.title = function ( form, element )
    {
        form    = $( form );
        element = $( element );

        var titleTag    = $( "head title" ),
            title       = form.find( ":input[name='paragraph-title[rootTitle]']" ),
            cacheTag    = titleTag.text().replace( /^\s+/, "" ).replace( /\s+$/, "" ) || "",
            cache       = ( title.val() || "" ),
            cacheTrim   = cache.replace( /^\s+/, "" ).replace( /\s+$/, "" ),
            index       = cacheTrim ? cacheTag.indexOf( cacheTrim ) : -1,
            before      = ~index ? cacheTag.substr( 0, index ) : cacheTag + " ",
            after       = ~index ? cacheTag.substr( index + cacheTrim.length ) : "",
            titles      = $( ".paragraph.paragraph-title" );

        title.on( "keyup change", function () {
            var val = title.val();
            titleTag.text( before + val + after );
            titles.find( "h1" ).text( ( val || before ) + after );
        } );

        titles.each( function () {
            var self = $( this );
            self.data( "original-title", self.find( "h1" ).text() );
        } );

        return {
            "update": function () {
                cacheTag = before + ( cache = title.val() ) + after;

                titles.each( function () {
                    var self = $( this );
                    self.data( "original-title", self.find( "h1" ).text() );
                } );
            },
            "restore": function () {
                titleTag.text( cacheTag );

                titles.each( function () {
                    var self = $( this );
                    self.find( "h1" ).text( self.data( "original-title" ) );
                } );
            }
        };
    };

} ( window, jQuery, zork ) );
