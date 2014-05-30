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
        element = $( element );

        var sortable = !! element.data( "jsCollectionSortable" ),
            template = element.find( "[data-template]" ).last(),
            addLabel = element.data( "jsCollectionAddlabel" ) || "default.add",
            relegend = function () {
                element.find( "> fieldset > legend" ).each( function ( index ) {
                    var leg  = $( this ),
                        orig = leg.data( "jsCollectionOriginalLegend" );

                    if ( ! orig )
                    {
                        leg.data(
                            "jsCollectionOriginalLegend",
                            orig = element.text()
                        );
                    }

                    leg.text( String( orig ).format( index + 1 ) );
                } );
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
            element.after(
                $( '<button type="button" />' )
                    .html( addLabel )
                    .click( function () {
                        template.before( template.data( "template" ).replace(
                            /__index__/g,
                            element.find( "> fieldset" ).length
                        ) );
                        relegend();
                    } )
            );
        }

        relegend();
    };

    global.Zork.Form.Element.prototype.collection.isElementConstructor = true;

} ( window, jQuery, zork ) );
