/**
 * autocompletetitle
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @package jQuery
 * @subpackage fn
 */
( function ( $ ) {

    $.widget( "ui.autocompletetitle", $.ui.autocomplete, {
        "_renderItem" : function ( ul, item ) {
            return $( "<li />" )
                    .append( $( "<a />" ).text( item.label )
                                         .attr( "title", item.title ) )
                    .appendTo( ul );
	}
    } );

} ( jQuery ) );
