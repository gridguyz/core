/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.plupload !== "undefined" )
    {
        return;
    }

    var dir = "/scripts/library/plupload",
        doneTranslated = js.core.translate( "default.done" ),
        uploadTranslated = js.core.translate( "default.upload" );

    js.script( dir + "/js/plupload.full.js" );

    var dialogContainer = null;

    /**
     * PlUpload element
     *
     * @memberOf Zork.Ui
     */
    global.Zork.Ui.prototype.plupload = function ( params )
    {
        params = $.extend( {}, js.ui.plupload.defaultParams, params || {} );

        var dialog   = $( "<div />" ),
            title    = params.title,
            label    = params.label,
            uploaded = params.uploaded,
            plugin   = params.queue ? "pluploadQueue" : "plupload";

        if ( params.queue )
        {
            js.script( dir + "/js/jquery.plupload.queue/jquery.plupload.queue.js" );
            js.style( dir + "/js/jquery.plupload.queue/css/jquery.plupload.queue.css" );
        }
        else
        {
            js.script( dir + "/js/jquery.ui.plupload/jquery.ui.plupload.js" );
            js.style( dir + "/js/jquery.ui.plupload/css/jquery.ui.plupload.css" );
        }

        delete params.queue;
        delete params.title;
        delete params.label;
        delete params.uploaded;

        if ( dialogContainer !== null )
        {
            dialogContainer.remove();
        }

        dialogContainer = dialog
            .dialog( {
                "title"     : title,
                "resizable" : false,
                "modal"     : true,
                "width"     : 800,
                "height"    : 450,
                "buttons"   : [ {
                    "text"  : label,
                    "click" : function () {
                        var uploader = dialog.plupload( "getUploader" ),
                            isDone   = function () {
                                return uploader.files.length <=
                                     ( uploader.total.uploaded +
                                       uploader.total.failed );
                            },
                            done     = function () {
                                var result = [];

                                $.each( uploader.files, function ( _, file ) {
                                    if ( file.status == global.plupload.DONE &&
                                         file.loaded == file.size &&
                                         file.size > 0 &&
                                         file.target_name &&
                                         file.name )
                                    {
                                        result.push( {
                                            "temp": file.target_name,
                                            "name": file.name,
                                            "size": file.size
                                        } );
                                    }
                                } );

                                uploaded( result );
                                dialog.dialog( "destroy" );
                            };

                        if ( ! isDone() )
                        {
                            dialog.nextAll( ".ui-dialog-buttonpane" )
                                  .find( ".ui-button" )
                                  .button( "disable" );

                            uploader.bind( "StateChanged", function() {
                                if ( isDone() )
                                {
                                    done();
                                }
                            } );

                            uploader.start();
                        }
                        else
                        {
                            done();
                        }
                    }
                } ]
            } )
            [plugin]( params );

        if ( js.core.browser.mozilla )
        {
            dialogContainer.find( ".plupload.html5" )
                           .css( {
                                "top": 0,
                                "left": 0,
                                "margin": 0,
                                "padding": 0,
                                "opacity": 0,
                                "fontSize": "72pt",
                                "display": "block",
                                "position": "relative"
                            } );

            dialogContainer.find( ".plupload.html5 :input[type='file']" )
                           .css( {
                                "top": 0,
                                "left": 0,
                                "margin": 0,
                                "padding": 0,
                                "opacity": 0,
                                "fontSize": "72pt",
                                "display": "block",
                                "position": "relative"
                            } );
        }
    };

    global.Zork.Ui.prototype.plupload.defaultParams = {
        "queue"                 : false,
        "label"                 : doneTranslated,
        "title"                 : uploadTranslated,
        "uploaded"              : function () {},
        "runtimes"              : "html5,flash,gears,silverlight,html4",
        "url"                   : "/app/" + js.core.defaultLocale + "/upload-parts",
        "max_file_size"         : "100mb",
        "chunk_size"            : "1mb",
        "unique_names"          : true,
        "resize"                : false,
        "filters"               : [ {
            "title"             : "All files",
            "extensions"        : "*"
        }, {
            "title"             : "Image files",
            "extensions"        : "jpg,gif,png"
        } ],
        "flash_swf_url"         : dir + "/js/plupload.flash.swf",
        "silverlight_xap_url"   : dir + "/js/plupload.silverlight.xap"
    };

} ( window, jQuery, zork ) );
