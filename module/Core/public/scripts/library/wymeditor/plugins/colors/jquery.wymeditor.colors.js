//Extend WYMeditor
WYMeditor.editor.prototype.colors = function(params) {
    var wym = this,
        box = jQuery(this._box),
        foreChosen = "#808080",
        backChosen = "#808080",
        palette = (!!params && !!params.palette &&
                params.palette instanceof Array) ? params.palette : [
            "#FFFFFF", "#E0E0E0", "#C0C0C0", "#A0A0A0", "#808080", "#606060", "#404040", "#202020", "#000000",
            "#FFC0C0", "#FFA0A0", "#FF8080", "#FF4040", "#FF0000", "#E00000", "#A00000", "#800000", "#400000",
            "#FFFFC0", "#FFFFA0", "#FFFF80", "#FFFF40", "#FFFF00", "#E0E000", "#A0A000", "#808000", "#404000",
            "#C0FFC0", "#A0FFA0", "#80FF80", "#40FF40", "#00FF00", "#00E000", "#00A000", "#008000", "#004000",
            "#C0FFFF", "#A0FFFF", "#80FFFF", "#40FFFF", "#00FFFF", "#00E0E0", "#00A0A0", "#008080", "#004040",
            "#C0C0FF", "#A0A0FF", "#8080FF", "#4040FF", "#0000FF", "#0000E0", "#0000A0", "#000080", "#000040",
            "#FFC0FF", "#FFA0FF", "#FF80FF", "#FF40FF", "#FF00FF", "#E000E0", "#A000A0", "#800080", "#400040"
        ];

    //construct buttons' html
    var html =
        '<li class="wym_tools_colors_font"><a title="Font color" name="ColorsFont" href="#"' +
            ' style="float: left; background-image: url(' + wym._options.basePath +
                'plugins/colors/icon_font.gif);">' +
            "Font color" +
        '</a><a title="Choose font color" name="ColorsFontChoose" href="#"' +
            ' style="float: left; border-left-width: 1px; width: 7px; background-color: ' + foreChosen +
                '; background-image: url(' + wym._options.basePath + 'plugins/colors/icon_dropdown.gif);">' +
            "Choose font color" +
        '</a></li>' +
        '<li class="wym_tools_colors_background"><a title="Background color" name="ColorsBackground" href="#"' +
            ' style="float: left; background-image: url(' + wym._options.basePath +
                'plugins/colors/icon_background.gif);">' +
            "Background color" +
        '</a><a title="Choose background color" name="ColorsBackgroundChoose" href="#"' +
            ' style="float: left; border-left-width: 1px; width: 7px; background-color: ' + backChosen +
                '; background-image: url(' + wym._options.basePath + 'plugins/colors/icon_dropdown.gif);">' +
            "Choose background color" +
        '</a></li>' +
        '<li class="wym_tools_colors_remove"><a title="Remove formatting" name="ColorsRemove" href="#"' +
            ' style="background-image: url(' + wym._options.basePath +
                'plugins/colors/icon_remove.gif);">' +
            "Remove formatting" +
        '</a></li>';

    //add the button to the tools box
    box.find(wym._options.toolsSelector + wym._options.toolsListSelector).append(html);

    var forecolor = function (color) {
            var sel = wym.selected();
            if (!!sel) sel = jQuery(sel);
            wym.wrap('<span style="color: ' + color +
                (!!sel && !!sel[0].style.backgroundColor ? "; background-color: " +
                sel.css("background-color") : "") + '">', "</span>");
            return false;
        },
        backcolor = function (color) {
            var sel = wym.selected();
            if (!!sel) sel = jQuery(sel);
            wym.wrap('<span style="background-color: ' + color +
                (!!sel && !!sel[0].style.color ? "; color: " +
                sel.css("color") : "") + '">', "</span>");
            return false;
        },
        remove = function () {
            var sel = wym._selected_image ?
                    wym._selected_image : wym.selected();
            jQuery(sel).attr("style", null);
            try { wym.unwrap(); } catch (e) { }
            return false;
        },
        chooser = function (element, set) {
            var parent = element.parent(),
                container = jQuery("<div/>"),
                close = function ()
                {
                    container.remove();
                };

            parent.css("position", "relative");

            container.css({
                "position": "absolute",
                "top": element.outerHeight(),
                "left": 0,
                "width": "126px",
                "padding": "2px",
                "background-color": "#eeeeee",
                "border": "1px solid #888888",
                "text-align": "center"
            });

            palette.forEach(function (color) {
                container.append('<button style="background-color: ' + color + ';"></button>');
            });

            jQuery("button", container).css({
                "border": "none",
                "padding": "0px",
                "margin": "2px",
                "width": "10px",
                "height": "10px",
                "cursor": "pointer"
            }).click(function () {
                var color = jQuery(this).css("background-color");
                element.css("background-color", color);
                set(color);
                close();
                return false;
            });

            if ("ColorPicker" in jQuery.fn)
            {
                container.append(
                    '<hr noshade="noshade" />' +
                    '<a href="#" title="More colors" name="MoreColors">More colors</a>'
                );
                var picker = jQuery("a", container);
                picker.css({
                    "text-align": "center",
                    "margin": "2px auto",
                    "background-image": "url(" + wym._options.basePath +
                        "plugins/colors/icon_more.png)"
                });
                picker.ColorPicker({
                    "color": "#888888",
                    "onSubmit": function(hsb, hex) {
                        var color = "#" + hex;
                        set(color);
                        element.css("background-color", color);
                        picker.ColorPickerHide();
                        close();
                        return true;
                    },
                    "onChange": function (hsb, hex) {
                        var color = "#" + hex;
                        set(color);
                        element.css("background-color", color);
                        return true;
                    }
                });
            }

            container.appendTo(parent);

            jQuery(document).add(jQuery(wym._doc)).one("click", close);
            return false;
        };

    //handle click event of font
    box.find('li.wym_tools_colors_font a:first-child').click(function() {
        return forecolor(foreChosen);
    });

    //handle click event of background
    box.find('li.wym_tools_colors_background a:first-child').click(function() {
        return backcolor(backChosen);
    });

    //handle click event of font choose
    box.find('li.wym_tools_colors_font a:last-child').click(function() {
        return chooser(jQuery(this), function (chosen) {
            forecolor(foreChosen = chosen);
        });
    });

    //handle click event of background choose
    box.find('li.wym_tools_colors_background a:last-child').click(function() {
        return chooser(jQuery(this), function (chosen) {
            backcolor(backChosen = chosen);
        });
    });

    //handle click event of remove
    box.find('li.wym_tools_colors_remove a').click(remove);
};

WYMeditor.WymClassMozilla.prototype.openBlockTag = function(tag, attributes)
{
    var attributes = this.validator.getValidTagAttributes(tag, attributes);

    // Handle Mozilla styled spans
    if (tag == 'span' && attributes.style) {
         var new_tag = this.getTagForStyle(attributes.style);
        if (new_tag) {
            tag = new_tag;
            this._tag_stack.pop();
            this._tag_stack.push(tag);
            attributes.style = '';
        }
    }

    this.output += this.helper.tag(tag, attributes, true);
};

WYMeditor.WymClassSafari.prototype.openBlockTag = function(tag, attributes)
{
    var attributes = this.validator.getValidTagAttributes(tag, attributes);

    // Handle Safari styled spans
    if (tag == 'span' && attributes.style) {
        var new_tag = this.getTagForStyle(attributes.style);
        if (new_tag) {
            tag = new_tag;
            this._tag_stack.pop();
            this._tag_stack.push(tag);
            attributes.style = '';

            // Should fix #125 - also removed the xhtml() override
            if(typeof attributes['class'] == 'string') {
                attributes['class'] = attributes['class'].replace(/apple-style-span/gi, '');
            }
        }
    }

    this.output += this.helper.tag(tag, attributes, true);
};
