/**
 * Validation functionalities
 * @package zork
 * @subpackage validate
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{

    "use strict";

    if ( typeof js.wizard !== "undefined" )
    {
        return;
    }

    var cancel      = null,
        finish      = null,
        close       = null;
     // dialog      = js.require( "js.ui.dialog" );

    /**
     * Draw a wizard
     *
     * @param {Object} params
     * @return Boolean
     */
    global.Zork.prototype.wizard = function ( params )
    {
        if ( null !== close )
        {
            return false;
        }

        js.style( "/styles/wizard.css" );
        params = params || {};

        if ( ! ( "url" in params ) )
        {
            return false;
        }

        params.url      = String( params.url );
        params.params   = params.params || {};

        var id      = "js-wizard-" + js.generateId.number(),
            wizard  = $( '<iframe id="' + id + '" name="' + id + '" />' ),
            i;

        for ( i in params.params )
        {
            params.url += ~ params.url.indexOf( "?" ) ? "&" : "?";
            params.url += i + "=" + String( params.params[i] );
        }

        if ( Function.isFunction( params.cancel ) )
        {
            cancel = params.cancel;
        }

        if ( Function.isFunction( params.finish ) )
        {
            finish = params.finish;
        }

        wizard.attr( {
                    "src"               : "javascript:void(0)",
                    "frameborder"       : "0",
                    "allowtransparency" : "true"
                } )
              .css( {
                    "width"             : "600px",
                    "margin"            : "0px",
                    "padding"           : "0px",
                    "border"            : "none",
                    "min-height"        : "100px"
                } );

        close = js.core.layer( wizard );

        if ( "form" in params && params.form )
        {
            params.form = $( params.form );

            params.form.attr( {
                "target": id,
                "action": params.url
            } );

            if ( ! ( "submit" in params ) || params.submit ) {
                params.form.submit();
            }
        }
        else
        {
            wizard.attr( "src", params.url )
        }

        return true;
    };

    global.Zork.prototype.wizard.version = "1.0";

    global.Zork.prototype.wizard.modulePrefix = [ "zork", "wizard" ];

    /**
     * Close a wizard
     *
     * @param {String} type
     * @param {HTMLElement|$} element
     * @return Boolean
     */
    global.Zork.prototype.wizard.close = function ( type, element )
    {
        if ( null !== close )
        {
            if ( "cancel" === type && null !== cancel )
            {
                element = $( element );
                cancel.call( element, element );
            }

            if ( "finish" === type && null !== finish )
            {
                element = $( element );
                finish.call( element, element );
            }

            close();
            close  = null;
            cancel = null;
            finish = null;
            return true;
        }

        return false;
    };

    /**
     * Cancel a wizard
     *
     * @param {HTMLElement|$} element
     * @return Boolean
     */
    global.Zork.prototype.wizard.cancel = function ( element )
    {
        return js.caller.js.wizard.close( 'cancel', element );
    };

    /**
     * Finish a wizard
     *
     * @param {HTMLElement|$} element
     * @return Boolean
     */
    global.Zork.prototype.wizard.finish = function ( element )
    {
        return js.caller.js.wizard.close( 'finish', element );
    };

    global.Zork.prototype.wizard.cancel.isElementConstructor = true;
    global.Zork.prototype.wizard.finish.isElementConstructor = true;

} ( window, jQuery, zork ) );
