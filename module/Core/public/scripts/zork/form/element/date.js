/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js, undefined )
{
    "use strict";

    if ( typeof js.form.element.date !== "undefined" )
    {
        return;
    }

    /**
     * Date form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.date = function ( element )
    {
        var lang = js.core.defaultLocale.substr( 0, 2 );
        element = $( element );

        if ( element.data( "jsFormElementHidden" ) === undefined )
        {
            element.data( "jsFormElementHidden", true );
        }

        js.require( "jQuery.datepicker.regional." + lang,
            function () {
                var format = element.data( "jsDateFormat" ) ||
                        $.datepicker.regional[lang].altFormat || "d MM yy.",
                    trigger = $( '<button class="ui-datepicker-trigger" ' +
                                 'type="button">...</button>' )
                              .button( {
                                  "icons": {
                                      "primary": "ui-icon-calculator"
                                  }
                              } ),
                    set = function ()
                    {
                        var formatted;
                        if ( element.val() )
                        {
                            var date = element.val().split( "-", 3 );
                            date[1]--;
                            date = Date.UTC.apply( Date, date );

                            if ( date )
                            {
                                formatted = $.datepicker.formatDate(
                                    format,
                                    new Date( date )
                                );
                            }
                        }

                        if ( ! formatted )
                        {
                            formatted = "...";
                        }

                        trigger.val( formatted );
                    };

                element.after( trigger );

                if ( ( element.attr( "type" ) === "date" )  &&
                     ! element.data( "jsFormElementHidden" ) )
                {
                    trigger.click( function () {
                        js.console.log("az");
                        element.click();
                        element.focus();
                    } );
                }
                else
                {
                    element.datepicker( {
                        // "value": element.val(),
                        "minDate": element.attr( "min" ) || element.data( "jsDateMin" ),
                        "maxDate": element.attr( "max" ) || element.data( "jsDateMax" ),
                        "dateFormat": "yy-mm-dd",
                        "onSelect": set
                    } );

                    trigger.click( function () {
                        js.console.log("ez");
                        element.datepicker( "show" );
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
                                       'class="js-date-empty">&nbsp;</button>' )
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
            } );
    };

    global.Zork.Form.Element.prototype.date.isElementConstructor = true;

} ( window, jQuery, zork ) );
