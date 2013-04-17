( function ( global, $ )
{
    "use strict";

    if ( typeof global.WYMeditor !== "undefined" )
    {
        return;
    }

    global.WYMeditor = {};

    var wymBase = "/scripts/library/wymeditor/",
        wymUrl = wymBase + "jquery.wymeditor.js",
        wymPlugins = [
            "hovertools", "resizable",                              // given plugins
            "alignment", "colors", "horizontal", "extendedDialogs"  // own plugins
        ];

    $( "head" ).append( $( "<script />" ).attr( {
        "type": "text/x-dummy",
        "src": wymUrl
    } ) );

    $.ajax( {
        "url": wymUrl,
        "async": false,
        "parse": "script"
    } );

    wymPlugins.forEach( function ( plugin ) {
        $.ajax( {
            "url": wymBase + "plugins/" + plugin +
                "/jquery.wymeditor." + plugin + ".js",
            "async": false,
            "parse": "script"
        } );
    } );

} ( window, jQuery ) );
