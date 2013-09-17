/**
 * Validation functionalities
 * @package zork
 * @subpackage validate
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.validate !== "undefined" )
    {
        return;
    }

    /**
     * @class Validate module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Validate = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "validate" ];
    };

    global.Zork.prototype.validate = new global.Zork.Validate();

    var ok = {
        "result": true,
        "message": ""
    };

    var fault = function ( text )
    {
        return {
            "result": false,
            "message": text
        };
    };

    /**
     * Checks if the supplied argument is not null.
     *
     * @param {Object} val
     * @returns {Object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.required = function ( val )
    {
        var result = ! Object.isEmpty( val );

        return {
            "result": result,
            "message": result ? "" : js.core.translate( "validate.required" )
        };
    };

    /**
     * Checks if the supplied arguments is neither null.
     *
     * @param {Object} val1
     * @param {Object} val2
     * @returns {Object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.alternate = function ( val1, val2 )
    {
        var result = ! ( Object.isEmpty( val1 ) && Object.isEmpty( val2 ) );

        return {
            "result": result,
            "message": result ? "" : js.core.translate( "validate.alternate" )
        };
    };

    /**
     * Checks if the supplied arguments is equal.
     *
     * @param {Object} val1
     * @param {Object} val2
     * @returns {Object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.equal = function ( val1, val2 )
    {
        var result = String( val1 ) === String( val2 );

        return {
            "result": result,
            "message": result ? "" : js.core.translate( "validate.equal" )
        };
    };

    /**
     * Checks if the supplied arguments is in incremental order.
     *
     * @param {Object} val1
     * @param {Object} val2
     * @param {Boolean} equal
     * @returns {Object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.lessThan = function ( val1, val2, equal )
    {
        if ( Object.isEmpty( val1 ) || Object.isEmpty( val2 ) )
        {
            return ok;
        }

        var result = equal ? val1 <= val2 : val1 < val2;

        return {
            "result": result,
            "message": result ? "" : js.core.translate( "validate.than.less" ) +
                ( equal ? " " + js.core.translate( "validate.than.equal" ) : "" )
        };
    };

    /**
     * Checks if the supplied arguments is in incremental order.
     *
     * @param {Object} val1
     * @param {Object} val2
     * @param {Boolean} equal
     * @returns {Object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.moreThan = function ( val1, val2, equal )
    {
        if ( Object.isEmpty( val1 ) || Object.isEmpty( val2 ) )
        {
            return ok;
        }

        var result = equal ? val1 >= val2 : val1 > val2;

        return {
            "result": result,
            "message": result ? "" : js.core.translate( "validate.than.more" ) +
                ( equal ? " " + js.core.translate( "validate.than.equal" ) : "" )
        };
    };

    /**
     * Check if supplied value matches suppliued pattern
     *
     * @param {String} val Value to be checked
     * @param {RegExp|String} pattern
     * @param {String} invalidmsg
     * @returns {Object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.pattern = function ( val, pattern, invalidmsg )
    {
        if ( Object.isEmpty( val ) )
        {
            return ok;
        }

        if ( ! RegExp.isRegExp( pattern ) )
        {
            pattern = new RegExp( "^" + pattern + "$" );
        }

        invalidmsg = js.core.translate( invalidmsg || "validate.pattern" );

        var result = pattern.test( val );

        return {
            "result": result,
            "message": result ? "" : invalidmsg.
                replace( "%value%", val ).
                replace( "%pattern%", pattern.source )
        };
    };

    /**
     * Check if supplied value contains a valid integer.
     *
     * @param {string} val Value to be checked
     * @returns {object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.integer = function ( val )
    {
        return this.pattern( val, "(-?[1-9][0-9]*|0)",
            "validate.invalidInteger" );
    };

    /**
     * Check if supplied value contains a valid scalar.
     *
     * @param {string} val Value to be checked
     * @returns {object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.scalar = function ( val )
    {
        return this.pattern( val, "(-?[1-9][0-9]*|0)(\\.[0-9]+)?",
            "validate.invalidScalar" );
    };

    /**
     * Check if supplied value contains a only numeric characters.
     *
     * @param {string} val Value to be checked
     * @returns {object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.numeric = function ( val )
    {
        return this.pattern( val, "[0-9]+", "validate.invalidNumeric" );
    };

    /**
     * Check if supplied value contains a only numeric characters.
     *
     * @param {string} val Value to be checked
     * @returns {object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.alpha = function ( val )
    {
        return this.pattern( val, "[A-Za-z]+", "validate.invalidAlpha" );
    };

    /**
     * Check if supplied value contains a only numeric characters.
     *
     * @param {string} val Value to be checked
     * @returns {object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.alnum = function ( val )
    {
        return this.pattern( val, "[A-Za-z0-9]+", "validate.invalidAlnum" );
    };

    /**
     * Check if supplied value is a valid email
     *
     * @param {string} val Value to be checked
     * @returns {object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.email = function ( val )
    {
        return this.pattern( val, "([\\w-\\.]+)@((\\[[0-9]{1,3}\\.[0-9]{1,3}" +
            "\\.[0-9]{1,3}\\.)|(([\\w-]+\\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\\]?)",
            "validate.invalidEmail" );
    };

    /**
     * Check if supplied value is a valid url
     *
     * @param {string} val Value to be checked
     * @returns {object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.url = function ( val )
    {
        return this.pattern( val, "((https?|s?ftp|gopher)://[a-z\\.-]+/?.*|" +
            "mailto:([\\w-\\.]+)@((\\[[0-9]{1,3}\\.[0-9]{1,3}\\." +
            "[0-9]{1,3}\\.)|(([\\w-]+\\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\\]?))",
            "validate.invalidUrl" );
    };

    /**
     * Checks if supplied value is in the min-max range.
     * Optional parameter is to tell that value can be equeal to
     * min and max values.
     *
     * @param {Number} val Supplied value to be measured.
     * @param {Number} min Minimum value.
     * @param {Number} max Maximum value.
     * @param {Boolean} [inclusive=true] Should the minimum and maximum values
     *        be included in the range.
     * @returns {Object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.between = function ( val, min, max, inclusive )
    {
        if ( Object.isEmpty( val ) || ( Object.isUndefined( min )
            && Object.isUndefined( max ) ) )
        {
            return ok;
        }

        val = String( val );
        if ( ! Object.isUndefined( min ) ) { min = String( min ); }
        if ( ! Object.isUndefined( max ) ) { max = String( max ); }
        if ( Object.isUndefined( inclusive ) ) { inclusive = true; }
        if ( inclusive === "false" ) { inclusive = false; }

        val = val.indexOf( "." ) >= 0 ?
            parseFloat( val ) : parseInt( val, 10 );

        if ( ! Object.isUndefined( min ) )
        {
            min = min.indexOf( "." ) >= 0 ?
                parseFloat( min ) : parseInt( min, 10 );
        }

        if ( ! Object.isUndefined( max ) )
        {
            max = max.indexOf( "." ) >= 0 ?
                parseFloat( max ) : parseInt( max, 10 );
        }

        if ( Object.isUndefined( min ) )
        {
            if ( inclusive && ( val <= max ) ) { return ok; }
            else if ( ! inclusive && ( val < max ) ) { return ok; }
        }
        else if ( Object.isUndefined( max ) )
        {
            if ( inclusive && ( val >= min ) ) { return ok; }
            else if ( ! inclusive && ( val > min ) ) { return ok; }
        }
        else
        {
            if ( inclusive && ( val >= min ) && ( val <= max ) ) { return ok; }
            else if ( ! inclusive && ( val > min ) && ( val < max ) ) { return ok; }
        }

        var text = js.core.translate( "validate.between" );
        text = text.replace(
            [ /%value%/g, /%min%/g, /%max%/g, /%inclusive%/g ],
            [ val, min, max, inclusive ?
                    js.core.translate( "validate.between.inclusive" ) :
                    js.core.translate( "validate.between.notInclusive" ) ]
        );
        return fault( text );
    };

    /**
     * Checks if supplied value's length is in the min-max range.
     * (always inclusive)
     *
     * @param {Number} val Supplied value to be measured.
     * @param {Number} min Minimum length.
     * @param {Number} max Maximum length.
     * @returns {Object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.length = function ( val, min, max )
    {
        if ( Object.isEmpty( val ) )
        {
            return ok;
        }

        val = String( val );

        if ( ! Object.isUndefined( max ) )
        {
            min = parseInt( min, 10 );

            if ( val.length < min )
            {
                return fault( js.core.translate( "validate.length.lower" ).replace(
                    [ /%value%/g, /%length%/g, /%min%/g ],
                    [ val, val.length, min ]
                ) );
            }
        }

        if ( ! Object.isUndefined( max ) )
        {
            max = parseInt( max, 10 );

            if ( val.length > max )
            {
                return fault(
                    js.core.translate( "validate.length.higher" ).replace(
                        [ /%value%/g, /%length%/g, /%max%/g ],
                        [ val, val.length, max ]
                    )
                );
            }
        }

        return ok;
    };

    /**
     * Checks if the given value is not exist in the array.
     *
     * @param {Object} needle
     * @param {Array} haystack
     * @returns {Object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.forbidden = function ( needle, haystack )
    {
        if ( Object.isEmpty( needle ) )
        {
            return ok;
        }

        var valid = true;

        if ( ! Array.isArray( haystack ) )
        {
            haystack = $.parseJSON( haystack );

            if ( ! Array.isArray( haystack ) )
            {
                throw new TypeError();
            }
        }

        haystack.forEach(
            function ( item )
            {
                valid = valid && ( String( needle ) !== String( item ) );
            }
        );

        return {
            "result": valid,
            "message": valid ? "" : js.core.translate( "validate.forbidden" ).
                replace( "%value%", needle )
        };
    };

    /**
     * Checks if the given value exists in the array.
     *
     * @param {Object} needle
     * @param {Array} haystack
     * @returns {Object} {result, message}
     * @type Object
     */
    global.Zork.Validate.prototype.inArray = function ( needle, haystack )
    {
        if ( Object.isEmpty( needle ) )
        {
            return ok;
        }

        var in_array = false;

        if ( ! Array.isArray( haystack ) )
        {
            haystack = $.parseJSON( haystack );

            if ( ! Array.isArray( haystack ) )
            {
                throw new TypeError();
            }
        }

        haystack.forEach(
            function ( item )
            {
                in_array = in_array || ( String( needle ) === String( item ) );
            }
        );

        return {
            "result": in_array,
            "message": in_array ? "" : js.core.translate( "validate.inArray" ).
                replace( "%value%", needle )
        };
    };

    /**
     * Checks if the given value valid by an rpc
     *
     * @param {String} val
     * @param {Array} vals
     * @param {String} method
     * @param {Array} params additional arguments
     * @type Object
     */
    global.Zork.Validate.prototype.rpc = function ( val, vals, method, params )
    {
        if ( Object.isEmpty( val ) )
        {
            return ok;
        }

        if ( ! Array.isArray( params ) && ! Object.isUndefined( params ) )
        {
            params = $.parseJSON( params );

            if ( ! Array.isArray( params ) && ! Object.isObject( params ) )
            {
                params = [];
            }
        }
        else
        {
            params = [];
        }

        if ( Array.isArray( params ) )
        {
            params.unshift( vals );
            params.unshift( val );
        }
        else
        {
            params.value = val;
            params.values = vals;
        }

        var result = js.core.rpc( method ).invoke( params );

        if ( Boolean.isBoolean( result ) )
        {
            if ( result )
            {
                return ok;
            }
            else
            {
                return fault( js.core.translate( "validate.rpc.default" ) );
            }
        }
        else if ( String.isString( result ) )
        {
            return result ? fault( js.core.translate( result ) ) : ok;
        }
        else if ( Array.isArray( result ) )
        {
            return {
                "result": result.shift(),
                "message": js.core.translate( result.shift() )
            };
        }
        else if ( Object.isObject( result ) )
        {
            result.message = js.core.translate( result.message );
            return result;
        }
        else
        {
            return fault( js.core.translate( "validate.rpc.error" ) );
        }
    };

    /**
     * Places css class and tooltip on invalid input field.
     *
     * @param {Object|String} input jQuery object or the selector itself.
     * @param {String} error_text
     */
    global.Zork.Validate.prototype.markInvalid = function ( input, error_text )
    {
        js.style( "/styles/scripts/validate.css" );

        input = $( input );
        input.addClass( "ui-state-error" );

        if ( input.hasClass( "ui-controls-before" ) )
        {
            input.prevAll( ".ui-input, .ui-button" ).
                addClass( "ui-state-error" );
        }
        if ( input.hasClass( "ui-controls-after" ) )
        {
            input.nextAll( ".ui-input, .ui-button" ).
                addClass( "ui-state-error" );
        }

        /* tooltip */
        input.attr( "title", error_text );
        js.require( "js.ui.toolTip", function ( toolTip ) {
            toolTip( input, {
                "event": "hover focus",
                "position": input.hasClass( "ui-controls-after" ) ? (
                    input.hasClass( "ui-controls-before" ) ? "s" : "e"
                ) : "w"
            } );
        } );
    };

    /**
     * Clears the invalid markings on the input field.
     *
     * @param {Object|String} input jQuery object or the selector itself.
     */
    global.Zork.Validate.prototype.markValid = function ( input )
    {
        input = $( input );
        input.removeClass( "ui-state-error" );

        if ( input.hasClass( "ui-controls-before" ) )
        {
            input.prevAll( ".ui-input, .ui-button" ).
                removeClass( "ui-state-error" );
        }
        if ( input.hasClass( "ui-controls-after" ) )
        {
            input.nextAll( ".ui-input, .ui-button" ).
                removeClass( "ui-state-error" );
        }

        /* tooltip */
        input.attr( "title", "" );
        input.attr( "original-title", "" );
    };

    /**
     * Validtors used in validateWith() / validate()
     * @type Object
     */
    global.Zork.Validate.prototype.singleValidators = {
        "alnum": [],
        "alpha": [],
        "between": [ "min", "max", "inclusive" ],
        "email": [],
        "forbidden": [ "values" ],
        "inArray": [ "values" ],
        "integer": [],
        "length": [ "min", "max" ],
        "numeric": [],
        "pattern": [ "pattern", "errorMessage" ],
        "required": [],
        "scalar": [],
        "url": []
    };

    /**
     * Validtors used in validateWith() / validate()
     * @type Object
     */
    global.Zork.Validate.prototype.multiValidators = {
        "alternate": [],
        "equal": [],
        "lessThan": [ "equal" ],
        "moreThan": [ "equal" ]
    };

    /**
     * Validtors used in validateWith() / validate()
     * @type Object
     */
    global.Zork.Validate.prototype.sumValidators = {
        "rpc": [ "method", "params" ]
    };

    var label = function ( input )
    {
        input = $( input );

        var label = input.attr( "id" );
        if ( label )
        {
            label = $( 'label[for="' + label + '"]' );
        }
        if ( ! label || label.size() < 1 )
        {
            label = input.closest( "label" );
        }
        if ( label.size() < 1 )
        {
            return input.attr( "title" ) || input.attr( "name" );
        }

        return label.text().replace( /^\s+/, "" ).replace( /\s+$/, "" );
    };

    /**
     * Validates with all available validator
     * @param {Object} input
     */
    global.Zork.Validate.prototype.validate = function ( input )
    {
        input = $( input );

        var validatrs   = String( input.data( "jsValidators" ) || "" ).split( /[\s,]+/ ),
            form        = input.closest( "form" ),
            name        = input.attr( "name" ),
            type        = input.attr( "type" ),
            val         = input.val();

        if ( type == "checkbox" )
        {
            if ( "[]" == name.substr( -2 ) )
            {
                val = form.find( ":input[type='checkbox'][name='" + name + "']:checked" )
                          .map( function () {
                              return $( this ).val();
                          } )
                          .get();
            }
            else if ( ! input.prop( "checked" ) )
            {
                val = "";
            }
        }

        if ( type == "radio" )
        {
            val = form.find( ":input[type='radio'][name='" + name + "']:checked" )
                      .val();
        }

        if ( Array.isArray( validatrs ) )
        {
            var sum = true,
                vals = null,
                message = "",
                self = this;
            validatrs.forEach( function ( v )
            {
                var data = "js" + v.toUpperCaseFirst(),
                    args = [ val ], vlr, out;

                if ( typeof self.singleValidators[v] !== "undefined" )
                {
                    vlr = self.singleValidators[v];

                    vlr.forEach( function ( param ) {
                        args.push(
                            input.attr( param.toLowerCase() ) ||
                            input.data( data + param.toUpperCaseFirst() )
                        );
                    } );

                    out = self[v].apply( self, args );
                    sum = sum && out.result;
                    message = message || out.message;
                }
                else if ( typeof self.multiValidators[v] !== "undefined" )
                {
                    vlr = self.multiValidators[v];
                    var field = form.find( '[name="' +
                            input.data( data + "Field" ) +
                        '"]' );
                    args.push( field.val() );

                    vlr.forEach( function ( param ) {
                        args.push(
                            input.attr( param.toLowerCase() ) ||
                            input.data( data + param.toUpperCaseFirst() )
                        );
                    } );

                    out = self[v].apply( self, args );
                    out.message = out.message.
                        replace( "%field%", label( field ) );
                    sum = sum && out.result;
                    message = message || out.message;
                }
                else if ( typeof self.sumValidators[v] !== "undefined" )
                {
                    if ( vals == null )
                    {
                        vals = {};

                        form.serializeArray().forEach( function ( field ) {
                            vals[ field.name ] = field.value;
                        } );
                    }

                    vlr = self.sumValidators[v];
                    args.push( vals );

                    vlr.forEach( function ( param ) {
                        args.push(
                            input.attr( param.toLowerCase() ) ||
                            input.data( data + param.toUpperCaseFirst() )
                        );
                    } );

                    out = self[v].apply( self, args );
                    sum = sum && out.result;
                    message = message || out.message;
                }
                else
                {
                    js.console.warn( "Not valid validator: ", v );
                }
            } );

            if ( sum )
            {
                this.markValid( input );
                return true;
            }
            else
            {
                this.markInvalid( input, message );
                return false;
            }
        }

        return true;
    };

} ( window, jQuery, zork ) );
