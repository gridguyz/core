/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js, undefined )
{
    "use strict";

    if ( typeof js.form.element.cssUnit !== "undefined" )
    {
        return;
    }

    var intUnits = [ "px", "%" ],
        defUnits = "px|pt|pc|em|ex|cm|mm|in|%";

    /**
     * Css-units element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.cssUnit = function ( element )
    {
        element = $( element );
        element.hide();

        var negativ = ( element.attr( "pattern" )[0] == "-" ),
            units   = ( element.data( "jsUnits" ) || defUnits ).split( "|" ),
            origval = element.val(),
            value   = "" === origval ? null : parseFloat( origval ) || 0,
            postfix = origval.replace( /^-?[0-9]+(\.[0-9]+)?/, "" ),
            spinner = $( '<input type="number" />' ),
            unitsel = $( '<select />' ).css( { "width": "6ex", "min-width": "4ex" } ),
            update  = function () {
                element.val( null === value ? "" : String( value ) + postfix )
                       .trigger( "change" );
            };

        if ( ! postfix || ! ~ units.indexOf( postfix ) )
        {
            postfix = units[0];
        }

        element.after( unitsel )
               .after( spinner );

        unitsel.on( "change click", function () {
            postfix = unitsel.val();
            var intStep = ~ intUnits.indexOf( postfix );

            if ( spinner.prop( "type" ) == "number" )
            {
                spinner.attr( "step", intStep ? 1 : 0.01 );
            }
            else
            {
                spinner.spinner( "option", "step", intStep ? 1 : 0.01 );
            }

            if ( intStep )
            {
                spinner.val( parseInt( spinner.val(), 10 ) );
            }

            update();
        } );

        $.each( units, function ( index, unit ) {
            unitsel.append( $( "<option />", {
                "value"     : unit,
                "text"      : unit,
                "selected"  : unit == postfix
            } ) );
        } );

        spinner.on( "change keyup blur", function () {
            var v = spinner.val();
            value = "" === v ? null : parseFloat( spinner.val() ) || 0;
            update();
        } );

        if ( spinner.prop( "type" ) == "number" )
        {
            spinner.attr( {
                "min"   : negativ ? null : "0",
                "step"  : ~ intUnits.indexOf( postfix ) ? 1 : 0.01
            } ).val( value );
        }
        else
        {
            spinner.spinner( {
                "min"   : negativ ? undefined : 0,
                "step"  : ~ intUnits.indexOf( postfix ) ? 1 : 0.01,
                "value" : value,
                "stop"  : function ( event ) {
                    var change = $.Event( "change", {
                        "originalEvent": event
                    } );

                    spinner.trigger( change );
                    element.trigger( change );
                }
            } );
        }

     /* element.fixedspinner( {
            "min": negativ ? undefined : 0,
            "postfix": postfix,
            "allowedPostfixes": units,
            "stop": function () {
                element.trigger( "change" );
            }
        } ); */
    };

    global.Zork.Form.Element.prototype.cssUnit.isElementConstructor = true;

} ( window, jQuery, zork ) );
