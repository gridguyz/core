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

        var title = element.attr( "title" );
        if ( title ) { title += "\n"; }
        title += element.text().replace( [ /^\s+/, /\s+$/ ], '' );
        element.attr( "title", title ).empty();

        var val = parseFloat( element.attr( "value" ) ||
            ( element.data( "jsProgressValue" ) / 100 ) );

        if ( element.attr( "max" ) )
        {
            val = val / parseFloat( element.attr( "max" ) );
        }

        element.progressbar( {
            "value": 100 * val
        } );
    };

    global.Zork.Ui.prototype.progress.isElementConstructor = true;

} ( window, jQuery, zork ) );
