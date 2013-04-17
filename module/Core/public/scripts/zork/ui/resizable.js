/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.resizable !== "undefined" )
    {
        return;
    }

    /**
     * Progress element
     *
     * @memberOf Zork.Ui
     */
    global.Zork.Ui.prototype.resizable = function ( element )
    {
        element = $( element );

        var aspectRatio = element.data( "jsResizableAspectRatio" ),
            grid        = element.data( "jsResizableGrid" );

        element.resizable( {
            "alsoResize"    : element.data( "jsResizableAlsoResize" ) || false,
            "animate"       : !! element.data( "jsResizableAnimate" ),
            "aspectRatio"   : ( aspectRatio === true ? true : parseFloat( aspectRatio ) ) || false,
            "autoHide"      : !! element.data( "jsResizableAutoHide" ),
            "containment"   : element.data( "jsResizableContainment" ) || false,
            "delay"         : element.data( "jsResizableDelay" ) || 0,
            "distance"      : element.data( "jsResizableDistance" ) || 1,
            "ghost"         : element.data( "jsResizableGhost" ) || false,
            "grid"          : grid ? $.map( String( grid ).split( /[,x]/, 2 ), global.parseFloat ) : false,
            "handles"       : element.data( "jsResizableHandles" ) || "e,s,se",
            "helper"        : element.data( "jsResizableHelper" ) || false,
            "maxHeight"     : parseInt( element.data( "jsResizableMaxHeight" ), 10 ) ||
                              parseInt( element.css( "max-height" ), 10 ) || null,
            "maxWidth"      : parseInt( element.data( "jsResizableMaxWidth" ), 10 ) ||
                              parseInt( element.css( "max-width" ), 10 ) || 10,
            "minHeight"     : parseInt( element.data( "jsResizableMinHeight" ), 10 ) ||
                              parseInt( element.css( "min-height" ), 10 ) || null,
            "minWidth"      : parseInt( element.data( "jsResizableMinWidth" ), 10 ) ||
                              parseInt( element.css( "min-width" ), 10 ) || 10
        } );
    };

    global.Zork.Ui.prototype.resizable.isElementConstructor = true;

} ( window, jQuery, zork ) );
