/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form !== "undefined" )
    {
        return;
    }

    /**
     * @class Form module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Form = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "form" ];
    };

    global.Zork.prototype.form = new global.Zork.Form();

    /**
     * @class Form element module
     * @constructor
     * @memberOf Zork.Form
     */
    global.Zork.Form.Element = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "form", "element" ];
    };

    global.Zork.Form.prototype.element = new global.Zork.Form.Element();

    var checkPattern = function ( pattern, value ) {
            if ( typeof pattern === "undefined" ||
                 pattern === null || pattern === "" )
            {
                return true;
            }

            var result  = true,
                regex   = new RegExp( "^" + pattern + "$" );

            if ( Array.isArray( value ) )
            {
                $.each( value, function ( _, val ) {
                    if ( result && !! regex.test( val ) )
                    {
                        result = false;
                    }
                } );
            }
            else
            {
                result = !! regex.test( value );
            }

            return result;
        },
        checkMin = function ( min, value ) {
            if ( typeof min === "undefined" ||
                 min === null || min === "" )
            {
                return true;
            }

            var result  = true;

            if ( Array.isArray( value ) )
            {
                $.each( value, function ( _, val ) {
                    if ( result && val < min )
                    {
                        result = false;
                    }
                } );
            }
            else
            {
                result = value >= min;
            }

            return result;
        },
        checkMax = function ( max, value ) {
            if ( typeof max === "undefined" ||
                 max === null || max === "" )
            {
                return true;
            }

            var result = true;

            if ( Array.isArray( value ) )
            {
                $.each( value, function ( _, val ) {
                    if ( result && val > max )
                    {
                        result = false;
                    }
                } );
            }
            else
            {
                result = value <= max;
            }

            return result;
        },
        checkMinlength = function ( min, value ) {
            if ( typeof min === "undefined" ||
                 min === null || min === "" )
            {
                return true;
            }

            return value.length >= min;
        },
        checkMaxlength = function ( max, value ) {
            if ( typeof max === "undefined" ||
                 max === null || max === "" )
            {
                return true;
            }

            return value.length <= max;
        },
        checkForbidden = function ( values, value ) {
            if ( typeof values === "undefined" ||
                 values === null || values === "" )
            {
                return true;
            }

            values = $.parseJSON( values );

            if ( ! Array.isArray( values ) && values.length === 0 )
            {
                return true;
            }

            return ! $.inArray( value, values );
        },
        checkToken = function ( form, token ) {
            if ( typeof token === "undefined" ||
                 token === null || token === "" )
            {
                return true;
            }

            if ( ! ( token in form[0].elements ) )
            {
                return false;
            }

            return $( form[0].elements[token] ).val();
        },
        checkIdentical = function ( form, token, value ) {
            var res = checkToken( form, token );

            if ( ! Boolean.isBoolean( res ) )
            {
                res = res == value;
            }

            return res;
        },
        checkLessThan = function ( form, token, value ) {
            var res = checkToken( form, token );

            if ( ! Boolean.isBoolean( res ) )
            {
                res = res < value;
            }

            return res;
        },
        checkMoreThan = function ( form, token, value ) {
            var res = checkToken( form, token );

            if ( ! Boolean.isBoolean( res ) )
            {
                res = res > value;
            }

            return res;
        },
        checkAlternate = function ( form, token, value ) {
            var res = checkToken( form, token );
            return value || res;
        },
        checkRpcs = function ( form, rpcs, value ) {
            if ( typeof rpcs === "undefined" ||
                 rpcs === null || rpcs === "" )
            {
                return null;
            }

            rpcs = String( rpcs )
                    .replace( /^\s+/, "" )
                    .replace( /\s+$/, "" )
                    .split( /\s+/ );

            if ( rpcs.length === 0 )
            {
                return null;
            }

            var result  = null,
                data    = $( form ).serializeArray(),
                context = {};

            $.each( data, function ( _, datum ) {
                context[datum.name] = datum.value;
            } );

            $.each( rpcs, function ( _, rpc ) {
                if ( result )
                {
                    return;
                }

                var res = js.core.rpc( rpc )( value, context );

                if ( res === true )
                {
                    // noop
                }
                else if ( res === null )
                {
                    result = "validate.rpc.returnNull";
                }
                else if ( res === false )
                {
                    result = "validate.rpc.returnFalse";
                }
                else if ( String.isString( res ) )
                {
                    if ( res.length )
                    {
                        result = res;
                    }
                }
                else if ( Array.isArray( res ) )
                {
                    if ( ! ( 0 in res ) )
                    {
                        result = "validate.rpc.returnEmpty";
                    }

                    if ( res[0] === null || res[0] === false )
                    {
                        if ( 1 in res )
                        {
                            result = res[1];
                        }
                        else if ( res[0] === null )
                        {
                            result = "validate.rpc.returnNull";
                        }
                        else if ( res[0] === false )
                        {
                            result = "validate.rpc.returnFalse";
                        }
                    }
                }
                else if ( Object.isObject( res ) )
                {
                    if ( ! ( "success" in res ) )
                    {
                        result = "validate.rpc.returnEmpty";
                    }

                    if ( res.success === null || res.success === false )
                    {
                        if ( "message" in res )
                        {
                            result = res.message;
                        }
                        else if ( res.success === null )
                        {
                            result = "validate.rpc.returnNull";
                        }
                        else if ( res.success === false )
                        {
                            result = "validate.rpc.returnFalse";
                        }
                    }
                }
                else
                {
                    result = "validate.rpc.returnUnknown";
                }
            } );

            return result;
        };

    /**
     * HTML5 compliance forms
     *
     * @memberOf Zork.Form
     */
    global.Zork.Form.prototype.html5 = function ( form )
    {
        form = $( form );

        form.find( ":input[placeholder]:not([title])" )
            .each( function () {
                var self = $( this );
                self.attr( "title", self.attr( "placeholder" ) );
            } );

        var novalidate  = false,
            validate    = function ( evt ) {
                if ( novalidate || form.attr( "novalidate" ) )
                {
                    return true;
                }

                var valid = true;

                form.find( ":input:not(:disabled)" )
                    .each( function () {
                        var self = $( this ),
                            emsg = false,
                            mult = !! self.attr( "multiple" ),
                            type = self.attr( "type" ) ||
                                   String( self[0].tagName ).toLowerCase(),
                            val  = self.val(),
                            hasv = typeof this.checkValidity !== "undefined",
                            hasc = typeof this.setCustomValidity !== "undefined",
                            isv  = hasv ? this.checkValidity() : true,
                            pars = {};

                        if ( type === "checkbox" )
                        {
                            val = this.checked ? self.attr( "value" ) : null;
                        }
                        else if ( type === "radio" )
                        {
                            val = form.find( ":input[name=\"" + self.attr( "name" ) + "\"]:checked" )
                                      .map( function () { return $( this ).attr( "value" ); } )
                                      .get();
                        }
                        else if ( type === "email" && mult )
                        {
                            val = val.replace( /^\s+/, "" )
                                     .replace( /\s+$/, "" )
                                     .split( /\s*,\s*/ );
                        }

                        if ( type === "password" )
                        {
                            var arr = new Array( String( val ).length + 1 );
                            pars.value = arr.join( '*' );
                        }
                        else
                        {
                            pars.value = String( val );
                        }

                        if ( hasv && ! isv )
                        {
                            if ( type === "datetime" )
                            {
                                self.val( val.replace( /([ T][0-9]{2}:[0-9]{2}):[0-9]{2}(Z|[+-]\d{2}:?\d{2})$/, '$1:00$2' ) );
                                isv = this.checkValidity();
                            }
                            else if ( type === "datetime-local" )
                            {
                                self.val( val.replace( /([ T][0-9]{2}:[0-9]{2}):[0-9]{2}$/, '$1:00' ) );
                                isv = this.checkValidity();
                            }
                        }

                        if ( ! hasv || ( ! isv && type === "radio" ) )
                        {
                            isv   = true;
                            emsg  = "";

                            if ( self.attr( "required" ) &&
                                 ( val === null || val.length === 0 ) )
                            {
                                isv   = false;
                                emsg  = "validate.required";
                            }
                            else if ( ! checkPattern( self.attr( "pattern" ), val ) )
                            {
                                isv   = false;
                                emsg  = "validate.regex";
                                pars.pattern = self.attr( "title" ) ||
                                               self.attr( "pattern" );
                            }
                            else if ( ! checkMin( self.attr( "min" ), val ) )
                            {
                                isv   = false;
                                emsg  = "validate.between.min";
                                pars.min = self.attr( "min" );
                            }
                            else if ( ! checkMax( self.attr( "max" ), val ) )
                            {
                                isv   = false;
                                emsg  = "validate.between.max";
                                pars.max = self.attr( "max" );
                            }
                            else if ( ! checkMaxlength( self.attr( "maxlength" ), val ) )
                            {
                                isv   = false;
                                emsg  = "validate.length.higher";
                                pars.max = self.attr( "maxlength" );
                                pars.length = val.length;
                            }
                            else
                            {
                                switch ( type )
                                {
                                    case 'color':
                                        if ( ! /^#[a-fA-F0-9]{6}$/.test( val ) )
                                        {
                                            isv   = false;
                                            emsg  = "validate.color";
                                        }
                                        break;

                                    case 'date':
                                        if ( ! /^\d{4,}-\d{2}-\d{2}$/.test( val ) )
                                        {
                                            isv   = false;
                                            emsg  = "validate.date";
                                        }
                                        break;

                                    case 'datetime':
                                        if ( ! /^\d{4,}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}(:\d{2}(\.\d+))?(Z|[+-]\d{2}:?\d{2})$/.test( val ) )
                                        {
                                            isv   = false;
                                            emsg  = "validate.datetime";
                                        }
                                        break;

                                    case 'datetime-local':
                                        if ( ! /^\d{4,}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}(:\d{2}(\.\d+))?$/.test( val ) )
                                        {
                                            isv   = false;
                                            emsg  = "validate.datetime-local";
                                        }
                                        break;

                                    case 'email':
                                        if ( ! /^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*$/.test( val ) )
                                        {
                                            isv   = false;
                                            emsg  = "validate.email";
                                        }
                                        break;

                                    case 'month':
                                        if ( ! /^\d{4,}-\d{2}$/.test( val ) )
                                        {
                                            isv   = false;
                                            emsg  = "validate.month";
                                        }
                                        break;

                                    case 'number':
                                    case 'range':
                                        if ( ! /^([1-9][0-9]*|0)(\.[0-9]+)?$/.test( val ) )
                                        {
                                            isv   = false;
                                            emsg  = "validate.number";
                                        }
                                        break;

                                    case 'time':
                                        if ( ! /^\d{2}:\d{2}(:\d{2}(\.\d+))?$/.test( val ) )
                                        {
                                            isv   = false;
                                            emsg  = "validate.month";
                                        }
                                        break;

                                    case 'url':
                                        if ( ! /^(https?|s?ftp):\/\/[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)(\/.*)?$/.test( val ) )
                                        {
                                            isv   = false;
                                            emsg  = "validate.url";
                                        }
                                        break;

                                    case 'week':
                                        if ( ! /^\d{4,}-W\d{2}?$/.test( val ) )
                                        {
                                            isv   = false;
                                            emsg  = "validate.week";
                                        }
                                        break;
                                }
                            }
                        }

                        if ( ! emsg && isv )
                        {
                            if ( ! checkMinlength( self.attr( "data-validate-minlength" ), val ) )
                            {
                                isv   = false;
                                emsg  = "validate.length.lower";
                                pars.min = self.attr( "data-validate-minlength" );
                                pars.length = val.length;
                            }
                            else if ( ! checkForbidden( self.attr( "data-validate-forbidden" ), val ) )
                            {
                                isv   = false;
                                emsg  = "validate.forbidden.forbidden";
                            }
                            else if ( ! checkIdentical( form, self.attr( "data-validate-identical" ), val ) )
                            {
                                isv   = false;
                                emsg  = "validate.identical.notMatch";
                            }
                            else if ( ! checkAlternate( form, self.attr( "data-validate-alternate" ), val ) )
                            {
                                isv   = false;
                                emsg  = "validate.alternate.noneGiven";
                            }
                            else if ( ! checkLessThan( form, self.attr( "data-validate-less-than" ), val ) )
                            {
                                isv   = false;
                                emsg  = "validate.lessThan.notLess";
                            }
                            else if ( ! checkMoreThan( form, self.attr( "data-validate-more-than" ), val ) )
                            {
                                isv   = false;
                                emsg  = "validate.moreThan.notMore";
                            }
                            else
                            {
                                emsg = checkRpcs( form, self.attr( "data-validate-rpcs" ), val );

                                if ( emsg && emsg.length )
                                {
                                    isv   = false;
                                }
                            }
                        }

                        if ( isv )
                        {
                            self.removeClass( "invalid" );

                            if ( hasv )
                            {
                                if ( hasc )
                                {
                                    try
                                    {
                                        this.setCustomValidity( "" );
                                    }
                                    catch ( e )
                                    { }
                                }
                                else
                                {
                                    self.tooltip( {
                                        "items": "*",
                                        "content": ""
                                    } );
                                }
                            }
                        }
                        else
                        {
                            self.addClass( "invalid" );

                            if ( emsg )
                            {
                                emsg = js.core.translate( emsg );

                                for ( var i in pars )
                                {
                                    emsg = emsg.replace( '%' + i + '%', pars[i] );
                                }

                                if ( hasv && hasc )
                                {
                                    try
                                    {
                                        this.setCustomValidity( emsg );
                                    }
                                    catch ( e )
                                    { }

                                    $( this ).one( "change", function () {
                                        try
                                        {
                                            this.setCustomValidity( "" );
                                        }
                                        catch ( e )
                                        { }
                                    } );
                                }
                                else
                                {
                                    self.tooltip( {
                                        "items": "*",
                                        "content": emsg
                                    } );
                                }
                            }
                        }

                        valid = valid && isv;
                    } );

                if ( ! valid )
                {
                    if ( ! ( 'checkValidity' in form[0] ) )
                    {
                        $( ":input.invalid:first" ).focus();
                        evt.preventDefault();
                    }
                }
            };

        form.find( ":input" )
            .each( function ( index, element ) {
                js.form.element.html5( this );
            } );

        form.on( "submit", validate )
            .on( "click", ":submit[formnovalidate]", function () {
                novalidate = true;
            } )
            .on( "click", ":submit:not([formnovalidate])", function ( evt ) {
                novalidate = false;
                return validate.call( this, evt );
            } )
            .on( "click", ":submit[formaction]", function () {
                if ( ! ( 'formAction' in this ) )
                {
                    form.attr( "action", $( this ).attr( "formaction" ) );
                }
            } )
            .on( "click", ":submit[formenctype]", function () {
                if ( ! ( 'formEnctype' in this ) )
                {
                    form.attr( "enctype", $( this ).attr( "formenctype" ) );
                }
            } )
            .on( "click", ":submit[formmethod]", function () {
                if ( ! ( 'formMethod' in this ) )
                {
                    form.attr( "method", $( this ).attr( "formmethod" ) );
                }
            } )
            .on( "click", ":submit[formtarget]", function () {
                if ( ! ( 'formTarget' in this ) )
                {
                    form.attr( "target", $( this ).attr( "formtarget" ) );
                }
            } );
    };

    /**
     * HTML5 compliance form-elements
     *
     * @memberOf Zork.Form
     */
    global.Zork.Form.Element.prototype.html5 = function ( element )
    {
        element = $( element );

        if ( element.data( "jsForm" ) == "basic" )
        {
            return;
        }

        var tag = String( element[0].tagName ).toLowerCase(),
            attrType = String( element.attr( "type" ) || tag ),
            realType = String( element.prop( "type" ) || tag );

        if ( attrType == realType )
        {
            switch ( attrType )
            {
                case 'color':
                    var val  = element[0].getAttribute( "value" ),
                        name = String( element.attr( "name" ) || "" ),
                        text = $( '<input type="text">' ).val( val );

                    element.prop( "disabled", ! val );

                    text.change( function () {
                        var val = text.val();
                        element.val( val )
                               .prop( "disabled", ! val )
                               .trigger( "change" );
                    } );

                    element.parent()
                           .on( "mouseenter mouseover mousemove", function () {
                               element.prop( "disabled", false );
                           } )
                           .on( "mouseleave mouseout", function () {
                               element.prop( "disabled", ! text.val() );
                           } );

                    element.change( function () {
                        text.val( element.prop( "disabled" ) ? "" : element.val() );
                    } );

                    element.before( text )
                           .css( {
                                "width": element.height(),
                                "min-width": "1em"
                           } );

                    if ( name && ! /\[\]$/.test( name ) )
                    {
                        element.before(
                            $( '<input type="hidden">' ).attr( {
                                "name"  : name,
                                "value" : ""
                            } )
                        );
                    }
                    break;
            }
        }
        else
        {
            switch ( attrType )
            {
                case 'color':
                    if ( ! element.is( '[data-js-type~="js.form.element.color"]' ) )
                    {
                        js.require( "js.form.element.color" )( element );
                    }
                    break;

                case 'date':
                    if ( ! element.is( '[data-js-type~="js.form.element.date"]' ) )
                    {
                        js.require( "js.form.element.date" )( element );
                    }
                    break;

                case 'datetime':
                case 'datetime-local':
                    if ( ! element.is( '[data-js-type~="js.form.element.dateTime"]' ) )
                    {
                        js.require( "js.form.element.dateTime" )( element );
                    }
                    break;

                case 'number':
                    if ( ! element.is( '[data-js-type~="js.form.element.number"]' ) )
                    {
                        js.require( "js.form.element.number" )( element );
                    }
                    break;

                case 'range':
                    if ( ! element.is( '[data-js-type~="js.form.element.range"]' ) )
                    {
                        js.require( "js.form.element.range" )( element );
                    }
                    break;

             /* case 'select':
                    js.style( "/styles/scripts/selectmenu.css" );
                    js.require( "jQuery.ui.selectmenu", function () {
                        var prefix = element.data( "jsSelectIconprefix" );
                        element.selectmenu( {
                            "icons": !!prefix,
                            "iconsPrefix": prefix || null,
                            "select" : function () {
                                element.trigger( "change" );
                            }
                        } );
                    } );
                    break; */

                case 'time':
                    if ( ! element.is( '[data-js-type~="js.form.element.time"]' ) )
                    {
                        js.require( "js.form.element.time" )( element );
                    }
                    break;
            }
        }
    };

    /**
     * Validator form
     *
     * @memberOf Zork.Form
     */
    global.Zork.Form.prototype.validator = function ( form )
    {
        form = $( form );

        var novalidate  = false,
            validate    = function ()
            {
                if ( novalidate || form.attr( "novalidate" ) )
                {
                    return true;
                }

                var result = true;

                form.find( "[data-js-validators]:input:not(:disabled)" )
                    .each( function () {
                            result = js.validate.validate( this ) && result;
                        } );

                if ( ! result )
                {
                    $( "[data-js-validators].ui-state-error:first" ).focus();
                }

                return result;
            };

        js.require( "js.validate", function () {
            form.submit( validate );

            form.find( ":submit[formnovalidate]" )
                .live( "click", function () {
                    novalidate = true;
                } );

            form.find( ":submit:not([formnovalidate])" )
                .live( "click", function () {
                    novalidate = false;
                    return validate.call( this );
                } );
        } );

        $( ":submit", form ).parent().inputset();
    };

    global.Zork.Form.prototype.validator.isElementConstructor = true;

    /**
     * Form cancel
     *
     * @memberOf Zork.Form
     */
    global.Zork.Form.prototype.cancel = function ( form, buttons )
    {
        form = $( form );

        var submitButtons = form.find( "button:submit" ),
            submitInputs = form.find( "input:submit" ),
            button, input, label;

        buttons = buttons || Object( form.data( "jsCancelButtons" ) );

        for ( label in buttons )
        {
            ( function ( lab, url ) {
                var click = null;

                if ( typeof url === "function" )
                {
                    click = url;
                }
                else
                {
                    click = function () { global.location.href = url; };
                }

                button = $( "<button type='button' />").html( lab );
                input = $( "<input type='button' />").val( lab );
                $( [ button[0], input[0] ] )
                    .addClass( "ui-button-cancel" )
                    .click( click );
                submitButtons.after( button );
                submitInputs.after( input );

            } (
                js.core.translate( label, form.attr( "lang" ) ||
                                          form.attr( "xml:lang" ) || null ),
                buttons[label]
            ) );
        }

        // $( ":button, :submit", form ).button();
        $( ":submit", form ).parent().inputset();
    };

    global.Zork.Form.prototype.cancel.isElementConstructor = true;

    /**
     * Button set
     *
     * @memberOf Zork.Form
     */
    global.Zork.Form.prototype.buttonset = function ( element )
    {
        element = $( element );
        element.buttonset();
    };

    global.Zork.Form.prototype.buttonset.isElementConstructor = true;

    /**
     * Input set
     *
     * @memberOf Zork.Form
     */
    global.Zork.Form.prototype.inputset = function ( element )
    {
        element = $( element );
        element.inputset();
    };

    global.Zork.Form.prototype.inputset.isElementConstructor = true;

} ( window, jQuery, zork ) );
