/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.select !== "undefined" )
    {
        return;
    }

    js.require( "jQuery.ui.selectmenu" );
    js.style( "/styles/scripts/selectmenu.css" );

    $.widget( "ui.selectmenuicons", $.ui.selectmenu, {
        "options": {
            "iconsPrefix": "ui-icon-"
	},
        "_renderItem": function ( ul, item ) {

            var li   = this._super( ul, item ),
                elem = $( item.element ),
                iprx = this.options.iconsPrefix,
                icon = elem.data( "jsSelectIcon" ),
                val  = String(
                    item.value ||
                    item.label ||
                    elem.attr( "value" )
                );

            if ( icon != "none" && ( icon || ( iprx && item.value ) ) )
            {
                li.find( "a:first" )
                  .prepend( $( "<span />", {
                      "class": "ui-icon " + ( icon || (
                          String( iprx ) + val
                            .toLowerCase()
                            .replace( /[^a-z0-9_-]/g, "-" )
                      ) )
                  } ) );
            }

            return li;
	}
    } );

    /**
     * Select form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.select = function ( element )
    {
        element = $( element );

        var iconPrefix = element.data( "jsSelectIconprefix" ) || false;

        if ( iconPrefix || element.find( "option[data-icon]" ).length )
        {
            element.selectmenuicons( {
                "icons": true,
                "iconsPrefix": iconPrefix,
                "select" : function () {
                    element.trigger( "change" );
                }
            } );
        }
        else
        {
            element.selectmenu( {
                "select" : function () {
                    element.trigger( "change" );
                }
            } );
        }

    };

    global.Zork.Form.Element.prototype.select.isElementConstructor = true;

} ( window, jQuery, zork ) );
