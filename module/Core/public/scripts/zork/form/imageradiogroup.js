/**
 * RadioGroup image list
 * @package zork
 * @subpackage form
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */

( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.imageRadioGroup !== "undefined" )
    {
        return;
    }

//    js.require( "js.ui.tabs" );
//    js.require( "js.form.fieldsettabs" );
//    js.require( "jQuery.fn.vslider");

    /**
     * Makes radigroup options to images
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.prototype.imageRadioGroup = function ( element )
    {
        js.style('/styles/imageradiogroup.css');

        element = $( element );

        var itemsPerRow             = parseInt(element.data("jsImageradiogroupItemsperrow")||"4", 10),
            renderFieldsetTabs      = element.data( "jsImageradiogroupFieldsettabs" ),
            renderVslider           = element.data( "jsImageradiogroupVslider" ),
            descriptionKeyPattern   = element.data( "jsImageradiogroupDescriptionkey") || "",
            imageSrcPattern         = element.data( "jsImageradiogroupImagesrc") || "";

        if( Object.isUndefined( renderFieldsetTabs ) )
        {
            renderFieldsetTabs = true;
        }

        if( Object.isUndefined( renderVslider ) )
        {
            renderVslider = true;
        }

        if( renderVslider ){ js.require( "jQuery.fn.vslider"); }
        if( renderFieldsetTabs ){ js.require( "js.form.fieldsettabs" ); }

        if( renderFieldsetTabs )
        {
            if( ! element.data( "jsFieldsettabsSearch" ) )
            {
                element.data( "jsFieldsettabsSearch", ' ' );
            }
            js.form.fieldsetTabs(  element );
        }

        element.addClass( "imageradiogroup")
               .addClass(element.data( "jsImageradiogroupClass" ));

        var eleParent,parentContainers = [];
        element.find( "label" ).each( function(idx,eleRadioItem) {
            var input = $(eleRadioItem).find( ":radio, :checkbox" ),
                          iconSrc = imageSrcPattern
                                    .replace(/\[value\]/g, input.attr("value")),
                          descrText = js.core.translate(
                                        descriptionKeyPattern
                                        .replace(/\[value\]/g, input.attr("value"))
                                     ,js.core.userLocale),
                        button = $( '<div class="button"/>' )
                                 .html("<span>"+js.core.translate( "default.select", js.core.userLocale )+"</span>" );
            $(eleRadioItem)
                .prepend( $( "<img alt='icon' />" ).attr( "src", iconSrc ) )
                .prepend( $( "<p class='description'>"+descrText+"</p>" ) )
                .append( button );
            input.on( "click", function(evt) {
                element.find( "label" ).removeClass("selected");
                $(evt.target.parentNode).addClass("selected");
            } );
            input.filter(":checked").closest("label").addClass("selected");
            if( eleParent != eleRadioItem.parentNode )
            {
                eleParent = eleRadioItem.parentNode;
                parentContainers.push(eleRadioItem.parentNode);
            }
        } );

        $.each( parentContainers, function(idx,eleContainer) {
            var eleRow,
                eleRowsContainer = $('<div />').appendTo(eleContainer);
            $(eleContainer).find("label").each( function(idxItem,eleItem) {
                if( idxItem%itemsPerRow==0 )
                {
                    eleRow = $('<div />').appendTo(eleRowsContainer);
                }
                eleRow.append(eleItem);
            });

            if( renderVslider )
            {
                if( element.data("jsVsliderItemheight") )
                {
                    $(eleRowsContainer).data("jsVsliderItemheight",element.data("jsVsliderItemheight") );
                }
                $(eleRowsContainer).vslider({"items":"div"});
            }

        });

        if( renderFieldsetTabs && renderVslider )
        {
            $( ".imageradiogroup" ).tabs( {"activate": function(evt,ui) {
                ui.newPanel.find(".ui-vslider").vslider('refresh');
            }});
        }

        if( renderVslider )
        {
            setTimeout( function(){ $(".ui-vslider").vslider("refresh") },100 );
        }
    };

    global.Zork.Form.prototype.imageRadioGroup.isElementConstructor = true;

} ( window, jQuery, zork ) );



