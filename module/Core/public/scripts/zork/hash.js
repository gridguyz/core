/**
 * Hash functionalities
 * @package zork
 * @subpackage hash
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.hash !== "undefined" )
    {
        return;
    }
    
    /**
     * @class Hash module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Hash = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "hash" ];
    };

    global.Zork.prototype.hash = new global.Zork.Hash();

} ( window, jQuery, zork ) );
