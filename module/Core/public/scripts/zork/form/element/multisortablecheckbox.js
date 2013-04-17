/**
 * Form functionalities for multiVoteOption form element
 * @package zork
 * @subpackage form
 * @author Sipi
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.multiSortableCheckbox !== "undefined" )
    {
        return;
    }

    /**
     * Multi vote option
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.multiSortableCheckbox = function ( element )
    {
        element = $( element );
        
        element.sortable({
//                        cancel: 'a',
                revert: true
//                start: events.startDrag,
//                stop: events.stopDrag
//                        cursor: "url(/images/modules/featuresVote/grabCursor.png)"
        });
    };

    global.Zork.Form.Element.prototype.multiSortableCheckbox.isElementConstructor = true;

} ( window, jQuery, zork ) );
