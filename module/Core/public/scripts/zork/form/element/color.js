/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.color !== "undefined" )
    {
        return;
    }

    /**
     * Color form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.color = function ( element )
    {
        js.style( "/styles/scripts/colorpicker.css" );
        js.require( "jQuery.fn.ColorPicker", function () {
            element = $( element );

            var color = element.val(),
                picker = true,
                invert = function ( hex )
                {
                    var r = /#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})/,
                        m = hex.match( r );

                    if ( m && m.length )
                    {
                        m.shift();
                        m = m.map( function ( c ) { return parseInt( c, 16 ); } );
                        var themin = Math.min.apply( Math, m ),
                            themax = Math.max.apply( Math, m );

                        return ( ( themin + themax ) / 511 ) > 0.5 ?
                            "#000000" : "#ffffff";
                    }

                    return "#000000";
                };

            element.css( {
                "filter": "",
                "background": color,
                "color": invert( color )
            } );

            if ( element.attr( "type" ) === "color" && js.core.browser.opera )
            {
                picker = false;
            }
            else
            {
                element.ColorPicker( {
                    "color": color,
                    "onSubmit": function ( hsb, hex, rgb, el )
                    {
                        element.val( "#" + hex )
                               .trigger( "change" )
                               .ColorPickerHide();
                    },
                 /* "onChange": function ( hsb, hex, rgb, el )
                    {
                        element.val( "#" + hex )
                               .trigger( "change" );
                    }, */
                    "onBeforeShow": function ()
                    {
                        $( this ).ColorPickerSetColor( this.value.replace( /^#/, "" ) );
                    },
                    "onShow": function ( pkr )
                    {
                        $( pkr ).fadeIn( 500 )
                                .find( "*:not(:input):not(:button):not(:submit)" )
                                .andSelf()
                                .prop( "unselectable", "on" );

                        return false;
                    },
                    "onHide": function ( pkr )
                    {
                        $( pkr ).fadeOut( 500 );
                        return false;
                    }
                } );
            }

            element.on( "change", function () {
                var self = $( this ),
                    val = self.val();

                self.css( "background", val )
                    .css( "color", invert( val ) );
            } );

            element.on( "keyup", function () {
                if ( picker )
                {
                    $( this ).ColorPickerSetColor( this.value.replace( /^#/, "" ) );
                }

                $( this ).trigger( "change" );
            } );
        } );
    };

    global.Zork.Form.Element.prototype.color.isElementConstructor = true;

} ( window, jQuery, zork ) );
