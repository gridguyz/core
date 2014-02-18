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
            } else if ( $.isPlainObject( this ) ) {
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
            } else {
                set.append( this );
            }
        } );

        setTimeout( function () {
            $( cm.getWrapperElement() ).append( sets.append( set.buttonset() ) );
            cm.refresh();
        }, 500 );

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
            var generation,
                node    = element[0],
                mode    = element.data( "jsCodeeditorMode" ) || "text/plain",
                theme   = String( element.data( "jsCodeeditorTheme" ) || "" ).toLowerCase() || "default",
                lineNum = !! element.data( "jsCodeeditorLinenumbers" ),
                switchFullscreen = function () {
                    var fs = ! mirror.getOption( "fullScreen" );
                    mirror.setOption( "fullScreen", fs );

                    if ( fs )
                    {
                        switchButton.blur().button( "option", "icons", {
                            "primary": "ui-icon-newwin"
                        } );
                    }
                    else
                    {
                        switchButton.blur().button( "option", "icons", {
                            "primary": "ui-icon-extlink"
                        } );
                    }
                },
                switchBack = function () {
                    if ( mirror.getOption( "fullScreen" ) )
                    {
                        mirror.setOption( "fullScreen", false );

                        switchButton.blur().button( "option", "icons", {
                            "primary": "ui-icon-extlink"
                        } );
                    }
                },
                switchButton = $( '<button type="button">' )
                    .attr( "title", js.core.translate( "default.fullscreen" ) )
                    .button( {
                        "text": false,
                        "icons": { "primary": "ui-icon-extlink" },
                    } )
                    .click( switchFullscreen ),
                mirror = CodeMirror.fromTextArea( node, {
                    "mode": mode,
                    "theme": theme,
                    "tabSize": 2,
                    "indentWithTabs": true,
                    "lineNumbers": lineNum,
                    "extraKeys": {
                        "F11": switchFullscreen,
                        "Esc": switchBack
                    }
                } );

            if ( theme !== "default" )
            {
                js.style( codeMirrorPath + "/theme/" + theme + ".css" );
            }

            element.data( "jsCodeeditorWidget", mirror );
            generation = mirror.getDoc().changeGeneration();

            mirror.on( "change", function () {
                var doc = mirror.getDoc();

                if ( ! doc.isClean( generation ) )
                {
                    generation = doc.changeGeneration();
                    element.val( doc.getValue() )
                           .trigger( "change" );
                }
            } );

            mirror.createToolbar( [ {
                "text": false,
                "title":js.core.translate( "default.undo" ),
                "icons": { "primary": "ui-icon-arrowreturnthick-1-w" },
                "click": "undo"
            }, {
                "text": false,
                "title":js.core.translate( "default.redo" ),
                "icons": { "primary": "ui-icon-arrowreturnthick-1-e" },
                "click": "redo"
            }, "|", {
                "text": false,
                "title":js.core.translate( "default.find" ),
                "icons": { "primary": "ui-icon-search" },
                "click": "find"
            }, {
                "text": false,
                "title":js.core.translate( "default.findPrevious" ),
                "icons": { "primary": "ui-icon-seek-prev" },
                "click": "findPrev"
            }, {
                "text": false,
                "title":js.core.translate( "default.findNext" ),
                "icons": { "primary": "ui-icon-seek-next" },
                "click": "findNext"
            }, {
                "text": false,
                "title":js.core.translate( "default.replace" ),
                "icons": { "primary": "ui-icon-arrowrefresh-1-e" },
                "click": "replace"
            }, {
                "text": false,
                "title":js.core.translate( "default.replaceAll" ),
                "icons": { "primary": "ui-icon-refresh" },
                "click": "replaceAll"
            }, "|", {
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
            }, switchButton ] );
        }
    };

    global.Zork.Form.Element.prototype.codeEditor.isElementConstructor = true;

} ( window, jQuery, zork ) );
