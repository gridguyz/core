/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js, undefined )
{
    "use strict";

    if ( typeof js.form.element.codeEditor !== "undefined" )
    {
        return;
    }

    var codeMirrorVersion   = "3.21",
        codeMirrorPath      = "/scripts/library/codemirror-" + codeMirrorVersion;

    js.style( codeMirrorPath + "/lib/codemirror.css" );
    js.style( codeMirrorPath + "/addon/dialog/dialog.css" );
    js.style( codeMirrorPath + "/addon/display/fullscreen.css" );
    js.style( "/styles/scripts/codeeditor.css" );
    js.script( codeMirrorPath + "/lib/compressed-zork.js" );
    js.script( codeMirrorPath + "/addon/dialog/dialog.js" );
    js.script( codeMirrorPath + "/addon/display/fullscreen.js" );
    js.script( codeMirrorPath + "/addon/search/searchcursor.js" );
    js.script( codeMirrorPath + "/addon/search/search.js" );

    CodeMirror.defineExtension( "createToolbar", function ( buttons ) {
        var cm          = this,
            settpl      = '<span class="CodeMirror-toolbar-buttonset">',
            buttpl      = '<button type="button">',
            sets        = $( '<div class="CodeMirror-toolbar">' ),
            set         = $( settpl );

        $.each( buttons, function () {
            if ( ! this || this === "|" ) {
                sets.append( set.buttonset() );
                set = $( settpl );
            } else if ( typeof this.click !== "undefined" ) {
                var click = this.click,
                    title = String( this.title || "" );

                delete this.click;
                delete this.title;

                if ( ! Function.isFunction( click ) )
                {
                    var com = String( click );
                    click   = function () { cm.execCommand( com ); };
                }

                set.append(
                    $( buttpl )
                        .button( this )
                        .attr( "title", title )
                        .click( click )
                );
            }
        } );

        $( cm.getWrapperElement() ).prepend( sets.append( set.buttonset() ) );
        return sets[0];
    } );

    /**
     * Code-editor element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.codeEditor = function ( element )
    {
        element = $( element );

        if ( element.is( "textarea" ) )
        {
            var node    = element[0],
                mode    = element.data( "jsCodeeditorMode" ) || "text/html",
                theme   = String( element.data( "jsCodeeditorTheme" ) || "" ).toLowerCase() || "default",
                lineNum = !! element.data( "jsCodeeditorLinenumbers" ),
                mirror  = CodeMirror.fromTextArea( node, {
                    "mode": mode,
                    "theme": theme,
                    "tabSize": 2,
                    "indentWithTabs": true,
                    "lineNumbers": lineNum
                } );

            if ( theme !== "default" )
            {
                js.style( codeMirrorPath + "/theme/" + theme + ".css" );
            }

            mirror.createToolbar( [ {
                "text": false,
                "title":js.core.translate( "default.search" ),
                "icons": { "primary": "ui-icon-search" },
                "click": "search"
            }, {
                "text": false,
                "title": js.core.translate( "default.insert" ),
                "icons": { "primary": "ui-icon-image" },
                "click": function () {
                    js.caller.zork.require( "js.core.pathselect", function ( pathselect ) {
                        pathselect( {
                            "file"      : true,
                            "select"    : function ( file ) {
                                mirror.getDoc().replaceSelection( String( file ) );
                            }
                        } );
                    } );
                }
            }, {
                "text": false,
                "title":js.core.translate( "default.fullscreen" ),
                "icons": { "primary": "ui-icon-extlink" },
                "click": "fullscreen"
            } ] );
        }
    };

    global.Zork.Form.Element.prototype.codeEditor.isElementConstructor = true;

} ( window, jQuery, zork ) );
