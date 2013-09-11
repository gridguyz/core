/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.progress !== "undefined" )
    {
        return;
    }

    /**
     * Progress element
     *
     * @memberOf Zork.Ui
     */
    global.Zork.Ui.prototype.progress = function ( element )
    {
        element = $( element );
        element.css( { "display": "block", "width": "100%" } );

        var title = element.attr( "title" ),
            min   = parseFloat( element.attr( "min" ) || element.data( "jsProgressMin" ) ) || 0,
            max   = parseFloat( element.attr( "max" ) || element.data( "jsProgressMax" ) ) || 100,
            indet = ( element.attr( "value" ) === null ||
                      element.attr( "value" ) === "" ) &&
                    ( element.data( "jsProgressValue" ) === null ||
                      element.data( "jsProgressValue" ) === "" ||
                      element.data( "jsProgressValue" ) === false ),
            value = indet ? false : parseFloat( element.attr( "value" ) || element.data( "jsProgressValue" ) );

        if ( title ) { title += "\n"; }
        title += element.text().replace( [ /^\s+/, /\s+$/ ], '' );
        element.attr( "title", title ).empty();

        element.progressbar( {
            "max": max - min,
            "value": value === false ? false : value - min
        } );
    };

    global.Zork.Ui.prototype.progress.isElementConstructor = true;

} ( window, jQuery, zork ) );
