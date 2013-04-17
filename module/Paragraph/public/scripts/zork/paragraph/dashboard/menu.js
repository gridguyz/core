/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.menu !== "undefined" )
    {
        return;
    }

    /**
     * @class Menu dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.menu = function ( form, element )
    {
        form    = $( form );
        element = $( element );

        var navigation  = element.find( "ul.navigation" ),
            navparent   = navigation.parent(),
            menuId      = form.find( ":input[name='paragraph-menu[menuId]']" ),
            current     = menuId.val(),
            horizontal  = form.find( ":input[name='paragraph-menu[horizontal]']" ),
            before      = {
                "menuId": current,
                "menuHtml": navparent.html(),
                "horizontal": navigation.is( ".horizontal" )
            },
            changeTime  = null,
            changeMenu  = function () {
                var val = menuId.val();

                if ( val != current )
                {
                    current = val;
                    $.get(
                        "/app/" + js.core.defaultLocale + "/menu/render/" + val + "/" +
                            ( navigation.is( ".horizontal" ) ? "horizontal" : "vertical" ),
                        function ( nav ) {
                            navparent.html( nav );
                            navigation = navparent.find( "> ul.navigation" );
                        },
                        "text"
                    );
                }
            };

        menuId.on( "click change", function () {
            if ( changeTime )
            {
                clearTimeout( changeTime );
            }

            if ( menuId.val() != current )
            {
                changeTime = setTimeout( changeMenu, 2000 );
            }
        } );

        horizontal.on( "click", function () {
                        $( this ).blur();
                    } )
                  .on( "change", function () {
                        if ( this.checked ) {
                            navigation.removeClass( "vertical" )
                                      .addClass( "horizontal" );
                        } else {
                            navigation.removeClass( "horizontal" )
                                      .addClass( "vertical" );
                        }
                    } );

        return {
            "update": function () {
                before = {
                    "menuId": menuId.val(),
                    "menuHtml": navparent.html(),
                    "horizontal": horizontal[0].checked
                }
            },
            "restore": function () {
                navparent.html( before.menuHtml );
                navigation = navparent.find( "> ul.navigation" );

                if ( before.horizontal ) {
                    navigation.removeClass( "vertical" )
                              .addClass( "horizontal" );
                } else {
                    navigation.removeClass( "horizontal" )
                              .addClass( "vertical" );
                }
            }
        };
    };

} ( window, jQuery, zork ) );
