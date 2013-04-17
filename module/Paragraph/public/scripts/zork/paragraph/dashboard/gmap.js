/**
 * Paragraph dashboard
 * @package zork
 * @subpackage paragraph
 * @author Sipos Zoltán
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.dashboard.gmap !== "undefined" )
    {
        return;
    }

    /**
     * @class Google Map dashborad
     * @memberOf global.Zork.Paragraph.prototype.dashboard
     */
    global.Zork.Paragraph.prototype.dashboard.gmap = function ( form, element )
    {
        form    = $( form );
        element = $( element ).children().children();
//      js.console.log( element );
        var formElements = {
                "areaId"    : form.find( ":input[name='paragraph-gmap[areaId]']" ),
                "width"     : form.find( ":input[name='paragraph-gmap[width]']" ),
                "height"    : form.find( ":input[name='paragraph-gmap[height]']" ),
                "mapType"   : form.find( ":input[name='paragraph-gmap[mapType]']" )
            },
            before = null,
            locale = element.children().data( "locale" ),
            ajaxRefresh = function() {
                if ( before === null )
                {
                    before = element.children().detach();
                }
                js.core.loadRawElement( {
                    "url": "/app/" + locale + "/gmap/"
                        + formElements.areaId.val() + "/"
                        + formElements.width.val() + "/"
                        + formElements.height.val() + "/"
                        + formElements.mapType.val(),
                    "target": element
                } );
            };

        formElements.areaId.on( "change", ajaxRefresh );
        formElements.width.on( "change", ajaxRefresh );
        formElements.height.on( "change", ajaxRefresh );
        formElements.mapType.on( "change", ajaxRefresh );

        return {
            "update": function () {
                before = null;
            },
            "restore": function () {
                if ( before !== null )
                {
                    element.children().remove();

                    element.append(
                        before
                    );
                }
            }
        };
    };

} ( window, jQuery, zork ) );
