/**
 * Paragraph functionalities
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.snippet !== "undefined" )
    {
        return;
    }

    /**
     * @class Snippet module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Paragraph.Snippet = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "paragraph", "snippet" ];
    };

    global.Zork.Paragraph.prototype.snippet = new global.Zork.Paragraph.Snippet();

    var typeToMode = {
        "css": "text/css",
        "js": "text/javascript"
    };

    /**
     * Snippet: type
     *
     * @param {HTMLElement|$} element
     */
    global.Zork.Paragraph.Snippet.prototype.type = function ( element )
    {
        element = $( element );

        var form = $( element[0].form || element.closest( "form" ) ),
            code = form.find( ":input[name=code]" ),
            cm   = code.data( "jsCodeeditorWidget" ),
            update = function () {
                var mode, val = element.val();

                if ( ( val in typeToMode ) && ( mode = typeToMode[val] ) )
                {
                    code.data( "jsCodeeditorMode", mode );
                    cm = cm || code.data( "jsCodeeditorWidget" );

                    if ( cm )
                    {
                        cm.setOption( "mode", mode );
                    }
                }
            };

        element.on( "change", update );
        update();
    };

    global.Zork.Paragraph.Snippet.prototype.type.isElementConstructor = true;

} ( window, jQuery, zork ) );
