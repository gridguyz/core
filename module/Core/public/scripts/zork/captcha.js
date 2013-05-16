/**
 * Captcha functionalities
 * @package zork
 * @subpackage user
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.captcha !== "undefined" )
    {
        return;
    }

    /**
     * @class Captcha module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Captcha = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "captcha" ];
    };

    global.Zork.prototype.captcha = new global.Zork.Captcha();

    global.Zork.Captcha.prototype.regenerate = function ( element )
    {
        element = $( element );
        var id  = element.data( "jsCaptchaId" ),
            img = element.parent().find( "img:first" ),
            src = img.attr( "src" );

        if ( id && img.length ) {
            element.click( function () {
                img.attr( "src", "/images/common/blank.gif" )
                   .css( "background", "url(\"/images/scripts/loading.gif\") 50% 50% no-repeat" );

                js.core.rpc( {
                    "method": "Grid\\Core\\Model\\Captcha::regenerate",
                    "callback": function () {
                        img.css( "background", "" )
                           .attr( "src", src + "?" + String( Math.random() ).replace( /^0?\./, "" ) )
                    }
                } )( id );
            } );
        }
    };

    global.Zork.Captcha.prototype.regenerate.isElementConstructor = true;

} ( window, jQuery, zork ) );
