/**
 * Paragraph functionalities
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.columnsPercentages !== "undefined" )
    {
        return;
    }

    var cssom = js.require( "js.cssom" ),
        sheet = cssom.sheet(),
        rules = {};

    /**
     * Reflect css
     *
     * @param {HTMLElement|$} element
     */
    global.Zork.Paragraph.prototype.columnsPercentages = function ( element )
    {
        element = $( element );

        var inputs = element.find( ":input" ),
            hndlrs = $( "<div />" )
                .addClass( "ui-helper-clearfix" )
                .css( {
                    "display": "block",
                    "position": "relative",
                    "text-align": "center"
                } );

        inputs.each( function () {
                    var self = $( this ),
                        pid  = self.data( "jsParagraphRepresent" ),
                        pcnt = $( "#paragraph-" + pid + "-container" ),
                        val  = parseInt( self.val() ) || 0,
                        hnd  = $( "<div />" )
                                .text(
                                    self.parent( "label" )
                                        .text()
                                        .replace( /[^0-9]/g, "" )
                                )
                                .css( {
                                    "float": "left",
                                    "height": "20px",
                                    "margin": "0px -1px",
                                    "padding": "20px 0px",
                                    "overflow": "hidden",
                                    "line-height": "20px"
                                } ),
                        resize = function () {
                            var resz    = $( this ),
                                parent  = resz.parent(),
                                all     = parent.width(),
                                next    = resz.next(),
                                val     = Math.min(
                                    100, parseInt( 100 * resz.width() / all )
                                ),
                                nval;

                            resz.css( "width", val + "%" );

                            self.val( val )
                                .trigger( "change" );

                            nval = Math.max( 0, parseInt(
                                parent.children()
                                      .map( function () {
                                          return this == next[0]
                                              ? 0 : parseInt( $( this )[0].style.width );
                                      } )
                                      .get()
                                      .reduce( function ( prev, next ) {
                                          return prev + next;
                                      } ) * -1 + 100
                            ) );

                            next.css( "width", nval + "%" );

                            self.parent()
                                .nextAll( "label:first" )
                                .find( ":input:first" )
                                .val( nval )
                                .trigger( "change" );
                        };

                    if ( ! val )
                    {
                        self.val( String( val = parseInt(
                            100 * pcnt.width() / pcnt.parent().width()
                        ) ) );
                    }

                    hndlrs.append( hnd.css( {
                        "width": String( val ) + "%"
                    } ) );

                    hnd.addClass( "ui-state-default" )
                       .resizable( {
                            "handles": "e",
                            "resize": resize,
                            "stop": resize
                        } )
                       .find( ".ui-resizable-handle" )
                       .hover( function () {
                           $( this ).parent()
                                    .next()
                                    .andSelf()
                                    .addClass( "ui-state-active" )
                       }, function () {
                           $( this ).parent()
                                    .next()
                                    .andSelf()
                                    .removeClass( "ui-state-active" )
                       } );

                    self.on( "change keyup", function ( evt ) {
                        var pid  = self.data( "jsParagraphRepresent" ),
                            sel  = "#paragraph-" + pid + "-container" +
                                   ".paragraph-container" +
                                   ".paragraph-column-container",
                            val  = parseInt( self.val(), 10 ) || 0,
                            rule = rules[sel] = rules[sel] || sheet.rules( sel ),
                            width;

                        if ( val <= 0 )
                        {
                            val = 0;
                            self.val( "0" );
                        }

                        width = String( val ) + "%";
                        rule.set( "width", width, true );
                        hnd.css( "width", width );
                    } );
                } );

        hndlrs.find( "> :last-child" )
              .resizable( "destroy" );

        element.append( hndlrs );
    };

    global.Zork.Paragraph.prototype.columnsPercentages.isElementConstructor = true;

} ( window, jQuery, zork ) );
