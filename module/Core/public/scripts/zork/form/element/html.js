/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    // "use strict";

    if ( typeof js.form.element.html !== "undefined" )
    {
        return;
    }

    var debug       = true,
        version     = "3.5.4.1",
        urlBase     = "/scripts/library/tiny_mce-" + version,
        script      = urlBase + "/tiny_mce" + ( debug ? "_src" : "" ) + ".js",
        language    = String( js.core.defaultLocale || "en" ).substr( 0, 2 ),
        paramOnElem = {
            "jsHtmlSkin"            : "skin",
            "jsHtmlTheme"           : "theme",
            "jsHtmlPlugins"         : "plugins",
            "jsHtmlButtonSet"       : "buttonSet",
            "jsHtmlResizing"        : "theme_advanced_resizing",
            "jsHtmlResizeHorizontal": "theme_advanced_resize_horizontal"
        };

    /**
     * Html form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.html = function ( element, params )
    {
        var i, k;

        if ( typeof global.tinyMCE == "undefined" )
        {
            js.script( urlBase + "/jquery.tinymce.js" );
        }

        element = $( element );

        if ( /^\s*$/.test( element.val() ) )
        {
            element.val( "<p>&nbsp;</p>" );
        }

        var p = $.extend( {}, js.form.element.html.defaultSettings );

        $.each( paramOnElem, function ( attr, key ) {
            var val = element.data( attr );

            if ( ! Object.isUndefined( val ) )
            {
                switch ( val )
                {
                    case "true":
                        p[key] = true;
                        break;

                    case "false":
                        p[key] = false;
                        break;

                    default:
                        p[key] = val;
                        break;
                }
            }
        } );

        params = $.extend( p, params || {} );

        if ( "plugins" in params )
        {
            if ( params.plugins in js.form.element.html.pluginAliases )
            {
                params.plugins = js.form.element.html.pluginAliases[params.plugins];
            }

            if ( Array.isArray( params.plugins ) )
            {
                params.plugins = params.plugins.join( "," );
            }
        }

        if ( "buttonSet" in params &&
             params.buttonSet in js.form.element.html.buttonSets )
        {
            params = $.extend(
                params,
                js.form.element.html.buttonSets[params.buttonSet]
            );

            delete params.buttonSet;
        }

        for ( i = 1; ( ( k = "theme_advanced_buttons" ) + i ) in params; ++i )
        {
            if ( Array.isArray( params[k] ) )
            {
                params[k] = params[k].join( "," );
            }
        }

        $.each( params, function ( key, value ) {
            if ( Function.isFunction( value ) && value.elementBound )
            {
                params[key] = value( element );
            }
        } );

        element.outerWidth( element.parent().width() )
               .tinymce( params );
    };

    global.Zork.Form.Element.prototype.html.isElementConstructor = true;

    /**
     * Default settings for wym constructor
     * @type Object
     */
    global.Zork.Form.Element.prototype.html.defaultSettings = {
        "script_url"                        : script,
        "content_css"                       : "/styles/defaults.css",
        "language"                          : language,
        "theme"                             : "advanced",
        "skin"                              : "default",
        "plugins"                           : "advanced",
        "relative_urls"                     : true,
        "convert_urls"                      : false,
        "document_base_url"                 : global.location.protocol +
                                              "//" + global.location.host,
        "theme_advanced_toolbar_location"   : "top",
        "theme_advanced_toolbar_align"      : "left",
        "theme_advanced_statusbar_location" : "bottom",
        "theme_advanced_resizing"           : true,
        "theme_advanced_resize_horizontal"  : false,
        "buttonSet"                         : "advanced",
        "setup"                             : function ( editor ) {
            editor.onLoadContent.add( function( editor ) {
                setTimeout( function () {
                    $( editor.contentAreaContainer ).find( "iframe" )
                                                    .trigger( "load" );
                }, 1 );
            } );
        },
        "onchange_callback"                 : $.extend( function ( element ) {
            return function ( instance ) {
                element.trigger( "change" );
            };
        }, {
            "elementBound": true
        } ),
        "file_browser_callback"             : function ( field, url, type, win ) {
            var input = $( "form [name='" + field + "']", win.document ),
                val   = String( url ? url : input.val() )
                            .replace( /^https?:\/\/[^\/]/, "" );

            js.caller.zork.require( "js.core.pathselect", function ( pathselect ) {
                pathselect( {
                    "directory" : "",
                    "file"      : val,
                    "select"    : function ( changed ) {
                        input.val( changed )
                             .trigger( "change" );

                        if ( typeof win.ImageDialog != "undefined" )
                        {
                            if ( win.ImageDialog.getImageData )
                            {
                                win.ImageDialog.getImageData();
                            }

                            if ( win.ImageDialog.showPreviewImage )
                            {
                                win.ImageDialog.showPreviewImage( changed );
                            }
                        }
                    }
                } );
            } );
        }
    };

    global.Zork.Form.Element.prototype.html.buttonSets = {
        "simple": {
            "theme_advanced_buttons1"           : [
                "bold", "italic", "underline", "strikethrough", "|",
                "justifyleft", "justifycenter", "justifyright", "justifyfull", "|",
                "ltr", "rtl", "|",
                "styleselect", "formatselect", "fontselect", "|",
                "link", "unlink", "|",
                "abbr", "sub", "sup", "|",
                "code"
            ],
            "theme_advanced_buttons2"           : false,
            "theme_advanced_buttons3"           : false,
            "theme_advanced_buttons4"           : false
        },
        "advanced": {
            "theme_advanced_buttons1"           : [
                "bold", "italic", "underline", "strikethrough", "|",
                "justifyleft", "justifycenter", "justifyright", "justifyfull", "|",
                "ltr", "rtl", "|",
                "styleselect", "formatselect", "fontselect", "fontsizeselect", "|",
                "cut", "copy", "paste", "pastetext", "pasteword", "template", "|",
                "search", "replace", "|",
                "print", "fullscreen", "preview"
            ],
            "theme_advanced_buttons2"           : [
                "bullist", "numlist", "|",
                "outdent", "indent", "blockquote", "|",
                "undo", "redo", "|",
                "link", "unlink", "anchor", "image", "cleanup", "code", "|",
                "forecolor", "backcolor", "|",
                "hr", "removeformat", "visualaid", "|",
                "abbr", "sub", "sup", "|",
                "charmap", "emotions", "iespell", "media", "advhr", "|",
                "tablecontrols"
            ],
            "theme_advanced_buttons3"           : false,
            /* "insertlayer", "moveforward", "movebackward", "absolute", "|",
                "styleprops", "cite", "acronym", "del", "ins", "|",
                "attribs", "nonbreaking" */
            "theme_advanced_buttons4"           : false
        }
    };

    global.Zork.Form.Element.prototype.html.pluginAliases = {
        "simple": [
            "style", "save", "advlink", "iespell",
            "jqueryinlinepopups", "preview", "media",
            "contextmenu", "paste", "directionality",
            "visualchars", "nonbreaking", "xhtmlxtras"
        ],
        "advanced": [
            "pagebreak", "style", "layer", "table", "save",
            "advhr", "advimage", "advlink", "emotions",
            "iespell", "jqueryinlinepopups", "insertdatetime",
            "preview", "media", "searchreplace", "print",
            "contextmenu", "paste", "directionality",
            "fullscreen", "noneditable", "visualchars",
            "nonbreaking", "xhtmlxtras", "template"
        ]
    };

} ( window, jQuery, zork ) );
