/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.image !== "undefined" )
    {
        return;
    }

    js.require( "js.paragraph.image" );

    /**
     * @class Image dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.image = function ( form, element )
    {
        form    = $( form );
        element = $( element );
        var img = element.find( "img:first" ),
            link = element.find( "a.image-paragraph-link:first" ),
            caption = element.find( "figcaption:first" ),
            elements = {
                "url"       : form.find( ":input[name='paragraph-image[url]']" ),
                "width"     : form.find( ":input[name='paragraph-image[width]']" ),
                "height"    : form.find( ":input[name='paragraph-image[height]']" ),
                "method"    : form.find( ":input[name='paragraph-image[method]']" ),
                "bgColor"   : form.find( ":input[name='paragraph-image[bgColor]']:not([type='hidden'])" ),
                "caption"   : form.find( ":input[name='paragraph-image[caption]']" ),
                "alternate" : form.find( ":input[name='paragraph-image[alternate]']" ),
                "linkTo"    : form.find( ":input[name='paragraph-image[linkTo]']" ),
                "linkTarget": form.find( ":input[name='paragraph-image[linkTarget]']" ),
                "lightBox"  : form.find( ":input[name='paragraph-image[lightBox]']:not([type='hidden'])" )
            },
            before = {
                "url"           : img.attr( "src" ),
                "caption"       : caption.html(),
                "captionEmpty"  : caption.hasClass( "empty" ),
                "alternate"     : img.attr( "alt" ),
                "linkTo"        : elements.linkTo.val(),
                "linkTarget"    : elements.linkTarget.val(),
                "lightBox"      : elements.lightBox.attr( "checked" )
            },
            changeTtl = 2000,
            changeTimeout = null,
            reloadImage = function () {
                var src = parseInt( elements.width.val(), 10 ) ||
                          parseInt( elements.height.val(), 10 )
                            ? js.core.thumbnail( {
                                  "url"     : elements.url.val(),
                                  "width"   : elements.width.val(),
                                  "height"  : elements.height.val(),
                                  "method"  : elements.method.val(),
                                  "bgcolor" : elements.bgColor.prop( "disabled" )
                                            ? null : elements.bgColor.val()
                              } )
                            : elements.url.val();

                img.one( "load", function () {
                        $( this ).css( {
                            "width"          : "",
                            "height"         : "",
                            "max-width"      : "",
                            "max-height"     : ""
                        } );
                    } )
                   .attr( "src", src );
            },
            changeImage = function ( evt ) {
                if ( evt )
                {
                    var prefix = elements.method.val() == "fit" ? "max-" : "";

                    if ( changeTimeout )
                    {
                        clearTimeout( changeTimeout );
                    }

                    if ( parseInt( elements.width.val(), 10 ) )
                    {
                        img.css(
                            prefix + "width",
                            parseInt( elements.width.val(), 10 )
                        );
                    }

                    if ( parseInt( elements.height.val(), 10 ) )
                    {
                        img.css(
                            prefix + "height",
                            parseInt( elements.height.val(), 10 )
                        );
                    }

                    changeTimeout = setTimeout( changeImage, changeTtl );
                }
                else
                {
                    changeTimeout = null;
                    reloadImage();
                }
            };

        elements.url.on( "change", reloadImage );
        elements.width.on( "keyup change", changeImage );
        elements.height.on( "keyup change", changeImage );
        elements.method.on( "click change", reloadImage );
        elements.bgColor.on( "keyup change", changeImage );

        elements.caption.on( "keyup change", function () {
            var val = $( this ).val();

            caption.html( val )
                   .toggleClass( "empty", ! val );
        } );

        elements.alternate.on( "keyup change", function () {
            var val = $( this ).val();

            img.attr( {
                "alt": val,
                "title": val
            } );
        } );

        $( [ elements.linkTo[0], elements.linkTarget[0] ] )
            .on( "keyup change", function () {
                var to      = elements.linkTo.val(),
                    target  = elements.linkTarget.val();

                link.attr( {
                    "href": to ? to : null,
                    "target": to && target ? target : null
                } );
            } );

        elements.lightBox.on( "click change", function () {
            js.paragraph.image.removeLightboxEvent( link );

            if ( $( this ).prop( "checked" ) )
            {
                js.paragraph.image( link );
            }
        } );

        return {
            "update": function () {
                var captionVal = elements.caption.val();

                before = {
                    "url"           : img.attr( "src" ),
                    "caption"       : captionVal,
                    "captionEmpty"  : ! captionVal,
                    "alternate"     : elements.alternate.val(),
                    "linkTo"        : elements.linkTo.val(),
                    "linkTarget"    : elements.linkTarget.val(),
                    "lightBox"      : elements.lightBox.prop( "checked" )
                };

                link.attr( {
                    "href": before.linkTo ? before.linkTo : null,
                    "target": before.linkTo && before.linkTarget ? before.linkTarget : null
                } );
            },
            "restore": function () {
                caption.html( before.caption )
                       .toggleClass( "empty", before.captionEmpty );

                link.attr( {
                    "href": before.linkTo ? before.linkTo : null,
                    "target": before.linkTo && before.linkTarget ? before.linkTarget : null
                } );

                img.attr( {
                    "src": before.url,
                    "alt": before.alternate,
                    "title": before.alternate
                } );
            }
        };
    };

} ( window, jQuery, zork ) );
