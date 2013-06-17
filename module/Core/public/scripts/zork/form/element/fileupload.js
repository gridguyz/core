/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.fileUpload !== "undefined" )
    {
        return;
    }

    /**
     * File uploader form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.fileUpload = function ( element )
    {
        element = $( element );

        var thumbnail,
            uploadButton,
            resetButton,
            deleteButton,
            original    = element.val(),
            pattern     = element.data( "jsUploadPattern" )     || "~%s.%s",
            types       = element.data( "jsUploadTypes" )       || "*",
            image       = /^image\/(\*|[^,]+)$/.test( types ),
            uploadIcon  = element.data( "jsUploadUploadIcon" )  ||
                          "ui-icon-arrowthickstop-1-n",
            resetIcon   = element.data( "jsUploadResetIcon" )   ||
                          "ui-icon-arrowreturnthick-1-w",
            deleteIcon  = element.data( "jsUploadDeleteIcon" )  ||
                          "ui-icon-trash",
            uploadLabel = element.data( "jsUploadUploadLabel" ) ||
                          js.core.translate( "default.upload" ),
            resetLabel  = element.data( "jsUploadResetLabel" )  ||
                          js.core.translate( "default.reset" ),
            deleteLabel = element.data( "jsUploadDeleteLabel" ) ||
                          js.core.translate( "default.delete" ),
            cancelLabel = element.data( "jsUploadCancelLabel" ) ||
                          js.core.translate( "default.cancel" ),
            updateThumb = function () {};

        if ( image )
        {
            $( "<div />" )
                .insertBefore( element )
                .append(
                    thumbnail = $( "<img />" )
                        .attr( "alt", "thumbnail" )
                        .addClass( "ui-helper-hidden" )
                        .css( {
                            "max-width": "100px",
                            "max-height": "100px"
                        } )
                )
                .css( {
                    "height": "100px",
                    "line-height": "100px",
                    "padding": "0.5em 2em",
                    "vertical-aling": "middle"
                } );

            updateThumb = function ( val ) {
                var regexp = /^\/uploads\/[^\/]+/;

                if ( "" == val || Object.isUndefined( val ) )
                {
                    thumbnail.addClass( "ui-helper-hidden" );
                }
                else if ( regexp.test( val ) )
                {
                    val = val.replace( regexp, '' );
                    thumbnail.removeClass( "ui-helper-hidden" )
                             .attr( "src", js.core.thumbnail( val ) );
                }
                else
                {
                    thumbnail.removeClass( "ui-helper-hidden" )
                             .attr( "src", val );
                }
            };

            updateThumb( String( original ) );
        }

        deleteButton = $( "<button type='button' />" )
            .attr( "title", deleteLabel )
            .insertAfter( element )
            .button( { "text": "", "icons": {
                "primary": deleteIcon
            } } );

        deleteButton.click( function () {
            this.blur();

            element.val( "" )
                   .trigger( "change" );

            updateThumb( "" );
        } );

        resetButton = $( "<button type='button' />" )
                        .attr( "title", resetLabel )
                        .insertAfter( element )
                        .button( { "text": "", "icons": {
                            "primary": resetIcon
                        } } );

        resetButton.click( function () {
            this.blur();

            element.val( original )
                   .trigger( "change" );

            updateThumb( original );
        } );

        uploadButton = $( "<button type='button' />" )
                        .attr( "title", uploadLabel )
                        .insertAfter( element )
                        .button( { "text": "", "icons": {
                            "primary": uploadIcon
                        } } );

        element.parent().inputset();

        var first = true,
            form = null,
            buttons = {},
            target = js.generateId(),
            src = "/app/" + js.core.defaultLocale + "/upload" +
                  "?types=" + encodeURIComponent( types ) +
                  "&pattern=" + encodeURIComponent( pattern ),
            frame = $( "<iframe />" )
                        .css( {
                            "width": 400,
                            "height": 100,
                            "border": "none",
                            "margin": "0px auto",
                            "overflow": "hidden"
                        } )
                        .attr( {
                            "src": src,
                            "id": target,
                            "name": target,
                            "scrolling": "no",
                            "frameborder": "0",
                            "allowtransparency": "true"
                        } ),
            dialog = $( "<div />" )
                        .attr( "title", uploadLabel )
                        .css( "text-align", "center" )
                        .append( frame );

        frame.on( "ready load", function () {
            var contents = frame.contents(),
                result   = contents.find( "#result" );

            if ( 0 < result.length )
            {
                var message = result.data( "jsUploadMessage" ),
                    success = result.data( "jsUploadSuccess" ),
                    value   = result.data( "jsUploadFile" );

                if ( success )
                {
                    element.val( value )
                           .trigger( "change" );
                    updateThumb( value );
                }

                js.require( "zork.ui.message", function () {
                    js.ui.message( {
                        "title": js.core.translate( success
                                ? "default.info" : "default.warning" ),
                        "message": message
                    } );
                } );

                setTimeout( function () {
                    dialog.dialog( "close" );
                }, 10 );
            }
            else
            {
                form = contents.find( "form" );
                form.find( ":submit" ).hide();
            }
        } );

        buttons[uploadLabel] = function () {
            if ( form )
            {
                var submit = form.find( ":submit:first" );

                if ( submit.length )
                {
                    submit.click();
                }
                else
                {
                    form.submit();
                }
            }
        };

        buttons[cancelLabel] = function () {
            dialog.dialog( "close" );
        };

        uploadButton.click( function () {
            this.blur();

            if ( first )
            {
                first = false;

                dialog.dialog( {
                    "width"     : 500,
                    "height"    : 230,
                    "modal"     : true,
                    "buttons"   : buttons
                } );
            }
            else
            {
                frame.attr( "src", src + "&_rnd=" +
                    String( Math.random() ).replace( /^0?./, "" ) );
                dialog.dialog( "open" );
            }
        } );
    };

    global.Zork.Form.Element.prototype.fileUpload.isElementConstructor = true;

} ( window, jQuery, zork ) );
