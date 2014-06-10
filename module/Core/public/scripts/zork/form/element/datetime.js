/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js, undefined )
{
    "use strict";

    if ( typeof js.form.element.dateTime !== "undefined" )
    {
        return;
    }

    /**
     * Datetime form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.dateTime = function ( element )
    {
        var lang = js.core.defaultLocale.substr( 0, 2 );
        element = $( element );

        if ( element.data( "jsFormElementHidden" ) === undefined )
        {
            element.data( "jsFormElementHidden", true );
        }

        js.require( [ "jQuery.fn.timepicker",
            "jQuery.datepicker.regional." + lang,
            "jQuery.timepicker.regional." + lang ],
            function () {
                var minDate = Date.parse( element.attr( "min" ) ||
                        element.data( "jsDateMin" ) ),
                    maxDate = Date.parse( element.attr( "max" ) ||
                        element.data( "jsDateMax" ) ),
                    format = element.data( "jsDateFormat" ) ||
                        $.datepicker.regional[lang].altFormat || "d MM yy.",
                    trigger = $( '<button class="ui-datetimepicker-trigger" ' +
                                 'type="button"></button>' )
                              .button( {
                                  "label": "...",
                                  "icons": {
                                      "primary": "ui-icon-calculator"
                                  }
                              } ),
                    set = function () {
                        var formatted;
                        if ( element.val() )
                        {
                            var datetime = element.val().split( /[\sT]+/, 2 ),
                                date = datetime[0].split( "-", 3 );

                            date[1]--;
                            date = Date.UTC.apply( Date, date );

                            if ( date )
                            {
                                formatted = $.datepicker.formatDate(
                                    format,
                                    new Date( date )
                                ) + " " + datetime[1];
                            }
                        }

                        if ( ! formatted )
                        {
                            formatted = "...";
                        }

                        trigger.button( "option", "label", formatted );
                    };

                element.after( trigger );

                if ( ( element.attr( "type" ) === "datetime" ||
                     element.attr( "type" ) === "datetime-local" )  &&
                     ! element.data( "jsFormElementHidden" ) &&
                     ( js.core.browser.opera ) )
                {
                    trigger.click( function () {
                        element.click();
                        element.focus();
                    } );
                }
                else
                {
                    var timeZone = "",
                        offset   = "+0000",
                        offsetHour,
                        offsetMin;

                    if ( element.attr( "type" ) === "datetime" ||
                         element.data( "jsDatetimeZone" ) )
                    {
                        timeZone    = "z";
                        offset      = - ( new Date ).getTimezoneOffset();
                        offsetMin   = offset % 60;
                        offsetHour  = parseInt( ( offset - offsetMin ) / 60 );
                        offsetMin   = Math.abs( offsetMin );
                        offsetHour  = Math.abs( offsetHour );
                        offset      = ( offset < 0 ? "-" : "+" )
                                    + ( offsetHour < 10 ? "0" : "" )
                                    + offsetHour
                                    + ( offsetMin < 10 ? "0" : "" )
                                    + offsetMin;
                    }

                    element.datetimepicker( {
                        "hourGrid": 4,
                        "minuteGrid": 10,
                        "secondGrid": 10,
                        "showSecond": true,
                        // "value": element.val(),
                        "minDate": minDate ? new Date( minDate ) : null,
                        "maxDate": maxDate ? new Date( maxDate ) : null,
                        "dateFormat": "yy-mm-dd",
                        "timeFormat": "HH:mm:ss" + timeZone,
                        "defaultTimezone": offset,
                        "timezoneIso8601": false,
                        "separator": "T",
                        "onSelect": set
                    } );

                    trigger.click( function () {
                        element.datetimepicker( "show" );
                    } );
                }

                element.change( set );
                if ( element.val() )
                {
                    set();
                }

                if ( element.data( "jsFormElementHidden" ) )
                {
                    element.css( {
                        "width": "1px",
                        "min-width": "1px",
                        "max-width": "1px",
                        "visibility": "hidden"
                    } );

                    var validators = String( element.data( "jsValidators" ) || "" ).split( /[\s,]+/ ),
                        req_attr = element.attr( "required" ),
                        required = req_attr === "true" || req_attr === "required" ||
                            validators.indexOf( "required" ) >= 0;

                    if ( ! required )
                    {
                        var empty = $( '<button type="button" ' +
                                       'class="js-date-empty"></button>' )
                                    .button( {
                                        "text": false,
                                        "icons": {
                                            "primary": "ui-icon-trash"
                                        }
                                    } );

                        trigger.after( empty );
                        empty.click( function () {
                            element.val( "" )
                                   .trigger( "change" );
                        } );
                    }
                }
                else
                {
                    element.addClass( "ui-controls-after" );
                }

                element.parent().inputset();
            }
        );
    };

    global.Zork.Form.Element.prototype.dateTime.isElementConstructor = true;

} ( window, jQuery, zork ) );
