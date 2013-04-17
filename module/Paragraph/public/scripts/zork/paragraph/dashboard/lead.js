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
     // element = $( element );

        var leads = $( ".paragraph.paragraph-lead" );

        leads.each( function () {
            var self = $( this );
            self.data( "original-lead-image", self.find( ".lead-image" ).attr( "src" ) );
            self.data( "original-lead-text", self.find( ".lead-text" ).html() );
        } );

        form.find( ":input[name='paragraph-lead[rootImage]']" )
            .on( "keyup change", function () {
                var val = $( this ).val();

                if ( val )
                {
                    leads.find( ".lead-image" )
                         .attr( "src", js.core.thumbnail( {
                                "url"    : val,
                                "width"  : 100,
                                "height" : 100,
                                "method" : "fit"
                            } ) )
                         .css( "display", "" );
                }
                else
                {
                    leads.find( ".lead-image" )
                         .attr( "src", "" )
                         .css( "display", "none" );
                }
            } );

        form.find( ":input[name='paragraph-lead[rootText]']" )
            .on( "keyup change", function () {
                leads.find( ".lead-text" )
                     .html( $( this ).val() );
            } );

        return {
            "update": function () {
                leads.each( function () {
                    var self = $( this );
                    self.data( "original-lead-image", self.find( ".lead-image" ).attr( "src" ) );
                    self.data( "original-lead-text", self.find( ".lead-text" ).html() );
                } );
            },
            "restore": function () {
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
