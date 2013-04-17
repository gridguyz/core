/**
 * User interface functionalities
 * @package zork
 * @subpackage ui
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.ui.dialog !== "undefined" )
    {
        return;
    }

    global.Zork.Ui.prototype.loginWith = function ( element )
    {
        js.style( "/styles/modules/User/loginWith.css" );
        element = $( element );

        var list = $( "<ul />" ).css( "float", "none" ),
            options = Object( element.data( "jsLoginwith" ) ),
            pos = {
                "my" : "center center",
                "at" : "center center",
                "of" : element
            },
            click = function ()
            {
                global.location.href = this.href;
            },
            href = function ( url )
            {
                url = String( url );
             /* url += ~ url.indexOf( '?' ) ? '&' : '?';
                url += "returnUri=";
                url += ( global.encodeURIComponent || global.escape )( global.location.href ); */
                return url;
            },
            shown = false, i;

        for ( i in options )
        {
            list.append(
                $( "<li />" ).
                    addClass( i.toLowerCase() ).
                    append(
                        $( "<a />" ).
                            text( i ).
                            click( click ).
                            attr( "href", href( options[i] ) )
                    )
            );
        }

        list.menu();

        element.click( function ()
        {
            if ( shown )
            {
                list.dialog( "option", "position", pos ).
                     dialog( "open" );
            }
            else
            {
                list.dialog( {
                    "dialogClass"   : "js-login-with",
                    "draggable"     : false,
                    "position"      : pos,
                    "resizable"     : false,
                    "title"         : element.text(),
                    "width"         : "auto",
                    "minHeight"     : 100
                } );

                shown = true;
            }

            return false;
        } );
    };

    global.Zork.Ui.prototype.loginWith.isElementConstructor = true;

} ( window, jQuery, zork ) );
