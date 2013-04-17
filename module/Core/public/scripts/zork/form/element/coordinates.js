/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.coordinates !== "undefined" )
    {
        return;
    }

    /**
     * Coordinate picker form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.coordinates = function ( element )
    {
        js.require( "zork.gmap.liveMap", function () {
            element = $( element );

            var form,
                lat  = element.data( "jsCoordinatesLatitude" )  || "input:eq(0)",
                lng  = element.data( "jsCoordinatesLongitude" ) || "input:eq(1)",
                calc = element.data( "jsCoordinatesCalculate" ),
                pick = element.data( "jsCoordinatesPick" ),
                from = element.data( "jsCoordinatesCalculateFrom" ),
                calcLabel = element.data( "jsCoordinatesCalculateLabel" ) ||
                       js.core.translate( "default.calculate" ),
                pickLabel = element.data( "jsCoordinatesPickLabel" ) ||
                       js.core.translate( "default.pick" );

            lat = $( lat, element );
            lng = $( lng, element );

            if ( js.gmap.isStatic || ! lat.length || ! lng.length )
            {
                return;
            }

            form = lat[0].form || lng[0].form;

            if ( from )
            {
                from = from.replace( /\s+/, "" ).split( "," );

                if ( calc )
                {
                    calc = $( calc, element );

                    if ( ! calc.length )
                    {
                        calc = false;
                    }
                }

                if ( ! calc )
                {
                    calc = $( "<input />" ).
                        attr( "type", "button" ).
                        appendTo( element ).
                        button();
                }

                calc.val( calcLabel ).click( function ()
                {
                    var addr = [];

                    from.forEach( function ( fr )
                    {
                        addr.push( form.elements[fr].value );
                    } );

                    js.gmap.liveMap.location( addr.join( ", " ), function ( res )
                    {
                        if ( res )
                        {
                            lat.val( res.lat() )
                               .trigger( "change" );
                            lng.val( res.lng() )
                               .trigger( "change" );
                        }
                    } );
                } );
            }

            if ( pick )
            {
                pick = $( pick, element );

                if ( ! pick.length )
                {
                    pick = false;
                }
            }

            if ( ! pick )
            {
                pick = $( "<input />" ).
                    attr( "type", "button" ).
                    appendTo( element ).
                    button();
            }

            pick.val( pickLabel ).click( function ()
            {
                this.blur();

                var container = $( "<div />" ).
                                css( {
                                    "width"  : "600px",
                                    "height" : "500px"
                                } ),
                    map       = $( "<div />" ).
                                appendTo( container ),
                    layer     = js.core.layer( container ),
                    coords    = null,
                    marker    = null,
                    center    = null,
                    zoom      = 16,
                    setCenter = function ( lat, lng, z )
                    {
                        center  = lat + "," + lng;
                        zoom    = z;

                        var m = map.data( "js-gmap" );

                        if ( m && m.setCenter )
                        {
                            m.setCenter( new google.maps.LatLng(
                                lat,
                                lng
                            ) );

                            m.setZoom( zoom );
                        }
                    },
                    drop      = function ( crd )
                    {
                        coords[0] = crd.lat();
                        coords[1] = crd.lng();
                    },
                    remove    = function ()
                    {
                        this.setMap( null );
                        marker = this;
                        coords = null;
                    },
                    addmrk    = function ( crd )
                    {
                        if ( ! coords )
                        {
                            if ( ! marker )
                            {
                                marker = new google.maps.Marker( {
                                    "draggable" : true
                                } );

                                google.maps.event.addListener( marker, "dragend",
                                    function ( evt )
                                    {
                                        drop.call( marker, evt.latLng );
                                    }
                                );

                                google.maps.event.addListener( marker, "click",
                                    function ( evt )
                                    {
                                        remove.call( marker, evt.latLng );
                                    }
                                );

                                google.maps.event.addListener( marker, "rightclick",
                                    function ( evt )
                                    {
                                        remove.call( marker, evt.latLng );
                                    }
                                );
                            }

                            marker.setPosition( crd );
                            marker.setMap( this );
                            coords = [ crd.lat(), crd.lng() ];
                        }
                    };

                if ( lat.val() && lng.val() )
                {
                    coords = [
                        parseFloat( lat.val() ),
                        parseFloat( lng.val() )
                    ];

                    center = coords.join( "," );
                }
                else
                {
                    if ( navigator.geolocation )
                    {
                        navigator.geolocation.getCurrentPosition( function ( p ) {
                            setCenter(
                                p.coords.latitude,
                                p.coords.longitude,
                                16
                            );
                        } );
                    }

                    var region  = navigator.userLanguage || navigator.language ||
                                  navigator.browserLanguage,
                        match   = /^([a-z]+)[-_](.*)$/.exec( region );

                    region = String( match ? match[2] :
                        region.substring( 0, 2 ) ).toLowerCase();

                    js.gmap.liveMap.location( region, function ( res ) {
                        if ( res )
                        {
                            setCenter( res.lat(), res.lng(), 5 );
                        }
                    }, region );

                    center = "0,0";
                    zoom   = 2;
                }

                js.gmap.liveMap( {
                    "appendTo"  : map,
                    "center"    : center,
                    "width"     : 600,
                    "height"    : 470,
                    "click"     : addmrk,
                    "context"   : addmrk,
                    "zoom"      : zoom,
                    "markers"   : coords ? [ {
                        "locations" : [ center ],
                        "draggable" : true,
                        "drop"      : drop,
                        "click"     : remove,
                        "context"   : remove
                    } ] : null
                } );

                container.append(
                    $( "<div />" ).append(
                        $( "<input />" ).
                            attr( "type", "button" ).
                            val( js.core.translate( "default.pick" ) ).
                            click( function ()
                            {
                                if ( coords )
                                {
                                    lat.val( coords[0] )
                                       .trigger( "change" );
                                    lng.val( coords[1] )
                                       .trigger( "change" );
                                }
                                else
                                {
                                    lat.val( "" )
                                       .trigger( "change" );
                                    lng.val( "" )
                                       .trigger( "change" );
                                }

                                layer();
                            } )
                    ).append(
                        $( "<input />" ).
                            attr( "type", "button" ).
                            val( js.core.translate( "default.cancel" ) ).
                            click( layer )
                    ).css( {
                        "text-align": "right",
                        "padding-top": "5px"
                    } ).buttonset()
                );
            } );
        } );
    };

    global.Zork.Form.Element.prototype.coordinates.isElementConstructor = true;

} ( window, jQuery, zork ) );
