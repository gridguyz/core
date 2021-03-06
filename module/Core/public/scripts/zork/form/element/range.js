/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.range !== "undefined" )
    {
        return;
    }

    /**
     * Range (slider) form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.range = function ( element )
    {
        js.style( "/styles/scripts/range.css" );
        element = $( element );
        element.after( '<div class="js-range-trigger">&nbsp;</div>' );

        var slider  = $( "+ .js-range-trigger", element ),
            min     = parseFloat( element.attr( "min" ) || element.data( "jsRangeMin" ) ) || 0,
            max     = parseFloat( element.attr( "max" ) || element.data( "jsRangeMax" ) ) || 100,
            step    = parseFloat( element.attr( "step" ) || element.data( "jsRangeStep" ) ) || 1,
            origv   = element.val(),
            value   = parseFloat( origv ) || 0;

        slider.slider( {
            "value"         : value || 0,
            "min"           : min,
            "max"           : max,
            "step"          : step,
            "orientation"   : element.height() > element.width() ? "vertical" : "horizontal",
            "slide"         : function ( event, ui ) {
                var value = parseFloat( ui.value ) || 0;

                slider.attr( "title", value );
                element.val( value )
                       .trigger( "change" );
            }
        } );

        if ( element.data( "jsRangeShowinput" ) )
        {
            var change = function () {
                var change = false,
                    value = parseFloat( element.val() ) || 0 ;

                if ( ! Object.isUndefined( min ) && value < min )
                {
                    value   = min;
                    change  = true;
                }

                if ( ! Object.isUndefined( max ) && value > max )
                {
                    value   = max;
                    change  = true;
                }

                if ( change )
                {
                    element.val( value )
                           .trigger( "change" );
                }

                slider.slider( "value", value );
            };

            element.on( "keyup change", change );
        }
        else
        {
            element.addClass( "ui-helper-hidden" );
        }
    };

    global.Zork.Form.Element.prototype.range.isElementConstructor = true;

} ( window, jQuery, zork ) );
