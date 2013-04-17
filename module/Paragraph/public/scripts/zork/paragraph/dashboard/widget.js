/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.widget !== "undefined" )
    {
        return;
    }

    /**
     * @class Widget dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.widget = function ( form, element )
    {
        form    = $( form );
        element = $( element );

        var before = element.find( ".paragraph .paragraph-children:first" ).html(),
            update = function ( html ) {
                var owrite      = global.document.write,
                    owriteln    = global.document.writeln;

                try {
                    global.document.write = function ( code ) {
                        element[0].innerHTML += code;
                    };

                    global.document.writeln = function ( code ) {
                        element[0].innerHTML += code + "\n";
                    };

                    element.find( ".paragraph .paragraph-children:first" )
                           .html( "" )
                           .html( html );
                } catch ( e ) {
                    js.console.error( e );
                }

                global.document.write   = owrite;
                global.document.writeln = owriteln;
            };

        form.find( '[name="paragraph-widget[snippets][]"]' )
            .on( "click", function () { this.blur(); } )
            .on( "change", function () {
                var self = $( this ),
                    snip = self.val(),
                    url  = js.core.uploadsUrl + "/snippets/" + snip;

                if ( /\.js$/.test( snip ) )
                {
                    if ( this.checked )
                    {
                        js.script( url );
                    }
                }
                else if ( /\.css$/.test( snip ) )
                {
                    if ( this.checked )
                    {
                        js.style( url );
                    }
                    else
                    {
                        $( 'head link[href="' + url + '"]' ).remove();
                    }
                }
            } );

        /*
        form.find( ":input[name='paragraph-widget[code]']" )
            .on( "keyup change", function () {
                element.find( ".paragraph:first" ).html(
                    $( this ).val()
                             .replace( /<script(\s[^>]*)?>.*?<\/script>/g, "" )
                );
            } );
        */

        return {
            "update": function () {
                before = form.find( ":input[name='paragraph-widget[code]']" ).val();
                update( before );
            },
            "restore": function () {
                update( before );
            }
        };
    };

} ( window, jQuery, zork ) );
