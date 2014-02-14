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
    js.style( codeMirrorPath + "/addon/fold/foldgutter.css" );
    js.style( codeMirrorPath + "/addon/hint/show-hint.css" );
    js.style( codeMirrorPath + "/addon/lint/lint.css" );
    js.style( codeMirrorPath + "/addon/merge/merge.css" );
    js.style( codeMirrorPath + "/addon/tern/tern.css" );
    js.style( "/styles/scripts/codeeditor.css" );
    js.script( codeMirrorPath + "/lib/compressed-zork.js" );

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
                var click = this.click;
                delete this.click;

                if ( ! Function.isFunction( click ) )
                {
                    var com = String( click );
                    click   = function () { cm.execCommand( com ); };
                }

                set.append( $( buttpl ).button( this ).click( click ) );
            }
        } );

        $( cm.getWrapperElement() ).prepend( sets.append( set.buttonset() ) );
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
                params  = {
                    "mode": element.data( "jsCodeeditorMode" ) || "text/html",
                    "theme": element.data( "jsCodeeditorTheme" ) || "default"
                },
                mirror  = CodeMirror.fromTextArea( node, params );

            if ( params.theme !== "default" )
            {
                js.style( codeMirrorPath + "/theme/" + params.theme + ".css" );
            }

            mirror.createToolbar( [ {
                "text": true,
                "label": js.core.translate( "default.insert" ),
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
            } ] );
        }
    };

    global.Zork.Form.Element.prototype.codeEditor.isElementConstructor = true;

} ( window, jQuery, zork ) );
