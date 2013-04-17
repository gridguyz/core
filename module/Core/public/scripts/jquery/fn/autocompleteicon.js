/**
 * autocompletetitle
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @package jQuery
 * @subpackage fn
 */
( function ( $ ) {

    $.widget( "ui.autocompleteicon", $.ui.autocomplete, {
        "_renderItem" : function ( ul, item ) {
            var icon = $( "<span>" )
                        .attr( {
                            "src": item.icon,
                            "alt": item.alt || ""
                        } )
                        .css( {
                            "float": "left",
                            "width": "2em",
                            "height": "2em",
                            "margin-right": "1ex"
                        } ),
                link = $( "<a />" )
                        .text( item.label )
                        .prepend( icon );

            if ( item.icon )
            {
                icon.css( {
                    "background": 'url("' + item.icon + '") no-repeat 50% 50%'
                } );
            }

            if ( item.description )
            {
                link.append(
                    $( "<span>" )
                        .text( item.description )
                        .append( '<br style="clear:both;height:1px;" />' )
                        .css( {
                            "display": "block",
                            "font-size": "smaller"
                        } )
                );
            }

            return $( "<li />" )
                    .attr( "title", item.title || "" )
                    .css( "position", "relative" )
                    .append( link )
                    .appendTo( ul );
	}
    } );

} ( jQuery ) );
