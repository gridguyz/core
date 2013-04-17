WYMeditor.SKINS['zork'] =
{
    "init": function (wym)
    {
        var post = function (wym)
        {
            var toolsClasses = ".wym_tools_undo, .wym_tools_redo, .wym_tools_link," +
                    " .wym_tools_unlink, .wym_tools_ordered_list, .wym_tools_unordered_list," +
                    " .wym_tools_table, .wym_tools_paste, .wym_tools_superscript," +
                    " .wym_tools_subscript, .wym_tools_indent, .wym_tools_outdent",
                advancedClasses = ".wym_tools_colors_font, .wym_tools_colors_background," +
                    " .wym_tools_colors_remove, .wym_tools_horizontal, .wym_tools_html",
                notSpecialClasses = ".wym_tools_strong, .wym_tools_emphasis," +
                    " .wym_tools_image, .wym_tools_preview",
                alignmentClasses = ".wym_tools_alignment_none, .wym_tools_alignment_left," +
                    " .wym_tools_alignment_center, .wym_tools_alignment_right, .wym_tools_alignment_justify," +
                    " .wym_tools_alignment_float_left, .wym_tools_alignment_float_right";

            var root = jQuery(wym._options.toolsSelector + " > ul"),
                tools = jQuery('<li class="wym_tools_tools wym_dropdown"><a title="Tools">Tools</a><ul/></li>'),
                advanced = jQuery('<li class="wym_tools_advanced wym_dropdown"><a title="Advanced tools">Advanced tools</a><ul/></li>'),
                special = jQuery('<li class="wym_tools_special wym_dropdown"><a title="Special tools">Special tools</a><ul/></li>'),
                alignment = jQuery('<li class="wym_tools_alignment wym_dropdown"><a title="Alignment">Alignment</a><ul/></li>');

            jQuery("ul", tools).append(jQuery(toolsClasses, root));
            jQuery("ul", advanced).append(jQuery(advancedClasses, root));
            jQuery("ul", alignment).append(jQuery(alignmentClasses, root));
            jQuery("ul", special).append(jQuery(wym._options.toolsSelector + " > ul > li").not(notSpecialClasses));

            tools.appendTo(root);
            advanced.appendTo(root);
            special.appendTo(root);
            alignment.appendTo(root);
        }

        if (typeof wym._options.postInit == "function")
        {
            var originalPost = wym._options.postInit;
            wym._options.postInit = function (wym)
            {
                originalPost.call(wym, wym);
                post.call(wym, wym);
            }
        }
        else
            wym._options.postInit = post;

        //move the containers panel to the top area
        jQuery(wym._options.containersSelector + ', '
          + wym._options.classesSelector, wym._box)
          .appendTo(jQuery("div.wym_area_top", wym._box))
          .addClass("wym_dropdown");

        //render following sections as buttons
        jQuery(wym._box).find(wym._options.toolsSelector)
          .addClass("wym_buttons");

        //hide following sections
        jQuery(wym._options.classesSelector, wym._box).hide();

        //make hover work under IE < 7
        jQuery(wym._box).find(".wym_section").hover(
            function ()
            {
                jQuery(this).addClass("hover");
            },
            function ()
            {
                jQuery(this).removeClass("hover");
            }
        );
    }
};
