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
                var disabled = ! field.val(),
                    method   = null;

                element.attr( "disabled", disabled ? "disabled" : null )
                       .prop( "disabled", disabled );

                if ( element.is( ":ui-spinner" ) )
                {
                    method = "spinner";
                }
                else if ( element.is( ":ui-datepicker" ) )
                {
                    method = "datepicker";
                }
                else if ( element.is( ":ui-button" ) )
                {
                    method = "button";
                }
                else if ( element.is( ":ui-datetimepicker" ) )
                {
                    method = "datetimepicker";
                }
                else if ( element.is( ":ui-timepicker" ) )
                {
                    method = "timepicker";
                }
                else if ( element.is( ":ui-input" ) )
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
