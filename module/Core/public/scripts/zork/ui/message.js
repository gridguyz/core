/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.message !== "undefined" )
    {
        return;
    }

    var
     /* PERMISSION_ALLOWED      = 0,
        PERMISSION_NOT_ALLOWED  = 1,
        PERMISSION_DENIED       = 2,
        notifications           = ( typeof Notification !== "undefined" &&
            Function.isFunction( global.navigator.permissionLevel ) &&
            Function.isFunction( global.navigator.requestPermission ) ) ||
                global.notifications ||
                global.webkitNotifications, */
        defaultsSet = false,
        isLeft = function () {
            return $( ".js-adminmenu.js-adminmenu-right" ).length ||
                   $( "[data-js-type~='js.admin.menu'], " +
                      "[data-js-type~='zork.admin.menu']" )
                        .data( "jsAdminmenuPosition" ) == "right";
        },
        notify = function ( params )
        {
            js.style( "/styles/scripts/jgrowl.css" );
            js.require( "jQuery.fn.jGrowl", function () {
                var p = {
                    "header": params.title,
                    "sticky": !! params.important,
                    "life": params.display
                };

                if ( ! defaultsSet )
                {
                    defaultsSet = true;

                    $.jGrowl.defaults.closerTemplate = "<div>[ "
                        + js.core.translate( "default.closeAll", js.core.userLocale )
                    + "] </div>";
                }

                if ( isLeft() )
                {
                    $.jGrowl.defaults.position = "top-left";

                    $( "#jGrowl" ).removeClass( "top-right" )
                                  .addClass( "top-left" );
                }
                else
                {
                    $.jGrowl.defaults.position = "top-right";

                    $( "#jGrowl" ).removeClass( "top-left" )
                                  .addClass( "top-right" );
                }

                if ( Function.isFunction( params.open ) )
                {
                    p.beforeOpen = params.open;
                }

                if ( Function.isFunction( params.close ) )
                {
                    p.beforeClose = params.close;
                }

                $.jGrowl( params.message, p );
            } );
        } /*,
        permissionGranted = function ()
        {
            notify = function ( params )
            {
                var p;

                if ( typeof Notification !== "undefined" )
                {
                    p = new Notification(
                        null,
                        params.title || "",
                        params.message.
                            replace( /<br\s*\/?>/, "\n" ).
                            replace( /<[^>]+>/, " " )
                    );
                }
                else if ( Function.isFunction( notifications.createHTMLNotification ) )
                {
                    p = notifications.createHTMLNotification(
                        "data:text/html;charset=utf-8," +
                        encodeURIComponent(
                            "<h4>" + ( params.title || "" ) + "</h4>" +
                            params.message
                        )
                    );
                }
                else if ( Function.isFunction( notifications.createWebNotification ) )
                {
                    p = notifications.createWebNotification(
                        "data:text/html;charset=utf-8," +
                        encodeURIComponent(
                            "<h4>" + ( params.title || "" ) + "</h4>" +
                            params.message
                        )
                    );
                }
                else
                {
                    p = notifications.createNotification(
                        null,
                        params.title || "",
                        params.message.
                            replace( /<br\s*\/?>/, "\n" ).
                            replace( /<[^>]+>/, " " )
                    );
                }

                if ( Function.isFunction( params.open ) )
                {
                    p.onshow = params.open;
                    p.ondisplay = params.open;
                }

                if ( Function.isFunction( params.close ) )
                {
                    p.onclose = params.close;
                }

                p.show();

                if ( ! params.important )
                {
                    setTimeout( function(){
                        p.cancel();
                    }, parseInt( params.display, 10 ) || 1000 );
                }
            };
        },
        permissionLevel = function ()
        {
            var result = PERMISSION_DENIED;

            if ( typeof notifications !== "undefined" &&
                 typeof notifications.checkPermission === "function" )
            {
                return notifications.checkPermission();
            }

            if ( typeof global.navigator.permissionLevel === "function" )
            {
                result = global.navigator.permissionLevel( "notifications" );

                switch ( true )
                {
                    case result > 0:
                        return PERMISSION_ALLOWED;

                    case result < 0:
                        return PERMISSION_DENIED;

                    default:
                        return PERMISSION_NOT_ALLOWED;
                }
            }

            return result;
        }*/;

    /**
     * Progress element
     *
     * @memberOf Zork.Ui
     */
    global.Zork.Ui.prototype.message = function ( params )
    {
        if ( params instanceof $ || Object.isElement( params ) )
        {
            params = $( params );
            var important = params.data( "jsMessageImportant" );

            js.caller.js.require( "js.ui.message" )._notify( {
                "message": params.html(),
                "title": params.attr( "title" ) ||
                    params.data( "jsMessageTitle" ) ||
                    js.ui.message.defaultParams.header,
                "important": important === "true" || important === true,
                "display": parseInt( params.data( "jsMessageDisplay" ), 10 ) ||
                    js.ui.message.defaultParams.display
            } );

            params.remove();
        }
        else
        {
            params = $.extend( {}, js.ui.message.defaultParams, params );
            js.caller.js.require( "js.ui.message" )._notify( params );
        }
    };

    /**
     * Default parameters
     * @type Object
     */
    global.Zork.Ui.prototype.message.defaultParams =
        {
            "title": $( "head > title" ).text(),
            "display": 3333
        };

    global.Zork.Ui.prototype.message.isElementConstructor = true;

    /**
     * Hidden notify
     * @type Object
     */
    global.Zork.Ui.prototype.message._notify = function ( params )
    {
        return notify( params );
    };

 /* global.Zork.Ui.prototype.message.requestPermission = function ()
    {
        var cb = function()
        {
            if ( permissionLevel() == PERMISSION_ALLOWED )
            {
                permissionGranted();
            }
        };

        if ( Function.isFunction( global.navigator.requestPermission ) )
        {
            global.navigator.requestPermission( "notifications", cb );
        }

        if ( Function.isFunction( notifications.requestPermission ) )
        {
            notifications.requestPermission( cb );
        }
    };

    if ( typeof notifications !== "undefined" )
    {
        switch ( permissionLevel() )
        {
            case PERMISSION_ALLOWED:
                permissionGranted();
                break;

            case PERMISSION_NOT_ALLOWED:
                notify( {
                    "title": js.ui.message.defaultParams.title,
                    "message": '<a href="javascript:zork.ui.message.' +
                        'requestPermission()">Your browser supports native ' +
                        'notifications.<br />Click here to enable.</a>',
                    "important": true
                } );
                break;

            case PERMISSION_DENIED:
                break;
        }
    } */

} ( window, jQuery, zork ) );
