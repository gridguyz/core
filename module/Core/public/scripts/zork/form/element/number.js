/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.number !== "undefined" )
    {
        return;
    }

    /**
     * Range (slider) form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.number = function ( element )
    {
        element = $( element );

        var min     = parseFloat( element.attr( "min" ) || element.data( "jsNumberMin" ) ) || null,
            max     = parseFloat( element.attr( "max" ) || element.data( "jsNumberMax" ) ) || null,
            step    = parseFloat( element.attr( "step" ) || element.data( "jsNumberStep" ) ) || 1,
            origv   = element.val(),
            value   = parseFloat( origv ) || 0;

        element.spinner( {
            "value" : value || 0,
            "min"   : min,
            "max"   : max,
            "step"  : step
        } );
    };

    global.Zork.Form.Element.prototype.number.isElementConstructor = true;

} ( window, jQuery, zork ) );
