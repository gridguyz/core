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
            elements = {
                "url"       : form.find( ":input[name='paragraph-image[url]']" ),
                "width"     : form.find( ":input[name='paragraph-image[width]']" ),
                "height"    : form.find( ":input[name='paragraph-image[height]']" ),
                "method"    : form.find( ":input[name='paragraph-image[method]']" ),
                "bgColor"   : form.find( ":input[name='paragraph-image[bgColor]']" ),
                "caption"   : form.find( ":input[name='paragraph-image[caption]']" ),
                "alternate" : form.find( ":input[name='paragraph-image[alternate]']" ),
                "linkTo"    : form.find( ":input[name='paragraph-image[linkTo]']" ),
                "linkTarget": form.find( ":input[name='paragraph-image[linkTarget]']" ),
                "lightBox"  : form.find( ":input[name='paragraph-image[lightBox]']" )
            },
            before = {
                "url"       : img.attr( "src" ),
                "caption"   : element.find( "figcaption" ).html(),
                "alternate" : img.attr( "alt" ),
                "linkTo"    : form.find( ":input[name='paragraph-image[linkTo]']" ).val(),
                "linkTarget": form.find( ":input[name='paragraph-image[linkTo]']" ).val(),
                "lightBox"  : form.find( ":input[name='paragraph-image[lightBox]']" )[1].checked
            },
            changeTtl = 2000,
            changeTimeout = null,

            reloadImage = function () {
                img.one( "load", function () {
                        $( this ).css( {
                            "width"          : "",
                            "height"         : "",
                            "max-width"      : "",
                            "max-height"     : ""
                        } );
                    } )
                   .attr( {
                        "src" : js.core.thumbnail(
                            elements.url.val(), {
                                "width"     : elements.width.val(),
                                "height"    : elements.height.val(),
                                "method"    : elements.method.val(),
                                "bgcolor"   : elements.bgColor.val()
                            }
                        )
                    } );
            },
            changeImage = function ( evt ) {
                if ( evt )
                {
                    if ( changeTimeout )
                    {
                        clearTimeout( changeTimeout );
                    }

                    if ( elements.method.val() == "fit" )
                    {
                        img.css( {
                            "max-width"  : parseInt( elements.width.val(), 10 ),
                            "max-height" : parseInt( elements.height.val(), 10 )
                        } );
                    }
                    else
                    {
                        img.css( {
                            "width"  : parseInt( elements.width.val(), 10 ),
                            "height" : parseInt( elements.height.val(), 10 )
                        } );
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
            var val = $( this ).val(),
                cap = element.find( "figcaption" );

            cap.html( val );

            if ( val )
            {
                cap.removeClass( "empty" );
            }
            else
            {
                cap.addClass( "empty" );
            }
        } );

        elements.alternate.on( "keyup change", function () {
            var val = $( this ).val();

            img.attr( {
                "alt": val,
                "title": val
            } );
        } );

        return {
            "update": function () {
                before = {
                    "url": img.attr( "src" ),
                    "caption": elements.caption.val(),
                    "alternate": elements.alternate.val(),
                    "linkTo"    : form.find( ":input[name='paragraph-image[linkTo]']" ).val(),
                    "linkTarget": form.find( ":input[name='paragraph-image[linkTarget]']" ).val(),
                    "lightBox"  : form.find( ":input[name='paragraph-image[lightBox]']" )[1].checked
                };

                if ( before.linkTo )
                {
                    link.attr( "href", before.linkTo );

                    if ( before.linkTarget )
                    {
                        link.attr( "target", before.linkTarget );
                    }
                    else
                    {
                        link.attr( 'target', null );
                    }
                }
                else
                {
                    link.attr( {
                        "href": null,
                        "target": null
                    } );
                }

                js.paragraph.removeImageLightboxEvent( link );

                if ( before["lightBox"] )
                {
                    js.paragraph.image( link );
                }
            },
            "restore": function () {
                element.find( "figcaption" )
                       .html( before.caption );

                if ( before.linkTo )
                {
                    link.attr( "href", before.linkTo );

                    if ( before.linkTarget )
                    {
                        link.attr( "target", before.linkTarget );
                    }
                    else
                    {
                        link.attr( "target", null );
                    }
                }
                else
                {
                    link.attr( {
                        "href": null,
                        "target": null
                    } );
                }

                img.attr( {
                    "src": before.url,
                    "alt": before.alternate,
                    "title": before.alternate
                } );
            }
        }
    };

} ( window, jQuery, zork ) );
