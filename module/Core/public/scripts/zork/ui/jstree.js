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

    if ( typeof js.ui.jsTree !== "undefined" )
    {
        return;
    }

    global.Zork.Ui.prototype.jsTree = function ( element )
    {
        element = $( element );

        var moveRpcMethod           = element.data( "jsTreeMoveRpcMethod" ) || null,
            moveRpcParamSource      = element.data( "jsTreeMoveRpcParamSource" ) || "sourceNode",
            moveRpcParamRelated     = element.data( "jsTreeMoveRpcParamRelated" ) || "relatedNode",
            moveRpcParamPosition    = element.data( "jsTreeMoveRpcParamPosition" ) || "position",
            moveRpcResultSuccess    = element.data( "jsTreeMoveRpcResultSuccess" ) || "success";

        js.require( "jQuery.fn.jstree", function ()  {
            $.jstree._themes = "/scripts/library/jstree/themes/";

            element.jstree( {
                        "core" : {
                            "initially_open": element.find( "> ul > li:first" )
                                                     .attr( "id" )
                        },
                        "plugins" : [ "themes", "html_data", "ui", "dnd", "crrm" ]
                    } )
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
                    } );
        } );
    }

    global.Zork.Ui.prototype.jsTree.isElementConstructor = true;

} ( window, jQuery, zork ) );
