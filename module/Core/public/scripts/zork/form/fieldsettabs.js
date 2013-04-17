/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.fieldsetTabs !== "undefined" )
    {
        return;
    }

    js.require( "js.ui.tabs" );

    /**
     * Fieldset as tabs element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.prototype.fieldsetTabs = function ( element )
    {
        element     = $( element );
        var tabs    = $( "<div />" ),
            navs    = $( "<ul />" ).appendTo( tabs ),
            active  = element.data( "jsTabsActive" ) || null,
            search  = element.data( "jsFieldsettabsSearch" ) || "> dl > dd",
            count   = 0,
            prefix  = String( element.attr( "id" ) ||
                              element.attr( "name" ) ||
                              js.generateId() ) + "-";

        element.find( search + " > fieldset" )
               .each( function () {
                   count++;
                   var attrs    = {},
                       fieldset = $( this ),
                       legend   = fieldset.find( "> legend:first" ),
                       id       = prefix + String( fieldset.attr( "id" ) ||
                                                   fieldset.attr( "name" ) ||
                                                   js.generateId() );

                   fieldset.each( function () {
                       var i    = 0,
                           attr = this.attributes,
                           l    = attr.length,
                           a;

                       for ( ; i < l; ++i )
                       {
                           a = attr.item( i );
                           attrs[a.nodeName] = a.nodeValue;
                       }
                   } );

                   if ( "id" in attrs )
                   {
                       id = attrs.id;
                   }
                   else
                   {
                       attrs.id = id;
                   }

                   navs.append(
                       $( "<li />" ).append(
                           $( "<a />" ).attr( "href", "#" + id )
                                       .text( legend.text() )
                       )
                   );

                   legend.remove();
                   tabs.append(
                       $( "<div />" ).attr( attrs )
                                     .append( fieldset.children() )
                   );

                   if ( fieldset.parent()[0] != element[0] )
                   {
                       fieldset.parent()
                               .remove();
                   }
                   else
                   {
                       fieldset.remove();
                   }
               } );

        if ( count > 0 )
        {
            tabs.prependTo( element )
                .data( "jsTabsEvent", element.data( "jsTabsEvent" ) )
                .data( "jsTabsActive", active ? ( prefix + active ) : null )
                .data( "jsTabsPlacement", element.data( "jsTabsPlacement" ) )
                .data( "jsTabsCollapsible", element.data( "jsTabsCollapsible" ) );

            js.ui.tabs( tabs );
            js.core.parseDocument( tabs );
        }
        else
        {
            navs = null;
            tabs = null;
        }
    };

    global.Zork.Form.prototype.fieldsetTabs.isElementConstructor = true;

} ( window, jQuery, zork ) );
