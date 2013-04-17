//Extend WYMeditor
WYMeditor.editor.prototype.alignment = function() {
    var wym = this,
        box = jQuery(this._box),
        sides = {
            "NONE": "",
            "LEFT": "left",
            "RIGHT": "right",
            "CENTER": "center",
            "JUSTIFY": "justify"
        };

    //construct buttons' html
    var html =
        '<li class="wym_tools_alignment_none"><a title="Alignment: none" name="AlignmentNone" href="#"' +
            ' style="background-image: url(' + wym._options.basePath +
                'plugins/alignment/icon_none.gif)">' +
            "Alignment: none" +
        '</a></li>' +
        '<li class="wym_tools_alignment_left"><a title="Alignment: left" name="AlignmentLeft" href="#"' +
            ' style="background-image: url(' + wym._options.basePath +
                'plugins/alignment/icon_left.gif)">' +
            "Alignment: left" +
        '</a></li>' +
        '<li class="wym_tools_alignment_center"><a title="Alignment: center" name="AlignmentCenter" href="#"' +
            ' style="background-image: url(' + wym._options.basePath +
                'plugins/alignment/icon_center.gif)">' +
            "Alignment: center" +
        '</a></li>' +
        '<li class="wym_tools_alignment_right"><a title="Alignment: right" name="AlignmentRight" href="#"' +
            ' style="background-image: url(' + wym._options.basePath +
                'plugins/alignment/icon_right.gif)">' +
            "Alignment: right" +
        '</a></li>' +
        '<li class="wym_tools_alignment_justify"><a title="Alignment: justify" name="AlignmentJustify" href="#"' +
            ' style="background-image: url(' + wym._options.basePath +
                'plugins/alignment/icon_justify.gif)">' +
            "Alignment: justify" +
        '</a></li>' +
        '<li class="wym_tools_alignment_float_left"><a title="Float: left" name="AlignmentFloatLeft" href="#"' +
            ' style="background-image: url(' + wym._options.basePath +
                'plugins/alignment/icon_float_left.gif)">' +
            "Float: left" +
        '</a></li>' +
        '<li class="wym_tools_alignment_float_right"><a title="Float: right" name="AlignmentFloatRight" href="#"' +
            ' style="background-image: url(' + wym._options.basePath +
                'plugins/alignment/icon_float_right.gif)">' +
            "Float: right" +
        '</a></li>';

    //add the button to the tools box
    box.find(wym._options.toolsSelector + wym._options.toolsListSelector)
        .append(html);

    var align = function (side, forceFloat)
    {
        var selection = null;
        if (wym._selected_image || forceFloat)
        {
            selection = jQuery(!!wym._selected_image ? wym._selected_image : wym.selected());
            switch (side)
            {
                case sides.NONE:
                case sides.LEFT:
                case sides.RIGHT:
                    selection.css("float", side);
                    break;
                case sides.CENTER:
                case sides.JUSTIFY:
                    selection.css("float", "");
                    break;
                default:
                    WYMEditor.console.error("Unknown alignment", side);
            }
        }
        else
        {
            selection = jQuery(wym.selected());
            switch (side)
            {
                case sides.NONE:
                case sides.LEFT:
                case sides.RIGHT:
                case sides.CENTER:
                case sides.JUSTIFY:
                    selection.css("text-align", side);
                    break;
                default:
                    WYMEditor.console.error("Unknown alignment", side);
            }
        }
        return false;
    }

    //handle click event of none
    box.find('li.wym_tools_alignment_none a').click(function() {
        return align(sides.NONE);
    });

    //handle click event of left
    box.find('li.wym_tools_alignment_left a').click(function() {
        return align(sides.LEFT);
    });

    //handle click event of right
    box.find('li.wym_tools_alignment_right a').click(function() {
        return align(sides.RIGHT);
    });

    //handle click event of center
    box.find('li.wym_tools_alignment_center a').click(function() {
        return align(sides.CENTER);
    });

    //handle click event of justify
    box.find('li.wym_tools_alignment_justify a').click(function() {
        return align(sides.JUSTIFY);
    });

    //handle click event of left
    box.find('li.wym_tools_alignment_float_left a').click(function() {
        return align(sides.LEFT, true);
    });

    //handle click event of right
    box.find('li.wym_tools_alignment_float_right a').click(function() {
        return align(sides.RIGHT, true);
    });
};
