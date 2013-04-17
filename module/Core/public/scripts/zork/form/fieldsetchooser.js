/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.fieldsetChooser !== "undefined" )
    {
        return;
    }

    /**
     * Button-set element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.prototype.fieldsetChooser = function ( element )
    {
        element = $( element );

        var form    = $( element[0].form || element.parents( "form:first" ) ),
            prefix  = element.data( "jsFieldsetchooserPrefix" ) || "",
            postfix = element.data( "jsFieldsetchooserPostfix" ) || "",
            change  = function () {
                var val = element.val(),
                    nam = val ? "[name=\"" + prefix + val + postfix + "\"]" : "",
                    not = val ? ":not(" + nam + ")" : "";

                form.find( "fieldset" + nam )
                    .show()
                    .parent( "dd" )
                    .prev( "dt" )
                    .show();

                form.find( "fieldset" + not )
                    .hide()
                    .parent( "dd" )
                    .prev( "dt" )
                    .hide();
            };

        if ( form.length )
        {
            element.on( "change click", change );
            change();
        }
    };

    global.Zork.Form.prototype.fieldsetChooser.isElementConstructor = true;

} ( window, jQuery, zork ) );
