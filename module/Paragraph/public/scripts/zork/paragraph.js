/**
 * User interface functionalities
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js, undefined )
{
    "use strict";

    if ( typeof js.paragraph !== "undefined" )
    {
        return;
    }

    /**
     * @class User module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Paragraph = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "paragraph" ];
    };

    global.Zork.prototype.paragraph = new global.Zork.Paragraph();

    /**
     * Paragraph edit/move tolerance
     * @type Number
     */
    global.Zork.Paragraph.prototype.tolerance = 25;

    /**
     * @type $
     */
    var _root     = null,
        cssom     = js.require( "js.cssom" ),
        wizard    = js.require( "js.wizard" ),
        customize = js.require( "js.customize" ),
        message   = js.require( "js.ui.message" ),
        reflect   = js.require( "js.paragraph.reflectCss" ),
        editable  = "[data-paragraph-id]:not(.paragraph-edit-disabled)",
        draggable = '[data-paragraph-properties~="drag"]' + editable,
        droppable = '[data-paragraph-properties~="drop"]' + editable;

    /**
     * Paragraph header instance
     * @type $
     */
    global.Zork.Paragraph.prototype.header = function ()
    {
        return $( js.paragraph.header.template ).hide();
    };

    /**
     * Header template
     * @type String
     */
    global.Zork.Paragraph.prototype.header.template =
        '<div class="paragraph-edit-header">' +
            '<span class="down">&nbsp;</span>' +
            '<span class="up">&nbsp;</span>' +
            '<div class="actions">' +
                '<span class="title">&nbsp;</span>' +
                '<span class="edit" title="' +
                    js.core.translate( "paragraph.editableHeader.edit", js.core.userLocale ) +
                '">&nbsp;</span>' +
                '<span class="append" title="' +
                    js.core.translate( "paragraph.editableHeader.append", js.core.userLocale ) +
                '">&nbsp;</span>' +
                '<span class="delete" title="' +
                    js.core.translate( "paragraph.editableHeader.delete", js.core.userLocale ) +
                '">&nbsp;</span>' +
            '</div>' +
        '</div>';

    /**
     * Header locking
     * @type String
     */
    global.Zork.Paragraph.prototype.header.lock = false;

    /**
     * Edit paragraphs mode
     * @param {HTMLElement} root root paragraph to edit
     * @type undefined
     */
    global.Zork.Paragraph.prototype.edit = function ( root )
    {
        js.style( "/styles/scripts/paragraph.css" );
        _root = root = $( root );

        if ( root.data( "paragraphEditMode" ) === "on" )
        {
            return;
        }

        root.addClass( "paragraph-editmode" )
            .data( "paragraphEditMode", "on" );

        var self            = this,
            locking         = null,
            header          = js.paragraph.header(),
            lastDraw        = null,
            lastDropState   = null,
            leave = function ( event )
            {
                if ( ! self.header.lock || event.force === true )
                {
                    lastDraw = null;

                    root.find( ".paragraph-container.outline" )
                        .andSelf()
                        .removeClass( "outline" );

                    header.hide( "fast", function ()
                    {
                        header.detach();
                    } );
                }
            },
            mmove = function ( event )
            {
                if ( ! self.header.lock || event.force === true )
                {
                    var para = $( event.srcElement || event.target );
                    para = para.closest( editable );

                    if ( para.get( 0 ) === lastDraw )
                    {
                        return;
                    }
                    else
                    {
                        lastDraw = para.get( 0 );
                    }

                    var title = "",
                        types = String( para.data( "paragraphProperties" ) )
                                            .split( /\s+/ );

                    root.find( ".paragraph-container.outline" )
                        .andSelf()
                        .removeClass( "outline" );

                    header.appendTo( para )
                          .show( "fast" );

                    para.addClass( "outline" );

                    if ( ! ( title = para.data( "paragraphName" ) ) )
                    {
                        title = js.core.translate(
                            "paragraph.type." +
                            para.data( "paragraphType" ),
                            js.core.userLocale
                        );
                    }

                    header.find( ".title" )
                          .text( title )
                          .disableSelection();

                    header.find( ".edit" )
                          .css( "display", ~ types.indexOf( "edit" ) ? "" : "none" );

                    header.find( ".delete" )
                          .css( "display", ~ types.indexOf( "delete" ) &&
                                           ! para.find( "[data-paragraph-id]:not([data-paragraph-properties~=delete])" ).length ? "" : "none" );

                    header.find( ".append" )
                          .css( "display", para.data( "paragraphOnlyParentOf" ) ? "" : "none" );
                }
            },
            onUpdate = function ( event, ui )
            {
                if ( ui.sender && ui.sender.length )
                {
                    return;
                }

                var item        = ui.item,
                    finish      = js.core.layer(),
                    related     = item.next( "[data-paragraph-id]" ),
                    position    = "before";

                if ( ! related.length )
                {
                    related     = item.parents( "[data-paragraph-id]:first" );
                    position    = "append";
                }

                js.core.rpc( {
                    "method"    : "Grid\\Paragraph\\Model\\Paragraph\\Rpc::moveNode",
                    "callback"  : function ( result ) {
                        finish();

                        if ( result.success )
                        {
                            if ( item.data( "paragraphType" ) == "column" )
                            {
                                customize.reload();
                            }

                            message( {
                                "title": js.core.translate(
                                        "paragraph.drop.title",
                                        js.core.userLocale
                                    ),
                                "message": js.core.translate(
                                        "paragraph.drop.message",
                                        js.core.userLocale
                                    ).format( js.core.translate(
                                        "paragraph.type." +
                                        item.data( "paragraphType" ),
                                        js.core.userLocale
                                    ) )
                            } );
                        }
                    }
                } ).invoke( {
                    "sourceNode"    : item.data( "paragraphId" ),
                    "relatedNode"   : related.data( "paragraphId" ),
                    "position"      : position
                } );

                self.header.lock = locking;
            },
            add = function ( _, element )
            {
                element = $( element || this );

                if ( element.is( droppable ) )
                {
                    var type            = element.data( "paragraphType" ),
                     // onlyChildOf     = element.data( "paragraphOnlyChildOf" ),
                        onlyParentOf    = element.data( "paragraphOnlyParentOf" ),
                        sortable        = "> .paragraph > .paragraph-children",
                        connect         = "";

                    if ( onlyParentOf != "" )
                    {
                        if ( onlyParentOf != "*" )
                        {
                            connect = '[data-paragraph-only-parent-of="' + onlyParentOf + '"]';
                        }

                        element.find( sortable )
                               .sortable( {
                                    "appendTo": "body",
                                 // "cancel": "> .paragraph-container > .paragraph",
                                    "containment": false, // "window",
                                    "forceHelperSize": true,
                                    "forcePlaceholderSize": true,
                                    "tolerance": "pointer",
                                    "handle": ".paragraph-edit-header .actions .title",
                                    "revert": true,
                                    "connectWith": droppable + connect + sortable,
                                    "placeholder": "paragraph-placeholder",
                                    "items": "> .paragraph-container",
                                    "update": onUpdate,
                                    "start": function ( event, ui ) {
                                        locking = self.header.lock;
                                        self.header.lock = true;

                                        var item        = ui.item,
                                            placeholder = ui.placeholder,
                                            type        = item.data( "paragraphType" );

                                        placeholder.addClass( "paragraph-" + type + "-placeholder" );

                                        if ( type == "column" )
                                        {
                                            placeholder.css( {
                                                "float": "left",
                                                "width": item.css( "width" )
                                            } );
                                        }
                                    },
                                    "stop": function () {
                                        self.header.lock = locking;
                                    }
                                } );
                    }
                }
            };

        header.find( ".up" )
              .click( function ( event ) {
                    var para    = header.parent( draggable ),
                        parent  = para.parents( draggable + ":first" );

                    if ( parent.size() > 0 &&
                         js.core.distance( para, parent ) < self.tolerance )
                    {
                        mmove( {
                            "force": true,
                            "target": parent
                        } );
                    }

                    event.preventDefault();
                } );

        header.find( ".down" )
              .click( function ( event ) {
                    var para    = header.parent( draggable ),
                        child   = para.find( draggable + ":first" );

                    if ( child.size() > 0 &&
                         js.core.distance( para, child ) < self.tolerance )
                    {
                        mmove( {
                            "force": true,
                            "target": child
                        } );
                    }

                    event.preventDefault();
                } );

        header.find( ".title" )
              .click( function ( event ) {
                    self.header.lock = ! self.header.lock;
                    event.preventDefault();
                } );

        header.find( ".edit" )
              .click( function ( event ) {
                    var para = header.parent( draggable + "," + droppable );
                    self.header.lock = false;
                    js.paragraph.dashboard( para );
                    event.preventDefault();
                } );

        header.find( ".delete" )
              .click( function ( event ) {
                    var para = header.parent( draggable + "," + droppable );

                    js.require( "js.ui.dialog" ).confirm( {
                        "message"   : js.core.translate(
                                "default.areYouSure",
                                js.core.userLocale
                            ),
                        "yes"       : function () {
                            var finish = js.core.layer();

                            js.core.rpc( {
                                "method"    : "Grid\\Paragraph\\Model\\Paragraph\\Rpc::deleteNode",
                                "callback"  : function ( result ) {
                                    finish();

                                    if ( result.success )
                                    {
                                        message( {
                                            "title": js.core.translate(
                                                    "paragraph.delete.title",
                                                    js.core.userLocale
                                                ),
                                            "message": js.core.translate(
                                                    "paragraph.delete.message",
                                                    js.core.userLocale
                                                ).format( js.core.translate(
                                                    "paragraph.type." +
                                                    para.data( "paragraphType" )
                                                ) )
                                        } );

                                        para.hide(
                                            "blind",
                                            {
                                                "easing": "easeOutQuart"
                                            },
                                            "fast",
                                            function () {
                                                para.remove();
                                            }
                                        );
                                    }
                                }
                            } ).invoke( {
                                "sourceNode": para.data( "paragraphId" )
                            } );
                        }
                    } );

                    event.preventDefault();
                } );

        header.find( ".append" )
              .click( function () {
                    var para = header.parent( draggable );
                    js.paragraph.append( para );
                } );

        root.find( droppable )
            .andSelf()
            .each( add );

        root.on( "mousemove", mmove )
            .on( "mouseleave", leave )
            .data( "paragraph.edit.add", add )
            .data( "paragraph.edit.reset", function () {
                root.off( "mousemove", mmove );
                root.off( "mouseleave", leave );
                root.find( droppable + ":ui-sortable" )
                    .sortable( "destroy" );
                root.removeData( "paragraph.edit.add" );
                root.removeData( "paragraph.edit.reset" );
            } );
    };

    global.Zork.Paragraph.prototype.edit.isElementConstructor = true;

    /**
     * Set paddings in paragraph
     * @param {HTMLElement} root root paragraph to reset
     * @param {Number} padding padding to set
     * @type undefined
     */
    global.Zork.Paragraph.prototype.padding = function ( root, padding )
    {
        _root   = root = $( root );
        padding = parseInt( padding, 10 );

        this.padding.sheet = this.padding.sheet || cssom.sheet();
        this.padding.sheet
            .rules( ".paragraph-editmode, .paragraph-editmode .paragraph-container" )
            .set( "padding", padding ? String( Number( padding ) ) + "px 0px" : null );
    };

    /**
     * Remove edit & move modes from paragraph
     * @param {HTMLElement} root root paragraph to reset
     * @type undefined
     */
    global.Zork.Paragraph.prototype.reset = function ( root )
    {
        _root = root = $( root );
        this.padding( root );
        var header = $( ".paragraph-edit-header" );

        if ( header && header.length )
        {
            header.hide( "fast" );
        }

        if ( root.data( "paragraphEditMode" ) == "on" )
        {
            root.removeClass( "paragraph-editmode" );
            root.data( "paragraphEditMode", "off" );
            root.data( "paragraph.edit.reset" )();
        }

        root.find( ".paragraph-container.outline" )
            .removeClass( "outline" );

        root.find( ".paragraph-container.ui-state-disabled" )
            .removeClass( "ui-state-disabled" );
    };

    /**
     * Move paragraphs mode
     * @param {HTMLElement} element element to move
     * @type undefined
     */
    global.Zork.Paragraph.prototype.add = function ( element )
    {
        element     = $( element );
        var par     = element.find( editable + ":first" ).detach(),
            edit    = false,
            id      = null,
            method  = null;

        if ( ! Object.isUndefined( _root ) )
        {
            if ( _root.data( "paragraphEditMode" ) == "on" )
            {
                edit = true;
            }
        }

        if ( edit )
        {
            this.reset( _root );
        }

        switch ( true )
        {
            case !! ( id = element.data( "paragraphAddAfter" ) ):
                method = "after";
                break;

            case !! ( id = element.data( "paragraphAddBefore" ) ):
                method = "before";
                break;

            case !! ( id = element.data( "paragraphAddParent" ) ):
                method = "append";
                break;
        }

        if ( id && method )
        {
            $( "[data-paragraph-id=" + id + "]:first" )[ method ]( par );
        }
        else
        {
            js.console.error( "'id' and 'method' cannot be identified" );
        }

        if ( edit )
        {
            this.edit( _root );
        }
    }

    global.Zork.Paragraph.prototype.add.isElementConstructor = true;

    /**
     * Append wizard
     * @param {HTMLElement} container
     * @type undefined
     */
    global.Zork.Paragraph.prototype.append = function ( container )
    {
        js.require( "js.color" );

        container = $( container );

        var id      = container.data( "paragraphId" ),
            child   = container.data( "paragraphOnlyParentOf" );

        if ( id && child )
        {
            wizard( {
                "url"   : "/app/" + js.core.userLocale +
                          "/paragraph/create/" + ( child == "*" ? "" : child ) +
                          "?adminLocale=" + js.core.defaultLocale,
                "params": {
                    "parentId": id
                },
                "cancel": function ( cancel ) {
                    cancel = $( cancel );

                    message( {
                        "title": cancel.attr( "title" ),
                        "message": cancel.text()
                    } );
                },
                "finish": function ( finish ) {
                    finish = $( finish );

                    var layer = js.core.layer(),
                        type  = finish.data( "paragraphType" ),
                        paragraph = {
                            "id": finish.data( "paragraphId" ),
                            "type": type,
                            "root": finish.data( "paragraphRoot" ),
                            "parent": finish.data( "paragraphParent" )
                        };

                    message( {
                        "title": finish.attr( "title" ),
                        "message": finish.text()
                    } );

                    if ( _root )
                    {
                        js.paragraph.reset( _root );
                    }

                    $.ajax( {
                     // "cache": false,
                        "dataType": "text",
                        "error": function ( xhr, status ) {
                            layer();
                            js.console.error( {
                                "xhr": xhr,
                                "status": status
                            } );
                        },
                        "success": function ( data ) {
                            if ( /^columns?$/.test( type ) )
                            {
                                customize.reload();
                            }

                            data = String( data );
                            $( $.parseHTML( String( ( data.match( /<head(\s+[^>]*)?>[\s\S]*<\/head>/i ) || [""] )[0] )
                                    .replace( /<(\/head|head(\s+[^>]*)?)>/, "" )
                                    .replace( /^[^<]/, "" ) ) )
                                .find( "link[rel='stylesheet']" )
                                .each( function () {
                                    var self = $( this );

                                    if ( self.attr( "id" ) == "customizeStyleSheet" )
                                    {
                                        $( "#customizeStyleSheet" ).remove();
                                    }

                                    js.link( self.attr( "href" ), {
                                        "rel": "stylesheet",
                                        "type": self.attr( "type" ),
                                        "media": self.attr( "media" )
                                    } );
                                } );

                            layer();
                            $( ":data(jGrowl.instance)" )
                                .data( "jGrowl.instance" )
                                .shutdown();

                            js.core.parseDocument(
                                $( "body:first" ).html(
                                    String( ( data.match( /<body(\s+[^>]*)?>[\s\S]*<\/body>/i ) || [""] )[0] )
                                          .replace( /<(\/body|body(\s+[^>]*)?)>/, "" )
                                )
                            );

                            var para = $( "#paragraph-" + paragraph.id );

                            if ( para.length )
                            {
                                var offset = para.offset(),
                                    fore = js.color.parse( para.css( "color" ) ).toHsl(),
                                    back = js.color.parse( para.css( "background-color" ) ).toHsl();

                                if ( offset )
                                {
                                    $( "html, body" ).animate( {
                                        "scrollTop": offset.top,
                                        "scrollLeft": offset.left
                                    }, 1000, "easeOutQuart" );
                                }

                                para.css( {
                                        "color"             : fore.l < 0.5 ? "#000000" : "#ffffff",
                                        "backgroundColor"   : back.l < 0.5 ? "#000000" : "#ffffff",
                                        "backgroundImage"   : "none"
                                    } )
                                    .animate( {
                                        "color"             : String( fore ),
                                        "backgroundColor"   : String( back )
                                    }, 2000, "easeOutQuart", function () {
                                        para.css( {
                                            "color"             : "",
                                            "backgroundColor"   : "",
                                            "backgroundImage"   : ""
                                        } );
                                    } );

                            }
                        }
                    } );
                }
            } );
        }
    }

    /**
     * Show dashboard
     * @param {HTMLElement} para
     * @type undefined
     */
    global.Zork.Paragraph.prototype.dashboard = function ( para )
    {
        js.style( "/styles/scripts/paragraph.css" );
        para = $( para );

        var body    = $( "body" ),
            layer   = $( "<div />" ).appendTo( body ),
            element = $( "<div />" ).appendTo( body ),
            display = true,
            id      = para.data( "paragraphId" ),
            name    = para.data( "paragraphName" ),
            type    = para.data( "paragraphType" ),
            url     = "/app/" + js.core.userLocale +
                      "/paragraph/edit/" + id +
                      "?adminLocale=" + js.core.defaultLocale,
            title   = name ? name : js.core.translate(
                            "paragraph.type." + type,
                            js.core.userLocale
                        ),
            form    = $( "<form />" ),
            header  = $( "<div />" ),
            text    = $( "<span />" )
                        .addClass( "text" )
                        .text( title ),
            toggle  = $( "<button />" )
                        .addClass( "toggle" )
                        .button( {
                            "icons": {
                                "primary": "ui-icon-circle-triangle-s"
                            },
                            "text": false
                        } ),
            save    = $( "<button />" )
                        .addClass( "save" )
                        .text( js.core.translate(
                            "default.save", js.core.userLocale
                        ) )
                        .button( {
                            "icons": {
                                "primary": "ui-icon-disk"
                            }
                        } ),
            savenexit = $( "<button />" )
                            .addClass( "savenexit" )
                            .text( js.core.translate(
                                "default.saveAndExit", js.core.userLocale
                            ) )
                            .button( {
                                "icons": {
                                    "primary": "ui-icon-disk",
                                    "secondary": "ui-icon-close"
                                }
                            } ),
            cancel  = $( "<button />" )
                        .addClass( "cancel" )
                        .text( js.core.translate(
                            "default.cancel", js.core.userLocale
                        ) )
                        .button( {
                            "icons": {
                                "secondary": "ui-icon-close"
                            }
                        } ),
            buttons = $( "<div />" )
                        .addClass( "js-dashboard-buttons" )
                        .append( save )
                        .append( savenexit )
                        .append( cancel )
                        .buttonset(),
            resize  = function () {
                element.css( {
                    "width": "",
                    "top": "auto"
                } );

                form.outerHeight(
                    element.height()
                        - header.outerHeight()
                        - buttons.outerHeight()
                );

                $( ".paragraph-layout-container" ).css(
                    "padding-bottom",
                    element.height()
                );
            },
            lastHeight = 200,
            handlers   = {},
            saveAction = function ( update ) {
                if ( update && handlers.update )
                {
                    handlers.update();
                }

                var input = form.find( ":input[name$='Element[name]']" ),
                    name = input.val();

                if ( input.length )
                {
                    para.data( "paragraphName", name );
                }

                if ( name )
                {
                    text.text( title = name );
                }

                $.post( url, form.serialize(), function ( response ) {
                    message( {
                        "title": js.core.translate(
                            "paragraph.save.title", js.core.userLocale
                        ),
                        "message": response
                    } );
                }, "text" );
            },
            exitAction = function ( restore ) {
                if ( restore && handlers.restore )
                {
                    handlers.restore();
                    reflect.destruct( form );
                }

                element.remove();
                layer.remove();
                $( ".paragraph-layout-container" ).css(
                    "padding-bottom",
                    ""
                );
            };

        layer.addClass( "js-dashboard-layer" );
        element.addClass( "js-dashboard ui-widget" )
               .addClass( "ui-widget-content ui-corner-all" )
               .prepend( header.append( text ).append( toggle ) )
               .append( form )
               .append( buttons );

        header.addClass( "js-dashboard-header ui-widget-header ui-corner-right" );

        toggle.click( function () {
            display = ! display;

            if ( display )
            {
                element.resizable( "enable" );

                form.animate( {
                    "opacity": 1,
                    "height": lastHeight
                }, "fast", "swing" );

                toggle.button( "option", "icons", {
                    "primary": "ui-icon-circle-triangle-s"
                } );
            }
            else
            {
                lastHeight = form.height();

                element.resizable( "disable" )
                       .css( "height", "auto" );

                form.animate( {
                    "height": 0,
                    "opacity": 0
                }, "fast", "swing" );

                toggle.button( "option", "icons", {
                    "primary": "ui-icon-circle-triangle-n"
                } );
            }
        } );

        save.click( function () {
            saveAction( true );
        } );

        savenexit.click( function () {
            saveAction( false );
            exitAction( false );
        } );

        cancel.click( function () {
            exitAction( true );
        } );

        element.resizable( {
            "handles": "n",
            "minHeight": 200,
            "maxHeight": 600,
            "resize": resize,
            "stop": resize
        } );

        $( ".paragraph-layout-container" ).css(
            "padding-bottom",
            "600px"
        );

        js.core.loadRawElement( {
            "url": url,
            "target": form,
            "replace": true,
            "complete": function () {
                form = element.find( "form:first" );
                js.require( "js.paragraph.dashboard." + type, function ( t ) {
                    handlers = t( form, para );
                } );
            }
        } );
    };

    global.Zork.Paragraph.prototype.dashboard.isElementConstructor = true;

    global.Zork.Paragraph.prototype.dashboard.version = "1.0";

    global.Zork.Paragraph.prototype.dashboard.modulePrefix =
        [ "zork", "paragraph", "dashboard" ];

    /**
     * Content select form-element
     * @param {HTMLElement} element
     * @type undefined
     */
    global.Zork.Paragraph.prototype.contentSelect = function ( element )
    {
        js.require( "jQuery.fn.autocompleteicon" );
        element = $( element );

        var minLength   = element.data( "jsContentselectMinLength" ) || 1,
            selected    = element.find( ":selected" ),
            input       = $( "<input type='text' />" ),
            change      = function ( _, ui ) {
                var val, lab;

                if ( ui.item )
                {
                    val = ui.item.value;
                    lab = ui.item.label || ui.item.description;
                }
                else
                {
                    val = "";
                    lab = "";
                }

                input.val( lab );
                element.val( val )
                       .trigger( "change" );
            };

        if ( selected.length )
        {
            input.val( selected.text() );
        }

        element.removeAttr( "multiple" )
               .addClass( "ui-helper-hidden" )
               .prop( "multiple", false )
               .after( input );

        input.autocompleteicon( {
            "minLength": minLength,
            "source": function ( request, response ) {
                var result = [],
                    term = request.term
                                  .toLowerCase()
                                  .replace( /^\s+/, "" )
                                  .replace( /\s+$/, "" )
                                  .replace( /\s+/, " " );

                element.find( "option" ).each( function () {
                    var self    = $( this ),
                        val     = self.val(),
                        text    = self.text(),
                        title   = String( self.data( "titleText" ) || "" ),
                        search  = String( text + " " + title )
                                    .toLowerCase()
                                    .replace( /^\s+/, "" )
                                    .replace( /\s+$/, "" )
                                    .replace( /\s+/, " " );

                    if ( ~search.indexOf( term ) )
                    {
                        result.push( {
                            "value": val,
                            "label": text,
                            "icon": self.data( "leadImage" )
                                ? js.core.thumbnail( self.data( "leadImage" ), {
                                      "width": 25,
                                      "height": 25
                                  } )
                                : null,
                            "title": self.data( "created" )
                                ? String( new Date( self.data( "created" ) ) )
                                : "",
                            "description": title
                        } );
                    }
                } );

                response( result );
            },
            "change": change,
            "select": function ( event, ui ) {
                event.preventDefault();
                change.call( this, event, ui );
            }
        } );
    };

    global.Zork.Paragraph.prototype.contentSelect.isElementConstructor = true;

} ( window, jQuery, zork ) );
