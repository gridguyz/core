/**
 * User interface functionalities
 * @package zork
 * @subpackage user
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.customize !== "undefined" )
    {
        return;
    }

    var customCssSelector = "head link.customize-stylesheet[data-customize]";

    /**
     * @class Customize module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Customize = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "customize" ];
    };

    global.Zork.prototype.customize = new global.Zork.Customize();

    global.Zork.Customize.prototype.reload = function ()
    {
        $( customCssSelector ).each( function () {
            var css     = $( this ),
                id      = css.data( "customize" ),
                href    = js.core.uploadsUrl
                        + "/customize/custom." + id + "."
                        + Number( new Date() ).toString(36)
                        + ".css",
                link    = $( "<link />" )
                            .one( "ready load", function () {
                                css.remove();
                            } )
                            .attr( {
                                "href"              : href,
                                "type"              : "text/css",
                                "rel"               : "stylesheet",
                                "class"             : "customize-stylesheet",
                                "data-customize"    : id
                            } )
                            .data( "customize", id );

                css.removeClass( "customize-stylesheet" );
                $( "head" ).append( link );
        } );
    };

    global.Zork.Customize.prototype.properties = function ( element )
    {
        js.require( "js.ui.dialog" );
        element = $( element );

        var addButton = $( "<button type='button' />" ).text(
                element.data( "jsPropertiesAddLabel" ) ||
                    js.core.translate( "customize.form.rules.addProperty" )
            );

        addButton.click( function () {
            js.ui.dialog.prompt( {
                "message": js.core.translate( "customize.form.addMessage" ),
                "input": function ( prop ) {
                    if ( prop && ( prop = String( prop ).replace( /[^a-zA-Z0-9\-]/, "" ) ) ) {

                        element
                            .prepend(
                                $( "<dd />" )
                                    .append(
                                        $( "<input type='text'>" )
                                            .attr( {
                                                "name": "properties[" + prop + "][value]"
                                            } )
                                    )
                                    .append(
                                        $( "<label />" )
                                            .append(
                                                $( "<input type='checkbox' value='important'>" )
                                                    .attr( {
                                                        "name": "properties[" + prop + "][priority]"
                                                    } )
                                            )
                                            .append( js.core.translate( "customize.form.important" ) )
                                    )
                            )
                            .prepend(
                                $( "<dt />" )
                                    .append(
                                        $( "<label />" )
                                            .attr( "for", "properties[" + prop + "][value]" )
                                            .text( prop )
                                    )
                            );
                    }
                }
            } );
        } );

        element.before( addButton );
    };

    global.Zork.Customize.prototype.properties.isElementConstructor = true;

    global.Zork.Customize.prototype.preview = function ( element )
    {
        element  = $( element );

        var form = element.length
                 ? $( element[0].form || element.closest( "form" ) )
                 : null,
            previewButton = $( "<input type='submit' />" ).val(
                element.data( "jsCustomizePreviewLabel" ) ||
                    js.core.translate( "customize.preview.label" )
            );

        if ( form && form.length )
        {
            var rootId  = form.find( ':input[name="rootId"]' ).val(),
                id      = parseInt( rootId, 10 ) || "global";

            previewButton.click( function () {
                var action = form.prop( "action" ) || form.attr( "action" ) || "",
                    target = form.prop( "target" ) || form.attr( "target" ) || "_self",
                    newact = "/app/" + js.core.defaultLocale + "/admin/customize-css/preview/" + id;

                form.attr( "action", newact )
                    .prop( "action", newact )
                    .attr( "target", "_blank" )
                    .prop( "target", "_blank" );

                setTimeout( function () {

                    form.attr( "action", action )
                        .prop( "action", action )
                        .attr( "target", target )
                        .prop( "target", target );

                }, 500 );
            } );

            element.after( previewButton )
                   .parent()
                   .buttonset();
        }
    };

    global.Zork.Customize.prototype.preview.isElementConstructor = true;

} ( window, jQuery, zork ) );
