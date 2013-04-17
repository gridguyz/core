/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.cssImage !== "undefined" )
    {
        return;
    }

    var encode = function ( str ) {
            return String( str ).replace( '"', '\\22' );
        },
        decode = function ( str ) {
            return String( str ).replace(
                /\\[0-9a-fA-F]{2,6} ?/g,
                function ( match ) {
                    return String.fromCharCode( parseInt(
                        String( match ).replace( / $/, "" )
                                       .replace( /^\\/, "" ),
                        16
                    ) );
                }
            );
        };

    /**
     * Css-image element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.cssImage = function ( element )
    {
        element = $( element );

        var emptyIcon    = element.data( "jsCssimageEmptyIcon" )   ||
                           "ui-icon-cancel",
            resetIcon    = element.data( "jsCssimageResetIcon" )   ||
                           "ui-icon-arrowreturnthick-1-w",
            selectIcon   = element.data( "jsCssimageSelectIcon" )  ||
                           "ui-icon-arrowthickstop-1-n",
            disableIcon  = element.data( "jsCssimageDisableIcon" )  ||
                           "ui-icon-trash",
            emptyLabel   = element.data( "jsCssimageEmptyLabel" ) ||
                           js.core.translate( "default.setDefault" ),
            resetLabel   = element.data( "jsCssimageResetLabel" ) ||
                           js.core.translate( "default.reset" ),
            selectLabel  = element.data( "jsCssimageSelectLabel" ) ||
                           js.core.translate( "default.select" ),
            disableLabel = element.data( "jsCssimageDisableLabel" ) ||
                           js.core.translate( "default.disable" );

        element.attr( "readonly", "readonly" );

        $( "<button type='button' />" )
            .attr( "title", disableLabel )
            .insertAfter( element )
            .button( { "text": "", "icons": {
                "primary": disableIcon
            } } )
            .click( function () {
                this.blur();

                element.val( "none" )
                       .trigger( "change" );
            } );

        $( "<button type='button' />" )
            .attr( "title", selectLabel )
            .insertAfter( element )
            .button( { "text": "", "icons": {
                "primary": selectIcon
            } } )
            .click( function () {
                var match, file;
                this.blur();

                if ( ( match = element.val().match( /\s*url\("(.*)"\)\s*/ ) ) &&
                       match.length )
                {
                    file = decode( match[1] );
                }

                js.caller.js.require( "js.core.pathselect", function () {
                    js.core.pathselect( {
                        "directory" : false,
                        "file"      : file || "",
                        "select"    : function ( val ) {
                            element.val( 'url("' + encode( val ) + '")' )
                                   .trigger( "change" );
                        }
                    } );
                } );
            } );

        $( "<button type='button' />" )
            .attr( "title", resetLabel )
            .insertAfter( element )
            .button( { "text": "", "icons": {
                "primary": resetIcon
            } } )
            .click( function () {
                this.blur();

                element.val( element[0].getAttribute( "value" ) )
                       .trigger( "change" );
            } );

        $( "<button type='button' />" )
            .attr( "title", emptyLabel )
            .insertAfter( element )
            .button( { "text": "", "icons": {
                "primary": emptyIcon
            } } )
            .click( function () {
                this.blur();

                element.val( "" )
                       .trigger( "change" );
            } );

        element.parent().inputset();
    };

    global.Zork.Form.Element.prototype.cssImage.isElementConstructor = true;

} ( window, jQuery, zork ) );
