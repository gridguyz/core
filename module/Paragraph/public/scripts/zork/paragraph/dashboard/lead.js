/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.lead !== "undefined" )
    {
        return;
    }

    /**
     * @class Lead dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.lead = function ( form, element )
    {
        form    = $( form );
        element = $( element );

        var leads = $( ".paragraph.paragraph-lead" ),
            img = element.find( ".lead-image" ),
            elements = {
                "rootText"      : form.find( ":input[name='paragraph-lead[rootText]']" ),
                "rootImage"     : form.find( ":input[name='paragraph-lead[rootImage]']" ),
                "imageWidth"    : form.find( ":input[name='paragraph-lead[imageWidth]']" ),
                "imageHeight"   : form.find( ":input[name='paragraph-lead[imageHeight]']" ),
                "imageMethod"   : form.find( ":input[name='paragraph-lead[imageMethod]']" ),
                "imageBgColor"  : form.find( ":input[name='paragraph-lead[imageBgColor]']:not([type='hidden'])" ),
            },
            before = {
                "imageWidth"    : img.data( "jsLeadImageWidth" ),
                "imageHeight"   : img.data( "jsLeadImageHeight" ),
                "imageMethod"   : img.data( "jsLeadImageMethod" ),
                "imageBgColor"  : img.data( "jsLeadImageBgcolor" )
            },
            changeTtl = 2000,
            changeTimeout = null,
            reloadImage = function () {
                var w = parseInt( elements.imageWidth.val(), 10 ),
                    h = parseInt( elements.imageHeight.val(), 10 ),
                    src = js.core.thumbnail( {
                        "url"     : elements.rootImage.val(),
                        "width"   : w || h ? elements.imageWidth.val() : 100,
                        "height"  : w || h ? elements.imageHeight.val() : 100,
                        "method"  : elements.imageMethod.val(),
                        "bgcolor" : elements.imageBgColor.prop( "disabled" )
                                  ? null : elements.imageBgColor.val()
                    } );

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

                    if ( parseInt( elements.imageWidth.val(), 10 ) )
                    {
                        img.css(
                            prefix + "width",
                            parseInt( elements.imageWidth.val(), 10 )
                        );
                    }

                    if ( parseInt( elements.imageHeight.val(), 10 ) )
                    {
                        img.css(
                            prefix + "height",
                            parseInt( elements.imageHeight.val(), 10 )
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

        leads.each( function () {
            var self = $( this );
            self.data( "original-lead-image", self.find( ".lead-image" ).attr( "src" ) );
            self.data( "original-lead-text", self.find( ".lead-text" ).html() );
        } );

        elements.rootText.on( "keyup change", function () {
            leads.find( ".lead-text" )
                 .html( $( this ).val() );
        } );

        elements.rootImage.on( "keyup change", function () {
            var val = $( this ).val();

            if ( val )
            {
                leads.find( ".lead-image" )
                     .each( function () {
                         var t = $( this );
                         t.attr( "src", js.core.thumbnail( {
                            "url"    : val,
                            "width"  : t.data( "jsLeadImageWidth" ),
                            "height" : t.data( "jsLeadImageHeight" ),
                            "method" : t.data( "jsLeadImageMethod" ),
                            "bgcolor": t.data( "jsLeadImageBgcolor" )
                         } ) );
                     } )
                     .css( "display", "" );
            }
            else
            {
                leads.find( ".lead-image" )
                     .attr( "src", "" )
                     .css( "display", "none" );
            }
        } );

        elements.imageWidth.on( "keyup change", function () {
            var val = $( this ).val();

            img.attr( "data-js-lead-image-width", val )
               .data( "jsLeadImageWidth", val );

            changeImage();
        } );

        elements.imageHeight.on( "keyup change", function () {
            var val = $( this ).val();

            img.attr( "data-js-lead-image-height", val )
               .data( "jsLeadImageHeight", val );

            changeImage();
        } );

        elements.imageMethod.on( "click change", function () {
            var val = $( this ).val();

            img.attr( "data-js-lead-image-method", val )
               .data( "jsLeadImageMethod", val );

            reloadImage();
        } );

        elements.imageBgColor.on( "keyup change", function () {
            var val = $( this ).val();

            img.attr( "data-js-lead-image-bgcolor", val )
               .data( "jsLeadImageBgcolor", val );

            changeImage();
        } );

        return {
            "update": function () {
                before = {
                    "imageWidth"    : img.data( "jsLeadImageWidth" ),
                    "imageHeight"   : img.data( "jsLeadImageHeight" ),
                    "imageMethod"   : img.data( "jsLeadImageMethod" ),
                    "imageBgColor"  : img.data( "jsLeadImageBgcolor" )
                };

                leads.each( function () {
                    var self = $( this );
                    self.data( "original-lead-image", self.find( ".lead-image" ).attr( "src" ) );
                    self.data( "original-lead-text", self.find( ".lead-text" ).html() );
                } );
            },
            "restore": function () {

                img.data( "jsLeadImageWidth", before.imageWidth )
                   .data( "jsLeadImageHeight", before.imageHeight )
                   .data( "jsLeadImageMethod", before.imageMethod )
                   .data( "jsLeadImageBgcolor", before.imageBgColor )
                   .attr( "data-js-lead-image-width", before.imageWidth )
                   .attr( "data-js-lead-image-height", before.imageHeight )
                   .attr( "data-js-lead-image-method", before.imageMethod )
                   .attr( "data-js-lead-image-bgcolor", before.imageBgColor );

                leads.each( function () {
                    var self = $( this ),
                        val  = self.data( "original-lead-image" );

                    if ( val )
                    {
                        self.find( ".lead-image" )
                            .attr( "src", val )
                            .css( "display", "" );
                    }
                    else
                    {
                        self.find( ".lead-image" )
                            .attr( "src", "" )
                            .css( "display", "none" );
                    }

                    self.find( ".lead-text" ).html( self.data( "original-lead-text" ) );
                } );
            }
        };
    };

} ( window, jQuery, zork ) );
