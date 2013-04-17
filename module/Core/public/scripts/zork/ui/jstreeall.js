/**
 * jsTree
 *
 * @package zork
 * @subpackage ui
 * @author Szucs Akos
 */
( function ( global, $, js, undefined )
{
    "use strict";

    if ( typeof js.ui.jsTreeAll !== "undefined" )
    {
        return;
    }

    var dialog = false,
        lastEdit = null,
        lastCreate = null,
        framesBound = {},
        load = function ( name, url ) {
            if ( name in global.frames ) {
                var frame = $( "iframe[name='" + name + "']" );
                js.require( "js.form.cancel" );

                frame.attr( "src", "javascript:void(0)" )
                     .contents()
                     .find( "html" )
                     .html( '<img width="16" height="11" src="/images/scripts/loading.gif" ' +
                            'style="top: 50%; left: 50%; margin: -5px -8px; position: absolute;"/>' );

                if ( ! ( name in framesBound ) )
                {
                    framesBound[name] = true;
                    frame.on( "load", function () {
                        js.form.cancel(
                           frame.contents()
                                .find( "form:first" ),
                           {
                               "default.cancel": function () {
                                   frame.attr( "src", "javascript:void(0)" )
                                        .contents()
                                        .find( "html" )
                                        .html( "" )
                               }
                           }
                        );
                    } );
                }

                frame.attr( "src", url );

                if ( dialog ) {
                    $( "#" + dialog ).dialog( "open" );
                }
            } else {
                dialog = js.require( "js.ui.dialog" ).frame( {
                    "name": name == "_blank" ? "" : name,
                    "url": url
                } );
            }
        };

    global.Zork.Ui.prototype.jsTreeAll = function ( element )
    {
        element = $( element );

        var types                   = String( element.data( "jsTreeTypes" ) || "default" ).split( /\s*,\s*/g ),
            treeIdPrefix            = element.data( "jsTreeIdPrefix" ) || "",
            treeIconsUri            = element.data( "jsTreeIconsUri" ) || "",
            treeTypeLabels          = element.data( "jsTreeTypeLabels" ) || "",
            moveRpcMethod           = element.data( "jsTreeMoveRpcMethod" ) || null,
            moveRpcParamSource      = element.data( "jsTreeMoveRpcParamSource" ) || "sourceNode",
            moveRpcParamRelated     = element.data( "jsTreeMoveRpcParamRelated" ) || "relatedNode",
            moveRpcParamPosition    = element.data( "jsTreeMoveRpcParamPosition" ) || "position",
            moveRpcResultSuccess    = element.data( "jsTreeMoveRpcResultSuccess" ) || "success",
            deleteRpcMethod         = element.data( "jsTreeDeleteRpcMethod" ) || null,
            deleteRpcParamSource    = element.data( "jsTreeDeleteRpcParamSource" ) || "sourceNode",
            deleteRpcResultSuccess  = element.data( "jsTreeDeleteRpcResultSuccess" ) || "success",
            renameRpcMethod         = element.data( "jsTreeRenameRpcMethod" ) || null,
            renameRpcParamSource    = element.data( "jsTreeRenameRpcParamSource" ) || "sourceNode",
            renameRpcParamLabel     = element.data( "jsTreeRenameRpcParamLabel" ) || "label",
            renameRpcResultSuccess  = element.data( "jsTreeRenameRpcResultSuccess" ) || "success",
            createRootUri           = element.data( "jsTreeCreateRootUri" ) || "",
            createFormUri           = element.data( "jsTreeCreateFormUri" ) || "",
            createFormTarget        = element.data( "jsTreeCreateFormTarget" ) || "_blank",
            editFormUri             = element.data( "jsTreeEditFormUri" ) || "",
            editFormTarget          = element.data( "jsTreeEditFormTarget" ) || "_blank",

            /**
             * Create node function
             */
            createNode = function ( name, parentId, id, type ) {
                element.jstree(
                    "create",
                    parentId ? "#" + parentId : element,
                    "last",
                    {
                        "attr": {
                            "id": treeIdPrefix + id,
                            "data-js-tree-id": id,
                            "data-js-tree-type": type
                        },
                        "data": name
                    },
                    null,
                    true
                );
            };

        js.require( "jQuery.fn.jstree", function () {
            $.jstree._themes = "/scripts/library/jstree/themes/";

            var context     = {
                    "ccp": false,
                    "create": {
                        "label": js.core.translate( "default.new" ),
                        "submenu": {},
                        "action": function () {}
                    },
                    "rename": {
                        "label": js.core.translate( "default.rename" ),
                        "action": function ( obj ) {
                            element.jstree( "rename", obj );
                        }
                    },
                    "remove": {
                        "label": js.core.translate( "default.delete" ),
                        "action": function ( obj ) {
                            js.require( "js.ui.dialog" ).confirm( {
                                "message": js.core.translate( "default.areYouSure" ),
                                "yes": function () {
                                        var params = {};

                                        params[deleteRpcParamSource] = obj.data( "jsTreeId" );

                                        js.core.rpc( {
                                            "method"    : deleteRpcMethod,
                                            "callback"  : function ( result ) {
                                                if ( result && ! result[deleteRpcResultSuccess] )
                                                {
                                                    js.require( "js.ui.dialog" ).alert( {
                                                        "message"       : result.message || js.core.translate( "default.delete.failed" ),
                                                        "title"         : result.title   || js.core.translate( "default.delete" ),
                                                        "dialogClass"   : "ui-dialog-errorMessage"
                                                    } );
                                                }
                                                else if ( result )
                                                {
                                                    element.jstree( "delete_node", obj );

                                                    if ( result.message )
                                                    {
                                                        js.require( "js.ui.message" )( {
                                                            "message"       : result.message,
                                                            "title"         : result.title || js.core.translate( "default.info" ),
                                                            "dialogClass"   : "ui-dialog-infoMessage"
                                                        } );
                                                    }
                                                }
                                            }
                                        } ).invoke( params );
                                } // ,
                             // "no": function () {}
                            } );
                        }
                    }
                },
                typeData    = {};

            $.each( types, function ( _, type ) {
                typeData[type] = {
                    "valid_children": "all"
                };

                if ( treeIconsUri ) {
                    typeData[type].icon = {
                        "image": treeIconsUri.replace( /%type%/g, type )
                    };
                }

                context.create.submenu[type] = {
                    "label": js.core.translate( treeTypeLabels.replace( /%type%/g, type ) ),
                    "action": function ( obj ) {
                        var id              = obj.data( "jsTreeId" ),
                            selectedNodeId  = obj.attr( "id" );

                        if ( id ) {
                            load(
                                createFormTarget,
                                createFormUri.replace( /%parentId%/g, id )
                                             .replace( /%type%/g, type )
                            );

                            lastEdit   = null;
                            lastCreate = function ( params ) {
                                createNode(
                                    params.label,
                                    selectedNodeId,
                                    params.id,
                                    params.type
                                );
                            };
                        }
                    }
                };
            } );

            element.jstree( {
                "plugins" : [
                    "themes",
                    "html_data",
                    "ui",
                    "dnd",
                    "crrm",
                    "contextmenu",
                    "types"
                ],
                "core" : {
                    "initially_open": element.find( "> ul > li:first" )
                                             .attr( "id" )
                },
                "contextmenu": {
                    "items": context
                },
                "types": {
                    "type_attr": "data-js-tree-type",
                    "types": typeData
                },
                "ui": {
                    "select_limit": 1/*,
                    "select_range_modifier": false,
                    "select_multiple_modifier" : false*/
                }
            } );

            /**
             * Select with dblclick
             */
            element.on( "dblclick.jstree", function ( e ) {
                        var node = $( e.target ).parent( "li" ),
                            id   = node.data( "jsTreeId" ),
                            type = node.data( "jsTreetype" );

                        if ( id ) {
                            load(
                                editFormTarget,
                                editFormUri.replace( /%nodeId%/g, id )
                                           .replace( /%type%/g, type )
                            );

                            lastEdit = function ( params ) {
                                element.jstree(
                                    "rename_node",
                                    node,
                                    params.label
                                );
                            };
                        }
                    } )
                    /**
                     * Drag and Drop, move node event
                     */
                   .on( "move_node.jstree", function ( e, data ) {
                        var params = {};

                        params[moveRpcParamSource]      = data.rslt.o.data( "jsTreeId" );
                        params[moveRpcParamRelated]     = data.rslt.r.data( "jsTreeId" );
                        params[moveRpcParamPosition]    = data.rslt.p;

                        js.core.rpc( {
                            "method"    : moveRpcMethod,
                            "callback"  : function ( result ) {
                                if ( result && ! result[moveRpcResultSuccess] )
                                {
                                    js.require( "js.ui.dialog" ).alert( {
                                        "message"       : result.message || js.core.translate( "default.move.failed" ),
                                        "title"         : result.title   || js.core.translate( "default.error" ),
                                        "dialogClass"   : "ui-dialog-errorMessage"
                                    } );
                                }
                                else if ( result && result.message )
                                {
                                    js.require( "js.ui.dialog" ).alert( {
                                        "message"       : result.message,
                                        "title"         : result.title   || js.core.translate( "default.info" ),
                                        "dialogClass"   : "ui-dialog-infoMessage"
                                    } );
                                }
                            }
                        } ).invoke( params );
                    } )
                    /**
                     * Rename node event
                     */
                   .on( "rename.jstree", function ( e, data ) {
                        var params = {};

                        if ( data.rslt.old_name !== data.rslt.new_name )
                        {
                            params[renameRpcParamSource]    = data.rslt.obj.data( "jsTreeId" );
                            params[renameRpcParamLabel]     = data.rslt.new_name;

                            js.core.rpc( {
                                "method"    : renameRpcMethod,
                                "callback"  : function ( result ) {
                                    if ( result && ! result[renameRpcResultSuccess] )
                                    {
                                        js.require( "js.ui.dialog" ).alert( {
                                            "message"       : result.message || js.core.translate( "default.rename.failed" ),
                                            "title"         : result.title   || js.core.translate( "default.error" ),
                                            "dialogClass"   : "ui-dialog-errorMessage"
                                        } );
                                    }
                                    else if ( result && result.message )
                                    {
                                        js.require( "js.ui.dialog" ).alert( {
                                            "message"       : result.message,
                                            "title"         : result.title   || js.core.translate( "default.info" ),
                                            "dialogClass"   : "ui-dialog-infoMessage"
                                        } );
                                    }
                                }
                            } ).invoke( params );
                        }
                    } );

            if ( element.data( "jsTreeButtonbarEdit" ) )
            {
                var edit = $( "<div />" ).addClass( "js-tree-buttonbar js-tree-editset" );

                edit.append(
                        $( '<button type="button" />' )
                            .button( {
                                "text"      : true,
                                "disabled"  : true,
                                "label"     : js.core.translate( "default.edit" ),
                                "icons"     : {
                                    "primary"   : "ui-icon-gear",
                                    "secondary" : null
                                }
                            } )
                    )
                    .append(
                        $( '<button type="button" />' )
                            .button( {
                                "text"  : true,
                                "label" : js.core.translate( "default.rename" ),
                                "icons" : {
                                    "primary"   : "ui-icon-pencil",
                                    "secondary" : null
                                }
                            } )
                            .click( function () {
                                var selected = element.jstree( "get_selected" );

                                if ( selected && selected.length ) {
                                    element.jstree( "rename", selected );
                                }
                            } )
                    )
                    .append(
                        $( '<button type="button" />' )
                            .button( {
                                "text"  : true,
                                "label" : js.core.translate( "default.edit" ),
                                "icons" : {
                                    "primary"   : "ui-icon-wrench",
                                    "secondary" : null
                                }
                            } )
                            .click( function () {
                                var selected = element.jstree( "get_selected" ),
                                    id       = selected && selected.data( "jsTreeId" ),
                                    type     = selected && selected.data( "jsTreetype" );

                                if ( selected && selected.length ) {
                                    load(
                                        editFormTarget,
                                        editFormUri.replace( /%nodeId%/g, id )
                                                   .replace( /%type%/g, type )
                                    );

                                    lastEdit = function ( params ) {
                                        element.jstree(
                                            "rename_node",
                                            selected,
                                            params.label
                                        );
                                    };
                                }
                            } )
                    )
                    .append(
                        $( '<button type="button" />' )
                            .button( {
                                "text"  : true,
                                "label" : js.core.translate( "default.delete" ),
                                "icons" : {
                                    "primary"   : "ui-icon-trash",
                                    "secondary" : null
                                }
                            } )
                            .click( function () {
                                var selected = element.jstree( "get_selected" ),
                                    id       = selected && selected.data( "jsTreeId" );

                                if ( selected && selected.length ) {
                                    js.require( "js.ui.dialog" ).confirm( {
                                        "message": js.core.translate( "default.areYouSure" ),
                                        "yes": function () {
                                            var params = {};

                                            params[deleteRpcParamSource] = id;

                                            js.core.rpc( {
                                                "method"    : deleteRpcMethod,
                                                "callback"  : function ( result ) {
                                                    if ( result && ! result[deleteRpcResultSuccess] )
                                                    {
                                                        js.require( "js.ui.dialog" ).alert( {
                                                            "message"       : result.message || js.core.translate( "default.delete.failed" ),
                                                            "title"         : result.title   || js.core.translate( "default.delete" ),
                                                            "dialogClass"   : "ui-dialog-errorMessage"
                                                        } );
                                                    }
                                                    else if ( result )
                                                    {
                                                        element.jstree( "delete_node", selected );

                                                        if ( result.message )
                                                        {
                                                            js.require( "js.ui.message" )( {
                                                                "message"       : result.message,
                                                                "title"         : result.title || js.core.translate( "default.info" ),
                                                                "dialogClass"   : "ui-dialog-infoMessage"
                                                            } );
                                                        }
                                                    }
                                                }
                                            } ).invoke( params );
                                        }
                                    } );
                                }
                            } )
                        );

                element.before( edit.buttonset().tooltip( {
                    "items": ".ui-button:not(:disabled)",
                    "position": {
                        "my": "left+10 center",
                        "at": "right center"
                    },
                    "content": function () {
                        return $( this ).find( ".ui-button-text" ).text();
                    }
                } ) );

                edit.find( ".ui-button:not(:disabled)" )
                    .hide();

                element.on( "deselect_node.jstree", function ( e ) {
                    edit.find( ".ui-button:not(:disabled)" )
                        .hide();
                } );

                element.on( "select_node.jstree", function ( e ) {
                    edit.find( ".ui-button:not(:disabled)" )
                        .show();
                }  );
            }

            if ( element.data( "jsTreeButtonbarCreate" ) )
            {
                var create = $( "<div />" ).addClass( "js-tree-buttonbar js-tree-createset" );

                create.append(
                    $( '<button type="button" />' )
                        .button( {
                            "text"      : true,
                            "disabled"  : true,
                            "label"     : js.core.translate( "default.new" ),
                            "icons"     : {
                                "primary"   : "ui-icon-plus",
                                "secondary" : null
                            }
                        } )
                );

                $.each( types, function ( _, type ) {
                    create.append(
                        $( '<button type="button" />' )
                            .button( {
                                "text"  : true,
                                "label" : js.core.translate( treeTypeLabels.replace( /%type%/g, type ) ),
                                "icons" : {
                                    "primary"   : "js-tree-" + type,
                                    "secondary" : null
                                }
                            } )
                            .click( function () {
                                var selected        = element.jstree( "get_selected" ),
                                    id              = selected && selected.data( "jsTreeId" ),
                                    selectedNodeId  = selected && selected.attr( "id" ),
                                    url             = selected && selected.length
                                                    ? createFormUri.replace( /%parentId%/g, id )
                                                    : createRootUri;

                                load(
                                    createFormTarget,
                                    url.replace( /%type%/g, type )
                                );

                                lastEdit   = null;
                                lastCreate = function ( params ) {
                                    createNode(
                                        params.label,
                                        selectedNodeId,
                                        params.id,
                                        params.type
                                    );
                                };
                            } )
                    );
                } );

                element.before( create.buttonset().tooltip( {
                    "items": ".ui-button:not(:disabled)",
                    "position": {
                        "my": "left+10 center",
                        "at": "right center"
                    },
                    "content": function () {
                        return $( this ).find( ".ui-button-text" ).text();
                    }
                } ) );
            }
        } );
    };

    global.Zork.Ui.prototype.jsTreeAll.isElementConstructor = true;

    global.Zork.Ui.prototype.jsTreeAll._lastCreate = function ( params ) {
        if ( lastCreate ) {
            if ( params.id && params.label ) {
                lastCreate( params );
            }

            lastCreate = null;
        }
    };

    global.Zork.Ui.prototype.jsTreeAll._lastEdit = function ( params ) {
        if ( lastEdit ) {
            if ( params.id && params.label ) {
                lastEdit( params );
            }
        }
    };

    global.Zork.Ui.prototype.jsTreeAll.lastCreate = function ( params ) {
        js.caller.js.ui.jsTreeAll._lastCreate( params );
    };

    global.Zork.Ui.prototype.jsTreeAll.lastEdit = function ( params ) {
        js.caller.js.ui.jsTreeAll._lastEdit( params );
    };

    global.Zork.Ui.prototype.jsTreeAll.command = function ( element ) {
        element = $( element );
        js.ui.jsTreeAll[ element.data( "jsTreeCommand" ) ](
            element.data( "jsTreeParams" )
        );
    };

    global.Zork.Ui.prototype.jsTreeAll.command.isElementConstructor = true;

} ( window, jQuery, zork ) );
