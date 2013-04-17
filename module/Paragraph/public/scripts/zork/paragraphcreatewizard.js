/**
 * ParagraphCreateWizard
 *
 * @package zork
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */

( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraphCreateWizard !== "undefined" )
    {
        return;
    }

    /**
     * Loads paragraphCreateWizard requirements
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.prototype.paragraphCreateWizard = function ( element )
    {
        js.style('/styles/scripts/paragraphcreatewizard.css');

        js.require( "js.form.imageradiogroup");

        js.form.imageRadioGroup(  element );
    }

    global.Zork.prototype.paragraphCreateWizard.isElementConstructor = true;

} ( window, jQuery, zork ) );
