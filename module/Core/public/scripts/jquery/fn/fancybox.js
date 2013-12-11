( function ( global, $ )
{
    "use strict";

    var version = "1.3.4",
        base    = "/scripts/library/fancybox",
        jsUrl   = base + "/jquery.fancybox-" + version + ".js",
        cssUrl  = base + "/jquery.fancybox-" + version + ".css";

    $( "head" ).append( $( "<script />", {
                    "type": "text/x-dummy",
                    "src": jsUrl
                } ) )
               .append( $( "<link />", {
                    "rel": "stylesheet",
                    "type": "text/css",
                    "media": "screen",
                    "src": cssUrl
                } ) );

    $.ajax( {
        "url": jsUrl,
        "async": false,
        "parse": "script"
    } );

    $.fancybox.init();

} ( window, jQuery ) );
