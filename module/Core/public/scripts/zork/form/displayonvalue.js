/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.displayOnValue !== "undefined" )
    {
        return;
    }

    /**
     * Only display field on a specific value
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.prototype.displayOnValue = function ( element )
    {
        element = $( element );

        var form    = $( element[0].form || element.parents( "form:first" ) ),
            name    = String( element.data( "jsDisplayonvalueField" ) || "" ),
            display = String( element.data( "jsDisplayonvalueValue" ) || "" ),
            sel     = "[name=\"" + name.replace( /"/g, "\\\"" ) + "\"]",
            toggle  = element.closest( "dd" ).prev( "dt" ).addBack(),
            field   = null,
            change  = function () {
                var current = field.length > 1
                            ? field.filter( ":checked:first" ).val()
                            : field.val();

                if ( String( current ) === display )
                {
                    toggle.show();
                }
                else
                {
                    toggle.hide();
                }

                field.blur();
            };

        if ( form.length && name )
        {
            field = form.find( 'input[type="checkbox"]' + sel +
                                ', input[type="radio"]' + sel );

            if ( ! field.length )
            {
                field = form.find( ":input:not([type=hidden])" + sel ).first();
            }

            if ( ! field.length )
            {
                field = form.find( "input[type=hidden]" + sel ).first();
            }

            if ( field.length )
            {
                field.on( "change click", change );
                setTimeout( change, 1 );
            }
        }
    };

    global.Zork.Form.prototype.displayOnValue.isElementConstructor = true;

} ( window, jQuery, zork ) );
