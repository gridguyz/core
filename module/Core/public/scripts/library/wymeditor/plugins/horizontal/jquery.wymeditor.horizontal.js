//Extend WYMeditor
WYMeditor.editor.prototype.horizontal = function() {
    var wym = this,
        box = jQuery(this._box);

    //construct the button's html
    var html =
        '<li class="wym_tools_horizontal"><a title="Horizontal rule" name="InsertSpecials" href="#"' +
            ' style="background-image: url(' + wym._options.basePath +
                'plugins/horizontal/icon_horizontal.gif)">' +
            "Horizontal rule" +
        '</a></li>';

    //add the button to the tools box
    box.find(wym._options.toolsSelector + wym._options.toolsListSelector)
        .append(html);

    //handle click event
    box.find('li.wym_tools_horizontal a').click(function() {
        wym.insert('<hr noshade="noshade" />');
        return false;
    });
};
