/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.dialog !== "undefined" )
    {
        return;
    }

    /**
     * Opened dialogs in FIFO (width id)
     */
    var opened = [];

    /**
     * Counting for unique id
     */
    var count = 0;

    /**
     * Dialog window
     *
     * @function
     * @memberOf JS.UI
     * @param {Object|HTMLElement|$} params
     * @param {String} params.message Message of the dialog window.
     * @param {String} params.title Title of the dialog window.
     * @type String
     */
    var dialog = function ( params )
    {
        js.style( "/styles/scripts/dialog.css" );

        var id, di;

        if ( params instanceof $ || Object.isElement( params ) )
        {
            params = $( params );
            id = params.attr( "id" );

            if ( ! id )
            {
                params.attr( "id", id = "js-dialog-" + ( ++count ) );
            }

            var p = {
                "buttons": {}
            };

            $( "input[type=reset], input[type=button], " +
                    "input[type=submit], button", params ).each(
                function ()
                {
                    var self = $( this );
                    p.buttons[ self.val() || self.html() ] = function()
                    {
                        self.click();
                        $( this ).dialog( "destroy" );
                        $( this ).remove();
                    };
                }
            ).remove();

            if ( $.isEmptyObject( p.buttons ) )
            {
                p.buttons[ js.core.translate( "default.ok" ) ] = function()
                {
                    $( this ).dialog( "destroy" );
                    $( this ).remove();
                };
            }

            if ( ! params.attr( "title" ) )
            {
                params.attr( "title", dialog.defaultParams.title );
            }

            di = params.remove().removeClass( "ui-helper-hidden" ).dialog( p );
        }
        else
        {
            params = $.extend( {}, dialog.defaultParams, params );

            if ( typeof params.id !== "undefined" )
            {
                id = params.id;
                delete params.id;

                var d = $( "#" + id );
                if ( d.size() >= 0 )
                {
                    dialog.destroy( id );
                }
            }
            else
            {
                id = "js-dialog-" + ( ++count );
            }

            di = $( '<div id="' + id + '" />' );

            if ( params.message instanceof $ ||
                 Object.isElement( params.message ) )
            {
                di.append( $( params.message ).remove() );
            }
            else
            {
                di.html( String( params.message ) );
            }

            di.attr( "title", params.title );

            $( "body" ).append( di );
            di.dialog( params );
        }

        opened.unshift( id );
        di.parents( ".ui-dialog" ).addClass( "js-dialog-container" );
        return id;
    };

    /**
     * Dialog is an element constructor
     *
     * @type String
     */
    dialog.isElementConstructor = true;

    /**
     * Dialog version
     *
     * @type String
     */
    dialog.version = "1.0";

    /**
     * Module prefix for inner use
     */
    dialog.modulePrefix = [ "zork", "core" ];

    /**
     * Default dialog params
     *
     * @type Object
     */
    dialog.defaultParams =
    {
        "message": "",
        "title": $( "head > title" ).text()
    };

    /**
     * Return the last (nth) opened dialog's id
     *
     * @function
     * @memberOf JS.UI.dialog
     * @param {Number} nth [optional] default: 0
     * @type String
     */
    dialog.getLastId = function ( nth )
    {
        return opened[nth || 0];
    };

    /**
     * Return the next dialog's id
     *
     * @function
     * @memberOf JS.UI.dialog
     * @type String
     */
    dialog.getNextId = function ()
    {
        return "js-dialog-" + ( 1 + count );
    };

    /**
     * Alert with design
     *
     * @function
     * @memberOf JS.UI.dialog
     * @param {Object} params
     * @param {String} params.message Message of the dialog window.
     * @param {String} params.title Title of the dialog window.
     * @param {String} [params.buttonLabel="Ok"] The button's label
     */
    dialog.alert = function ( params )
    {
        params = $.extend( {}, dialog.defaultParams, params );

        params.modal = true;
        params.resizable = false;

        var okEvent = params.ok;

        if ( typeof params.ok !== "undefined" )
        {
            delete params.ok;
        }

        if ( typeof params.buttons === "undefined" )
        {
            params.buttons = {};
        }

        if ( typeof params.buttonLabel === "undefined" )
        {
            params.buttonLabel = js.core.translate( "default.ok" );
        }

        params.buttons[params.buttonLabel] = function()
        {
            $( this ).dialog( "destroy" );

            if ( Function.isFunction( okEvent ) )
            {
                okEvent();
            }

            $( this ).remove();
        };

        delete params.buttonLabel;

        dialog( params );
    };

    /**
     * Modal, not resizable form.
     *
     * @function
     * @memberOf JS.UI.dialog
     * @param {Object} params
     * @param {String} params.message Message of the dialog window.
     * @param {String} params.title Title of the dialog window.
     */
    dialog.progress = function ( params )
    {
        params = $.extend( {}, dialog.progress.defaultParams, params );

        params.modal = true;
        params.resizable = false;

        dialog( params );
    };

    /**
     * Default progress-dialog params
     *
     * @type Object
     */
    dialog.progress.defaultParams =
    {
        "title": js.core.translate( "default.workInProgress" ),
        "message": js.core.translate( "default.pleaseWait" )
    };

    /**
     * Modal, not resizable form.
     *
     * @function
     * @memberOf JS.UI.dialog
     * @param {Object|HTMLElement} params
     * @param {String} params.message Message of the dialog window.
     * @param {String} params.title Title of the dialog window.
     */
    dialog.frame = function ( params )
    {
        var element,
            did, url,
            refresh = false;

        if ( Object.isElement( params ) || params instanceof $ )
        {
            element = $( params );
            params  = {
                "autoOpen": false,
                "id": element.data( "jsDialogFrameId" ),
                "name": element.data( "jsDialogFrameName" ),
                "url": element.data( "jsDialogFrameUrl" ),
                "refresh": !! element.data( "jsDialogFrameRefresh" ),
                "bind": element.data( "jsDialogFrameBind" ),
                "width": element.data( "jsDialogWidth" ) || 600,
                "height": element.data( "jsDialogHeight" ) || 400,
                "closeOnEscape": element.data( "jsDialogCloseOnEscape" ),
                "draggable": element.data( "jsDialogDraggable" ),
                "closeText": element.data( "jsDialogCloseText" ) || null,
                "dialogClass": element.data( "jsDialogClass" ) || "",
                "hide": element.data( "jsDialogHide" ) || "",
                "modal": element.data( "jsDialogModal" ),
                "resizable": !! element.data( "jsDialogResizable" ),
                "show": element.data( "jsDialogShow" ) || "",
                "position": element.data( "jsDialogPosition" ) || null,
                "stack": element.data( "jsDialogStack" ),
                "title": element.data( "jsDialogTitle" ) || element.attr( "title" ) || dialog.defaultParams.title,
                "zIndex": element.data( "jsDialogZIndex" ) || 1000,
                "buttons": []
            };

            if ( Object.isUndefined( params.closeOnEscape ) )   params.closeOnEscape    = true;
            if ( Object.isUndefined( params.draggable ) )       params.draggable        = true;
            if ( Object.isUndefined( params.stack ) )           params.stack            = true;
            if ( Object.isUndefined( params.modal ) )           params.modal            = true;

            if ( typeof params.position == "string" &&
                 ~ params.position.indexOf( "," ) )
            {
                params.position = params.position.split( "," );
            }
            else if ( params.position !== null &&
                      typeof params.position.of != "undefined" )
            {
                params.position.of = global;
            }

            element.find( "button, input[type=button]" )
                   .each( function () {
                       var $this = $( this );
                       params.buttons.push( {
                           "text": $this.text() || $this.val(),
                           "click": function () {
                               var event = $.Event( "click" );
                               $this.trigger( event );

                               if ( did && ! event.isDefaultPrevented() )
                               {
                                   $( "#" + did ).dialog( "close" );
                               }
                           }
                       } );
                   } );

            if ( ! params.url )
            {
                if ( element.is( "a" ) )
                {
                    params.url = element.attr( "href" );
                }
                else if ( element.is( "form" ) )
                {
                    params.url = element.attr( "action" );

                    if ( ! params.bind )
                    {
                        params.bind = "submit";
                    }
                }
                else
                {
                    params.url = element.attr( "href" ) || element.attr( "src" );
                }
            }

            element.on( params.bind || "click", function () {
                if ( did ) {
                    $( "#" + did ).dialog( "open" );
                }

                return false;
            } );
        }

        if ( params.refresh )
        {
            refresh = true;
            delete params.refresh;
        }

        if ( params.url )
        {
            params.id   = params.id     || params.name  || js.generateId();
            params.name = params.name   || params.id;

            params.message = dialog.frame.template
                .replace( '{id}', params.id )
                .replace( '{name}', params.name )
                .replace( '{url}', params.url )
                .replace( '{width}', ( Number( params.width ) || 600 ) - 50 )
                .replace( '{height}', ( Number( params.height ) || 400 ) - 50 );

            url = params.url;
            delete params.id;
            delete params.name;
            delete params.url;

            did = dialog( params );

            if ( refresh ) {
                $( "#" + did ).on( "dialogclose", function () {
                    $( this ).find( "iframe:first" )
                             .attr( "src", url );
                } );
            }

            return did;
        }

        return null;
    };

    dialog.frame.template = '<iframe ' +
        'id="{id}" name="{name}" src="{url}" frameborder="0" allowtransparency="true" ' +
        'style="border: none; background: transparent; width: {width}px; height: {height}px;">' +
    '</iframe>';

    /**
     * Dialog is an element constructor
     *
     * @type String
     */
    dialog.frame.isElementConstructor = true;

    /**
     * Modal, resizable form.
     *
     * @function
     * @memberOf JS.UI.dialog
     * @param {Object|HTMLElement} params
     * @param {String} params.message Message of the dialog window.
     * @param {String} params.title Title of the dialog window.
     */
    dialog.ajax = function ( params )
    {
        var element,
            did, url,
            load, loaded,
            refresh = false;

        if ( Object.isElement( params ) || params instanceof $ )
        {
            element = $( params );
            params  = {
                "autoOpen": false,
                "url": element.data( "jsDialogAjaxUrl" ),
                "refresh": !! element.data( "jsDialogAjaxRefresh" ),
                "bind": element.data( "jsDialogAjaxBind" ),
                "width": element.data( "jsDialogWidth" ) || 600,
                "height": element.data( "jsDialogHeight" ) || 400,
                "closeOnEscape": element.data( "jsDialogCloseOnEscape" ),
                "draggable": element.data( "jsDialogDraggable" ),
                "closeText": element.data( "jsDialogCloseText" ) || null,
                "dialogClass": element.data( "jsDialogClass" ) || "",
                "hide": element.data( "jsDialogHide" ) || "",
                "modal": element.data( "jsDialogModal" ),
                "resizable": !! element.data( "jsDialogResizable" ),
                "show": element.data( "jsDialogShow" ) || "",
                "position": element.data( "jsDialogPosition" ) || null,
                "stack": element.data( "jsDialogStack" ),
                "title": element.data( "jsDialogTitle" ) || element.attr( "title" ) || dialog.defaultParams.title,
                "zIndex": element.data( "jsDialogZIndex" ) || 1000,
                "buttons": []
            };

            if ( Object.isUndefined( params.closeOnEscape ) )   params.closeOnEscape    = true;
            if ( Object.isUndefined( params.draggable ) )       params.draggable        = true;
            if ( Object.isUndefined( params.stack ) )           params.stack            = true;
            if ( Object.isUndefined( params.modal ) )           params.modal            = true;

            if ( typeof params.position == "string" &&
                 ~ params.position.indexOf( "," ) )
            {
                params.position = params.position.split( "," );
            }
            else if ( params.position !== null &&
                      typeof params.position.of != "undefined" )
            {
                params.position.of = global;
            }

            element.find( "button, input[type=button]" )
                   .each( function () {
                       var $this = $( this );
                       params.buttons.push( {
                           "text": $this.text() || $this.val(),
                           "click": function () {
                               var event = $.Event( "click" );
                               $this.trigger( event );

                               if ( did && ! event.isDefaultPrevented() )
                               {
                                   $( "#" + did ).dialog( "close" );
                               }
                           }
                       } );
                   } );

            if ( ! params.url )
            {
                if ( element.is( "a" ) )
                {
                    params.url = element.attr( "href" );
                }
                else if ( element.is( "form" ) )
                {
                    params.url = element.attr( "action" );

                    if ( ! params.bind )
                    {
                        params.bind = "submit";
                    }
                }
                else
                {
                    params.url = element.attr( "href" ) || element.attr( "src" );
                }
            }

            element.on( params.bind || "click", function () {
                if ( did ) {
                    $( "#" + did ).dialog( "open" );
                }

                return false;
            } );
        }

        if ( params.refresh )
        {
            refresh = true;
            delete params.refresh;
        }

        if ( params.url )
        {
            url = params.url;
            delete params.url;
            loaded = function ( text ) {
                if ( did ) {
                    $( "#" + did ).html( text );
                } else {
                    params.message = text;
                    did = dialog( params );

                    if ( refresh ) {
                        $( "#" + did ).on( "dialogclose", load );
                    }
                }

                var buttons = Object.clone( params.buttons );

                $( "#" + did ).find( "form button, form input[type=button], form input[type=submit]" )
                              .each( function () {
                                  var $this = $( this ).hide();
                                  buttons.push( {
                                      "text": $this.text() || $this.val(),
                                      "click": function () {
                                          var event = $.Event( "click" );
                                          $this.trigger( event );

                                          if ( did && ! event.isDefaultPrevented() )
                                          {
                                              $( "#" + did ).dialog( "close" );
                                          }
                                      }
                                  } );
                              } );

                $( "#" + did ).dialog(
                    "option",
                    "buttons",
                    buttons
                );

                js.core.parseDocument( "#" + did );
            };

            load = function () {
                $.ajax( {
                    "url": url,
                    "async": true,
                    "cache": false,
                    "success": loaded,
                    "dataType": "text",
                    "error": function () {
                        loaded( js.core.translate( "default.error" ) );
                    }
                } );
            };

            load();
            return null;
        }

        return null;
    };

    /**
     * Dialog is an element constructor
     *
     * @type String
     */
    dialog.ajax.isElementConstructor = true;

    /**
     * Confirm dialog
     *
     * @function
     * @memberOf JS.UI.dialog
     * @param {Object|String} params
     * @param {String} params.message Message of the dialog window.
     * @param {String} params.title Title of the dialog window.
     * @param {Function} params.yes If user clicks to yes
     * @param {Function} params.no If user clicks to no
     * @param {HTMLElement} params.element If user clicks to no,
     *                      prevent default action on this element
     * @type Boolean
     */
    dialog.confirm = function ( params )
    {
        if ( Object.isUndefined( params ) )
        {
            params = {};
        }
        else if ( typeof params == "string" )
        {
            params = {
                "message": params
            };
        }
        else if ( Object.isElement( params ) || params instanceof $ )
        {
            params = {
                "element": params
            };
        }

        if ( ! params.message )
        {
            delete params.message;
        }

        params = $.extend(
            {},
            dialog.defaultParams,
            {
                "message": js.core.translate( "default.areYouSure" )
            },
            params
        );

        var yesEvent = params.yes,
            noEvent = params.no,
            element = params.element;

        if ( typeof params.yes !== "undefined" )
        {
            delete params.yes;
        }
        if ( typeof params.no !== "undefined" )
        {
            delete params.no;
        }
        if ( typeof params.element !== "undefined" )
        {
            delete params.element;
        }

        if ( typeof params.buttons === "undefined" )
        {
            params.buttons = {};
        }

        if ( typeof params.noLabel === "undefined" )
        {
            params.noLabel = js.core.translate( "default.no" );
        }

        params.buttons[params.noLabel] =
            function ()
            {
                $( this ).dialog( "destroy" );

                if ( Function.isFunction( noEvent ) )
                {
                    noEvent();
                }

                $( this ).remove();
            };

        if ( typeof params.yesLabel === "undefined" )
        {
            params.yesLabel = js.core.translate( "default.yes" );
        }

        params.buttons[params.yesLabel] =
            function ()
            {
                $( this ).dialog( "destroy" );

                if ( Function.isFunction( yesEvent ) )
                {
                    yesEvent();
                }

                if ( Object.isElement( element ) )
                {
                    element.setAttribute( "onclick", "" );
                    element.onclick = function () {};
                    element = $( element );

                    if ( element.is( "a, link" ) )
                    {
                        global.location.href = element.attr( "href" );
                    }
                 /* else if ( element.is( ":input" ) )
                    {
                        element[0].form.submit();
                    } */

                    element[0].click();
                }

                $( this ).remove();
            };

        delete params.noLabel;
        delete params.yesLabel;

        params.modal = true;

        dialog( params );

        return false;
    };

    /**
     * Prompt dialog
     *
     * @function
     * @memberOf JS.UI.dialog
     * @param {Object} params
     * @param {String} params.message Message of the dialog window
     * @param {String} params.title Title of the dialog window
     * @param {String} params.defaultValue Default value of the input
     * @param {Function} params.input If user clicks to ok
     * @param {Function} params.cancel If user clicks to cancel
     */
    dialog.prompt = function ( params )
    {
        params = $.extend( {}, dialog.defaultParams, params );

        var input = $( '<input type="text" />' ),
            inputEvent = params.input,
            cancelEvent = params.cancel,
            defaultValue = typeof params.defaultValue !== "undefined" ?
                String( params.defaultValue ) : "",
            dialogId = null;

        if ( typeof params.input !== "undefined" )
        {
            delete params.input;
        }
        if ( typeof params.cancel !== "undefined" )
        {
            delete params.cancel;
        }
        if ( typeof params.defaultValue !== "undefined" )
        {
            delete params.defaultValue;
        }

        params.message = $( "<div />" )
            .append(
                Object( params.message ) instanceof String
                    ? $( "<p />" ).html( params.message )
                    : params.message
            )
            .append(
                $( "<p />" )
                    .css( "text-align", "center" )
                    .append( input )
            );

        if ( typeof params.buttons === "undefined" )
        {
            params.buttons = {};
        }

        if ( typeof params.cancelLabel === "undefined" )
        {
            params.cancelLabel = js.core.translate( "default.cancel" );
        }

        params.buttons[params.cancelLabel] =
            function ()
            {
                $( this ).dialog( "destroy" );

                if ( Function.isFunction( cancelEvent ) )
                {
                    cancelEvent();
                }

                $( this ).remove();
            };

        if ( typeof params.okLabel === "undefined" )
        {
            params.okLabel = js.core.translate( "default.ok" );
        }

        params.buttons[params.okLabel] =
            function ()
            {
                var value = $( "#" + dialogId + " input" ).val();

                $( this ).dialog( "destroy" );

                if ( Function.isFunction( inputEvent ) )
                {
                    inputEvent( value );
                }

                $( this ).remove();
            };

        delete params.okLabel;
        delete params.cancelLabel;

        params.modal = true;

        dialog( params );

        dialogId = dialog.getLastId();

        input.keypress( function( e ) {
            if ( Number( e.which ) === 13 )
            {
                var value = String( this.value );

                $( "#" + dialogId ).dialog( "destroy" );

                if ( Function.isFunction( inputEvent ) )
                {
                    inputEvent( value );
                }

                $( this ).remove();
            }
        } ).val( defaultValue ).focus();
    };

    /**
     * Ask dialog (like prompt, just multiple inputs)
     *
     * @function
     * @memberOf JS.UI.dialog
     * @param {Object} params
     * @param {String} params.message Message of the dialog window.
     * @param {String} params.title Title of the dialog window.
     * @param {Object} params.inputs The inputs as name: descriptor pairs
     * @param {Function} params.input If user clicks to ok
     * @param {Function} params.cancel If user clicks to cancel
     * @example <p>
     *  js.ui.dialog.ask( {
     *      "title" : "sample",
     *      "message" : "sample ask",
     *      "inputs" : {
     *          "name": {
     *              "label" : "Name",
     *              "type" : "text", // default
     *              "default" : "Type your name"
     *          },
     *          "hobbies": {
     *              "label" : "Hobbies",
     *              "type" : "select-multiple",
     *              "options" : ["art", "film", "etc."] // can be an object
     *                                          // with value: label pairs too
     *          }
     *      },
     *      "input" : function ( results )
     *      {
     *          js.ui.dialog.alert( {
     *              "title": "sample",
     *              "message": "Dear " + results.name + "!\n" +
     *                  "Your hobbies: " + results.hobbies.join( ", " )
     *          } );
     *      }
     *  } );
     *
     *  // valid types are:
     *  // "text" (default), "password", "textarea",
     *  // "select-one", "select-multiple"
     * </p>
     */
    dialog.ask = function ( params )
    {
        params = $.extend( {}, dialog.defaultParams, params );

        var inputList = $( [] ),
            inputEvent = params.input,
            cancelEvent = params.cancel,
            inputs = typeof params.inputs !== "undefined" ? params.inputs : {},
            dialogId = null, i = null, j = null, inp = null,
            form = $( "<dl />" ),
            idPrefix = js.generateId() + "-",
            getResult = function ()
            {
                var result = {};

                for ( i in inputs )
                {
                    if ( ! Object.isUndefined( inputs, i ) )
                    {
                        if ( inputs[i].type == "select-multiple" )
                        {
                            result[i] = [];

                            $( "#" + idPrefix + i ).find( ":input:checked" ).
                                each( function ()
                                {
                                    result[i].push( this.value );
                                } );
                        }
                        else
                        {
                            result[i] = $( "#" + idPrefix + i ).val();
                        }
                    }
                }

                return result;
            };

        if ( typeof params.input !== "undefined" )
        {
            delete params.input;
        }
        if ( typeof params.cancel !== "undefined" )
        {
            delete params.cancel;
        }
        if ( typeof params.inputs !== "undefined" )
        {
            delete params.inputs;
        }

        params.message = $( "<div />" ).append( params.message );

        for ( i in inputs )
        {
            if ( ! Object.isUndefined( inputs, i ) )
            {
                form.append(
                    '<dt><label for="' + idPrefix + i + '">' +
                        ( inputs[i].label || i ) +
                    "</label></dt>"
                );

                if ( inputs[i].type == "select-multiple" )
                {
                    inp = $( '<div id="' + idPrefix + i + '" />' ).css( {
                        "display"       : "inline-block",
                        "text-align"    : "left"
                    } );

                    if ( ! Array.isArray( inputs[i]["default"] ) )
                    {
                        inputs[i]["default"] = [ String( inputs[i]["default"] ) ];
                    }

                    if ( Array.isArray( inputs[i].options ) )
                    {
                        inputs[i].options.forEach( function ( option )
                        {
                            inp.append(
                                '<label><input type="checkbox" name="' + i
                                    + '[]" value="' + option + '"' +
                                    ( inputs[i]["default"].indexOf( option )
                                        >= 0 ? ' checked="checked"' : "" ) +
                                ">" + option + "</label>"
                            );

                            inp.append( "<br />" );
                        } );
                    }
                    else
                    {
                        for ( j in inputs[i].options )
                        {
                            if ( ! Object.isUndefined( inputs[i].options, j ) )
                            {
                                inp.append(
                                    '<label><input type="checkbox" name="' + i
                                        + '[]" value="' + j + '"' +
                                        ( inputs[i]["default"].indexOf( j )
                                            >= 0 ? ' checked="checked"' : "" ) +
                                    ">" + inputs[i].options[j] + "</label>"
                                );

                                inp.append( "<br />" );
                            }
                        }
                    }
                }
                else
                {
                    if ( inputs[i].type == "select-one" )
                    {
                        inp = $( '<select id="' + idPrefix + i +
                            '" name="' + i + '" />' );

                        if ( Array.isArray( inputs[i].options ) )
                        {
                            inputs[i].options.forEach( function ( option )
                            {
                                inp.append(
                                    '<option value="' + option + '"' +
                                        ( option == inputs[i]["default"] ?
                                            ' selected="selected"' : "" ) +
                                    ">" + option + "</option>"
                                );
                            } );
                        }
                        else
                        {
                            for ( j in inputs[i].options )
                            {
                                if ( ! Object.isUndefined( inputs[i].options, j ) )
                                {
                                    inp.append(
                                        '<option value="' + j + '"' +
                                            ( j == inputs[i]["default"] ?
                                                ' selected="selected"' : "" ) +
                                        ">" + inputs[i].options[j] + "</option>"
                                    );
                                }
                            }
                        }
                    }
                    else if ( inputs[i].type == "textarea" )
                    {
                        inp = $( '<textarea id="' + idPrefix + i +
                            '" name="' + i + '" />' ).
                                val( inputs[i]["default"] );
                    }
                    else
                    {
                        inp = $( '<input type="' + inputs[i].type + '" id="' +
                            idPrefix + i + '" name="' + i + '"' + (
                                typeof inputs[i].checked !== "undefined" &&
                                inputs[i].checked ? ' checked="checked"' : ''
                            ) + 'value="' +
                                String( inputs[i]["default"] || "" )
                                    .replace( '&', '&amp;' )
                                    .replace( '>', '&gt;' )
                                    .replace( '<', '&lt;' )
                                    .replace( '"', '&quot;' ) + '" />' );
                    }
                }

                form.append( $( "<dd />" ).append( inp ) );
            }
        }

        params.message.append( form );

        if ( typeof params.buttons === "undefined" )
        {
            params.buttons = {};
        }

        if ( typeof params.cancelLabel === "undefined" )
        {
            params.cancelLabel = js.core.translate( "default.cancel" );
        }

        params.buttons[params.cancelLabel] =
            function ()
            {
                $( this ).dialog( "destroy" );

                if ( Function.isFunction( cancelEvent ) )
                {
                    cancelEvent();
                }

                $( this ).remove();
            };

        if ( typeof params.okLabel === "undefined" )
        {
            params.okLabel = js.core.translate( "default.ok" );
        }

        params.buttons[params.okLabel] =
            function ()
            {
                var res = getResult();

                $( this ).dialog( "destroy" );

                if ( Function.isFunction( inputEvent ) )
                {
                    inputEvent( res );
                }

                $( this ).remove();
            };

        delete params.okLabel;
        delete params.cancelLabel;

        params.modal = true;

        dialog( params );

        dialogId = dialog.getLastId();

        $( "#" + dialogId + " :input" ).keypress( function( e ) {
            if ( Number( e.which ) === 13 )
            {
                var res = getResult();

                $( "#" + dialogId ).dialog( "destroy" );

                if ( Function.isFunction( inputEvent ) )
                {
                    inputEvent( res );
                }

                $( this ).remove();
            }
        } ).first().focus();
    };

    /**
     * Destroy last (or the given id's) opened dialog (if any)
     *
     * @param {String} id
     */
    dialog.destroy = function ( id )
    {
        if ( id && global.document.getElementById( id ) )
        {
            $( "#" + id ).dialog( "destroy" );
            $( "#" + id ).remove();
            return id;
        }

        while ( opened.length > 0 )
        {
            id = opened.shift();
            if ( global.document.getElementById( id ) )
            {
                $( "#" + id ).dialog( "destroy" );
                $( "#" + id ).remove();
                return id;
            }
        }

        return null;
    };

    global.Zork.Ui.prototype.dialog = dialog;

} ( window, jQuery, zork ) );
