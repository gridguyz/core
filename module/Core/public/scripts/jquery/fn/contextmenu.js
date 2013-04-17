/**
 * contextmenu
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @package jQuery
 * @subpackage fn
 */
( function ( global, $, js )
{

    /**
     * Context menu event
     * @param {Object} data [optional] event data
     * @param {Function} handler [optional] handler function
     */
    $.fn.contextmenu = function ( data, handler ) {
        if ( Function.isFunction( handler ) )
        {
            return this.on( "contextmenu", data, handler );
        }

        if ( Function.isFunction( data ) )
        {
            return this.on( "contextmenu", data ); // data as handler
        }

        return this.trigger( "contextmenu" );
    };

    var trim = function ( str )
    {
        if ( Object.isUndefined( str ) ) { return ""; }
        return String( str ).replace( /^\s+/, "" ).replace( /\s+$/, "" );
    };

    /**
     * Generate context-menu user-interface
     *
     * @param {$|Array|Function} handler required<br />
     * if Function: will be executed as: handler = handler();<br />
     * if $: will be appended to the contextmenu<br />
     * if Array: will be appended after: $("&lt;li/&gt;").text(key).click(value)
     * @param {Boolean} isElements [optional] default: false
     */
    $.fn.contextui = function ( handler, isElements ) {
     // js.style( "/styles/scripts/menu.css" );

        return this.contextmenu( function ( evt ) {
            var data,
                menu = $( '<ul class="ui-contextmenu ui-menu ui-widget ' +
                    'ui-widget-content ui-corner-all" />' ),
                appendCommand = function ( command ) {
                    command.type = command.type || "command";

                    var itemContainer = $( '<li class="ui-menu-item" />' ).
                            addClass( "ui-menu-item-" + command.type ),
                        item;

                    if ( command.type == "separator" )
                    {
                        item = $( '<hr />' )
                    }
                    else
                    {
                        item = $( '<a class="ui-corner-all" />' )

                        if ( command.type == "checkbox" )
                        {
                            item.append( $( '<input type="checkbox" class="ui-menu-item-icon" />' ).
                                attr( "checked", command.checked ? "checked" : null ) );
                        }
                        else if ( command.type == "radio" )
                        {
                            item.append( $( '<input type="radio" class="ui-menu-item-icon" />' ).
                                attr( "checked", command.checked ? "checked" : null ) );
                        }
                        else if ( command.icon )
                        {
                            item.append( $( '<img class="ui-menu-item-icon" />' ).
                                attr( "src", command.icon ) );
                        }
                        else if ( command.iconClass )
                        {
                            item.append( $( '<span class="ui-menu-item-icon ' +
                                'ui-icon" />' ).addClass( command.iconClass ) );
                        }
                        else
                        {
                            item.append( $( '<span class="ui-menu-item-icon ' +
                                'ui-menu-item-icon-blank" />' ) );
                        }
                        if ( command.hint )
                        {
                            itemContainer.attr( "title", command.hint );
                        }
                        if ( command.key )
                        {
                            itemContainer.attr( "accesskey", command.key );
                        }
                        if ( command.disabled )
                        {
                            itemContainer.addClass( "disabled" );
                        }
                        if ( command.checked )
                        {
                            itemContainer.addClass( "checked" );
                        }
                        if ( command.label )
                        {
                            item.append( command.label );
                        }
                        if ( command.action )
                        {
                            command.context = command.context || item;
                            item.click( function ()
                            {
                                command.action.call( command.context );
                            } );
                        }
                    }

                    item.appendTo( itemContainer );

                    if ( Array.isArray( command.children ) &&
                        command.children.length > 0 )
                    {
                        var children = $( '<ul class="ui-menu ui-widget ' +
                            'ui-widget-content ui-corner-all" />' );
                        command.children.forEach( appendCommand.bind( children ) );

                        children.appendTo( itemContainer );
                    }

                    itemContainer.appendTo( this );
                },
                parseMenu = function ( elems ) {
                    var commands = [];
                    elems.each( function (  )
                    {
                        var self = $( this ), command = {}, t;
                        switch ( this.tagName.toLowerCase() )
                        {
                            case "a":
                                command.type        = "command";
                                command.label       = trim( self.text() );
                                command.hint        = self.attr( "title" );
                                command.icon        = $( "img", self ).
                                                        attr( "src" );
                                command.key         = self.attr( "accesskey" );
                                command.disabled    = false;
                                command.checked     = false;
                                command.action      = self.click;
                                command.context     = self;
                                break;
                            case "li":
                            case "label":
                                t = $( ":input, a", self );
                                if ( t.attr( "type" ) === "radio" ||
                                        t.attr( "type" ) === "checkbox" )
                                { command.type = t; }
                                else if ( t.length === 0 )
                                { command.type = "label"; }
                                else
                                { command.type = "command"; }
                                if ( t.attr( "type" ) === "image" )
                                { command.icon = self.attr( "src" ); }
                                else
                                { command.icon = $( "img", self ).
                                                    attr( "src" ); }
                                command.label       = trim( t.text() ) ||
                                                        trim( self.text() );
                                command.hint        = self.attr( "title" );
                                command.key         = self.attr( "accesskey" );
                                command.disabled    = !! t.attr( "disabled" );
                                command.checked     = !! t.attr( "checked" );
                                command.action      = t.click;
                                command.context     = t;
                                break;
                            case "option":
                                t = self.parent();
                                command.type        = t.attr( "multiple" ) ?
                                                        "checkbox" : "radio";
                                command.label       = self.attr( "label" ) ||
                                                        trim( self.text() );
                                command.hint        = self.attr( "title" );
                                command.icon        = null;
                                command.key         = self.attr( "accesskey" );
                                command.disabled    = !! self.
                                                        attr( "disabled" );
                                command.checked     = false;
                                command.action      = t.click;
                                command.context     = t;
                                break;
                            case "input":
                                t = self.attr( "type" );
                                if ( t === "radio" || t === "checkbox" )
                                {
                                    command.type    = t;
                                    command.label   = self.attr( "title" );
                                }
                                else
                                {
                                    command.type    = "command";
                                    command.label   = self.val();
                                }
                                command.icon        = ( t === "image" ) ?
                                                      self.attr( "src" ) : null;
                                command.hint        = self.attr( "title" );
                                command.key         = self.attr( "accesskey" );
                                command.disabled    = !! self.attr( "disabled" );
                                command.checked     = !! self.attr( "checked" );
                                if ( t === "radio" )
                                {
                                    command.action  = function ()
                                    {
                                        self.attr( "checked", "checked" );
                                        self.change();
                                    };
                                }
                                else if ( t === "checkbox" )
                                {
                                    command.action  = function ()
                                    {
                                        self.attr( "checked", "checked" );
                                        self.change();
                                    };
                                }
                                else
                                {
                                    command.action  = self.click;
                                }
                                command.context     = self;
                                break;
                            case "button":
                                command.type        = "command";
                                command.label       = trim( self.text() );
                                command.hint        = self.attr( "title" );
                                command.icon        = $( "img", self ).
                                                        attr( "src" );
                                command.key         = self.attr( "accesskey" );
                                command.disabled    = !! self.
                                                        attr( "disabled" );
                                command.checked     = false;
                                command.action      = self.click;
                                command.context     = self;
                                break;
                            case "command":
                                t = self.attr( "type" );
                                command.type        = ( t === "radio" ||
                                    t === "checkbox" ) ? t : "command";
                                command.label       = self.attr( "label" );
                                command.hint        = self.attr( "title" );
                                command.icon        = self.attr( "icon" );
                                command.key         = self.attr( "accesskey" );
                                command.disabled    = !! self.attr( "disabled" );
                                command.checked     = !! self.attr( "checked" );
                                if ( t === "radio" )
                                {
                                    command.action  = function ()
                                    {
                                        self.attr( "checked", "checked" );
                                        self.change();
                                    };
                                }
                                else if ( t === "checkbox" )
                                {
                                    command.action  = function ()
                                    {
                                        if ( self.attr( "checked" ) )
                                        {
                                            self.removeAttr( "checked" );
                                        }
                                        else
                                        {
                                            self.attr( "checked", "checked" );
                                        }
                                        self.change();
                                    };
                                }
                                else
                                {
                                    command.action  = self.click;
                                }
                                command.context     = self;
                                break;
                            case "hr":
                            case "br":
                            default:
                                command.type        = "separator";
                                break;
                        }

                        var same = $( self.parent()[0].
                            tagName.toLowerCase(), self );
                        if ( same.length > 0 )
                        {
                            command.children = parseMenu( same.children() );
                        }

                        commands.push( command );
                    } );
                    return commands;
                };

            menu.css( {
                "top"       : evt.pageY,
                "left"      : evt.pageX,
                "display"   : "block",
                "position"  : "absolute",
                "z-index"   : 100
            } );

            menu.appendTo( "body" );
            menu.hide();

            $( global.document ).one( "click contextmenu", function () {
                menu.remove();
                return false;
            } );

            if ( Function.isFunction( handler ) )
            {
                data = handler.call( this, evt );
            }
            else
            {
                data = handler;
            }

            if ( data instanceof $ )
            {
                if ( ! isElements ) { data = data.children(); }
                parseMenu( data ).forEach( appendCommand.bind( menu ) );
            }
            else if ( Array.isArray( data ) )
            {
                data.forEach( appendCommand.bind( menu ) );
            }
            else if ( Object.isObject( data ) )
            {
                var i;
                for ( i in data )
                {
                    if ( typeof data[i] !== "undefined" )
                    {
                        var command = {}, d = data[i];
                        command.label = i;

                        if ( Function.isFunction( d ) )
                        {
                            command.action = d;
                        }
                        else if ( String.isString( d ) )
                        {
                            command.type = d;
                        }
                        else
                        {
                            command.type = "separator";
                        }

                        appendCommand.call( menu, command );
                    }
                }
            }
            else
            {
                parseMenu( $( data ) ).forEach( appendCommand.bind( menu ) );
            }

            if ( evt.pageY + menu.outerHeight() > menu.parent().height() )
            {
                menu.css( "top", menu.parent().height() - menu.outerHeight() );
            }

            if ( evt.pageX + menu.outerWidth() > menu.parent().width() )
            {
                menu.css( "left", menu.parent().width() - menu.outerWidth() );
            }

            $( ".ui-menu-item:not(.disabled) > a", menu ).hover(
                function () {
                    $( this ).
                        addClass( "ui-state-hover" ).
                        attr( "id", "ui-active-menuitem" );
                },
                function () {
                    $( this ).
                        removeClass( "ui-state-hover" ).
                        removeAttr( "id" );
                }
            );

            menu.show( "fast" );

            return false;
        } );
    };

} ( window, jQuery, zork ) );
