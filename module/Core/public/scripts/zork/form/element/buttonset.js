/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.buttonset !== "undefined" )
    {
        return;
    }

    /**
     * Button-set element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.buttonset = function ( element )
    {
        element = $( element );
        element.buttonset();

        if ( element.data( "jsButtonsetOrientation" ) == "vertical" )
        {
            element.children( ".ui-button" )
                .removeClass( "ui-corner-all ui-corner-top ui-corner-left" +
                             " ui-corner-right ui-corner-bottom" )
				.filter( ":first" )
					.addClass( "ui-corner-top" )
				.end()
				.filter( ":last" )
					.addClass( "ui-corner-bottom" )
				.end();
        }
    };

    global.Zork.Form.Element.prototype.buttonset.isElementConstructor = true;

} ( window, jQuery, zork ) );
