( function ( global, $ )
{
    "use strict";

    var jsTreeUrl = "/scripts/library/jstree/jquery.jstree.js";

    $( "head" ).append( $( "<script />", {
        "type": "text/x-dummy",
        "src": jsTreeUrl
    } ) );

    $.ajax( {
        "url": jsTreeUrl,
        "async": false,
        "parse": "script"
    } );

} ( window, jQuery ) );
