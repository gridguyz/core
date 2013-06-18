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
            var link = $( "<a>" ).text( item.label );

            if ( item.description )
            {
                link.append(
                    $( "<span>" ).text( item.description )
                                 .append( '<br style="clear:both;height:1px;" />' )
                                 .css( {
                                     "display": "block",
                                     "font-size": "smaller"
                                 } )
                );
            }

            return $( "<li>" ).attr( "title", item.title || "" )
                              .css( "position", "relative" )
                              .append( link )
                              .appendTo( ul );
	}
    } );

} ( jQuery ) );
