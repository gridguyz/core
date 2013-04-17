//Extend WYMeditor
(function () {
    
    var originalInit = WYMeditor.INIT_DIALOG;

    WYMeditor.INIT_DIALOG = function (index) {

        var result      = originalInit(index),
            wym         = (window.opener || window.parent).WYMeditor.INSTANCES[index],
            selected    = !!wym._selected_image ? wym._selected_image : wym.selected(),
            sStamp      = wym.uniqueStamp(),
            dialogType  = jQuery(wym._options.dialogTypeSelector).val(),
            linkSubmit  = jQuery(wym._options.dialogLinkSelector + " " + wym._options.submitSelector),
            imageSubmit = jQuery(wym._options.dialogImageSelector + " " + wym._options.submitSelector),
            pasteSubmit = jQuery(wym._options.dialogPasteSelector + " " + wym._options.submitSelector),
            tableSubmit = jQuery(wym._options.dialogTableSelector + " " + wym._options.submitSelector),
            submits     = [];

        if (linkSubmit.length > 0) submits.push( linkSubmit[0] );
        if (imageSubmit.length > 0) submits.push( imageSubmit[0] );
        if (pasteSubmit.length > 0) submits.push( pasteSubmit[0] );
        if (tableSubmit.length > 0) submits.push( tableSubmit[0] );

        if (dialogType == WYMeditor.DIALOG_LINK)
        {
            //ensure that we select the link to populate the fields
            if (selected && selected.tagName && selected.tagName.toLowerCase != WYMeditor.A)
                selected = jQuery(selected).parentsOrSelf(WYMeditor.A);

            //fix MSIE selection if link image has been clicked
            if (!selected && wym._selected_image)
                selected = jQuery(wym._selected_image).parentsOrSelf(WYMeditor.A);
        }

        if (linkSubmit.length > 0)
        {
            linkSubmit.parent().before(
                '<div class="row row-indent"><label style="display: inline; width: auto;">' +
                    '<input type="checkbox" class="wym_target" name="target" value="_blank"' +
                        (!!selected && selected.attr("target") == "_blank" ?
                        ' checked="checked"' : '') + ' /> Open in new window' +
                '</label></div>'
            );

            linkSubmit.die();
            linkSubmit.unbind();
            linkSubmit.click(function () {
                var sUrl = jQuery(wym._options.hrefSelector).val();
                if (sUrl.length > 0) {
                    var link;

                    if (selected[0] && selected[0].tagName.toLowerCase() == WYMeditor.A) {
                        link = selected;
                    } else {
                        wym._exec(WYMeditor.CREATE_LINK, sStamp);
                        link = jQuery("a[href=" + sStamp + "]", wym._doc.body);
                    }

                    link.attr(WYMeditor.HREF, sUrl)
                        .attr(WYMeditor.TITLE, jQuery(wym._options.titleSelector).val())
                        .attr("target", jQuery(".wym_target")[0].checked ? jQuery(".wym_target").val() : null);
                }
                window.close();
                return false;
            });
        }

        if (imageSubmit.length > 0)
        {
            var align = "none",
                margin = 0,
                width = 100;

            if (!!selected && selected.tagName && selected.tagName.toLowerCase() == "img")
            {
                selected = jQuery(selected);
                align = selected.css("float") || align;
                margin = parseInt(selected.css("margin"), 10) || margin;
                width = parseInt(selected.css("width"), 10) || 0;
            }

            imageSubmit.parent().before(
                '<div class="row"><label>Alignment</label>' +
                    '<select class="wym_align" name="align">' +
                        '<option value="">none</option>' +
                        '<option value="left"' +
                            (align == "left" ? ' selected="selected"' : "") +
                        '>left</option>' +
                        '<option value="right"' +
                            (align == "right" ? ' selected="selected"' : "") +
                        '>right</option>' +
                    '</select>' +
                '</div>'
            );

            imageSubmit.parent().before(
                '<div class="row"><label>Margin</label>' +
                    '<input class="wym_margin" name="margin" value="' +
                        margin + '" type="text" />' +
                '</div>'
            );

            imageSubmit.parent().before(
                '<div class="row"><label>Width</label>' +
                    '<input class="wym_width" name="width" value="' +
                        width + '" type="text" />' +
                '</div>'
            );

            if ("slider" in jQuery.fn) {
                var drawSlider = function (cls) {
                        var inp = jQuery("." + cls);
                        inp.after('<div class="' + cls + '-slider" style="display: inline-block; width: 120px;"></div>');
                        var sli = jQuery("." + cls + "-slider");
                        sli.slider({
                            "value": inp.val(),
                            "min": 0,
                            "max": Math.max(3 * inp.val(), 100),
                            "step": 5,
                            "slide": function (event, ui) {
                                inp.val(ui.value);
                            }
                        });
                        inp.keyup(function () {
                            var val = parseInt(jQuery(this).val(), 10),
                                sl_min = sli.slider("option", "min"),
                                sl_max = sli.slider("option", "max");

                            sli.slider("value", Math.min(Math.max(val, sl_min), sl_max));
                        });
                    };

                drawSlider("wym_margin");
                drawSlider("wym_width");
            }

            imageSubmit.die();
            imageSubmit.unbind();
            imageSubmit.click(function () {
                var sUrl = jQuery(wym._options.srcSelector).val();
                if (sUrl.length > 0) {
                    wym._exec(WYMeditor.INSERT_IMAGE, sStamp);

                    var image = jQuery("img[src$=" + sStamp + "]", wym._doc.body);

                    image.attr(WYMeditor.SRC, sUrl)
                        .attr(WYMeditor.TITLE, jQuery(wym._options.titleSelector).val())
                        .attr(WYMeditor.ALT, jQuery(wym._options.altSelector).val())
                        .css("float", jQuery(".wym_align").val() || null)
                        .css("margin", jQuery(".wym_margin").val() + "px");

                    if (jQuery(".wym_width").val() > 0)
                    {
                        image.css("width", jQuery(".wym_width").val() + "px");
                        image.css("height", null);
                    }
                }
                window.close();
                return false;
            });
        }

        if (submits.length > 0)
        {
            jQuery(submits).
                parents( "fieldset" ).
                find( ".row" ).
                inputset();
        }

        return result;

    };
    
})();
