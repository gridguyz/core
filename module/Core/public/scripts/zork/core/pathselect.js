/**
 * Path selector
 * @package zork
 * @subpackage core
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.core.pathselect !== "undefined" )
    {
        return;
    }

    js.require( "js.ui.dialog" );
    js.require( "js.ui.message" );

    var defaultParams   = {
            "directory" : "",
            "file"      : true
        },
        dialog          = null,
        dialogActions   = null,
        dialogCurPath   = null,
        dialogPreview   = null,
        dialogEntries   = null,
        dialogInput     = null,
        nop             = function () {},
        validName       = function ( name ) {
            return name.replace( /^\.+/, "" )
                       .replace( /"/g, "'" )
                       .replace( /</g, "_" )
                       .replace( />/g, "_" )
                       .replace( /\*/g, "_" )
                       .replace( /\?/g, "_" )
                       .replace( /:/g, "-" )
                       .replace( /\|/g, "-" )
                       .replace( /\\/g, "-" )
                       .replace( /\//g, "-" );
        },
        dirname         = function ( path ) {
            path = path.replace( /\\/g, "/" );

            return ( path.indexOf( "/" ) >= 0 ) ?
                path.replace( /\/[^\/]*\/?$/, "" ) : "";
        },
        uploadsUrl      = js.core.uploadsUrl.replace( /\/+$/, "" ) + "/pages/",
        localize        = function ( path ) {
            var uploads = uploadsUrl.replace( /^\/+/, "" ).replace( /\/+$/, "" );
            path        = path.replace( /^\/+/, "" );

            if ( path.substr( 0, uploads.length ) == uploads )
            {
                path = path.substr( uploads.length ).replace( /^\/+/, "" );
            }

            return path;
        },
        pathIsLocal     = function ( path ) {
            var uploads = uploadsUrl.replace( /^\/+/, "" ).replace( /\/+$/, "" );
            path        = path.replace( /^\/+/, "" );
            return path.substr( 0, uploads.length ) == uploads;
        },
        mimeMain        = function ( mime ) {
            return mime
                ? String( mime )
                    .replace( /\/.*/, "" )
                    .toLowerCase()
                : "dir";
        },
        mimeFull        = function ( mime ) {
            return mime
                ? String( mime )
                    .replace( /[\/\.\+]+/g, "-" )
                    .toLowerCase()
                : "dir";
        },
        rpc             = {
            "pathInfo"  : function ( path, cb ) {
                return js.core.rpc( {
                    "callback"  : js.core.layer( cb ),
                    "method"    : "Grid\\Core\\Model\\FileSystem::pathInfo"
                } ).invoke( {
                    "path": $.isArray( path ) ? path : String( path )
                } );
            },
            "rightInfo"  : function ( path, cb ) {
                return js.core.rpc( {
                    "callback"  : js.core.layer( cb ),
                    "method"    : "Grid\\Core\\Model\\FileSystem::rightInfo"
                } ).invoke( {
                    "path": String( path )
                } );
            },
            "changeMod" : function ( path, mods, cb ) {
                return js.core.rpc( {
                    "callback"  : js.core.layer( cb ),
                    "method"    : "Grid\\Core\\Model\\FileSystem::changeMod"
                } ).invoke( {
                    "path": $.isArray( path ) ? path : String( path ),
                    "mods": Object( mods )
                } );
            },
            "makeDir"   : function ( path, cb ) {
                return js.core.rpc( {
                    "callback"  : js.core.layer( cb ),
                    "method"    : "Grid\\Core\\Model\\FileSystem::makeDir"
                } ).invoke( {
                    "path": $.isArray( path ) ? path : String( path )
                } );
            },
            "remove"    : function ( path, cb ) {
                return js.core.rpc( {
                    "callback"  : js.core.layer( cb ),
                    "method"    : "Grid\\Core\\Model\\FileSystem::remove"
                } ).invoke( {
                    "path": $.isArray( path ) ? path : String( path )
                } );
            },
            "rename"    : function ( path, to, cb ) {
                return js.core.rpc( {
                    "callback"  : js.core.layer( cb ),
                    "method"    : "Grid\\Core\\Model\\FileSystem::rename"
                } ).invoke( {
                    "path": $.isArray( path ) ? path : String( path ),
                    "to"  : $.isArray( to )   ? to   : String( to )
                } );
            },
            "copy"      : function ( path, to, cb ) {
                return js.core.rpc( {
                    "callback"  : js.core.layer( cb ),
                    "method"    : "Grid\\Core\\Model\\FileSystem::copy"
                } ).invoke( {
                    "path": $.isArray( path ) ? path : String( path ),
                    "to"  : $.isArray( to )   ? to   : String( to )
                } );
            },
            "uploaded"  : function ( temp, to, cb ) {
                return js.core.rpc( {
                    "callback"  : js.core.layer( cb ),
                    "method"    : "Grid\\Core\\Model\\FileSystem::uploaded"
                } ).invoke( {
                    "temp": $.isArray( temp ) ? temp : String( temp ),
                    "to"  : $.isArray( to )   ? to   : String( to )
                } );
            }
        },
        Pathselector    = function () {
            var selects = [],
                cancels = [];

            this.select = function ( callback )
            {
                if ( Function.isFunction( callback ) )
                {
                    selects.push( callback );
                }
                else
                {
                    selects.forEach( function ( back ) {
                        back.call( this, callback );
                    }, this );
                }

                return this;
            };

            this.cancel = function ( callback )
            {
                if ( Function.isFunction( callback ) )
                {
                    cancels.push( callback );
                }
                else
                {
                    cancels.forEach( function ( back ) {
                        back.call( this, callback );
                    }, this );
                }

                return this;
            };
        };

    /**
     * Pops up a file-selector layer,
     * where the user can manipulate the uploaded files,
     * have possibility to upload new files, and select one
     *
     * @param {Object} params
     * @param {Function} params.select
     * @param {Function} params.cancel
     * @param {String} params.directory
     * @param {String} params.file default: true
     * @return {Pathselector}
     */
    global.Zork.Core.prototype.pathselect = function ( params )
    {
        js.style( "/styles/scripts/pathselect.css" );
        js.require( "jQuery.fn.contextmenu" );

        params = $.extend( {}, defaultParams, params || {} );

        var // self     = js.core.pathselect,
            selector    = new Pathselector(),
            selectFile  = params.file !== false,
            start       = params.directory || "",
            returnIt    = params["return"] !== false,
            currDir     = "",
            currEnt     = "",
            currInf     = null,
            lastDir     = null,
            lastEnt     = null,
            clipboard   = {
                "cut": false,
                "from": "",
                "entries": []
            },
            actions     = {
                "create-dir": function ( evt ) {
                    js.ui.dialog.prompt( {
                        "title"         : js.core.translate( "pathselect.action.create-dir", js.core.userLocale ),
                        "message"       : js.core.translate( "pathselect.action.create-dir.message", js.core.userLocale ),
                        "defaultValue"  : js.core.translate( "pathselect.action.create-dir.default", js.core.userLocale ),
                        "input"         : function ( value ) {
                            value = validName( value );

                            if ( value )
                            {
                                rpc.makeDir(
                                    currDir + "/" + value,
                                    function ( result ) {
                                        js.ui.message( {
                                            "title"   : js.core.translate( "pathselect.action.create-dir", js.core.userLocale ),
                                            "message" : js.core.translate( "pathselect.action.create-dir." +
                                                        ( result ? "success" : "failed" ), js.core.userLocale )
                                        } );

                                        if ( result )
                                        {
                                            refresh( true );
                                        }
                                    }
                                );
                            }
                        }
                    } );

                    evt.preventDefault();
                    evt.stopPropagation();
                },
                "sep1"  : "|",
                "cut"   : function ( evt ) {
                    clipboard.cut       = true;
                    clipboard.from      = currDir;
                    clipboard.entries   = dialogEntries
                        .find( "> ul > li" )
                        .removeClass( "ui-priority-secondary" )
                        .find( ":checked" )
                        .map( function () {
                            return $( this )
                                .val()
                                .substr( currDir.length )
                                .replace( /^\//, "" );
                        } )
                        .get();

                    dialogEntries
                        .find( ":checked" )
                        .parent( "li" )
                        .addClass( "ui-priority-secondary" );

                    evt.preventDefault();
                    evt.stopPropagation();
                },
                "copy"  : function ( evt ) {
                    clipboard.cut       = false;
                    clipboard.from      = currDir;
                    clipboard.entries   = dialogEntries
                        .find( "> ul > li" )
                        .removeClass( "ui-priority-secondary" )
                        .find( ":checked" )
                        .map( function () {
                            return $( this )
                                .val()
                                .substr( currDir.length )
                                .replace( /^\//, "" );
                        } )
                        .get();

                    evt.preventDefault();
                    evt.stopPropagation();
                },
                "paste" : function ( evt ) {
                    var from = [],
                        to   = [];

                    if ( clipboard.entries.length && clipboard.from != currDir )
                    {
                        $.each( clipboard.entries, function ( index, entry ) {
                            from.push( clipboard.from + "/" + entry );
                            to.push( currDir + "/" + entry );
                        } );

                        rpc[ clipboard.cut ? "rename" : "copy" ](
                            from, to,
                            function ( result ) {
                                var success = 0;

                                if ( $.isArray( result ) )
                                {
                                    $.each( result, function ( index, succ ) {
                                        if ( succ )
                                        {
                                            success++;
                                        }
                                    } );
                                }

                                if ( success == 0 )
                                {
                                    js.ui.message( {
                                        "title"   : js.core.translate( "pathselect.action.paste", js.core.userLocale ),
                                        "message" : js.core.translate( "pathselect.action.paste.failed", js.core.userLocale )
                                    } );
                                }
                                else if ( success == clipboard.entries.length )
                                {
                                    js.ui.message( {
                                        "title"   : js.core.translate( "pathselect.action.paste", js.core.userLocale ),
                                        "message" : js.core.translate( "pathselect.action.paste.success", js.core.userLocale )
                                    } );
                                }
                                else
                                {
                                    js.ui.message( {
                                        "title"   : js.core.translate( "pathselect.action.paste", js.core.userLocale ),
                                        "message" : js.core.translate( "pathselect.action.paste.partial", js.core.userLocale )
                                                           .format( clipboard.entries.length, success )
                                    } );
                                }

                                if ( success > 0 )
                                {
                                    refresh( true );
                                }
                            }
                        );
                    }

                    evt.preventDefault();
                    evt.stopPropagation();
                },
                "sep2"  : "|",
                "rename": function ( evt ) {
                    var selected = dialogEntries
                            .find( ":checked" )
                            .map( function () {
                                return $( this )
                                    .val()
                                    .substr( currDir.length )
                                    .replace( /^\/+/, "" );
                            } )
                            .get();

                    if ( selected.length == 1 )
                    {
                        selected = selected[0];

                        js.ui.dialog.prompt( {
                            "title"         : js.core.translate( "pathselect.action.rename", js.core.userLocale ),
                            "message"       : js.core.translate( "pathselect.action.rename.message", js.core.userLocale ).format( selected ),
                            "defaultValue"  : selected,
                            "input"         : function ( value ) {
                                value = validName( value );

                                if ( value && value != selected )
                                {
                                    rpc.rename(
                                        currDir + "/" + selected,
                                        currDir + "/" + value,
                                        function ( result ) {
                                            js.ui.message( {
                                                "title"   : js.core.translate( "pathselect.action.rename", js.core.userLocale ),
                                                "message" : js.core.translate( "pathselect.action.rename." +
                                                            ( result ? "success" : "failed" ), js.core.userLocale )
                                            } );

                                            if ( result )
                                            {
                                                refresh( true );
                                            }
                                        }
                                    );
                                }
                            }
                        } );
                    }

                    evt.preventDefault();
                    evt.stopPropagation();
                },
                "delete": function ( evt ) {
                    var selected = dialogEntries
                            .find( ":checked" )
                            .map( function () {
                                return $( this )
                                    .val()
                                    .replace( /^\//, "" );
                            } )
                            .get();

                    if ( selected.length > 0 )
                    {
                        js.ui.dialog.confirm( {
                            "title"         : js.core.translate( "pathselect.action.delete", js.core.userLocale ),
                            "message"       : js.core.translate( "pathselect.action.delete.message", js.core.userLocale ).format( selected.length ),
                            "yes"           : function () {
                                rpc.remove( selected, function ( result ) {
                                    var success = 0,
                                        failed  = 0,
                                        i;

                                    for ( i = 0; i < result.length; ++i)
                                    {
                                        if ( result[i] )
                                        {
                                            success++;
                                        }
                                        else
                                        {
                                            failed++;
                                        }
                                    }

                                    if ( failed < 1 )
                                    {
                                        js.ui.message( {
                                            "title"   : js.core.translate( "pathselect.action.delete", js.core.userLocale ),
                                            "message" : js.core.translate( "pathselect.action.delete.success", js.core.userLocale )
                                        } );
                                    }
                                    else if ( success < 1 )
                                    {
                                        js.ui.message( {
                                            "title"   : js.core.translate( "pathselect.action.delete", js.core.userLocale ),
                                            "message" : js.core.translate( "pathselect.action.delete.failed", js.core.userLocale )
                                        } );
                                    }
                                    else
                                    {
                                        js.ui.message( {
                                            "title"   : js.core.translate( "pathselect.action.delete", js.core.userLocale ),
                                            "message" : js.core.translate( "pathselect.action.delete.%s.%s.%s", js.core.userLocale )
                                                          .format( success, failed, success + failed )
                                        } );
                                    }


                                    if ( result )
                                    {
                                        refresh( true );
                                    }
                                } );
                            }
                        } );
                    }

                    evt.preventDefault();
                    evt.stopPropagation();
                }
            },
            jumpTo      = function ( path ) {
                var p = String( path );

                return function ( evt ) {
                    currDir = p;
                    refresh();

                    if ( evt && evt.stopPropagation )
                    {
                        evt.stopPropagation();
                    }
                };
            },
            selectEnt   = function ( path ) {
                var p = String( path );

                return function ( evt ) {
                    currEnt = p;
                    refresh();

                    if ( evt && evt.stopPropagation )
                    {
                        evt.stopPropagation();
                    }
                };
            },
            preview     = function ( info ) {
                dialogPreview
                    .empty()
                    .append(
                        $( "<img />" )
                            .attr( {
                                "alt"   : js.core.translate( "pathselect.type.file", js.core.userLocale ),
                                "src"   : "/images/common/blank.gif",
                                "class" : " type-" + info.type + ( info.mime
                                        ? " mime-" + mimeMain( info.mime ) +
                                          " mime-" + mimeFull( info.mime )
                                        : "" )
                            } )
                    );

                if ( info.mime && info.mime.match(
                    /^image\/(vnd.microsoft.icon|icon?|x-icon?)$/
                ) )
                {
                    dialogPreview
                        .find( "img" )
                        .css( "background-image", info.uri );
                }
                else if ( info.mime && info.mime.match(
                    /^image\/(png|gif|jpeg)$/
                ) )
                {
                    dialogPreview
                        .find( "img" )
                        .css( "background-image", "url(" + js.core.thumbnail( {
                            "url"    : info.uri,
                            "width"  : 100,
                            "height" : 100,
                            "method" : "fit",
                            "mtime"  : info.time
                        } ) + ")" );
                }

                dialogPreview.append(
                    $( "<dl />" )
                        .append( $( "<dt />" ).text(
                            js.core.translate( "pathselect.detail.name", js.core.userLocale )
                        ) )
                        .append( $( "<dd />" ).text(
                            info.name
                        ) )
                        .append( $( "<dt />" ).text(
                            js.core.translate( "pathselect.detail.type", js.core.userLocale )
                        ) )
                        .append( $( "<dd />" ).text( info.mime
                            ? js.core.translate( "mime.type." + info.mime, js.core.userLocale )
                            : js.core.translate( "pathselect.type." + info.type, js.core.userLocale )
                        ) )
                        .append( $( "<dt />" ).text(
                            js.core.translate( "pathselect.detail.size", js.core.userLocale )
                        ) )
                        .append( $( "<dd />" ).text(
                            js.format.size( info.size )
                        ) )
                        .append( $( "<dt />" ).text(
                            js.core.translate( "pathselect.detail.time", js.core.userLocale )
                        ) )
                        .append( $( "<dd />" ).text(
                            js.format.datetime( info.time )
                        ) )
                        .append( $( "<dt />" ).text(
                            js.core.translate( "pathselect.detail.rights", js.core.userLocale )
                        ) )
                        .append(
                            $( "<dd />" )
                                .attr( "title", info.rights.owner )
                                .text(
                                    $.map( info.rights, function ( value, key ) {
                                        return key != "owner" && key != "group" && value
                                            ? js.core.translate( "pathselect.right." + key, js.core.userLocale )
                                            : null;
                                    } ).join( ", " )
                                )
                        )
                );
            },
            refresh     = function ( force ) {
                var tmp = [],
                    nowDir = false,
                    nowEnt = false,
                    enul, path;

             // dialogActions.empty();

                if ( force || currDir !== lastDir )
                {
                    nowDir = true;
                    lastDir = currDir;
                    dialogCurPath.empty();

                    dialogCurPath.append(
                        $( "<ul />" ).append(
                            path = $( '<li />' )
                                .attr( "data-js-pathselect-path", "" )
                                .attr( "data-js-pathselect-type", "root" )
                                .addClass( "js-pathselect-path-root" )
                                .addClass( "js-pathselect-path-dir" )
                                .click( jumpTo( "" ) )
                                .text( js.core.translate(
                                    "pathselect.path.root", js.core.userLocale
                                ) )
                        )
                    );

                    if ( currDir )
                    {
                        $.each( currDir.split( "/" ), function ( _, dir ) {
                            tmp.push( dir );
                            var to = tmp.join( "/" );

                            dialogCurPath
                                .find( "> ul" )
                                .append(
                                    path = $( '<li />' )
                                        .attr( "data-js-pathselect-path", "" )
                                        .attr( "data-js-pathselect-type", "root" )
                                        .addClass( "js-pathselect-path-dir" )
                                        .click( jumpTo( to ) )
                                        .text( dir )
                                );
                        } );
                    }

                    path.addClass( "js-pathselect-path-last" );

                    dialogEntries
                        .find( "> ul:ui-selectable" )
                            .selectable( "destroy" )
                        .end()
                        .empty()
                        .append( enul = $( "<ul />" ) );

                    rpc.pathInfo( currDir, function ( infos ) {

                        currInf = infos;

                        if ( infos && infos.entries && infos.entries.length )
                        {
                            $.each( infos.entries, function ( _, info ) {
                                if ( ! nowEnt && (
                                     currEnt.replace( /^\/+/, "" ) ==
                                        info.path.replace( /^\/+/, "" )
                                ) )
                                {
                                    preview( info );
                                }

                                enul.append(
                                    $( '<li />' )
                                        .attr( "unselectable", "on" )
                                        .attr( "data-js-pathselect-path", info.path )
                                        .attr( "data-js-pathselect-type", info.type )
                                        .addClass( "js-pathselect-entries-entry" )
                                        .addClass( "type-" + info.type )
                                        .addClass( "mime-" + mimeMain( info.mime ) )
                                        .addClass( "mime-" + mimeFull( info.mime ) )
                                        .click( function ( evt ) {
                                            var $ths = $( this );

                                            if ( evt.altKey  ||
                                                 evt.ctrlKey ||
                                                 evt.metaKey ||
                                                 $( evt.target ).is( "input" ) )
                                            {
                                                if ( $ths.is( ".ui-selected" ) )
                                                {
                                                    $ths.removeClass( "ui-selected" )
                                                        .find( "input" )
                                                        .prop( "checked", false );
                                                }
                                                else
                                                {
                                                    $ths.addClass( "ui-selected" )
                                                        .find( "input" )
                                                        .prop( "checked", true );
                                                }
                                            }
                                            else
                                            {
                                                enul.find( ".ui-selected" )
                                                    .removeClass( "ui-selected" )
                                                    .find( "input" )
                                                    .prop( "checked", false );

                                                $ths.addClass( "ui-selected" )
                                                    .find( "input" )
                                                    .prop( "checked", true );
                                            }

                                            evt.stopPropagation();
                                        } )
                                        .dblclick(
                                            info.type == "file"
                                                ? ( selectFile ? selectEnt( info.path ) : nop )
                                                : jumpTo( info.path )
                                        )
                                        .append(
                                            $( "<span />" )
                                                .addClass( "type" )
                                                .attr( "unselectable", "on" )
                                                .attr( "title", info.mime ? js.core.translate(
                                                    "mime.type." + info.mime, js.core.userLocale
                                                ) : "" )
                                                .text( js.core.translate(
                                                    "pathselect.type." + info.type, js.core.userLocale
                                                ) )
                                        )
                                        .append(
                                            $( '<input type="checkbox" />' )
                                                .attr( "title", info.path )
                                                .val( info.path )
                                        )
                                        .append(
                                            $( "<span />" )
                                                .addClass( "name" )
                                                .attr( "unselectable", "on" )
                                                .attr( "title", info.name )
                                                .text( info.name )
                                        )
                                );
                            } );
                        }

                        enul.selectable( {
                            "distance"   : 1,
                            "filter"     : "li",
                            "cancel"     : ":input, option",
                            "selected"   : function ( _, ui ) {
                                $( ui.selected )
                                    .find( "input" )
                                    .prop( "checked", true );
                            },
                            "unselected" : function ( _, ui ) {
                                $( ui.unselected )
                                    .find( "input" )
                                    .prop( "checked", false );
                            }
                        } );

                        enul.find( ".js-pathselect-entries-entry:first" )
                            .addClass( "ui-corner-top" );

                        enul.find( ".js-pathselect-entries-entry:last" )
                            .addClass( "ui-corner-bottom" );

                        enul.find( ".ui-corner-top.ui-corner-bottom" )
                            .removeClass( "ui-corner-top ui-corner-bottom" )
                            .addClass( "ui-corner-all" );

                        dialogActions
                            .find( ".js-pathselect-action-create-dir" )
                            .button( infos && infos.rights && infos.rights.write
                                     ? "enable" : "disable" );

                        dialogActions
                            .find( ".js-pathselect-action-paste" )
                            .button( infos && infos.rights && infos.rights.write
                                     ? "enable" : "disable" );

                        dialog
                            .nextAll( ".ui-dialog-buttonpane" )
                            .find( "button:first" )
                            .button( infos && infos.rights && infos.rights.write
                                     ? "enable" : "disable" );

                        if ( ! selectFile && currDir )
                        {
                            preview( infos );
                        }
                    } );

                    if ( ! selectFile )
                    {
                        if ( currDir )
                        {
                            dialogInput.val( uploadsUrl + currDir );
                        }
                        else
                        {
                            dialogInput.val( "" );
                        }
                    }
                }

                if ( currEnt !== lastEnt )
                {
                    lastEnt = currEnt;

                    if ( selectFile )
                    {
                        if ( currEnt )
                        {
                            dialogInput.val( uploadsUrl + currEnt );

                            if ( ! nowDir )
                            {
                                nowEnt = true;

                                if ( currInf && currInf.entries && currInf.entries.length )
                                {
                                    $.each( currInf.entries, function ( _, info ) {
                                        if ( currEnt.replace( /^\/+/, "" ) == info.path.replace( /^\/+/, "" ) )
                                        {
                                            preview( info );
                                        }
                                    } );
                                }
                            }
                        }
                        else
                        {
                            dialogInput.val( "" );
                        }
                    }
                }
            },
            openDialog  = function () {
                var doneSelected = false,
                    buttons,
                    i, set;

                if ( null === dialog )
                {
                    dialog = $( '<div id="js-pathselect" />' )
                        .addClass( "js-pathselect" )
                        .append( dialogActions = $( '<div class="js-pathselect-actions" />' ) )
                        .append( dialogCurPath = $( '<div class="js-pathselect-path" />'    ) )
                        .append( dialogPreview = $( '<div class="js-pathselect-preview" />' ) )
                        .append( dialogEntries = $( '<div class="js-pathselect-entries" />' ) )
                        .append( dialogInput   = $( '<input class="js-pathselect-input" />' ) );
                }
                else
                {
                    var realDialog = dialog.filter( ":ui-dialog" );

                    if ( realDialog.dialog( "isOpen" ) )
                    {
                        realDialog.dialog( "close" );
                    }

                    dialogPreview.empty();

                    var widget = dialog.parent( ".ui-dialog" );
                    widget.find( "~ .ui-front:last" )
                          .after( widget );
                }

                dialogInput.show().on( "mouseup", function () {
                    this.select();
                } );

                dialogEntries.click( function () {
                    dialogEntries
                        .find( ".ui-selected" )
                        .removeClass( "ui-selected" )
                        .find( "input" )
                        .prop( "checked", false );
                } );

                dialogActions.empty()
                             .append( set = $( '<span class="js-pathselect-action-set" />' ) );

                for ( i in actions )
                {
                    if ( actions[i] == "|" )
                    {
                        set.buttonset();

                        dialogActions
                            .append( '<span class="js-pathselect-separator" />' )
                            .append( set = $( '<span class="js-pathselect-action-set" />' ) );
                    }
                    else
                    {
                        set.append(
                            $( '<button type="button" />' )
                                .addClass( "js-pathselect-action" )
                                .addClass( "js-pathselect-action-" + i )
                                .click( actions[i] )
                                .button( {
                                    "text"  : true,
                                    "label" : js.core.translate(
                                        "pathselect.action." + i, js.core.userLocale
                                    )
                                } )
                        );
                    }
                }

                set.buttonset();

                dialog.dialog( {
                    "autoOpen"  : false,
                    "minWidth"  : 700,
                    "minHeight" : 550,
                    "modal"     : true,
                    "title"     : js.core.translate( "pathselect.dialog.title", js.core.userLocale ),
                    "open"      : function () {
                        dialogEntries.height( dialog.height() - (
                            dialogActions.outerHeight() +
                            dialogCurPath.outerHeight() +
                            dialogInput.outerHeight() + 10
                        ) );
                    },
                    "resize"        : function () {
                        dialogEntries.height( dialog.height() - (
                            dialogActions.outerHeight() +
                            dialogCurPath.outerHeight() +
                            dialogInput.outerHeight() + 10
                        ) );
                    }
                } );

                buttons = [ {
                    "text"  : js.core.translate( "default.upload", js.core.userLocale ),
                    "click" : function () {
                        js.require( "js.ui.plupload" );

                        js.ui.plupload( {
                            "uploaded": function ( files ) {
                                var temp = [],
                                    to   = [];

                                if ( files.length )
                                {
                                    $.each( files, function ( _, file ) {
                                        temp.push( file.temp );
                                        to.push( currDir + "/" + file.name );
                                    } );

                                    rpc.uploaded( temp, to, function ( result ) {
                                        if ( result )
                                        {
                                            refresh( true );
                                        }
                                    } );
                                }
                            }
                        } );
                    }
                } ];

                if ( returnIt )
                {
                    buttons.push( {
                        "text"  : js.core.translate( "default.cancel", js.core.userLocale ),
                        "click" : function () {
                            dialog.dialog( "close" );
                        }
                    } );

                    buttons.push( {
                        "text"  : js.core.translate( "default.select", js.core.userLocale ),
                        "click" : function () {
                            doneSelected = true;
                            selector.select( dialogInput.val() );
                            dialog.dialog( "close" );
                        }
                    } );
                }
                else
                {
                    buttons.push( {
                        "text"  : js.core.translate( "default.done", js.core.userLocale ),
                        "click" : function () {
                            doneSelected = true;
                            dialog.dialog( "close" );
                        }
                    } );
                }

                dialog.off( "dialogclose" )
                      .on( "dialogclose", function () {
                          if ( ! doneSelected ) {
                              selector.cancel( dialogInput.val() );
                          }
                      } )
                      .dialog( "option", "buttons", buttons )
                      .dialog( "open" );

                refresh();

                return selector;
            };

        if ( selectFile )
        {
            if ( typeof params.file == "boolean" ||
                 typeof params.file == "undefined" ||
                 params.file === null )
            {
                params.file = "";
            }
            else
            {
                params.file = String( params.file );
            }

            if ( params.file )
            {
                if ( pathIsLocal( params.file ) )
                {
                    start   = dirname( params.file );
                    currEnt = localize( params.file );
                }
                else
                {
                    start   = "";
                    currEnt = params.file;
                }
            }
        }

        if ( start )
        {
            if ( pathIsLocal( start ) )
            {
                currDir = localize( start );

                if ( ! selectFile )
                {
                    currEnt = currDir;
                }
            }
            else
            {
                currDir = "";

                if ( ! selectFile )
                {
                    currEnt = start;
                }
            }
        }

        if ( Function.isFunction( params.select ) )
        {
            selector.select( params.select );
        }

        if ( Function.isFunction( params.cancel ) )
        {
            selector.cancel( params.cancel );
        }

        return openDialog();
    };

} ( window, jQuery, zork ) );
