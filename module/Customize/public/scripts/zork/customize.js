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

    var css = $( "#customizeStyleSheet" );

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
        css = css && css.length ? css : $( "#customizeStyleSheet" );

        var href = js.core.uploadsUrl
                 + "/customize/custom."
                 + Number( new Date() ).toString(36)
                 + ".css",
            link = $( "<link />" )
                .one( "ready load", function ( evt ) {
                    if ( css.length )
                    {
                        css.remove();
                    }

                    css = $( this ).attr(
                        "id", "customizeStyleSheet"
                    );
                } )
                .attr( {
                    "href"  : href,
                    "type"  : "text/css",
                    "rel"   : "stylesheet"
                } );

        $( "head" ).append( link );
    };

    global.Zork.Customize.prototype.properties = function ( element )
    {
        js.require( "js.ui.dialog" );
        element = $( element );

        var addButton = $( "<button type='button' />" ).text(
                element.data( "jsPropertiesAddLabel" ) ||
                    js.core.translate( "customize.form.addProperty" )
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

} ( window, jQuery, zork ) );
