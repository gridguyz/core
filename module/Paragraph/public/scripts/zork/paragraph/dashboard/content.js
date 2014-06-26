/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.content !== "undefined" )
    {
        return;
    }

    /**
     * @class Content dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.content = function ( form, element )
    {
        form    = $( form );
        element = $( element );

        var titleTag    = $( "head title" ),
            title       = form.find( ":input[name='paragraph-content[title]']" ),
            created     = form.find( ":input[name='paragraph-content[created]']" ),
            userId      = form.find( ":input[name='paragraph-content[userId]']" ),
            leadImage   = form.find( ":input[name='paragraph-content[leadImage]']" ),
            leadText    = form.find( ":input[name='paragraph-content[leadText]']" ),
            before      = {
                "title": titleTag.text().replace( /^\s+/, "" ).replace( /\s+$/, "" ) || "",
            },
            base        = before.title.substr( 0, before.title.length - ( title.val() || "" ).length ),
            titles      = $( ".paragraph.paragraph-title" ),
            leads       = $( ".paragraph.paragraph-lead" ),
            infobars    = $( ".paragraph.paragraph-infobar" );

        title.on( "keyup change", function () {
            var val = title.val();
            titleTag.text( base + val );
            titles.find( "h1" ).text( val || base );
        } );

        titles.each( function () {
            var self = $( this );
            self.data( "original-title", self.find( "h1" ).text() );
        } );

        leadImage.on( "keyup change", function () {
            var val = leadImage.val();

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

        leadText.on( "keyup change", function () {
            var val = leadText.val();
            leads.find( ".lead-text" )
                 .html( val );
        } );

        leads.each( function () {
            var self = $( this );
            self.data( "original-lead-image", self.find( ".lead-image" ).attr( "src" ) );
            self.data( "original-lead-text", self.find( ".lead-text" ).html() );
        } );

        created.on( "keyup change", function () {
            var val = new Date( created.val() );
            infobars.find( ".published" )
                    .attr( "title", val )
                    .text( val );
        } );

        userId.on( "change", function () {
            var option      = userId.find( "option:selected" ),
                avatar      = option.data( "avatar" ),
                displayName = option.text();

            if ( avatar )
            {
                infobars.find( ".user-avatar img" )
                        .attr( "src", js.core.thumbnail( {
                               "url"    : avatar,
                               "width"  : 50,
                               "height" : 50,
                               "method" : "fit"
                           } ) )
                        .css( "display", "" );
            }
            else
            {
                infobars.find( ".user-avatar img" )
                        .attr( "src", "" )
                        .css( "display", "none" );
            }

            infobars.find( ".user-displayName" )
                    .css( "display", displayName ? "" : "none" )
                    .find( "a" )
                    .text( displayName );

            infobars.find( ".user-avatar, .user-displayName a" )
                    .each( function () {
                        var self = $( this );

                        self.attr(
                            "href",
                            String( self.attr( "href" ) )
                                        .replace( /[^\/]+$/, displayName )
                        );
                    } );
        } );

        infobars.each( function () {
            var self = $( this );
            self.data( "original-published-text", self.find( ".published" ).text() );
            self.data( "original-published-title", self.find( ".published" ).attr( "title" ) );
            self.data( "original-user-avatar", self.find( ".user-avatar img" ).attr( "src" ) );
            self.data( "original-user-display-name", self.find( ".user-displayName a" ).text() );
        } );

        return {
            "update": function () {
                before.title = base + title.val();

                titles.each( function () {
                    var self = $( this );
                    self.data( "original-title", self.find( "h1" ).text() );
                } );

                leads.each( function () {
                    var self = $( this );
                    self.data( "original-lead-image", self.find( ".lead-image" ).attr( "src" ) );
                    self.data( "original-lead-text", self.find( ".lead-text" ).html() );
                } );

                infobars.each( function () {
                    var self = $( this );
                    self.data( "original-published-text", self.find( ".published" ).text() );
                    self.data( "original-published-title", self.find( ".published" ).attr( "title" ) );
                    self.data( "original-user-avatar", self.find( ".user-avatar img" ).attr( "src" ) );
                    self.data( "original-user-display-name", self.find( ".user-displayName a" ).text() );
                } );
            },
            "restore": function () {
                titleTag.text( before.title );

                titles.each( function () {
                    var self = $( this );
                    self.find( "h1" ).text( self.data( "original-title" ) );
                } );

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

                infobars.each( function () {
                    var self        = $( this ),
                        avatar      = self.data( "original-user-avatar" ),
                        displayName = self.data( "original-user-display-name" );

                    self.find( ".published" ).text( self.data( "original-published-text" ) );
                    self.find( ".published" ).attr( "title", self.data( "original-published-title" ) );

                    if ( avatar )
                    {
                        self.find( ".user-avatar img" )
                            .attr( "src", avatar )
                            .css( "display", "" );
                    }
                    else
                    {
                        self.find( ".user-avatar img" )
                            .attr( "src", "" )
                            .css( "display", "none" );
                    }

                    self.find( ".user-displayName" )
                        .css( "display", displayName ? "" : "none" )
                        .find( "a" )
                        .text( displayName );

                    self.find( ".user-avatar, .user-displayName a" )
                        .each( function () {
                            var self = $( this );

                            self.attr(
                                "href",
                                String( self.attr( "href" ) )
                                        .replace( /[^\/]+$/, displayName )
                            );
                        } );
                } );
            }
        };
    };

} ( window, jQuery, zork ) );
