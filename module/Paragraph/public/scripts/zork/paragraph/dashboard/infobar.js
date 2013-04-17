/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.infobar !== "undefined" )
    {
        return;
    }

    /**
     * @class Lead dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.infobar = function ( form, element )
    {
        form    = $( form );
        element = $( element );

        var infobars                    = $( ".paragraph.paragraph-infobar" ),
            skin                        = form.find( ":input[name='paragraph-infobar[skin]']" ),
            displayUserAvatar           = form.find( ":input[type=checkbox][name='paragraph-infobar[displayUserAvatar]']" ),
            displayUserDisplayName      = form.find( ":input[type=checkbox][name='paragraph-infobar[displayUserDisplayName]']" ),
            displayPublishedDate        = form.find( ":input[type=checkbox][name='paragraph-infobar[displayPublishedDate]']" ),
            before                      = {
                "skin": skin.val(),
                "displayUserAvatar": displayUserAvatar.prop( "checked" ),
                "displayUserDisplayName": displayUserDisplayName.prop( "checked" ),
                "displayPublishedDate": displayPublishedDate.prop( "checked" )
            };

        infobars.each( function () {
            var self = $( this );
            self.data( "original-published-text", self.find( ".published" ).text() );
            self.data( "original-published-title", self.find( ".published" ).attr( "title" ) );
            self.data( "original-user-avatar", self.find( ".user-avatar img" ).attr( "src" ) );
            self.data( "original-user-display-name", self.find( ".user-displayName a" ).text() );
        } );

        skin.on( "change click", function () {
            element.find( "footer:first" )
                   .attr( "class", skin.val() );
        } );

        displayUserAvatar.on( "change click", function () {
            js.console.log( displayUserAvatar, displayUserAvatar.prop( "checked" ) );
            if ( displayUserAvatar.prop( "checked" ) )
            {
                element.find( ".user-avatar" )
                       .removeClass( "ui-helper-hidden" );
            }
            else
            {
                element.find( ".user-avatar" )
                       .addClass( "ui-helper-hidden" );
            }
        } );

        displayUserDisplayName.on( "change click", function () {
            if ( displayUserDisplayName.prop( "checked" ) )
            {
                element.find( ".user-displayName" )
                       .removeClass( "ui-helper-hidden" );
            }
            else
            {
                element.find( ".user-displayName" )
                       .addClass( "ui-helper-hidden" );
            }
        } );

        displayPublishedDate.on( "change click", function () {
            if ( displayPublishedDate.prop( "checked" ) )
            {
                element.find( ".published" )
                       .removeClass( "ui-helper-hidden" );
            }
            else
            {
                element.find( ".published" )
                       .addClass( "ui-helper-hidden" );
            }
        } );

        form.find( ":input[name='paragraph-infobar[rootCreated]']" )
            .on( "keyup change", function () {
                var val = new Date( $( this ).val() );

                infobars.find( ".published" )
                        .attr( "title", String( val ) )
                        .html( String( val ) );
            } );

        form.find( ":input[name='paragraph-infobar[rootUserId]']" )
            .on( "keyup change", function () {
                var option      = $( this ).find( "option:selected" ),
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
                            .parent()
                            .css( "display", "" );
                }
                else
                {
                    infobars.find( ".user-avatar img" )
                            .attr( "src", "" )
                            .parent()
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

        return {
            "update": function () {
                infobars.each( function () {
                    var self = $( this );
                    self.data( "original-published-text", self.find( ".published" ).text() );
                    self.data( "original-published-title", self.find( ".published" ).attr( "title" ) );
                    self.data( "original-user-avatar", self.find( ".user-avatar img" ).attr( "src" ) );
                    self.data( "original-user-display-name", self.find( ".user-displayName a" ).text() );
                } );

                before = {
                    "skin": skin.val(),
                    "displayUserAvatar": displayUserAvatar.prop( "checked" ),
                    "displayUserDisplayName": displayUserDisplayName.prop( "checked" ),
                    "displayPublishedDate": displayPublishedDate.prop( "checked" )
                };
            },
            "restore": function () {
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
                            .parent()
                            .css( "display", "" );
                    }
                    else
                    {
                        self.find( ".user-avatar img" )
                            .attr( "src", "" )
                            .parent()
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

                element.find( "footer:first" )
                       .attr( "class", before.skin );

               if ( before.displayUserAvatar )
                {
                    element.find( ".user-avatar" )
                           .removeClass( "ui-helper-hidden" );
                }
                else
                {
                    element.find( ".user-avatar" )
                           .addClass( "ui-helper-hidden" );
                }

                if ( before.displayUserDisplayName )
                {
                    element.find( ".user-displayName" )
                           .removeClass( "ui-helper-hidden" );
                }
                else
                {
                    element.find( ".user-displayName" )
                           .addClass( "ui-helper-hidden" );
                }

                if ( before.displayPublishedDate )
                {
                    element.find( ".published" )
                           .removeClass( "ui-helper-hidden" );
                }
                else
                {
                    element.find( ".published" )
                           .addClass( "ui-helper-hidden" );
                }
            }
        };
    };

} ( window, jQuery, zork ) );
