/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.codeEditor !== "undefined" )
    {
        return;
    }

    js.style( "/styles/scripts/codeeditor.css" );

    var getInputSelection = function ( el ) {
            var val     = el.value,
                start   = val.length,
                end     = val.length,
                normalizedValue,
                range,
                textInputRange,
                len,
                endRange;

            if ( typeof el.selectionStart == "number" &&
                 typeof el.selectionEnd == "number" )
            {
                start   = el.selectionStart;
                end     = el.selectionEnd;
            }
            else
            {
                range = global.document.selection.createRange();

                if ( range && range.parentElement() == el )
                {
                    len = el.value.length;
                    normalizedValue = el.value.replace( /\r\n/g, "\n" );

                    // Create a working TextRange that lives only in the input
                    textInputRange = el.createTextRange();
                    textInputRange.moveToBookmark( range.getBookmark() );

                    // Check if the start and end of the selection are at the very end
                    // of the input, since moveStart/moveEnd doesn't return what we want
                    // in those cases
                    endRange = el.createTextRange();
                    endRange.collapse( false );

                    if ( textInputRange.compareEndPoints( "StartToEnd", endRange ) > -1 )
                    {
                        start = end = len;
                    }
                    else
                    {
                        start = -textInputRange.moveStart( "character", -len );
                        start += normalizedValue.slice( 0, start ).split( "\n" ).length - 1;

                        if ( textInputRange.compareEndPoints( "EndToEnd", endRange ) > -1 )
                        {
                            end = len;
                        }
                        else
                        {
                            end = -textInputRange.moveEnd( "character", -len );
                            end += normalizedValue.slice( 0, end ).split( "\n" ).length - 1;
                        }
                    }
                }
            }

            return {
                "start": start,
                "end": end
            };
        },
        replaceSelectedText = function ( el, text ) {
            $( el ).each( function () {
                var sel = getInputSelection( this ), val = this.value;
                this.value = val.slice( 0, sel.start ) + text + val.slice( sel.end );
            } );
        };

    /**
     * Code-editor element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.codeEditor = function ( element )
    {
        var container = $( "<div />", {
                "class": "js-code-editor"
            } ),
            bar = $( "<div />", {
                "class": "js-code-editor-bar"
            } ),
            insert = $( "<button type='button' />" ).button( {
                "text": true,
                "label": js.core.translate( "default.insert" ),
                "icons": { "primary": "ui-icon-image" }
            } ).click( function () {
                js.caller.zork.require( "js.core.pathselect", function ( pathselect ) {
                    pathselect( {
                        "file"      : true,
                        "select"    : function ( file ) {
                            replaceSelectedText( element, file );
                        }
                    } );
                } );
            } ),
            fulls = $( "<button type='button' />" ).button( {
                "text": true,
                "label": js.core.translate( "default.fullscreen" ),
                "icons": { "secondary": "ui-icon-extlink" }
            } ).click( function () {
                container.resizable( "disable" )
                         .css( {
                             "top": "0px",
                             "left": "0px",
                             "width": "100%",
                             "height": "100%",
                             "opacity": "1",
                             "z-index": "300000",
                             "position": "fixed",
                             "max-height": "1000%"
                         } );

                element.css( {
                    "width": "100%",
                    "height": container.outerHeight() - bar.outerHeight(),
                    "max-height": "none !important"
                } );

                fulls.hide();
                back.show();
            } ),
            back = $( "<button type='button' />" ).button( {
                "text": true,
                "label": js.core.translate( "default.back" ),
                "icons": { "secondary": "ui-icon-newwin" }
            } ).click( function () {
                container.resizable( "enable" )
                         .css( {
                             "top": "",
                             "left": "",
                             "width": "",
                             "height": "",
                             "opacity": "",
                             "z-index": "",
                             "position": "",
                             "max-height": ""
                         } );

                element.css( {
                    "width": "",
                    "height": "",
                    "max-height": ""
                } );

                back.hide();
                fulls.show();
            } ).hide();

        element = $( element );

        container.insertBefore( element )
                 .append( bar.append( insert )
                             .append( fulls )
                             .append( back )
                             .buttonset() )
                 .append( element );

        container.resizable( {
            "handles": "s",
            "alsoResize": element
        } );
    };

    global.Zork.Form.Element.prototype.codeEditor.isElementConstructor = true;

} ( window, jQuery, zork ) );
