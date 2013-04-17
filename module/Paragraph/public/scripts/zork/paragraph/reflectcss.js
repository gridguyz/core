/**
 * Paragraph functionalities
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.reflectCss !== "undefined" )
    {
        return;
    }

    var cssom = js.require( "js.cssom" ),
        sheet = cssom.sheet(),
        rules = {},
        prop  = function ( property ) {
            return String( property ).replace( /[A-Z]/g, function ( letter ) {
                return "-" + letter.toLowerCase();
            } );
        };

    /**
     * Reflect css
     *
     * @param {HTMLElement|$} element
     */
    global.Zork.Paragraph.prototype.reflectCss = function ( element )
    {
        element = $( element );

        var selector = element.data( "jsReflectcssSelector" ),
            property = prop( element.data( "jsReflectcssProperty" ) ),
            children = ":input:not([data-js-reflectcss-skip]):not(:disabled)",
            events   = "click.reflectcss change.reflectcss keyup.reflectcss",
            rule     = rules[selector] = rules[selector] || sheet.rules( selector ),
            isInput  = element.is( ":input" ),
            change   = null,
            val      = function ( node, isEvt ) {
                var val = isEvt
                    ? ( node.is( ":disabled" ) ? "" : node.val() )
                    : node[0].getAttribute( "value" );

                if ( node.data( "jsReflectcssEscape" ) == "true" )
                {
                    val = '"'
                        + String( val || "" ).replace( '\\', '\\\\' )
                                             .replace( '"', '\\"' )
                        + '"';
                }

                return ( node.data( "jsReflectcssPrefix" ) || "" )
                     + ( val || "" )
                     + ( node.data( "jsReflectcssPostfix" ) || "" );
            };

        if ( isInput )
        {
            change = function ( evt ) {
                rule.set( property, val( element, !! evt ) );
            };

            element.on( events, change );
        }
        else
        {
            change = function ( evt ) {
                rule.set(
                    property,
                    element.find( children )
                           .map( function () {
                               return val( $( this ) );
                           } )
                           .get()
                           .reduce( function ( prev, next ) {
                               return String( prev ) + String( next );
                           } ),
                    !! evt
                );
            };

            element.on( events, children, change );
        }

        change();
    };

    /**
     * Destruct functionality
     */
    global.Zork.Paragraph.prototype.reflectCss.destruct = function ( element )
    {
        element = $( element );

        var find = "[data-js-type~='js.paragraph.reflectCss'], " +
                   "[data-js-type~='zork.paragraph.reflectCss']",
            destruct = function () {
                var reflect  = $( this ),
                    selector = reflect.data( "jsReflectcssSelector" ),
                    property = prop( reflect.data( "jsReflectcssProperty" ) );

                if ( reflect.is( ":input" ) )
                {
                    reflect.off( ".reflectcss" );
                }
                else
                {
                    reflect.off( ".reflectcss", ":input" );
                }

                if ( selector in rules )
                {
                    rules[selector].set( property, null );
                }
            };

        if ( element.is( find ) )
        {
            destruct.call( element );
        }
        else
        {
            element.find( find ).each( destruct );
        }
    };

    global.Zork.Paragraph.prototype.reflectCss.isElementConstructor = true;

} ( window, jQuery, zork ) );
