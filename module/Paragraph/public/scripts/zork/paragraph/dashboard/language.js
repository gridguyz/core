/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author Sipos Zoltán
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.language !== "undefined" )
    {
        return;
    }

    /**
     * @class vote dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.language = function ( form, element )
    {
        element = $( element );
        form    = $( form );

        var locales = element.find( ".language-variants .language-variant" ),
            inputs  = form.find( ":input[name='paragraph-language[locales][]']" ),
            before  = {};

        locales.each( function () {
            var $this   = $( this ),
                locale  = String( locale.data( "jsLocale" ) || "en" );

            before[locale] = $this.hasClass( "selected" );
        } );

        inputs.on( "click", function () {
            $( this ).blur();
        } );

        inputs.on( "change" , function () {
            if ( inputs.find( ":checked" ).length )
            {
                locales.each( function () {
                    var $this   = $( this ),
                        locale  = String( locale.data( "jsLocale" ) || "en" );

                    $this.toggleClass( "selected", inputs.find( "[value='" + locale + "']" ).prop( "checked" ) );
                } );
            }
            else
            {
                locales.each( function () {
                    var $this   = $( this ),
                        locale  = String( locale.data( "jsLocale" ) || "en" );

                    $this.addClass( "selected" );
                } );
            }
        } );

        return {
            "update": function () {
                locales.each( function () {
                    var $this   = $( this ),
                        locale  = String( locale.data( "jsLocale" ) || "en" );

                    before[locale] = $this.hasClass( "selected" );
                } );
            },
            "restore": function () {
                locales.each( function () {
                    var $this   = $( this ),
                        locale  = String( locale.data( "jsLocale" ) || "en" );

                    $this.toggleClass( "selected", before[locale] );
                } );
            }
        };

    };

} ( window, jQuery, zork ) );
