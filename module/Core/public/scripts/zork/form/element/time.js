/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.time !== "undefined" )
    {
        return;
    }

    /**
     * Time form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.time = function ( element )
    {
        js.require( ["jQuery.fn.timepicker",
            "jQuery.timepicker.regional." + js.core.defaultLocale.substr( 0, 2 ) ],
            function () {
                element = $( element );

                element.timepicker( {
                    "hourGrid"  : 4,
                    "minuteGrid": 10,
                    "secondGrid": 10,
                    "showSecond": true,
                 // "value"     : element.val(),
                    "timeFormat": "HH:mm:ss"
                } );

                var changeId = null,
                    changeFunc = function ()
                    {
                        changeId = null;

                        var vals = element.val().split( ":", 3 ),
                            date = new Date();

                        date.setHours(
                            parseInt( vals[0], 10 ) || 0,
                            parseInt( vals[1], 10 ) || 0,
                            parseInt( vals[2], 10 ) || 0
                        );

                        element.timepicker( "setDate", date )
                               .trigger( "change" );
                    };

                element.on( "keyup click", function () {
                    var val = element.val();

                    if ( val )
                    {
                        if ( changeId ) { clearTimeout( changeId ); }
                        changeId = setTimeout( changeFunc, 1000 );
                    }
                } );
            }
        );
    };

    global.Zork.Form.Element.prototype.time.isElementConstructor = true;

} ( window, jQuery, zork ) );
