/**
 * autocompletegroup
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @package jQuery
 * @subpackage fn
 */
( function ( $ ) {

    $.widget( "ui.autocompletegroup", $.ui.autocomplete, {
        "_renderMenu" : function( ul, items ) {
            var that = this,
                currentGroup = "";

            $.each( items, function( index, item ) {
                if ( String( item.group ) !== currentGroup ) {
                    ul.append( "<li class='ui-autocomplete-group'>" + item.group + "</li>" );
                    currentGroup = item.group;
                }

                that._renderItemData( ul, item );
            } );
        }
    } );

} ( jQuery ) );
