/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.collection !== "undefined" )
    {
        return;
    }

    /**
     * Range (slider) form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.collection = function ( element )
    {
        js.style( "/styles/scripts/formcollection.css" );
        element = $( element ).addClass( "js-form-collection" );

        var sortable = !! element.data( "jsCollectionSortable" ),
            template = element.find( "[data-template]" ).last(),
            addLabel = element.data( "jsCollectionAddlabel" ) || "default.add",
            relegend = function () {
                element.find( "> fieldset > legend" ).each( function ( index, leg ) {
                    leg  = $( leg );
                    var orig = leg.data( "jsCollectionOriginalLegend" );

                    if ( ! orig )
                    {
                        leg.data(
                            "jsCollectionOriginalLegend",
                            orig = leg.text()
                        );
                    }

                    leg.text( String( orig ).format( index + 1 ) );

                    if ( ! leg.find( "button" ).length )
                    {
                        leg.prepend(
                            $( '<button type="button">' )
                                .button( {
                                    "text": false,
                                    "icons": {
                                        "primary": "ui-icon-close"
                                    }
                                } )
                                .click( function () {
                                    leg.parent( "fieldset" )
                                       .remove();

                                    relegend();
                                } )
                        );
                    }
                } );

                element.accordion( "refresh" );
            };

        element.accordion( {
            "collapsible": true,
            "heightStyle": "content",
            "header": "> fieldset > legend"
        } );

        if ( sortable )
        {
            element.sortable( {
                "axis": "y",
                "handle": "legend",
                "stop": function( event, ui ) {
                    // IE doesn't register the blur when sorting
                    // so trigger focusout handlers to remove .ui-state-focus
                    ui.item.children( "h3" ).triggerHandler( "focusout" );
                    relegend();
                }
            } );
        }

        if ( template.length )
        {
            var nextIndex = element.find( "> fieldset" ).length;

            element.after(
                $( '<button type="button" />' )
                    .html( js.core.translate( addLabel ) )
                    .click( function () {
                        var ins = $( template.data( "template" ).replace(
                            /__index__/g,
                            nextIndex++
                        ) );

                        template.before( ins );
                        js.core.parseDocument( ins );
                        relegend();
                    } )
            );
        }

        relegend();
    };

    global.Zork.Form.Element.prototype.collection.isElementConstructor = true;

} ( window, jQuery, zork ) );
