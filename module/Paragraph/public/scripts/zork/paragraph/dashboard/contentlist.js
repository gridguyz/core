/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.contentList !== "undefined" )
    {
        return;
    }

    /**
     * @class Content-list dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.contentList = function ( form, element )
    {
        form    = $( form );
        element = $( element );

        var displayUser         = form.find( ":input[type=checkbox][name='paragraph-contentList[displayUser]']" ),
            displayCreated      = form.find( ":input[type=checkbox][name='paragraph-contentList[displayCreated]']" ),
            displayLeadImage    = form.find( ":input[type=checkbox][name='paragraph-contentList[displayLeadImage]']" ),
            displayLeadText     = form.find( ":input[type=checkbox][name='paragraph-contentList[displayLeadText]']" ),
            displayReadMore     = form.find( ":input[type=checkbox][name='paragraph-contentList[displayReadMore]']" ),
            before = {
                "displayUser":      !! displayUser.prop( "checked" ),
                "displayCreated":   !! displayCreated.prop( "checked" ),
                "displayLeadImage": !! displayLeadImage.prop( "checked" ),
                "displayLeadText":  !! displayLeadText.prop( "checked" ),
                "displayReadMore":  !! displayReadMore.prop( "checked" )
            },
            update = function ( selector ) {
                return function () {
                    element.find( ".content-list .content-entry " + selector )
                           .toggleClass( "ui-helper-hidden", ! $( this ).prop( "checked" ) );
                };
            };

        displayUser.on(      "click change", update( ".user"       ) );
        displayCreated.on(   "click change", update( ".created"    ) );
        displayLeadImage.on( "click change", update( ".lead-image" ) );
        displayLeadText.on(  "click change", update( ".lead-text"  ) );
        displayReadMore.on(  "click change", update( ".read-more"  ) );

        return {
            "update": function () {
                before = {
                    "displayUser":      !! displayUser.prop( "checked" ),
                    "displayCreated":   !! displayCreated.prop( "checked" ),
                    "displayLeadImage": !! displayLeadImage.prop( "checked" ),
                    "displayLeadText":  !! displayLeadText.prop( "checked" ),
                    "displayReadMore":  !! displayReadMore.prop( "checked" )
                };
            },
            "restore": function () {
                displayUser.prop(      "checked", before.displayUser      ).trigger( "change" );
                displayCreated.prop(   "checked", before.displayCreated   ).trigger( "change" );
                displayLeadImage.prop( "checked", before.displayLeadImage ).trigger( "change" );
                displayLeadText.prop(  "checked", before.displayLeadText  ).trigger( "change" );
                displayReadMore.prop(  "checked", before.displayReadMore  ).trigger( "change" );
            }
        };
    };

} ( window, jQuery, zork ) );
