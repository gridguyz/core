/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.enableOnSelect !== "undefined" )
    {
        return;
    }

    /**
     * Enable on select
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.prototype.enableOnSelect = function ( element )
    {
        element = $( element );

        var form    = $( element[0].form || element.parents( "form:first" ) ),
            name    = String( element.data( "jsEnableonselectField" ) || "" ),
            sel     = "[name=\"" + name.replace( /"/g, "\\\"" ) + "\"]",
            field   = null,
            change  = function () {
                var method   = null,
                    disabled = field.prop( "checked" ) === false
                            || field.prop( "selected" ) === false
                            || ! field.val();

                element.attr( "disabled", disabled ? "disabled" : null )
                       .prop( "disabled", disabled );

                if ( element.is( ":data('ui-spinner')" ) )
                {
                    method = "spinner";
                }
                else if ( element.is( ":data('ui-datepicker')" ) )
                {
                    method = "datepicker";
                }
                else if ( element.is( ":data('ui-button')" ) )
                {
                    method = "button";
                }
                else if ( element.is( ":data('ui-datetimepicker')" ) )
                {
                    method = "datetimepicker";
                }
                else if ( element.is( ":data('ui-timepicker')" ) )
                {
                    method = "timepicker";
                }
                else if ( element.is( ":data('ui-input')" ) )
                {
                    method = "input";
                }

                if ( method )
                {
                    element[method]( "option", "disabled", disabled );
                }
            };

        if ( form.length && name )
        {
            field = form.find( ":input:not([type=hidden])" + sel ).first();

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

    global.Zork.Form.prototype.enableOnSelect.isElementConstructor = true;

} ( window, jQuery, zork ) );
