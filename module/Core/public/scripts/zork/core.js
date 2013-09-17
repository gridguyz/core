/**
 * Core js-functionalities
 * @package zork
 * @subpackage core
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */

/**
 * HTML5 shiv - for IE html5 compatibility
 */
( function ( global ) {
    if ( ! /*@cc_on!@*/ 0 ) return;
    var e = [ 'abbr', 'article', 'aside', 'audio', 'bb', 'bdi', 'canvas', 'datagrid',
              'datalist', 'details', 'figure', 'figcaption', 'footer', 'header',
              'hgroup', 'keygen', 'mark', 'menu', 'meter', 'nav', 'output',
              'progress', 'section', 'source', 'time', 'track', 'video' ],
        i = e.length;
    while ( i-- ) global.document.createElement( e[i] );
} ( window ) );

( function ( global, $ )
{
    "use strict";

    if ( typeof global.zork !== "undefined" )
    {
        return;
    }

    /**
     * @class Zork Framework
     * @constructor.
     * @namespace
     */
    global.Zork = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork" ];
    };

    var js = global.js = global.zork = new global.Zork();

    /**
     * Add slashes
     *
     * @param {String} str text to add slashes
     * @param {Boolean} bothSlashes add ' escape too
     * @returns {String}
     * @type String
     */
    var addSlashes = function ( str, bothSlashes )
    {
        str = str.replace( "\\", "\\\\" ).
            replace( '"', '\\"' ).
            replace( String.fromCharCode( 0 ), "\\x00" ).
            replace( "\n", "\\n" );

        if ( !! bothSlashes )
        {
            str = str.replace( "'", "\\'" );
        }

        return str;
    };

    /**
     * @type Boolean
     */
    var documentReady = false;

    if ( typeof Object.defineProperty === "function" )
    {
        if ( typeof Object.isExtensible === "function" )
        {
            /**
             * Extends an object with a property
             *
             * @param {Object} obj
             * @param {String} prop
             * @param {Object} value
             * @param {Boolean} w
             * @type undefined
             */
            global.Zork.prototype.property =
            function definePropertyOrigWiExt( obj, prop, value, w )
            {
                w = !! w;

                if ( Object.isExtensible( obj ) )
                {
                    Object.defineProperty( obj, prop, {
                        "value"         : value,
                        "writeable"     : w,
                        "configurable"  : false,
                        "enumerable"    : false
                    } );
                }
                else
                {
                    obj[prop] = value;
                }
            };
        }
        else
        {
            global.Zork.prototype.property =
            function definePropertyOrigWoExt( obj, prop, value, w )
            {
                w = !! w;

                try
                {
                    Object.defineProperty( obj, prop, {
                        "value"         : value,
                        "writeable"     : w,
                        "configurable"  : false,
                        "enumerable"    : false
                    } );
                }
                catch ( e )
                {
                    obj[prop] = value;
                }
            };
        }
    }
    else if ( typeof Object.prototype.__defineGetter__ === "function" )
    {
        global.Zork.prototype.property =
        function definePropertyGetSet( obj, prop, value, w )
        {
            var v = value;
            w = !! w;

            Object.prototype.__defineGetter__.call(
                obj, prop, function ()
                {
                    return v;
                }
            );

            if ( w )
            {
                Object.prototype.__defineSetter__.call(
                    obj, prop, function ( val )
                    {
                        return v = val;
                    }
                );
            }
            else
            {
                Object.prototype.__defineSetter__.call(
                    obj, prop, function ( val )
                    {
                        if ( v === val )
                        {
                            return v;
                        }
                        else
                        {
                            throw new TypeError( "Cannot change this property" );
                        }
                    }
                );
            }
        };
    }
    else
    {
        global.Zork.prototype.property =
        function definePropertyRaw( obj, prop, value )
        {
            obj[prop] = value;
        };
    }

    if ( ! Boolean.isBoolean )
    {
        js.property( Boolean, "isBoolean",
            /**
             * Object is a boolean
             *
             * @param {Object} obj
             * @returns {Boolean}
             * @type Boolean
             * @memberOf Boolean
             * @name isBoolean
             */
            function booleanIsBoolean( obj )
            {
                return typeof obj === "boolean" || obj instanceof Boolean ||
                    Object.prototype.toString.call( obj ) === "[object Boolean]";
            }
        );
    }

    if ( ! Array.isArray )
    {
        js.property( Array, "isArray",
            /**
             * Object is an array
             *
             * @param {Object} obj
             * @returns {Boolean}
             * @type Boolean
             * @memberOf Array
             * @name isArray
             */
            function arrayIsArray( obj )
            {
                return obj instanceof Array ||
                    Object.prototype.toString.call( obj ) === "[object Array]";
            }
        );
    }

    if ( ! Date.isDate )
    {
        js.property( Date, "isDate",
            /**
             * Object is a date
             *
             * @param {Object} obj
             * @returns {Boolean}
             * @type Boolean
             * @memberOf Date
             * @name isDate
             */
            function dateIsDate( obj )
            {
                return obj instanceof Date ||
                    Object.prototype.toString.call( obj ) === "[object Date]";
            }
        );
    }

    if ( ! Date.now )
    {
        js.property( Date, "now",
            /**
             * Returns the numeric value corresponding to the current time.
             *
             * @returns {Number}
             * @type Number
             * @memberOf Date
             * @name now
             */
            function dateNow()
            {
                return Number( new Date() );
            }
        );
    }

    if ( ! Function.isFunction )
    {
        js.property( Function, "isFunction",
            /**
             * Object is a function
             *
             * @param {Object} obj
             * @returns {Boolean}
             * @type Boolean
             * @memberOf Function
             * @name isFunction
             */
            function functionIsFunction( obj )
            {
                return typeof obj === "function" || obj instanceof Function ||
                    Object.prototype.toString.call( obj ) === "[object Function]";
            }
        );
    }

    if ( ! Number.isNumber )
    {
        js.property( Number, "isNumber",
            /**
             * Object is a number
             *
             * @param {Object} obj
             * @returns {Boolean}
             * @type Boolean
             * @memberOf Number
             * @name isNumber
             */
            function numberIsNumber( obj )
            {
                return typeof obj === "number" || obj instanceof Number ||
                    Object.prototype.toString.call( obj ) === "[object Number]";
            }
        );
    }

    if ( ! Object.isObject )
    {
        js.property( Object, "isObject",
            /**
             * Object (variable) is an object, but not null
             *
             * @param {Object} obj
             * @returns {Boolean}
             * @type Boolean
             * @memberOf Object
             * @name isObject
             */
            function objectIsObject( obj )
            {
                return ( obj !== null ) &&
                    ( typeof obj !== "undefined" ) &&
                    ( ! Array.isArray( obj ) ) &&
                    ( ! Boolean.isBoolean( obj ) ) &&
                    ( ! Date.isDate( obj ) ) &&
                    ( ! Number.isNumber( obj ) ) &&
                    ( ! RegExp.isRegExp( obj ) ) &&
                    ( ! String.isString( obj ) ) &&
                    ( ! Function.isFunction( obj ) ) &&
                    ( typeof obj === "object" ||
                      Object.prototype.toString.call( obj ) === "[object Object]" );
            }
        );
    }

    if ( ! Object.isUndefined )
    {
        js.property( Object, "isUndefined",
            /**
             * Object (variable) is an object, but not null
             *
             * @param {Object} obj
             * @param {String} key [optional]
             * @returns {Boolean}
             * @type Boolean
             * @memberOf Object
             * @name isUndefined
             */
            function objectIsUndefined( obj, key )
            {
                if ( obj === null || obj === undefined ||
                    typeof obj === "undefined" ||
                    ( typeof obj === "number" && isNaN( obj ) ) )
                {
                    return true;
                }

                if ( typeof key !== "undefined" && (
                     obj[key] === null ||
                     obj[key] === undefined ||
                     typeof obj[key] === "undefined" ||
                     ( typeof obj[key] === "number" && isNaN( obj[key] ) )
                ) ) {
                    return true;
                }

                return false;
            }
        );
    }

    if ( ! Object.notUndefined )
    {
        js.property( Object, "notUndefined",
            /**
             * Return the first not undefined / null argument
             *
             * @param {Object} ...
             * @returns {Object}
             * @type Object
             * @memberOf Object
             * @name notUndefined
             */
            function objectNotUndefined()
            {
                var i, l = arguments.length;
                for ( i = 0; i < l; ++i )
                {
                    if ( ! Object.isUndefined( arguments[i] ) )
                    {
                        return arguments[i];
                    }
                }

                return undefined;
            }
        );
    }

    if ( ! Object.isEmpty )
    {
        js.property( Object, "isEmpty",
            /**
             * Object (variable) is an object, but not null
             *
             * @param {Object} obj
             * @returns {Boolean}
             * @type Boolean
             * @memberOf Object
             * @name isEmpty
             */
            function objectIsEmpty( obj )
            {
                var emptyFunc = /^\s*function\s*[^\(]*\(\s*[^\)]*\)\s*\{\s*("use strict";)?\s*\}\s*$/m;

                return Object.isUndefined( obj ) ||
                    ( typeof obj === "object" && $.isEmptyObject( obj ) ) ||
                    ( Array.isArray( obj ) && obj.length === 0 ) ||
                    ( Date.isDate( obj ) && Number( obj ) === 0 ) ||
                    ( Function.isFunction( obj ) && emptyFunc.test( String( obj ) ) ) ||
                    ( Number.isNumber( obj ) && isNaN( obj ) ) ||
                    ( RegExp.isRegExp( obj ) && obj.source === "" ) ||
                    ( String.isString( obj ) && obj === "" );
            }
        );
    }

    if ( ! Object.isNode )
    {
        js.property( Object, "isNode",
            /**
             * Object is a dom-element
             *
             * @param {Object} obj
             * @returns {Boolean}
             * @type Boolean
             * @memberOf Object
             * @name isNode
             */
            function objectIsNode( obj )
            {
                return (
                    typeof global.Node === "object" ?
                        obj instanceof global.Node :
                        typeof obj === "object" &&
                        typeof obj.nodeType === "number" &&
                        typeof obj.nodeName === "string"
                );
            }
        );
    }

    if ( ! Object.isElement )
    {
        js.property( Object, "isElement",
            /**
             * Object is a dom-element
             *
             * @param {Object} obj
             * @returns {Boolean}
             * @type Boolean
             * @memberOf Object
             * @name isElement
             */
            function objectIsElement( obj )
            {
                return (
                    typeof global.HTMLElement === "object" ?
                        obj instanceof global.HTMLElement :
                        typeof obj === "object" &&
                        obj.nodeType === 1 &&
                        typeof obj.nodeName === "string"
                );
            }
        );
    }

    if ( ! Object.getType )
    {
        js.property( Object, "getType",
            /**
             * Get type of the Object
             *
             * Can return:
             *  "undefined", "null", "nan", "array", "date", "number",
             *  "regexp", "function", "string", "object"
             *
             * @param {Object} obj
             * @returns {String}
             * @type String
             * @memberOf Object
             * @name getType
             */
            function objectGetType( obj )
            {
                if ( typeof obj === "undefined" || obj === undefined )
                {
                    return "undefined";
                }

                if ( obj === null )
                {
                    return "null";
                }

                if ( Array.isArray( obj ) )
                {
                    return "array";
                }

                if ( Date.isDate( obj ) )
                {
                    return "date";
                }

                if ( Number.isNumber( obj ) )
                {
                    return isNaN( obj ) ? "nan" : "number";
                }

                if ( RegExp.isRegExp( obj ) )
                {
                    return "regexp";
                }

                if ( Function.isFunction( obj ) )
                {
                    return "function";
                }

                if ( String.isString( obj ) )
                {
                    return "string";
                }

                return "object";
            }
        );
    }

    if ( ! RegExp.isRegExp )
    {
        js.property( RegExp, "isRegExp",
            /**
             * Object is a regexp
             *
             * @param {Object} obj
             * @returns {Boolean}
             * @type Boolean
             * @memberOf RegExp
             * @name isRegExp
             */
            function regExpIsRegExp( obj )
            {
                return obj instanceof RegExp ||
                    Object.prototype.toString.call( obj ) === "[object RegExp]";
            }
        );
    }

    if ( ! String.isString )
    {
        js.property( String, "isString",
            /**
             * Object is a string
             *
             * @param {Object} obj
             * @returns {Boolean}
             * @type Boolean
             * @memberOf String
             * @name isString
             */
            function stringIsString( obj )
            {
                return typeof obj === "string" || obj instanceof String ||
                    Object.prototype.toString.call( obj ) === "[object String]";
            }
        );
    }

    if ( ! Array.prototype.filter )
    {
        js.property( Array.prototype, "filter",
            /**
             * Creates a new array with all of the elements of this array
             * for which the provided filtering function returns true
             *
             * @param {Function} callback required
             * @param {Object} thisObject [optional]
             * @returns {Array}
             * @type Array
             * @memberOf Array.prototype
             * @name filter
             */
            function arrayFilter( callback, thisObject )
            {
                if ( ! Function.isFunction( callback ) )
                {
                    throw new TypeError();
                }

                var res = [], i, l = this.length;
                thisObject = thisObject || this;
                for ( i = 0; i < l; ++i )
                {
                    var val = this[i];
                    if ( callback.call( res, val, i, this ) )
                    {
                        res.push( val );
                    }
                }

                return res;
            }
        );
    }

    if ( ! Array.prototype.forEach )
    {
        js.property( Array.prototype, "forEach",
            /**
             * Calls a function for each element in the array
             *
             * @param {Function} callback required
             * @param {Object} thisObject [optional]
             * @returns {undefined}
             * @type undefined
             * @memberOf Array.prototype
             * @name forEach
             */
            function arrayForEach( callback, thisObject )
            {
                if ( ! Function.isFunction( callback ) )
                {
                    throw new TypeError();
                }

                thisObject = thisObject || this;
                var i, l = this.length;
                for ( i = 0; i < l; ++i )
                {
                    callback.call( thisObject, this[i], i, this );
                }
            }
        );
    }

    if ( ! Array.prototype.every )
    {
        js.property( Array.prototype, "every",
            /**
             * Returns true if every element in this array satisfies
             * the provided testing function
             *
             * @param {Function} callback required
             * @param {Object} thisObject [optional]
             * @returns {Boolean}
             * @type Boolean
             * @memberOf Array.prototype
             * @name every
             */
            function arrayEvery( callback, thisObject )
            {
                if ( ! Function.isFunction( callback ) )
                {
                    throw new TypeError();
                }

                thisObject = thisObject || this;
                var i, l = this.length;
                for ( i = 0; i < l; ++i )
                {
                    if ( ! callback.call( thisObject, this[i], i, this ) )
                    {
                        return false;
                    }
                }

                return true;
            }
        );
    }

    if ( ! Array.prototype.map )
    {
        js.property( Array.prototype, "map",
            /**
             * Creates a new array with the results of calling
             * a provided function on every element in this array
             *
             * @param {Function} callback required
             * @param {Object} thisObject [optional]
             * @returns {Array}
             * @type Array
             * @memberOf Array.prototype
             * @name map
             */
            function arrayMap( callback, thisObject )
            {
                if ( ! Function.isFunction( callback ) )
                {
                    throw new TypeError();
                }

                var result = [], i, l = this.length;
                thisObject = thisObject || this;
                for ( i = 0; i < l; ++i )
                {
                    result[i] = callback.call( thisObject, this[i], i, this );
                }
                return result;
            }
        );
    }

    if ( ! Array.prototype.some )
    {
        js.property( Array.prototype, "some",
            /**
             * Returns true if at least one element in this array satisfies
             * the provided testing function
             *
             * @param {Function} callback required
             * @param {Object} thisObject [optional]
             * @returns {Boolean}
             * @type Boolean
             * @memberOf Array.prototype
             * @name some
             */
            function arraySome( callback, thisObject )
            {
                if ( ! Function.isFunction( callback ) )
                {
                    throw new TypeError();
                }

                thisObject = thisObject || this;
                var i, l = this.length;
                for ( i = 0; i < l; ++i )
                {
                    if ( callback.call( thisObject, this[i], i, this ) )
                    {
                        return true;
                    }
                }
                return false;
            }
        );
    }

    if ( ! Array.prototype.reduce )
    {
        js.property( Array.prototype, "reduce",
            /**
             * Apply a function simultaneously against two values of the array
             * (from left-to-right) as to reduce it to a single value
             *
             * @param {Function} callback required
             * @param {Object} initial [optional]
             * @returns {Object}
             * @type Object
             * @memberOf Array.prototype
             * @name reduce
             */
            function arrayReduce( callback, initial )
            {
                if ( ! Function.isFunction( callback ) )
                {
                    throw new TypeError();
                }

                var previous = null,
                    current = null,
                    i = 0,
                    l = this.length;

                if ( Object.isUndefined( initial ) )
                {
                    previous = this[i++];
                }
                else
                {
                    previous = initial;
                }

                current = previous;

                for ( ; i < l; ++i )
                {
                    current = callback( current, this[i], i, this );
                }

                return current;
            }
        );
    }

    if ( ! Array.prototype.reduceRight )
    {
        js.property( Array.prototype, "reduceRight",
            /**
             * Apply a function simultaneously against two values of the array
             * (from right-to-left) as to reduce it to a single value
             *
             * @param {Function} callback required
             * @param {Object} initial [optional]
             * @returns {Object}
             * @type Object
             * @memberOf Array.prototype
             * @name reduceRight
             */
            function arrayReduceRight( callback, initial )
            {
                if ( ! Function.isFunction( callback ) )
                {
                    throw new TypeError();
                }

                var previous = null,
                    current = null,
                    i = this.length - 1;

                if ( Object.isUndefined( initial ) )
                {
                    previous = this[i--];
                }
                else
                {
                    previous = initial;
                }

                current = previous;

                for ( ; i >= 0; --i )
                {
                    current = callback( current, this[i], i, this );
                }

                return current;
            }
        );
    }

    if ( ! Array.prototype.indexOf )
    {
        js.property( Array.prototype, "indexOf",
            /**
             * Returns the index of the element in an array
             * (form the beginning; if any) othervise: -1
             *
             * @param {Object} elem required
             * @param {Number} fromIndex [optional]
             * @returns {Number}
             * @type Number
             * @memberOf Array.prototype
             * @name indexOf
             */
            function arrayIndexOf( elem, fromIndex )
            {
                fromIndex = fromIndex || 0;
                if ( fromIndex < 0 )
                {
                    fromIndex += this.length;
                }

                var i, l = this.length;
                for ( i = fromIndex; i < l; ++i )
                {
                    if ( elem === this[i] )
                    {
                        return i;
                    }
                }

                return -1;
            }
        );
    }

    if ( ! Array.prototype.lastIndexOf )
    {
        js.property( Array.prototype, "lastIndexOf",
            /**
             * Returns the index of the element in an array
             * (from the end; if any) othervise: -1
             *
             * @param {Object} elem required
             * @param {Number} fromIndex [optional]
             * @returns {Number}
             * @type Number
             * @memberOf Array.prototype
             * @name indexOf
             */
            function arrayLastIndexOf( elem, fromIndex )
            {
                fromIndex = fromIndex || -1;
                if ( fromIndex < 0 )
                {
                    fromIndex += this.length;
                }

                var i;
                for ( i = fromIndex; i >= 0; --i )
                {
                    if ( elem === this[i] )
                    {
                        return i;
                    }
                }

                return -1;
            }
        );
    }

    if ( ! Function.prototype.bind )
    {
        js.property( Function.prototype, "bind",
            /**
             * Creates a new function which, when called, itself calls this function
             * in the context of the provided value, with a given sequence of
             * arguments preceding any provided when the new function was called.
             *
             * @param {Object} obj
             * @returns {Function}
             * @type Function
             * @memberOf Function.prototype
             * @name bind
             */
            function functionBind( obj )
            {
                obj = obj || {};
                var slice = Array.prototype.slice,
                    args = slice.call( arguments, 1 ),
                    self = this,
                    Nop = function () { },
                    bound = function ()
                    {
                        return self.apply( this instanceof Nop ? this : obj,
                            args.concat( slice.call( arguments ) ) );
                    };

                Nop.prototype = self.prototype;
                bound.prototype = new Nop();

                return bound;
            }
        );
    }

    js.property( Array.prototype, "merge",
        /**
         * Merge two or more arrays, as sets
         *
         * @param {Array|Object} ...
         * @returns {Array}
         * @type Array
         * @memberOf Array.prototype
         * @name merge
         */
        function arrayMerge()
        {
            var result = this, i, l = arguments.length;

            for ( i = 0; i < l; ++i )
            {
                if ( Array.isArray( arguments[i] ) )
                {
                    result = result.merge.apply( this, arguments[i] );
                }
                else if ( Array.prototype.indexOf.call( result, arguments[i] ) < 0 )
                {
                    Array.prototype.push.call( result, arguments[i] );
                }
            }

            return result;
        }
    );

    js.property( Function, "isNative",
        /**
         * Function is a native one
         *
         * @param {Function} func
         * @returns {Boolean}
         * @type Boolean
         * @memberOf Function.prototype
         * @name isNative
         */
        function functionIsNative( func )
        {
            var nativeFunc = /^\s*function\s*[^\(]*\(\s*[^\)]*\)\s*\{\s*\[\s*native code\s*\]\s*\}\s*$/;
            return nativeFunc.test( String( func ) );
        }
    );

    js.property( Array.prototype, "clone",
        /**
         * Clone an array
         *
         * js-extension
         *
         * @returns {Array}
         * @type Array
         * @memberOf Array.prototype
         * @name clone
         */
        function arrayClone()
        {
            return this.concat();
        }
    );

    js.property( Date.prototype, "clone",
        /**
         * Clone a date
         *
         * js-extension
         *
         * @returns {Date}
         * @type Date
         * @memberOf Date.prototype
         * @name clone
         */
        function dateClone()
        {
            return new Date( Number( this ) );
        }
    );

    js.property( Function.prototype, "clone",
        /**
         * Clone a function
         *
         * js-extension
         *
         * @returns {Function}
         * @type Function
         * @memberOf Function.prototype
         * @name clone
         */
        function functionClone()
        {
            if ( Function.isNative( this ) )
            {
                return this;
            }

            var code = /^\s*function\s*[^\(]*\((\s*[^\)])*\)\s*\{(.*)\}\s*$/,
                func = code.exec( String( this ) );

            return new Function( func[1], func[2] );
        }
    );

    js.property( RegExp.prototype, "clone",
        /**
         * Clone a regexp
         *
         * js-extension
         *
         * @returns {RegExp}
         * @type RegExp
         * @memberOf RegExp.prototype
         * @name clone
         */
        function regExpClone()
        {
            return new RegExp(
                this.source,
                ( this.global       ? "g" : "" ) +
                ( this.ignoreCase   ? "i" : "" ) +
                ( this.multiline    ? "m" : "" )
            );
        }
    );

    js.property( Object, "clone",
        /**
         * Clone an object
         *
         * js-extension
         *
         * @param {Object} obj
         * @param {Boolean} cloneFunctions default: false
         * @returns {Object}
         * @type Object
         * @memberOf Object.prototype
         * @name clone
         */
        function objectClone( obj, cloneFunctions )
        {
            var result, i;
            cloneFunctions = cloneFunctions || false;
            if ( Object.isUndefined( obj ) )
            {
                result = null;
            }
            else if ( Array.isArray( obj ) )
            {
                result = Array.prototype.clone.call( obj );
            }
            else if ( Boolean.isBoolean( obj ) )
            {
                result = !! obj;
            }
            else if ( Date.isDate( obj ) )
            {
                result = Date.prototype.clone.call( obj );
            }
            else if ( Function.isFunction( obj ) )
            {
                if ( ! cloneFunctions )
                {
                    result = obj;
                }
                else
                {
                    result = Function.prototype.clone.call( obj );
                }
            }
            else if ( Number.isNumber( obj ) )
            {
                result = Number( obj );
            }
            else if ( RegExp.isRegExp( obj ) )
            {
                result = RegExp.prototype.clone.call( obj );
            }
            else if ( String.isString( obj ) )
            {
                result = String( obj );
            }
            else if ( Object.isNode( obj ) )
            {
                result = obj.cloneNode( true );
            }
            else if ( Object.isObject( obj ) )
            {
                if ( ! obj.____cloned )
                {
                    obj.____cloned = true;
                    result = {};

                    for ( i in obj )
                    {
                        if ( typeof obj[i] !== "undefined" && i !== "____cloned" )
                        {
                            result[i] = Object.clone( obj[i], cloneFunctions );
                        }
                    }

                    delete obj.____cloned;
                }
                else
                {
                    result = null;
                }
            }
            else
            {
                result = obj;
            }

            return result;
        }
    );

    js.property( String.prototype, "format",
        /**
         * Format strings like sprintf in c / php / etc...
         * @param {String|Number} ...
         * @returns {String}
         * @type String
         * @memberOf String.prototype
         * @name format
         */
        function stringFormat()
        {
            var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g,
                a = arguments,
                i = 0;

            var pad = function ( str, len, chr, leftJustify )
            {
                chr = chr || " ";
                var padding = ( str.length >= len ) ? "" :
                    Array( 1 + len - str.length >>> 0 ).join( chr );
                return leftJustify ? str + padding : padding + str;
            };

            var justify = function ( value, prefix, leftJustify,
                                     minWidth, zeroPad, customPadChar )
            {
                var diff = minWidth - value.length;

                if ( diff > 0 )
                {
                    if ( leftJustify || ! zeroPad )
                    {
                        value = pad( value, minWidth, customPadChar, leftJustify );
                    }
                    else
                    {
                        value = value.slice( 0, prefix.length ) +
                            pad( "", diff, "0", true ) +
                            value.slice( prefix.length );
                    }
                }

                return value;
            };

            var formatBaseX = function ( value, base, prefix, leftJustify,
                                         minWidth, precision, zeroPad )
            {
                // Note: casts negative numbers to positive ones
                var number = value >>> 0;
                prefix = prefix && number &&
                    { "2" : "0b", "8" : "0", "16": "0x" }[base] || "";
                value = prefix + pad( number.toString( base ),
                    precision || 0, "0", false );
                return justify( value, prefix, leftJustify, minWidth, zeroPad );
            };

            var formatString = function ( value, leftJustify, minWidth,
                                          precision, zeroPad, customPadChar )
            {
                if ( precision != null )
                {
                    value = value.slice( 0, precision );
                }

                return justify( value, "", leftJustify,
                    minWidth, zeroPad, customPadChar );
            };

            var doFormat = function ( substring, valueIndex, flags,
                                      minWidth, _, precision, type )
            {
                if ( substring == "%%" )
                {
                    return "%";
                }

                var number,
                    prefix,
                    method,
                    textTransform,
                    value,
                    leftJustify = false,
                    positivePrefix = "",
                    zeroPad = false,
                    prefixBaseX = false,
                    customPadChar = " ",
                    flagsl = flags.length;

                for ( var j = 0; flags && j < flagsl; j++ )
                {
                    switch ( flags.charAt( j ) )
                    {
                        case " ":
                            positivePrefix = " ";
                            break;

                        case "+":
                            positivePrefix = "+";
                            break;

                        case "-":
                            leftJustify = true;
                            break;

                        case "'":
                            customPadChar = flags.charAt( ++j );
                            break;

                        case "0":
                            zeroPad = true;
                            break;

                        case "#":
                            prefixBaseX = true;
                            break;
                    }
                }

                // parameters may be null, undefined, empty-string or real valued
                // we want to ignore null, undefined and empty-string values
                if ( ! minWidth )
                {
                    minWidth = 0;
                }
                else if ( minWidth == "*" )
                {
                    minWidth = + a[ i++ ];
                }
                else if ( minWidth.charAt( 0 ) == "*" )
                {
                    minWidth = + a[ minWidth.slice( 1, -1 ) ];
                }
                else
                {
                    minWidth = + minWidth;
                }

                // Note: undocumented perl feature:
                if ( minWidth < 0 )
                {
                    minWidth = - minWidth;
                    leftJustify = true;
                }

                if ( ! isFinite( minWidth ) )
                {
                    throw new Error( "format: (minimum-)width must be finite" );
                }

                if ( ! precision )
                {
                    precision = "fFeE".indexOf( type ) > -1 ?
                        6 : ( type == "d" ) ? 0 : undefined;
                }
                else if ( precision == "*" )
                {
                    precision = + a[ i++ ];
                }
                else if ( precision.charAt( 0 ) == "*" )
                {
                    precision = + a[ precision.slice( 1, -1 ) ];
                }
                else
                {
                    precision = + precision;
                }

                // grab value using valueIndex if required?
                value = valueIndex ? a[ parseInt( valueIndex.
                        slice( 0, -1 ), 10 ) - 1 ] : a[ i++ ];

                switch ( type )
                {
                    case "s":
                        return formatString( String( value ), leftJustify, minWidth,
                            precision, zeroPad, customPadChar );

                    case "c":
                        return formatString( String.fromCharCode( + value ),
                            leftJustify, minWidth, precision, zeroPad );

                    case "b":
                        return formatBaseX( value, 2, prefixBaseX, leftJustify,
                            minWidth, precision, zeroPad );

                    case "o":
                        return formatBaseX( value, 8, prefixBaseX, leftJustify,
                            minWidth, precision, zeroPad );

                    case "x":
                        return formatBaseX( value, 16, prefixBaseX, leftJustify,
                            minWidth, precision, zeroPad );

                    case "X":
                        return formatBaseX( value, 16, prefixBaseX, leftJustify,
                            minWidth, precision, zeroPad ).toUpperCase();

                    case "u":
                        return formatBaseX( value, 10, prefixBaseX, leftJustify,
                            minWidth, precision, zeroPad );

                    case "i":
                    case "d":
                        number = ( + value ) | 0;
                        prefix = number < 0 ? "-" : positivePrefix;
                        value = prefix + pad( String( Math.abs( number ) ),
                            precision, "0", false );

                        return justify( value, prefix, leftJustify,
                            minWidth, zeroPad );

                    case "e":
                    case "E":
                    case "f":
                    case "F":
                    case "g":
                    case "G":
                        number = + value;
                        prefix = number < 0 ? "-" : positivePrefix;
                        method = [ "toExponential", "toFixed", "toPrecision" ][
                            "efg".indexOf( type.toLowerCase() ) ];
                        textTransform = [ "toString", "toUpperCase" ][
                            "eEfFgG".indexOf( type ) % 2 ];
                        value = prefix + Math.abs( number )[ method ]( precision );
                        return justify( value, prefix, leftJustify,
                            minWidth, zeroPad )[ textTransform ]();

                    default:
                        return substring;
                }
            };

            return this.replace( regex, doFormat );
        }
    );

    js.property( String.prototype, "toLowerCaseFirst",
        /**
         * Lower case first character
         *
         * @returns {String}
         * @type String
         * @memberOf String.prototype
         * @name toLowerCaseFirst
         */
        function stringToLowerCaseFirst()
        {
            return this.replace( /^./, function ( match )
            {
                return match.toLowerCase();
            } );
        }
    );

    js.property( String.prototype, "toUpperCaseFirst",
        /**
         * Upper case first character
         *
         * @returns {String}
         * @type String
         * @memberOf String.prototype
         * @name toUpperCaseFirst
         */
        function stringToUpperCaseFirst()
        {
            return this.replace( /^./, function ( match )
            {
                return match.toUpperCase();
            } );
        }
    );

    var oStringReplace = String.prototype.replace;

    /**
     * Replace extended with array support
     *
     * @param {String|RegExp|Array} from
     * @param {String|Function|Array} to
     * @returns {String}
     * @type String
     * @memberOf String.prototype
     * @name replace
     */
    String.prototype.replace = function ( from, to )
    {
        if ( Array.isArray( from ) )
        {
            var f_to = Array.isArray( to ) ?
                    function ( idx ) {return to[idx];} :
                    function () {return to;},
                result = String( this ),
                i, l = from.length;

            for ( i = 0; i < l; ++i )
            {
                result = oStringReplace.call( result, from[i], f_to( i ) );
            }

            return result;
        }
        else
        {
            return oStringReplace.call( this, from, to );
        }
    };

    /**
     * Convert a variable to JSON-string
     * @param {Object} _var variable
     * @returns Variable in json format
     * @type String
     */
    if ( typeof global.JSON !== "undefined" )
    {
        $.toJSON = function ( _var )
        {
            return JSON.stringify( _var );
        };
    }
    else
    {
        $.toJSON = function ( _var )
        {
            var result = "";
            if ( _var === document || _var === window )
            {
                result += "{}";
            }
            else if ( Object.isUndefined( _var ) )
            {
                result += "null";
            }
            else if ( Array.isArray( _var ) )
            {
                result += "[";
                var afirst = true, ai, al = _var.length;
                for ( ai = 0; ai < al; ai++ )
                {
                    if ( afirst )
                    {
                        afirst = false;
                    }
                    else
                    {
                        result += ",";
                    }

                    result += $.toJSON( _var[ai] );
                }
                result += "]";
            }
            else if ( Number.isNumber( _var ) )
            {
                result += ( isNaN( _var ) || ! isFinite( _var ) ) ?
                    "null" : _var;
            }
            else if ( Boolean.isBoolean( _var ) )
            {
                result += _var ? "true" : "false";
            }
            else if ( String.isString( _var ) )
            {
                result += '"' + addSlashes( _var ) + '"';
            }
            else if ( Function.isFunction( _var ) &&
                _var.valueOf !== Object.prototype.valueOf )
            {
                result += $.toJSON( _var.valueOf() );
            }
            else if ( Object.isObject( _var ) )
            {
                $.toJSON._special = $.toJSON._special || ( "__jsonEncoded_" +
                    String( Math.random() ).replace( /^0?\./, "" ) );

                if ( ! _var[$.toJSON._special] )
                {
                    var hop = Object.prototype.hasOwnProperty;
                    _var[$.toJSON._special] = true;
                    result += "{";
                    var ofirst = true, oi;
                    for ( oi in _var )
                    {
                        if ( typeof _var[oi] !== "undefined" &&
                            oi !== $.toJSON._special && ( ! hop ||
                            hop.call( _var, oi ) ) )
                        {
                            if ( ofirst )
                            {
                                ofirst = false;
                            }
                            else
                            {
                                result += ",";
                            }

                            result += '"' + addSlashes( oi ) + '":';
                            result += $.toJSON( _var[oi] );
                        }
                    }
                    result += "}";
                    delete _var[$.toJSON._special];
                }
                else
                {
                    result += "null";
                }
            }
            else
            {
                result += '"' + addSlashes( String( _var ) ) + '"';
            }

            return result;
        };
    }

    /**
     * @class JConsole
     * @memberOf $
     * @memberOf jQuery
     * @memberOf Zork
     */
    var JConsole = function ()
    {
        /**
         * Prints a log-message to the console
         * @function
         * @name $.console.log
         * @name jQuery.console.log
         * @param {Object} ... variables
         * @type undefined
         */
        this.log = function () { };

        var t = this,
            con = !! global.console,
            timers = { },
            counters = { },
            /**
             * @param {Object} c context
             * @param {Function} f function
             * @returns {Function}
             * @type Function
             */
            crl = function ( c, f )
            {
                return function()
                {
                    try
                    {
                        if ( ! f.apply )
                        {
                            var i, l = arguments.length;
                            for ( i = 0; i < l; ++i )
                            {
                                f( arguments[i] );
                            }
                        }
                        else
                        {
                            f.apply( c, arguments );
                        }
                    }
                    catch( e ) { }
                };
            };

        // Firebug / modern browsers
        if ( con && !! global.console.log )
        {
            this.log = crl( console, console.log );
        }
        // Opera internal debugger
        else if ( !! global.opera && !!opera.postError )
        {
            this.log = crl( opera, opera.postError );
        }
        // IE internal debugger
        else if ( !! global.Debug && !! global.Debug.write )
        {
            this.log = crl( global.Debug, global.Debug.write );
        }
        // Firefox internal debugger
        else if ( !! global.dump )
        {
            this.log = crl( global, function ()
            {
                global.dump( String( arguments ) + "\n" );
            } );
        }

        /**
         * Prints an info-message to the console
         * @param {Object} ... variables
         * @type undefined
         */
        this.info = con && !! console.info ?
            crl( console, console.info ) : this.log;

        /**
         * Prints a warn-message to the console
         * @param {Object} ... variables
         * @type undefined
         */
        this.warn = con && !! console.warn ?
            crl( console, console.warn ) : this.log;

        /**
         * Prints a debug-message to the console
         * @param {Object} ... variables
         * @type undefined
         */
        this.debug = con && !! console.debug ?
            crl( console, console.debug ) : this.log;

        /**
         * Prints an error-message to the console
         * @param {Object} ... variables
         * @type undefined
         */
        this.error = con && !! console.error ?
            crl( console, console.error ) : this.log;

        /**
         * Prints an exception to the console
         * @param {Error} exception exception
         * @type undefined
         */
        this.exception = con && !! console.exception ?
            crl( console, console.exception ) : this.error;

        /**
         * Prints a message to the console, if expression is false
         * @param {Boolean} expr expression
         * @param {String} msg message
         * @type undefined
         */
        this.assert = con && !! console.assert ?
            crl( console, console.assert ) :
            function ( expr, msg )
            {
                if ( ! expr )
                {
                    t.error( new Error( String( msg ) ||
                        "Assertation failure" ) );
                }
            };

        /**
         * Prints the actual timestamp to the console
         * @param {Boolean} expr expression
         * @param {String} msg message
         * @type undefined
         */
        this.timeStamp = con && !! console.timeStamp ?
            crl( console, console.timeStamp ) :
            function ()
            {
                t.log( ( new Date() ).toLocaleString() );
            };

        /**
         * Start group
         * @type undefined
         */
        this.group = con && !! console.group ?
            crl( console, console.group ) :
            function ()
            {
                var args = [ "Group: " ], i, l = arguments.length;

                for ( i = 0; i < l; ++i )
                {
                    args.push( arguments[i] );
                }

                this.log.apply( this, args );
            };

        /**
         * Start group (collapsed)
         * @type undefined
         */
        this.groupCollapsed = con && !! console.groupCollapsed ?
            crl( console, console.groupCollapsed ) :
            crl( this, this.group );

        /**
         * End group
         * @type undefined
         */
        this.groupEnd = con && !! console.groupEnd ?
            crl( console, console.groupEnd ) :
            function ()
            {
                this.log( "Group end." );
            };

        /**
         * Start timer
         * @param {String} name [optional] timer name
         * @type undefined
         */
        this.time = con && !! console.time ?
            crl( console, console.time ) :
            function ( name )
            {
                name = name || "anonymous";
                timers[name] = Date.now();
                t.log( name + " timer started at ",
                ( new Date( timers[name] ) ).toLocaleString() );
            };

        /**
         * End timer
         * @param {String} name [optional] timer name
         * @type undefined
         */
        this.timeEnd = con && !! console.timeEnd ?
            crl( console, console.timeEnd ) :
            function ( name )
            {
                name = name || "anonymous";
                if ( typeof timers[name] !== "undefined" )
                {
                    var end = Date.now();
                    t.log( name + " timer ended at ",
                        ( new Date( end ) ).toLocaleString() );
                    t.log( name + " timer took up " +
                        ( end - timers[name] ) + " seconds" );
                    delete timers[name];
                }
                else
                {
                    t.warn( "There are no timers called " + name );
                }
            };

        /**
         * Counter
         * @param {String} name [optional] timer name
         * @type undefined
         */
        this.count = con && !! console.count ?
            crl( console, console.count ) :
            function ( name )
            {
                name = name || "anonymous";

                if ( typeof counters[name] === "undefined" )
                {
                    counters[name] = 0;
                }

                counters[name]++;
                t.log( name + " counter ", counters[name] );
            };

        /**
         * Inspect js variable in the console
         * @param {Boolean} expr expression
         * @param {String} msg message
         * @type undefined
         */
        this.dir = con && !! console.dir ?
            crl( console, console.dir ) :
            function ( inspect )
            {
                t.log( $.toJSON( inspect ) );
            };

        /**
         * Inspect dom node in the console
         * @param {Boolean} expr expression
         * @param {String} msg message
         * @type undefined
         */
        this.dirxml = con && !!console.dirxml ?
            crl( console, console.dirxml ) :
            function ( inspect )
            {
                if ( !inspect.nodeName || !inspect.attributes
                    || !inspect.getAttribute )
                {
                    return false;
                }

                var selector = String( inspect.nodeName ).toLowerCase();
                if ( !! inspect.id ) {selector += "#" + inspect.id;}
                if ( !! inspect.className )
                {
                    selector += "." +
                        String( inspect.className ).replace( /^\s+/, "" ).
                        replace( /\s+/, "." );
                }
                if ( ! inspect.nextSibling && !inspect.previousSibling )
                {
                    selector += ":only-child";
                }
                else
                {
                    if ( ! inspect.nextSibling )
                    {
                        selector += ":last-child";
                    }
                    if ( ! inspect.previousSibling )
                    {
                        selector += ":first-child";
                    }
                }
                if ( !! inspect.id && ( inspect.id === ( location.hash ||
                    location.target ).replace( "#", "" ) ) )
                {
                    selector += ":target";
                }
                if ( ! inspect.innerHTML )
                {
                    selector += ":empty";
                }
                if ( !! inspect.checked )
                {
                    selector += ":checked";
                }
                if ( !! inspect.disabled )
                {
                    selector += ":disabled";
                }
                if ( !! inspect.hasFocus && inspect.hasFocus() )
                {
                    selector += ":focus";
                }
                if ( !! inspect.getAttribute( "lang" ) )
                {
                    selector +=
                        ":lang(" + inspect.getAttribute( "lang" ) + ")";
                }
                var html = "<" + String( inspect.nodeName ).toLowerCase(),
                    i, l = inspect.attributes.length;
                for ( i = 0; i < l; ++i )
                {
                    html += " " + inspect.attributes.item(i).name + '="' +
                    addSlashes( inspect.attributes.item(i).value ) + '"';
                }
                html += ">";
                t.log( selector + " : " + html );
                return true;
            };

        /**
         * Clear console
         * @type undefined
         */
        this.clear = con && !!console.clear ?
            crl( console, console.clear ) :
            function () { };

        /**
         * Trace javascript
         * @type undefined
         */
        this.trace = con && !!console.trace ?
            crl( console, console.trace ) :
            function ()
            {
                var caller;

                if ( typeof arguments.caller !== "undefined" )
                {
                    caller = arguments.caller;
                }
                else if ( typeof arguments.callee !== "undefined" )
                {
                    caller = arguments.callee.caller;
                }

                if ( caller )
                {
                    t.group( "Trace route" );

                    while ( caller && ! caller.____trace )
                    {
                        t.log( caller );
                        caller.____trace = true;
                        caller = caller.caller;
                    }

                    if ( caller && caller.____trace )
                    {
                        t.warn( "Trace route recursion" );
                    }

                    t.groupEnd();

                    caller = arguments.caller || arguments.callee.caller;

                    while ( caller && caller.____trace )
                    {
                        delete caller.____trace;
                        caller = caller.caller;
                    }
                }
                else
                {
                    t.info( "Trace not implemented" );
                }
            };

        /**
         * Profile javascript
         * @type undefined
         */
        this.profile = con && !!console.profile ?
            crl( console, console.profile ) :
            function ()
            {
                t.info( "Profile not implemented" );
            };

        /**
         * Profile javascript end
         * @type undefined
         */
        this.profileEnd = con && !!console.profileEnd ?
            crl( console, console.profileEnd ) :
            function () { };

        /**
         * Profile javascript end
         * @type undefined
         */
        this.table = con && !!console.table ?
            crl( console, console.table ) :
            function ( table )
            {
                if ( Array.isArray( table ) )
                {
                    t.group( "Table" );
                    var i, l = table.length;
                    for ( i = 0; i < l; ++i )
                    {
                        t.dir( table[i] );
                    }
                    t.groupEnd();
                }
                else
                {
                    t.warn( "table must be an array" );
                }
            };
    };

    /**
     * Console
     * @type JConsole
     */
    global.Zork.prototype.console = $.console = new JConsole();

    /**
     * Debugs the current selection
     * @param {Object} ...
     * @type jQuery
     */
    $.fn.console = function ()
    {
        var args = Array.prototype.slice.call( arguments, 0 );
        args.push( this );
        $.console.log.apply( $.console, args );
        return this;
    };

    /**
     * Debug function
     */
    global.p = function ()
    {
        var t, i, l = arguments.length;
        for ( i = 0; i < l; ++i )
        {
            t = typeof arguments[i];

            if ( t === "object" )
            {
                js.console.dir( arguments[i] );
            }
            else
            {
                js.console.log( t, arguments[i] );
            }
        }
    };

    /**
     * Current window
     * @type Window
     */
    global.Zork.prototype.global = global.self;

    /**
     * Caller window
     * @type Window
     */
    global.Zork.prototype.caller = global.top || global.parent || global.self;

    /**
     * Last generated ID
     */
    var generated = 0;

    /**
     * Generate ID
     * @type String
     */
    global.Zork.prototype.generateId = function ()
    {
        return "js-generated-" + ( ++generated );
    };

    /**
     * Generate ID (number only)
     * @type Number
     */
    global.Zork.prototype.generateId.number = function ()
    {
        return ++generated;
    };

    /**
     * Is document ready / set-up onload
     *
     * @param {Function} onload [optional]
     * @type Boolean
     */
    global.Zork.prototype.ready = function ( onload )
    {
        if ( documentReady )
        {
            if ( Function.isFunction( onload ) )
            {
                onload.call( global );
            }

            return true;
        }
        else
        {
            if ( Function.isFunction( onload ) )
            {
                $( onload );
            }

            return false;
        }
    };

    /**
     * @class Format
     */
    global.Zork.Format = function ()
    { };

    /**
     * Format functionalities
     *
     * @param {Function} onload [optional]
     * @type Boolean
     */
    global.Zork.prototype.format = new global.Zork.Format();

    /**
     * Date format
     * @param {Date|String|Number} date
     * @type String
     */
    global.Zork.Format.prototype.date = function ( date )
    {
        return this.datetime( date, true );
    };

    /**
     * Datetime format
     * @param {Date|String|Number} date
     * @param {Boolean|String} dateOnly
     * @type String
     */
    global.Zork.Format.prototype.datetime = function ( date, dateOnly )
    {
        if ( ! Date.isDate( date ) )
        {
            if ( Number.isNumber( date ) )
            {
                date = date * 1000;
            }

            date = new Date( date );
        }

        switch ( true )
        {
            case Function.isFunction( date.toLocaleDateString )
                 && true === dateOnly:
                return date.toLocaleDateString();

            case Function.isFunction( date.toLocaleFormat )
                 && String.isString( dateOnly ):
                return date.toLocaleFormat( dateOnly );

            case Function.isFunction( date.toLocaleString ):
                return date.toLocaleString();

            default:
                return String( date );
        }
    };

    /**
     * Number format
     * @param {String|Number} number
     * @param {Number} precision
     * @type String
     */
    global.Zork.Format.prototype.number = function ( number, precision )
    {
        var pow;

        precision   = Number( precision || 2 );
        pow         = Math.pow( 10, precision );
        number      = Math.round( Number( number ) * pow ) / pow;

        if ( Function.isFunction( number.toLocaleString ) )
        {
            return number.toLocaleString();
        }

        return String( number );
    }

    /**
     * Size format
     * @param {String|Number} size
     * @param {Number} precision
     * @type String
     */
    global.Zork.Format.prototype.size = function ( size, precision )
    {
        var postfix;
        size = parseInt( size, 10 );

        switch ( true )
        {
            case size > 1000000000:
                size = size / ( 1024 * 1024 * 1024 );
                postfix = " GB";
                break;

            case size > 1000000:
                size = size / ( 1024 * 1024 );
                postfix = " MB";
                break;

            case size > 1000:
                size = size / 1024;
                postfix = " kB";
                break;

            default:
                postfix = " B";
                break;
        }

        return js.format.number( size, precision ) + postfix;
    };

    /**
     * Load a script
     *
     * @param {String|Object|Array} url the url setting if string or settings
     * @param {Object|Function|Boolean} settings settings container object or
     *        success setting if Function or async param if boolean
     * @param {Function} settings.beforeSend beforeSend event
     * @param {Boolean} settings.cache cahce befault: true
     * @param {Function} settings.complete complete event
     * @param {String} settings.contentType content type header
     * @param {Object} settings.context context of ajax-based events
     * @param {Boolean} settings.crossDomain is a cross-domain request
     * @param {String} settings.data data for post-requests
     * @param {Function} settings.error error event
     * @param {Object} settings.headers header name-value pairs
     * @param {Function} settings.success success event
     * @param {String} settings.url url
     * @returns Zork
     * @type Zork
     * @see jQuery.ajax
     */
    global.Zork.prototype.script = function ( url, settings )
    {
        if ( Array.isArray( url ) )
        {
            var self = this;
            url.forEach( function ( _url )
            {
                self.script( _url, settings );
            } );
            return this;
        }

        var s = String.isString( url ) ? { "url": url } : url;

        if ( Function.isFunction( settings ) )
        {
            s.success = settings;
        }
        else if ( Boolean.isBoolean( settings ) )
        {
            s.async = settings;
        }
        else if ( Object.isObject( settings ) )
        {
            s = $.extend( s, settings );
        }

        if ( typeof s.src !== "undefined" && typeof s.url === "undefined" )
        {
            s.url = s.src;
            delete s.src;
        }

        if ( typeof s.async === "undefined" )
        {
            s.async = false;
        }

        s.dataType = "script";
        s.global = false;
        if ( typeof s.cache === "undefined" )
        {
            s.cache = true;
        }

        $.ajax( s );
        js.console.log( "script '" + s.url + "' loaded" );
        return this;
    };

    /**
     * Create a link
     *
     * @param {String|Array} url
     * @param {Object} attr
     * @returns Zork
     * @type Zork
     */
    global.Zork.prototype.link = function ( url, attr )
    {
        var self = this;

        js.ready( function () {
            if ( Array.isArray( url ) )
            {
                url.forEach( function ( _url ) {
                    self.link( _url, attr );
                } );
                return;
            }

            attr = attr || {};

            if ( !! url.href )
            {
                $.extend( attr, url );
            }
            else
            {
                attr.href = String( url );
            }

            attr.rel  = attr.rel || "stylesheet";
            attr.href = encodeURI( attr.href );

            if ( $( 'head link[rel="' + attr.rel + '"][href="' + attr.href + '"]' ).length === 0 )
            {
                $( "head" ).append( $( "<link />" ).attr( attr ) );
            }

            js.console.log(
                attr.rel + " '" + attr.href + "' " +
                ( attr.type ? "(" + attr.type + ") " : "" ) + "linked"
            );
        } );

        return this;
    };

    /**
     * Load a style
     *
     * @param {String|Array} url
     * @param {String} type mime-type, default: "text/css"
     * @returns Zork
     * @type Zork
     */
    global.Zork.prototype.style = function ( url, type )
    {
        return this.link( url, {
            "rel": "stylesheet",
            "type": type || "text/css"
        } );
    };

    var topModules = {
            "$": "jQuery",
            "js": "zork"
        },
        loadedModules = {
            "jquery": $,
            "jquery/fn": $.fn,
            "jquery/ui": $.ui,
            "zork": js,
            "example": global.example = {}
        },
        searchNamedVariable = function ( pieces, context ) {
            pieces = pieces.clone();

            if ( pieces.length === 0 )
            {
                return context;
            }

            var piece = pieces.shift();
            if ( typeof context[piece] !== "undefined" )
            {
                return searchNamedVariable( pieces, context[piece] );
            }

            return null;
        },
        requireOne = function ( path, callback, context ) {
            if ( ! Array.isArray( path ) )
            {
                path = String( path ).replace( /^[\\\/\.,\s]+/, "" ).
                    replace( /[\\\/\.,\s]+$/, "" ).split( /[\\\/\.,\s]+/ );
            }

            if ( typeof topModules[path[0]] !== "undefined" )
            {
                path[0] = topModules[path[0]];
            }

            var parent = path.clone(),
                name = parent.pop(),
                url = path.join( "/" ).toLowerCase(),
                afterParent = function ( parent )
                {
                    if ( typeof loadedModules[url] !== "undefined" )
                    {
                        if ( Function.isFunction( callback ) )
                        {
                            callback.call( context, loadedModules[url] );
                        }

                        return loadedModules[url];
                    }

                    // var loaded = searchNamedVariable( path, global );
                    if ( ! Object.isUndefined( parent, name ) )
                    {
                        var loaded = parent[name];
                        loadedModules[url] = loaded;

                        if ( Function.isFunction( callback ) )
                        {
                            callback.call( context, loaded );
                        }

                        return loaded;
                    }

                    if ( Function.isFunction( callback ) )
                    {
                        js.script( "/scripts/" + url + ".js", function ()
                        {
                            callback.call(
                                context,
                                loadedModules[url] = parent[name]
                            );
                        } );

                        return null;
                    }
                    else
                    {
                        js.script( "/scripts/" + url + ".js", false );
                        return loadedModules[url] = parent[name];
                    }
                };

            if ( parent.length > 0 )
            {
                if ( Function.isFunction( callback ) )
                {
                    return requireOne( parent, afterParent, context );
                }
                else
                {
                    return afterParent( requireOne( parent ) );
                }
            }
            else
            {
                return afterParent( global );
            }
        };

    /**
     * Load a module's script
     *
     * @param {Array|String} paths path(s) of the module (one or more)
     * ex.: "zork.form", ["zork.form", "zork.ui"]
     * @param {Function} callback if provided,
     * it makes async request to load the module
     * @param {Object} context context for the callback if provided
     * @type Object
     */
    global.Zork.prototype.require = function ( paths, callback, context )
    {
        var result, path, i, l;
        context = context || js;

        if ( ! Function.isFunction( callback ) )
        {
            callback = false;
        }

        if ( Array.isArray( paths ) )
        {
            result = [];
            l = paths.length;

            if ( Function.isFunction( callback ) )
            {
                for ( i = 0; i < l; ++i )
                {
                    path = paths[i];

                    ( function ( idx, p ) {
                        requireOne( p, function ( module ) {
                            result[idx] = module;

                            for ( var i = 0; i < l; ++i )
                            {
                                if ( Object.isUndefined( result[i] ) )
                                {
                                    return;
                                }
                            }

                            callback.apply( this, result );
                        }, context );
                    } )( i, path );
                }

                return null;
            }
            else
            {
                for ( i = 0; i < l; ++i )
                {
                    result[i] = requireOne( paths[i] );
                }

                return result;
            }
        }
        else
        {
            return requireOne( paths, callback, context );
        }
    };

    /**
     * @class Core module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Core = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "core" ];
    };

    loadedModules[ "zork/core" ] =
        global.Zork.prototype.core =
        new global.Zork.Core();

    /**
     * @type String
     */
    global.Zork.Core.prototype.defaultLocale    = "en";

    /**
     * @type String
     */
    global.Zork.Core.prototype.userLocale       = "en";

    /**
     * @type String
     */
    global.Zork.Core.prototype.uploadsUrl       = "/uploads";

    /**
     * @type Object
     */
    var translateCache = {};

    /**
     * Translate by key
     *
     * @param {Object} key
     * @param {Object} loc
     * @type String
     */
    global.Zork.Core.prototype.translate = function ( key, loc )
    {
        var useCache = this.translate.useCache,
            keyArray = key.split( "." ),
            module = keyArray[0],
            locale = loc || this.defaultLocale,
            lang = locale.substr( 0, 2 );

        if ( typeof this.translate._domainRpc == "undefined" )
        {
            this.translate._domainRpc = js.core.rpc( {
                "method": "Grid\\Core\\Model\\Translate::textDomain"
            } );
        }

        if ( typeof this.translate._textRpc == "undefined" )
        {
            this.translate._textRpc = js.core.rpc( {
                "method": "Grid\\Core\\Model\\Translate::translate"
            } );
        }

        if ( ! translateCache[locale] )
        {
            translateCache[locale] = {};
        }

        if ( ! translateCache[locale][module] )
        {
            if ( useCache )
            {
                translateCache[locale][module] =
                    js.require( "js.store" ).cache( "translate-" +
                        locale + "-" + module );
            }

            if ( ! translateCache[locale][module] )
            {
                translateCache[locale][module] = this.translate._domainRpc(
                    module, locale
                );
            }

            translateCache[locale][module] = translateCache[locale][module] || {};
        }

        if ( translateCache[locale][module][key] )
        {
            return translateCache[locale][module][key];
        }
        else if ( locale === 'en' )
        {
            js.console.warn(
                "No language translation found for: ", key,
                " in language: ", lang
            );

            return this.translate._textRpc( key, locale );
        }
        else if ( locale === lang )
        {
            return this.translate( key, 'en' );
        }
        else
        {
            return this.translate( key, lang );
        }
    };

    /**
     * Use browser-cache
     */
    global.Zork.Core.prototype.translate.useCache = true;

    /**
     * Thumbnail url
     *
     * @param {String|Object} url
     * @param {Object} params
     * @param {String} params.url
     * @param {String} params.method
     * @param {String} params.bgcolor
     * @param {String} params.width
     * @param {String} params.height
     * @param {String} params.filters
     */
    global.Zork.Core.prototype.thumbnail = function ( url, params )
    {
        var p = $.extend( {}, js.core.thumbnail.defaults ),
            server = global.location.protocol + "//" + global.location.host;

        if ( Object.isObject( url ) )
        {
            p = $.extend( p, url );
        }
        else
        {
            p.url = String( url );

            if ( Object.isObject( params ) )
            {
                p = $.extend( p, params );
            }
        }

        if ( ! Array.isArray( p.filters ) )
        {
            if ( Object.isEmpty( p.filters ) )
            {
                p.filters = [];
            }
            else
            {
                p.filters = [ p.filters ];
            }
        }

        if ( p.url.indexOf( server ) === 0 )
        {
            p.url = "/" + p.url.substr( server.length ).replace( /^\//, "" );
        }

        if ( p.url.indexOf( "/uploads" ) !== 0 )
        {
            p.url = js.core.uploadsUrl + "/" + p.url.replace( /^\//, "" );

            if ( p.url.indexOf( server ) === 0 )
            {
                p.url = p.url.substr( server.length ).replace( /^\//, "" );
            }
        }

        if ( p.url.indexOf( "/uploads" ) === 0 )
        {
            p.url = p.url.substr( "/uploads".length ).replace( /^\//, "" );
        }
        else
        {
            return false;
        }

        if ( "bgColor" in p )
        {
            p.bgcolor = p.bgColor;
            delete p.bgColor;
        }

        if ( "bgcolor" in p )
        {
            p.bgcolor = String( p.bgcolor ).replace( /^#/, "" );
        }

        $.each( js.core.thumbnail.defaults, function ( key, value ) {
            p[key] = p[key] || value;
        } );

        return server + "/thumbnails/" +
            p.url.replace( /\/[^\/]*\/?$/, "" ).replace( /^\//, "" ) +
            "/" + p.method +
            "/" + p.width +
            "x" + p.height +
            "/" + p.bgcolor +
            "/" + ( p.filters.length < 1
                      ? "none"
                      : p.filters.join( "-" )
                  ) +
            "/" + parseInt( p.mtime || 1, 10 ) +
            "/" + p.url.replace( /^.*\//g, "" );
    };

    /**
     * Default thumbnail params
     */
    global.Zork.Core.prototype.thumbnail.defaults = {
        "method"    : "fit",
        "bgcolor"   : "transparent",
        "width"     : 100,
        "height"    : 100,
        "filters"   : []
    };

    /**
     * Create an rpc-function
     *
     * @param {Object|String} settings settings container object
     * or method as string
     * @param {String} settings.method method name
     * @param {Function} settings.callback [optional] default: null
     * @param {String} settings.format [optional] default: "json"
     * (now only "json")
     * @param {Object} settings.context [optional]
     * default is the function-result
     * @returns Callable rpc-bound function
     * (methods: (), invoke, call, apply, properties: response, result)
     * @type Function
     */
    global.Zork.Core.prototype.rpc = function ( settings )
    {
        if ( String.isString( settings ) || ! Object.isObject( settings ) )
        {
            settings = { "method": String( settings ) };
        }

        if ( Object.isUndefined( settings.format ) )
        {
            settings.format = "json";
        }

        var _s = settings,
            rpcFunc = $.extend(
                function () {
                    var args = [],
                        i, l = arguments.length;

                    for ( i = 0; i < l; ++i )
                    {
                        args.push( arguments[i] );
                    }

                    return rpcFunc.invoke( args );
                },
                {
                    "response": null,
                    "result": null,
                    "invoke": function () {
                        js.console.error( "invoke is not implemented" );
                    }
                }
            );

        if ( Object.isUndefined( _s.context ) )
        {
            _s.context = rpcFunc;
        }

        switch ( _s.format )
        {
            case "json":
                var process = function ( data ) {
                    rpcFunc.response = data;

                    if ( rpcFunc.response.error )
                    {
                        js.console.debug( rpcFunc.response.error, _s );

                        throw new Error(
                            "#" + rpcFunc.response.error.code +
                            ": " + rpcFunc.response.error.message
                        );
                    }
                    else
                    {
                        rpcFunc.result = rpcFunc.response.result;

                        if ( !! _s.callback )
                        {
                            _s.callback.call( _s.context, rpcFunc.result );
                        }
                    }
                };

                rpcFunc.invoke = function ( params ) {
                    var outerData = $.ajax( {
                        "async": !! _s.callback,
                        "cache": false,
                        "contentType": "application/json",
                        "data": $.toJSON( {
                            "jsonrpc": "2.0",
                            "method": String( _s.method ),
                            "params": params,
                            "id": js.generateId.number()
                        } ),
                        "dataType": "json",
                        "error": ! _s.callback ?
                            null :
                            function ( xhr, status, thrown )
                            {
                                js.console.error( "Rpc call status: " +
                                    status );
                                js.console.debug( _s );
                                js.console.log( xhr.responseText );
                                js.console.log( thrown );
                            },
                        "success": ! _s.callback ? null : process,
                        "type": "POST",
                        "url": "/app/" + js.core.defaultLocale + "/rpc.json"
                    } );

                    if ( ! _s.callback )
                    {
                        if ( typeof outerData.responseText !== "undefined" )
                        {
                            outerData = $.parseJSON( outerData.responseText );
                        }

                        process( outerData );
                        return rpcFunc.result;
                    }
                    else
                    {
                        return null;
                    }
                };

                break;
            default:
                js.console.error( "Unexpected format", _s );
        }

        rpcFunc.call = function ( context ) {
            _s.context = context;

            var args = [],
                i, l = arguments.length;

            for ( i = 1; i < l; ++i )
            {
                args.push( arguments[i] );
            }

            return this.invoke( args );
        };

        rpcFunc.apply = function ( context, params ) {
            _s.context = context;

            if ( Array.isArray( params ) )
            {
                var args = [],
                    i, l = params.length;

                for ( i = 0; i < l; ++i )
                {
                    args.push( params[i] );
                }

                return this.invoke( args );
            }

            return this.invoke( params );
        };

        return rpcFunc;
    };

    if ( typeof $.fn.dataset === "undefined"  )
    {
        /**
         * Get/set data attributes
         *
         * @param {String} name
         * @param {String} set [optional]
         * @type String|$.fn
         * @see $.fn.data
         * @deprecated
         */
        $.fn.dataset = function ( name, set ) {
            if ( Object.isUndefined( name ) || this.size() <= 0 )
            {
                return typeof set === "undefined" ? null : this;
            }

            if ( typeof name === "object" )
            {
                for ( var i in name )
                {
                    this.dataset( i, name[i] );
                }

                return this;
            }

            var node = this.get( 0 );
            if ( typeof node.dataset !== "undefined" &&
                 typeof set === "undefined" &&
                 String.isString( name ) )
            {
                return node.dataset[name];
            }

            var attr = "data-" + String( name ).replace( /[A-Z]/g,
                function ( match ) {
                    return "-" + match.toLowerCase();
                }
            );

            return "undefined" === typeof set
                ? this.attr( attr )
                : this.attr( attr, set );
        };
    }

    if ( typeof $.fn.fixedspinner === "undefined"  )
    {
        $.widget( "ui.fixedspinner", $.ui.spinner, {
            "options": {
                "prefix": "",
                "postfix": "",
                "allowedPrefixes": [],
                "allowedPostfixes": []
            },
            "_parse": function ( value ) {
                var match;
                value = String( value );

                if ( ( match = value.match( /^[^0-9\.]+/ ) ) && match.length &&
                     ~ this.options.allowedPrefixes.indexOf( match[0] ) )
                {
                    this._setOption( "prefix", match[0] );
                }

                if ( ( match = value.match( /[^0-9\.]+$/ ) ) && match.length &&
                     ~ this.options.allowedPostfixes.indexOf( match[0] ) )
                {
                    this._setOption( "postfix", match[0] );
                }

                return + value.replace( /[^0-9\.]+/, "" )
                              .replace( /\.+$/, "" )
                              .replace( /^\.+([0-9+]\.[0-9+])/, "$1" );
            },
            "_format": function ( value ) {
                return String( this.options.prefix ) +
                       String( value ) +
                       String( this.options.postfix );
            },
            "_setOption": function( key, value ) {
                if ( key === "allowedPrefixes" || key === "allowedPostfixes" ) {
                    if ( Array.isArray( value ) ) {
                        value = String( value ).split( /[\s\|,]+/ );
                    }
                }

                if ( key === "prefix" || key === "postfix" ) {
                    value = String( value );
                }

                this._super( key, value );
            }
        } );
    }

    /* too buggy to implement
    if ( typeof $.fn.propertychange === "undefined" )
    {
        var propchange = function ( event ) {
            $( this ).trigger( $.Event( "propertychange" ), {
                "originalEvent": event,
                "which": event.attrName,
                "propertyName": event.attrName
            } );
        };

        /**
         * DOM property change event (W3C: DOMAttrModified)
         *
         * @param {Object} data [optional] event data
         * @param {Function} handler [optional] handler function
         *
        $.fn.propertychange = function ( data, handler ) {
            $( this ).off( "DOMAttrModified", propchange );
            $( this ).on( "DOMAttrModified", propchange );

            if ( Function.isFunction( handler ) )
            {
                return this.on( "propertychange", data, handler );
            }

            if ( Function.isFunction( data ) )
            {
                return this.on( "propertychange", data ); // data as handler
            }

            return this.trigger(
                data instanceof $.Event ? data : "propertychange"
            );
        };
    }
    */

    /**
     * ui.inputset widget
     */
    $.widget( "ui.inputset", {
        "options": {
            "items": ":input, :button, :submit, :reset, :data(input), :data(button)"
        },
        "_create": function () {
            this.element.addClass( "ui-inputset" );
        },
        "_init": function () {
            this.refresh();
        },
        "_setOption": function( key, value ) {
            if ( key === "disabled" )
            {
                this.inputs.propAttr( "option", key, !! value );
            }

            $.Widget.prototype._setOption.apply( this, arguments );
        },
        "refresh": function () {
            var rtl = this.element.css( "direction" ) === "rtl";

            if ( this.inputs && this.inputs.length )
            {
                this.inputs
                    .removeClass( "ui-inputset-element" );
            }

            this.inputs = this.element.find( this.options.items )
                .addClass( "ui-inputset-element" )
                .removeClass( "ui-corner-all ui-corner-left ui-corner-right" )
                .filter( ":not(.ui-hidden)" )
                    .filter( ":first" )
                        .addClass( rtl ? "ui-corner-right" : "ui-corner-left" )
                    .end()
                    .filter( ":last" )
                        .addClass( rtl ? "ui-corner-left" : "ui-corner-right" )
                    .end()
                    .filter( ".ui-corner-left.ui-corner-right" )
                        .removeClass( "ui-corner-left ui-corner-right" )
                        .addClass( "ui-corner-all" )
                    .end()
                .end();
        },
        "destroy": function () {
            this.element.removeClass( "ui-inputset" );

            this.inputs.removeClass( "ui-inputset-element ui-corner-all " +
                                     "ui-corner-left ui-corner-right" );

            $.Widget.prototype.destroy.call( this );
        }
    } );

    /**
     * Returns the distance (in pixels) of 2 elements
     * @param {String|HTMLElement|$} e1
     * @param {String|HTMLElement|$} e2
     * @type {Number}
     */
    global.Zork.Core.prototype.distance = function ( e1, e2 )
    {
        var o0 = {
                "top"   : 0,
                "left"  : 0
            },
            o1 = $( e1 ).offset() || o0,
            o2 = $( e2 ).offset() || o0;

        return Math.sqrt(
            Math.pow( o1.top - o2.top, 2 ) +
            Math.pow( o1.left - o2.left, 2 )
        );
    };

    /**
     * Draws a blocking layer
     * @param {String} content
     * @param {Function} resume
     * @type Function
     */
    global.Zork.Core.prototype.layer = function ( content, resume )
    {
        if ( Function.isFunction( content ) )
        {
            resume = content;
            content = false;
        }

        var minWidth = 100,
            minHeight = 100,
            layer = $( '<div class="ui-overlay" />' ),
            overlay = $( '<div class="ui-widget-overlay" />' ),
            shadow = $( '<div class="ui-widget-shadow" />' ),
            intervalFunc = function () {
                var cwidth, cheight,
                    width, height,
                    inner, iwidth, iheight,
                    stop, sleft,
                    aheight = $( global ).height() - 10;

                shadow.stop( true, false );
                content.stop( true, false )
                       .css( {
                            "height": "auto",
                            "padding": "0px"
                        } );

                if ( content.is( "iframe" ) )
                {
                    try
                    {
                        inner = content.contents();
                    }
                    catch ( e )
                    {
                        return;
                    }

                    if ( ! inner.length || ! inner[0].body )
                    {
                        // not fully loaded yet
                        js.console.log( "iframe not fully loaded yet, wait more 1000 ms" );
                        setTimeout( intervalFunc, 1000 );
                        return;
                    }

                    content.width( minWidth )
                           .height( minHeight );

                    try
                    {
                        iwidth  = inner.outerWidth();
                        iheight = inner.outerHeight();
                    }
                    catch ( e )
                    {
                        js.console.dir( e );

                        if ( "stack" in e )
                        {
                            js.console.log( e.stack );
                        }

                        try
                        {
                            iwidth  = inner.width();
                            iheight = inner.height();
                        }
                        catch ( e )
                        {
                            js.console.dir( e );

                            if ( "stack" in e )
                            {
                                js.console.log( e.stack );
                            }

                            iwidth  = content.width();
                            iheight = content.height();
                        }
                    }

                    content.width(  cwidth  = Math.max( minWidth,  iwidth + 20 ) )
                           .height( cheight = Math.max( minHeight, iheight     ) )
                           .css( {
                               "paddint-right": "25px",
                               "padding-bottom": "25px"
                           } );

                    setTimeout( function () {
                        content.css( {
                            "paddint-right": "0px",
                            "padding-bottom": "0px"
                        } );
                    }, 1 );
                }
                else
                {
                    cwidth  = Math.max( minWidth,  content.width()  );
                    cheight = Math.max( minHeight, content.height() );
                }

                if ( cheight > aheight )
                {
                    content.css( "height", cheight = aheight );
                }

                width   = ( cwidth / 2 )
                        + parseInt( shadow.css( "borderLeftWidth" ), 10 );
                height  = ( cheight / 2 )
                        + parseInt( shadow.css( "borderTopWidth" ), 10 );
                stop    = parseInt( shadow.css( "borderTopWidth" ), 10 )
                        + parseInt( shadow.css( "paddingTop" ), 10 );
                sleft   = parseInt( shadow.css( "borderLeftWidth" ), 10 )
                        + parseInt( shadow.css( "paddingLeft" ), 10 );

                shadow.animate( {
                    "width"         : parseInt( width * 2, 10 ) + "px",
                    "height"        : parseInt( height * 2, 10 ) + "px",
                    "margin-top"    : "-" + parseInt( height + stop, 10 ) + "px",
                    "margin-left"   : "-" + parseInt( width + sleft, 10 ) + "px"
                }, "fast" );

                content.animate( {
                    "margin-top": "-" + parseInt( height, 10 ) + "px",
                    "margin-left": "-" + parseInt( width, 10 ) + "px"
                }, "fast" );
            },
            update = function () {
                setTimeout( intervalFunc, 100 );
                setTimeout( intervalFunc, 1600 );
            };

        layer.css( {
            "position"  : "fixed",
            "top"       : "0px",
            "left"      : "0px",
            "width"     : "100%",
            "height"    : "100%",
            "z-index"   : 100
        } );

        if ( "object" == typeof content &&
             ( "minWidth" in content || "minHeight" in content ) ) {
            minWidth  = Math.max( minWidth,  content.minWidth  || 0 );
            minHeight = Math.max( minHeight, content.minHeight || 0 );
            content   = content.content || "";
        }

        content = $( content || '<img src="/images/scripts/loading.gif" />' );

        layer.append( overlay )
             .append( shadow )
             .append( content );

        shadow.css( {
            "position"  : "absolute",
            "top"       : "50%",
            "left"      : "50%"
        } );

        content.css( {
            "position"  : "absolute",
            "top"       : "50%",
            "left"      : "50%"
        } );

        $( "body" ).append( layer.fadeIn() );

        intervalFunc();

        if ( content.is( "iframe" ) )
        {
            content.on( "load", update );

            try
            {
                content.contents()
                       .on( "load", "img, script, iframe", update );
            }
            catch ( e )
            {
            }
        }

        if ( content.is( ".ui-tabs" ) )
        {
            content.on( "tabsactivate tabsload", update );
        }

        content.on( "load", "img, script, iframe", update );
        content.on( "tabsactivate tabsload", ".ui-tabs", update );

        return function () {
            if ( Function.isFunction( resume ) )
            {
                resume.apply( this, arguments );
            }

            layer.fadeOut( "fast", function () {
                layer.remove();
            } );
        };
    };

    /**
     * Load a json request
     *
     * @param {String|Object} url the url setting if string or settings
     * @param {Object|Function|Boolean} settings settings container object
     *        or success setting if Function or async param if boolean
     * @param {Function} settings.beforeSend beforeSend event
     * @param {Boolean} settings.cache cahce befault: true
     * @param {Function} settings.complete complete event
     * @param {String} settings.contentType content type header
     * @param {Object} settings.context context of ajax-based events
     * @param {Boolean} settings.crossDomain is a cross-domain request
     * @param {String} settings.data data for post-requests
     * @param {Function} settings.error error event
     * @param {Object} settings.headers header name-value pairs
     * @param {Function} settings.success success event
     * @param {String} settings.url url
     * @returns {jqXHR} the jQuery XMLHttpRequest representation
     * @type jqXHR
     * @see jQuery.ajax
     */
    global.Zork.Core.prototype.ajaj = function ( url, settings )
    {
        var s = typeof url.url !== "undefined"
                ? url : {
                    "url": String( url )
                };

        if ( Function.isFunction( settings ) )
        {
            s.success = settings;
        }
        else if ( Boolean.isBoolean( settings ) )
        {
            s.async = settings;
        }
        else if ( Object.isObject( settings ) )
        {
            s = $.extend( s, settings );
        }

        s.dataType = "json";
        return $.ajax( s );
    };

    /**
     * Parse elements
     *
     * @param {String} selector [optional] dafault: "body"
     */
    global.Zork.Core.prototype.parseDocument = function ( selector )
    {
        selector = selector || "head, body";

        $( selector ).find( "form" ).each( function () {
            var self = $( this );
            js.require( "js.form.html5", function () {
                if ( ! self.data( "jsFormParsed" ) )
                {
                    js.form.html5( self );
                    self.data( "jsFormParsed", true );
                }
            } );
        } );

        $( selector ).find( "[data-js-type]" ).each( function () {
            var t = this, self = $( this ),
                types = String( self.data( "jsType" ) || "" ).split( /[\s,]+/ );

            if ( ! self.data( "jsTypeParsed" ) )
            {
                types.forEach( function ( typeName ) {
                    if ( typeName )
                    {
                        js.require( typeName, function ( type ) {
                            if ( Function.isFunction( type ) &&
                                type.isElementConstructor )
                            {
                                var parent = typeName.split( "." );
                                parent.pop();
                                parent = searchNamedVariable( parent, global );
                                type.call( parent, t );
                            }
                            else
                            {
                                js.console.error( "Type is not a valid element " +
                                    "constructor: " + typeName );
                            }
                        } );
                    }
                } );

                self.data( "jsTypeParsed", true );
            }
        } );
    };

    /**
     * Load a json request and append to / replace a target
     *
     * @param {Object} settings settings container object
     * @param {Function} settings.beforeSend beforeSend event
     * @param {Boolean} settings.cache cahce befault: true
     * @param {Function} settings.complete complete event
     * @param {String} settings.contentType content type header
     * @param {Object} settings.context context of ajax-based events
     * @param {Boolean} settings.crossDomain is a cross-domain request
     * @param {String} settings.data data for post-requests
     * @param {Function} settings.error error event
     * @param {Object} settings.headers header name-value pairs
     * @param {Function} settings.success success event
     * @param {String} settings.url url
     * @param {String|HTMLElement|$.fn} settings.target element to replace
     * @param {Boolean} settings.append the result appended to target
     *        (or replace it)
     * @param {Boolean} settings.prepend the result appended before the target
     * @returns {jqXHR} the jQuery XMLHttpRequest representation
     * @type jqXHR
     * @see jQuery.ajax
     */
    global.Zork.Core.prototype.loadElement = function ( settings )
    {
        if ( settings.target !== "undefined" )
        {
            var target = $( settings.target ),
                append = !! settings.append,
                prepend = !! settings.prepend,
                replace = !! settings.replace,
                layer = js.core.layer(),
                self = this;

            settings = $.extend( {}, settings );

            if ( typeof settings.target !== "undefined" )
            {
                delete settings.target;
            }
            if ( typeof settings.append !== "undefined" )
            {
                delete settings.append;
            }
            if ( typeof settings.prepend !== "undefined" )
            {
                delete settings.prepend;
            }
            if ( typeof settings.replcae !== "undefined" )
            {
                delete settings.replcae;
            }

            return this.ajaj( settings )
                       .done( function ( result ) {
                            if ( prepend )
                            {
                                target.prepend( result.content );
                            }
                            else if ( append )
                            {
                                target.append( result.content );
                            }
                            else if ( replace )
                            {
                                target.replaceWith( result.content );
                            }
                            else
                            {
                                target.html( result.content );
                            }

                            if ( typeof result.links !== "undefined" &&
                                 Array.isArray( result.links ) &&
                                 result.links.length > 0 )
                            {
                                js.link( result.links );
                            }

                            if ( typeof result.styles !== "undefined" &&
                                 Array.isArray( result.styles ) &&
                                 result.styles.length > 0 )
                            {
                                js.style( result.styles );
                            }

                            if ( typeof result.scripts !== "undefined" &&
                                 Array.isArray( result.scripts ) &&
                                 result.scripts.length > 0 )
                            {
                                js.script( result.scripts );
                            }

                            if ( typeof result.modules !== "undefined" &&
                                 Array.isArray( result.modules ) &&
                                 result.modules.length > 0 )
                            {
                                js.require.call( js, result.modules );
                            }

                            self.parseDocument( target );

                       } )
                       .always( layer );
        }
        else
        {
            js.console.warn( "Target required", settings );
            return null;
        }
    };

    /**
     * Load a json request and append to / replace a target
     *
     * @param {Object} settings settings container object
     * @param {Function} settings.beforeSend beforeSend event
     * @param {Boolean} settings.cache cahce befault: true
     * @param {Function} settings.complete complete event
     * @param {String} settings.contentType content type header
     * @param {Object} settings.context context of ajax-based events
     * @param {Boolean} settings.crossDomain is a cross-domain request
     * @param {String} settings.data data for post-requests
     * @param {Function} settings.error error event
     * @param {Object} settings.headers header name-value pairs
     * @param {Function} settings.success success event
     * @param {String} settings.url url
     * @param {String|HTMLElement|$.fn} settings.target element to replace
     * @param {Boolean} settings.append the result appended to target
     *        (or replace it)
     * @param {Boolean} settings.prepend the result appended before the target
     * @returns {jqXHR} the jQuery XMLHttpRequest representation
     * @type jqXHR
     * @see jQuery.ajax
     */
    global.Zork.Core.prototype.loadRawElement = function ( settings )
    {
        if ( settings.target !== "undefined" )
        {
            var target = $( settings.target ),
                append = !! settings.append,
                prepend = !! settings.prepend,
                replace = !! settings.replace,
                success = settings.success,
                layer = js.core.layer( settings.complete );

            settings = $.extend( {}, settings );

            if ( typeof settings.target !== "undefined" )
            {
                delete settings.target;
            }
            if ( typeof settings.append !== "undefined" )
            {
                delete settings.append;
            }
            if ( typeof settings.prepend !== "undefined" )
            {
                delete settings.prepend;
            }
            if ( typeof settings.replace !== "undefined" )
            {
                delete settings.replace;
            }
            if ( typeof settings.complete !== "undefined" )
            {
                delete settings.complete;
            }
            if ( typeof settings.success !== "undefined" )
            {
                delete settings.success;
            }

            settings.dataType = "text";

            return $.ajax( settings )
                    .done( function ( result ) {
                        var parent = target.parent();

                        if ( prepend )
                        {
                            target.prepend( result );
                        }
                        else if ( append )
                        {
                            target.append( result );
                        }
                        else if ( replace )
                        {
                            target.replaceWith( result );
                        }
                        else
                        {
                            target.html( result );
                        }

                        if ( replace )
                        {
                            js.core.parseDocument( parent );
                        }
                        else
                        {
                            js.core.parseDocument( target );
                        }

                        if ( success )
                        {
                            success.call( this, result );
                        }
                    } )
                    .always( layer );
        }
        else
        {
            js.console.warn( "Target required", settings );
            return null;
        }
    };

    /**
     * Returns token value if user-agent contains the token, otherwise false
     * @param {String} token
     * @param {Function} getValue [optional]
     * @type {Number|Boolean}
     */
    global.Zork.Core.prototype.browser = function ( token, getValue )
    {
        var ua = global.navigator.userAgent,
            idx = ua.indexOf( token );

        if ( idx < 0 )
        {
            return false;
        }

        if ( ! Function.isFunction( getValue ) )
        {
            getValue = parseFloat;
        }

        return getValue( ua.substr( idx + token.length ) );
    };

    global.Zork.Core.prototype.browser.opera = (
        ( global.navigator.appName === "Opera" ) ||
        ( global.navigator.userAgent.indexOf( "Opera/" ) >= 0 ) ||
        (
            typeof global.opera !== "undefined" &&
            typeof global.opera.constructor === "function" &&
            Function.isNative( global.opera.constructor )
        )
    ) ? (
        global.Zork.Core.prototype.browser( "Version/" ) ||
        global.Zork.Core.prototype.browser( "Opera/" ) ||
        parseFloat( global.opera.version() )
    ) : false;

    global.Zork.Core.prototype.browser.operaMini = (
        ( global.navigator.userAgent.indexOf( "Opera Mini/" ) >= 0 ) ||
        (
            typeof global.operamini !== "undefined" &&
            typeof global.operamini.constructor === "function" &&
            Function.isNative( global.operamini.constructor )
        )
    ) ? (
        global.Zork.Core.prototype.browser( "Opera Mini/" ) ||
        global.Zork.Core.prototype.browser( "Opera/" ) ||
        parseFloat( global.opera.version() )
    ) : false;

    global.Zork.Core.prototype.browser.firefox =
        global.Zork.Core.prototype.browser( "Firefox/" );

    global.Zork.Core.prototype.browser.gecko = (
        global.navigator.userAgent.indexOf( "Gecko/" ) >= 0
    ) ? (
        global.Zork.Core.prototype.browser( "rv:" )
    ) : false;

    global.Zork.Core.prototype.browser.chrome =
        global.Zork.Core.prototype.browser( "Chrome/" );

    global.Zork.Core.prototype.browser.safari = (
        global.navigator.userAgent.indexOf( "Safari/" ) >= 0
    ) ? (
        global.Zork.Core.prototype.browser( "Version/" )
    ) : false;

    global.Zork.Core.prototype.browser.webkit =
        global.Zork.Core.prototype.browser( "WebKit/" );

    global.Zork.Core.prototype.browser.msie = (
        ! global.Zork.Core.prototype.browser.opera
    ) ? (
        global.Zork.Core.prototype.browser( "MSIE " )
    ) : false;

    var addBodyClasses = function ()
    {
        var i, html = $( "html" ),
            classBase = "js-browser-",
            sample = global.document.createElement( "div" ),
            ie = js.core.browser.msie;

        for ( i in js.core.browser )
        {
            if ( typeof js.core.browser[i] !== "undefined" && i !== "prototype" &&
                typeof Function.prototype[i] === "undefined" && js.core.browser[i] )
            {
                html.addClass( classBase + i ).addClass( classBase + i + "-" +
                    parseInt( js.core.browser[i], 10 ) ).addClass( classBase +
                    i + "-" + String( js.core.browser[i] ).replace( ".", "-" ) );
            }
        }

        if ( ! $.support.ajax )
        {
            html.addClass( classBase + "no-ajax" );
        }
        if ( ! $.support.boxModel )
        {
            html.addClass( classBase + "quirks-boxes" );
        }
        if ( ! $.support.opacity )
        {
            html.addClass( classBase + "no-opacity" );
        }

        try
        {
            sample.style.color = "rgba(0,0,0,0.5)";
        }
        catch( e )
        {
            html.addClass( classBase + "no-rgba" );
        }

        if ( ie && ( ie >= 5.5 ) && ( ie < 10 ) )
        {
            html.addClass( classBase + "gradient-filter" );
        }
    };

    /**
     * Onload
     */
    $( function ()
    {
        documentReady         = true;
        js.core.defaultLocale = $( "meta[name='zork:locale']" ).attr( "content" )     || "en";
        js.core.userLocale    = $( "meta[name='zork:userlocale']" ).attr( "content" ) || js.core.defaultLocale;
        js.core.uploadsUrl    = $( "meta[name='zork:uploads']" ).attr( "content" );

        addBodyClasses();
        js.core.parseDocument();
    } );

    /**
     * @class Ui module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Ui = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "ui" ];
    };

    loadedModules[ "zork/ui" ] =
        global.Zork.prototype.ui =
        new global.Zork.Ui();

 /* var onErrorOld = global.onerror,
        onErrorInside = false,
        onErrorRpc = null;

    global.onerror = function ( message, file, line )
    {
        if ( ! onErrorInside )
        {
            onErrorInside = true;

            if ( onErrorRpc === null )
            {
                onErrorRpc = js.core.rpc( {
                    "method": "coreErrorHandler",
                    "callback": function () {
                        js.console.log( "Error log sent:", message,
                            "@", file, ":", line );
                    }
                } );
            }

            onErrorRpc.invoke( {
                "message": message,
                "file": file,
                "line": line
            } );

            if ( message.charAt( 0 ) === "@" )
            {
                message = js.core.translate( message.replace( /^@/, "" ) );
            }

            js.require( "js.ui.dialog", function ( dialog ) {
                dialog( {
                    "title": js.core.translate( "default.error.dialog.error" ),
                    "message": message
                } );
            } );

            onErrorInside = false;
        }

        if ( Function.isFunction( onErrorOld ) )
        {
            return onErrorOld.call( message, file, line );
        }

        return false;
    }; */

    /**
     * Parse url-like query
     *
     * @param {String} query query to parse
     * @param {String} separator [optional] default: "&"
     * @param {String} assignment [optional] default: "="
     */
    var parseQuery = function ( query, separator, assignment )
    {
        separator = separator || "&";
        assignment = assignment || "=";
        var result = {};
        String( query ).split( separator ).forEach( function ( part )
        {
            var idx = part.indexOf( assignment ),
                key = null, value = null;
            if ( idx === 0 ) {return;}
            if ( idx < 0 )
            {
                key = part;
                value = true;
            }
            else
            {
                key = part.substr( 0, idx );
                value = part.substr( idx + 1 );
            }
            result[key] = value;
        } );
        return result;
    };

    /**
     * Stay logged-in forever
     */
    ( function () {
        var timeout = 1100000,
            method = "Grid\\User\\Model\\User\\Rpc::status",
            status = js.core.rpc( {
                "method"    : method,
                "callback"  : function ( res ) {
                    js.console.log( method, res );
                    if ( res.loggedIn ) {
                        check();
                    }
                }
            } ),
            check = function () {
                setTimeout( status, timeout );
            };

        check();
    } () );

} ( window, jQuery ) );
