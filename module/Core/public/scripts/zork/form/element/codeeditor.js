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
        codeMirrorPath      = "/scripts/library/codemirror-" + codeMirrorVersion,
        codeMirrorDefaults  = {
            "mode"  : "text/html",
            "theme" : "default"
        };

    js.style( codeMirrorPath + "/lib/codeeditor.css" );
    js.script( codeMirrorPath + "/lib/compressed-zork.css" );

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
                params  = {
                    "mode": element.data( "jsCodeeditorMode" ) || codeMirrorDefaults.mode,
                    "theme": element.data( "jsCodeeditorTheme" ) || codeMirrorDefaults.theme
                },
                mirror  = CodeMirror.fromTextArea( node, params ),
                insert  = $( "<button type='button' />" ).button( {
                                "text": true,
                                "label": js.core.translate( "default.insert" ),
                                "icons": { "primary": "ui-icon-image" }
                            } ).click( function () {
                                js.caller.zork.require( "js.core.pathselect", function ( pathselect ) {
                                    pathselect( {
                                        "file"      : true,
                                        "select"    : function ( file ) {
                                            mirror.getDoc().replaceSelection( String( file ) );
                                        }
                                    } );
                                } );
                            } );

            mirror.addWidget( { "line": 1, "ch": 70 }, insert[0], true );
        }
    };

    global.Zork.Form.Element.prototype.codeEditor.isElementConstructor = true;

} ( window, jQuery, zork ) );
