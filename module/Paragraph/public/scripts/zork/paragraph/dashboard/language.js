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
        element = $(element);

        form = $(form);

        var before = element.html();

        form.find('input[type=checkbox]').on( 'change' , function() {
            var htmlContent     = '',
                uri             = element.find('div.language-container').data('jsUri').split('|'),
                translation     = element.find('div.language-container').data('jsTranslation').split('|'),
                locale          = element.find('div.language-container').data('jsLocale').split('|'),
                activeLocale    = element.find('div.language-container').data('jsActiveLocale').split('|'),
                checkbox        = form.find(':input[type=checkbox]'),
                i;

            for ( i in uri )
            {
                if ( checkbox[i].checked )
                {
                    htmlContent += '<li class="list-item list-item-' + locale[i] + '"><a href="' + uri[i];

                    if ( activeLocale == locale[i] )
                    {
                        htmlContent += '" class="active';
                    }

                    htmlContent += '">' + translation[i] + '</a></li>';
                }
            }

            element.find('ul.language-list').html(htmlContent);
        });

        return {
            "update": function () {
                before = element.html();
            },
            "restore": function () {
                element.html(before);
            }
        };

    };

} ( window, jQuery, zork ) );
