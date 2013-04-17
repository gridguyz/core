/**
 * RowSet view helper javascript interface
 * @package zork
 * @subpackage user
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.rowSet !== "undefined" )
    {
        return;
    }

    /**
     * @class User module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.RowSet = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "rowSet" ];
    };

    global.Zork.prototype.rowSet = function ( element )
    {
        element = $( element );

        var id      = element.data( "jsRowsetId" ),
            enc     = global.encodeURIComponent || global.escape,
            reload  = function ( data ) {
                js.core.loadRawElement( {
                    "type": "POST",
                    "target": $( "#" + id ),
                    "replace": true,
                    "url": "?",
                    "data": data
                } );
            };

        element.on( "click", ":submit", function ( evt ) {
            var $this   = $( this ),
                submit  = $this.attr( "name" ),
                t;

            if ( submit )
            {
                submit += "=" + enc( $this.attr( "value" ) );
            }

            reload( t = element.serialize() + ( submit ? "&" + submit : "" ) );

            $this.blur();
            evt.preventDefault();
            return false;
        } );
    };

    global.Zork.prototype.rowSet.isElementConstructor = true;

} ( window, jQuery, zork ) );
