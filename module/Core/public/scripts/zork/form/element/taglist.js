/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.tagList !== "undefined" )
    {
        return;
    }

    js.style( "/styles/scripts/taglist.css" );

    /**
     * Tags element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.tagList = function ( element )
    {
        element = $( element ).addClass( "js-tag-list ui-widget" );

        var name   = element.data( "name" ),
            search = $( '<input type="search" class="ui-state-default" />' ),
            seadiv = $( '<div class="js-tag-search" />' ),
            add    = function ( event, ui ) {
                var val   = String( ui && ui.item ? ui.item.value : search.val() ),
                    found = false;

                if ( val )
                {
                    element.find( ":input[type=hidden]" )
                           .each( function () {
                               found = found || (
                                   String( val ).toLowerCase() ===
                                   String( $( this ).val() ).toLowerCase()
                               );
                           } );

                    if ( found )
                    {
                        setTimeout( function () { search.val( "" ); }, 100 );
                        return;
                    }

                    var input = $( '<input type="hidden" />' ).attr( {
                                    "name": name,
                                    "value": val
                                } ),
                        label = $( "<label />", {
                                    "text": val,
                                    "class": "js-tag ui-state-default ui-widget-content ui-corner-all"
                                } ).hide(),
                        close = $( '<button type="button" />' )
                                .button( {
                                    "text": false,
                                    "icons": {
                                        "primary": "ui-icon-close"
                                    }
                                } )
                                .click( function () {
                                    label.hide( "fast", function () {
                                        label.remove();
                                    } );
                                } );

                    seadiv.after(
                        label.prepend( input )
                             .append( close )
                    );

                    label.show( "fast", function () {
                        search.val( "" );
                    } );
                }
            };

        element.find( ":input" )
               .each( function () {
                   var self  = $( this ),
                       label = $( "<label />", {
                                    "text": self.val(),
                                    "class": "js-tag ui-state-default ui-widget-content ui-corner-all"
                                } ),
                       close = $( '<button type="button" />' )
                                .button( {
                                    "text": false,
                                    "icons": {
                                        "primary": "ui-icon-close"
                                    }
                                } )
                                .click( function () {
                                    label.hide( "fast", function () {
                                        label.remove();
                                    } );
                                } );

                   label.insertBefore( self )
                        .prepend( self )
                        .append( close );
               } );

        element.prepend(
            seadiv
                .append(
                    search.autocomplete( {
                        "source": "/app/" + js.core.defaultLocale + "/tag/search",
                        "minLength": 2,
                        "select": add
                    } )
                )
                .append(
                    $( '<button type="button" />' )
                        .button( {
                            "text": false,
                            "icons": {
                                "primary": "ui-icon-plus"
                            }
                        } )
                        .click( add )
                )
        );
    };

    global.Zork.Form.Element.prototype.tagList.isElementConstructor = true;

} ( window, jQuery, zork ) );
