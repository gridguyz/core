/**
 * Layout Select UI
 *
 * @package zork
 * @subpackage form
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */

( function ( global, $, js )
{
    "use strict";

    if ( typeof js.layoutSelect !== "undefined" )
    {
        return;
    }

    js.require( "jQuery.fn.vslider");

    /**
     * Generates layout select user interface from radio inputs
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.prototype.layoutSelect = function ( element )
    {
        js.style('/styles/scripts/layoutselect.css');

        element = $( element );

        var defaultKey              = element.data( "jsLayoutselectDefaultkey")
                                   || "paragraph.form.content.layout.default",
            descriptionKeyPattern   = element.data( "jsLayoutselectDescriptionkey")
                                   || "paragraph.form.content.layout.default-description",
            imageSrcPattern         = element.data( "jsLayoutselectImagesrc") || "",
            selectType              = element.data( "jsLayoutselectType") || "locale",
            itemsPerRow             = parseInt(element.data("jsLayoutselectItemsperrow"))>0
                                    ? parseInt(element.data("jsLayoutselectItemsperrow"))
                                    : ( selectType == "local" ? 6 : 3 );

        element.addClass( "layout-select "+selectType);

        element.find( "label" ).each( function(idx,eleRadioItem) {
            var input = $(eleRadioItem).find( ":radio" ),
                inner = $('<div class="inner"/>').append(input),
                title = $('<div class="title"/>').html( $(eleRadioItem).html() || js.core.translate(defaultKey) ),
                overlay = $('<div class="overlay"/>'),

                innerButtons = $('<div class="buttons"/>').appendTo(inner),
                innerDescription = $('<p class="description"/>').appendTo(inner),

                innerButtonsSelect = $('<span class="select"/>')
                                    .html(
                                        js.core.translate("default.select"
                                                          ,js.core.userLocale)
                                        )
                                    .appendTo(innerButtons),
                dateCreated = input.data("created") || '-',
                dateModified = input.data("lastModified") || '-';

            $(eleRadioItem).html('')
               .append(inner)
               .append(title)
               .append(overlay);


            if( selectType == 'import' )
            {
                innerDescription.html(
                    js.core.translate(
                        descriptionKeyPattern.replace("[value]",
                                                      input.attr( "value"))
                        ,js.core.userLocale));

                var imageSrc = imageSrcPattern
                               .replace("[value]",input.attr( "value"));
                inner.prepend( $( "<img alt='icon' />" ).attr( "src", imageSrc ));
            }
            else//selectType == 'locale'
            {
                if( input.attr( "value") )
                {
                innerDescription
                    .append(
                        $('<div/>')
                            .append( $('<span/>').html(
                                js.core.translate("default.lastmodified",
                                                  js.core.userLocale)
                                +': ') )
                            .append( $('<span/>').html(dateModified) )
                    )
                    .append(
                        $('<div/>')
                            .append( $('<span/>').html(
                                js.core.translate("default.created",
                                                  js.core.userLocale)
                                +': ') )
                            .append( $('<span/>').html(dateCreated) )
                    );

                js.core.translate("default.created",js.core.userLocale)
                    innerButtons.prepend(
                        $('<a class="preview"/>')
                        .html(
                            js.core.translate("default.preview"
                                              ,js.core.userLocale)
                            )
                        .attr('href','/app/'+js.core.userLocale
                                      +'/paragraph/render/'+input.attr( "value"))
                        .attr('target','_blank')
                    );
                }
            }


            innerButtonsSelect.on( "click", function(evt) {
                element.find( "label" ).removeClass("selected");
                $(evt.target.parentNode.parentNode.parentNode).addClass("selected");
            } );


        } );



        var eleRow,
            eleRowsContainer = $('<div/>').appendTo(element);

        element.find( "label" ).each( function(idxItem,eleItem) {
            if( idxItem%itemsPerRow==0 )
            {
                eleRow = $('<div />').appendTo(eleRowsContainer);
            }
            eleRow.append(eleItem);
        } );


        $(eleRowsContainer).vslider({"items":"div", "itemheight":( selectType == 'local' ? 300 : 255 )});

        {
            setTimeout( function(){ $('.ui-vslider').vslider('refresh') },100 );
        }
    };

    global.Zork.prototype.layoutSelect.isElementConstructor = true;

} ( window, jQuery, zork ) );
