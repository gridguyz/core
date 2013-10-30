/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.details !== "undefined" )
    {
        return;
    }

    /**
     * Details (html5) element
     *
     * @memberOf Zork.Ui
     */
    global.Zork.Ui.prototype.details = function ( element )
    {
        element = $( element );

        if ( element.is( "details" ) )
        {
            var summary = element.find( "> summary" ).first(),
                open    = !! element.attr( "open" );

            if ( 0 in summary && ! ( "open" in element ) )
            {
                if ( ! open )
                {
                    element.children()
                           .not( summary )
                           .css( "display", "none" );
                }

                summary.click( function ( event ) {
                    event.preventDefault();
                    open = ! open;
                    element.attr( "open", open ? "open" : null )
                           .children()
                           .not( summary )
                           .css( "display", open ? "" : "none" );
                } );
            }
        }
    };

    global.Zork.Ui.prototype.details.isElementConstructor = true;

} ( window, jQuery, zork ) );
