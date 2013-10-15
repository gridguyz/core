/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.onOff !== "undefined" )
    {
        return;
    }

    /**
     * On/off (checkbox) form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.onOff = function ( element )
    {
        element = $( element );

        if ( element.is( "input[type=checkbox]" ) )
        {
            js.style( "/styles/scripts/onoff.css" );
            element.addClass( "ui-helper-hidden" );

            var checked = element.prop( "checked" ),
                button  = $( "<button type='button' />" )
                            .addClass( "js-form-onoff-button" )
                            .insertAfter( element )
                            .append( "<span class='js-form-onoff-slider' />" );

            if ( element.prop( "checked" ) )
            {
                button.addClass( "js-form-onoff-checked" );
            }

            button.on( "click dblclick", function () {
                checked = ! element.prop( "checked" );
                button.toggleClass( "js-form-onoff-checked", checked );
                element.prop( "checked", checked )
                       .trigger( "change" );
            } );

            element.on( "change", function () {
                if ( checked != element.prop( "checked" ) )
                {
                    checked = ! checked;
                    button.toggleClass( "js-form-onoff-checked", checked );
                }
            } );
        }
    };

    global.Zork.Form.Element.prototype.number.isElementConstructor = true;

} ( window, jQuery, zork ) );
