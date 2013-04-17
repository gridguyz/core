( function ( global, $, js )
{
    "use strict";

    var cs = global.document.createElement( "canvas" );

    if ( typeof cs.getContext !== "undefined" )
    {
        global.FlashCanvasOptions = global.FlashCanvasOptions || {};
        global.FlashCanvasOptions.swfPath = "/scripts/library/flashcanvas/";

        var flashCanvasUrl =
            global.FlashCanvasOptions.swfPath + "flashcanvas.js";

        $( "head" ).append( $( "<script />", {
            "type": "text/x-dummy",
            "src": flashCanvasUrl
        } ) );

        global.FlashCanvasOptions = global.FlashCanvasOptions || {};
        global.FlashCanvasOptions.swfPath = "/scripts/library/flashcanvas/";

        js.script( flashCanvasUrl );
    }

    /**
     * Initialize a canvas and return its context (or false on failure)
     * @param {String} context default: "2d"
     * @type CanvasRenderingContext
     */
    $.fn.canvas = function ( context )
    {
        context = context || "2d";
        var element = this[0];

        if ( element.tagName.toLowerCase() !== "canvas" )
        {
            element = this.find( "canvas:first" )[0];
            if ( ! element )
            {
                element = global.document.createElement( "canvas" );
                this.empty().append( element );
            }
        }

        if ( typeof FlashCanvas !== "undefined" )
        {
            if ( ! this.data( "jsCanvasInited" ) )
            {
                var width = this.attr( "width" ),
                    height = this.attr( "height" );
                if ( width ) { this.css( "width", width + "px" ); }
                if ( height ) { this.css( "height", height + "px" ); }
                FlashCanvas.initElement( element );
                this.data( "jsCanvasInited", true );
            }

            if ( context !== "2d" )
            {
                return false;
            }
        }

        try
        {
            return element.getContext( context );
        }
        catch ( error )
        {
            if ( typeof $.console !== "undefined" )
            {
                $.console.error( error );
            }
        }

        return false;
    };

} ( window, jQuery, zork ) );
