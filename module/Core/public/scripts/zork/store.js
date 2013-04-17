/**
 * User interface functionalities
 * @package zork
 * @subpackage store
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.store !== "undefined" )
    {
        return;
    }

    /**
     * @class Store module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Store = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "store" ];
    };

    global.Zork.prototype.store = new global.Zork.Store();

    var storageDoc,
        prefix = "js-",
        sessionGet = function ()
        {
            js.console.error( "session not supported" );
        },
        sessionSet = sessionGet,
        cacheGet = function ()
        {
            js.console.error( "cache not supported" );
        },
        cacheSet = cacheGet,
        cacheIterator = null;

    if ( typeof global.sessionStorage !== "undefined" )
    {
        sessionGet = function ( key )
        {
            return $.parseJSON( global.sessionStorage.getItem( prefix + key ) );
        };

        sessionSet = function ( key, value )
        {
            if ( Object.isUndefined( value ) )
            {
                global.sessionStorage.removeItem( prefix + key );
            }
            else
            {
                global.sessionStorage.setItem( prefix + key,
                    $.toJSON( value ) );
            }
            return value;
        };
    }
    else if ( typeof global.globalStorage !== "undefined" ) // older ff
    {
        sessionGet = function ( key )
        {
            return $.parseJSON( global.globalStorage[global.location.hostname].
                getItem( prefix + "ses-" + key ) );
        };

        sessionSet = function ( key, value )
        {
            if ( Object.isUndefined( value ) )
            {
                global.globalStorage[global.location.hostname].
                    removeItem( prefix + "ses-" + key );
            }
            else
            {
                global.globalStorage[global.location.hostname].
                    setItem( prefix + "ses-" + key, $.toJSON( value ) );
            }
            return value;
        };
    }
    else if ( typeof global.document.createElement( "input" ).addBehavior !== "undefined" )
    {
        var storageFrame = $( '<iframe id="html5Storage" src="/" ' +
            'style="display: none"></iframe>' );
        $( "body" ).append( storageFrame );
        storageDoc = storageFrame.contents()[0];
        storageDoc.close();

        var _sessionStorage = storageDoc.createElement(
            '<input id="html5SessionStorage" type="hidden" />'
        );
        _sessionStorage.addBehavior( "#default#userData" );
        storageDoc.body.appendChild( _sessionStorage );

        if ( typeof _sessionStorage.save !== "undefined" &&
             typeof _sessionStorage.load !== "undefined" )
        {
            var exp = new Date();
            exp.setDate( exp.getDate() + 1 );
            _sessionStorage.expires = exp.toUTCString();

            sessionGet = function ( key )
            {
                _sessionStorage.load( _sessionStorage.id );
                return $.parseJSON( _sessionStorage.
                    getAttribute( prefix + key ) );
            };

            sessionSet = function ( key, value )
            {
                _sessionStorage.load( _sessionStorage.id );

                if ( Object.isUndefined( value ) )
                {
                    _sessionStorage.removeAttribute( prefix + key );
                }
                else
                {
                    _sessionStorage.setAttribute( prefix + key,
                        $.toJSON( value ) );
                }

                _sessionStorage.save( _sessionStorage.id );
                return value;
            };
        }
    }
    else if ( typeof global.document.cookie !== "undefined" )
    {
        sessionGet = function ( key )
        {
            var nameEQ = prefix + String( key ).replace( "_61", "=" ).
                replace( "__", "_" ) + '=',
                ca = global.document.cookie.split( ';' ),
                i, l = ca.length;

            for ( i = 0; i < l; i++ )
            {
                var c = ca[i];

                while ( c.charAt( 0 ) === ' ' )
                {
                    c = c.substring( 1, c.length );
                }

                if ( c.indexOf( nameEQ ) === 0 )
                {
                    return $.parseJSON(
                        c.substring( nameEQ.length, c.length ).
                            replace( "\\59", ";" ).replace( "\\\\", "\\" )
                    );
                }
            }

            return null;
        };

        sessionSet = function ( key, value )
        {
            var date = new Date();
            date.setTime(
                date.getTime() + (
                    ( Object.isUndefined( value ) ? -1 : 1 ) *
                        86400000 // 24 * 60 * 60 * 1000
                )
            );

            var expires = '; expires=' + date.toGMTString();
            global.document.cookie = prefix +
                key.replace( "_", "__" ).replace( "=", "_61" ) + '=' +
                ( Object.isUndefined( value ) ? "" : $.toJSON( value ).
                    replace( "\\", "\\\\" ).replace( ";", "\\59" ) ) +
                expires + '; path=/';
        };
    }

    if ( typeof global.localStorage !== "undefined" )
    {
        cacheGet = function ( key )
        {
            var result = global.localStorage.getItem( prefix + key );
            if ( Object.isUndefined( result ) ) { return []; }

            result = result.split( ";", 3 );
            if ( parseInt( result[1], 10 ) < Date.now() )
            {
                global.localStorage.removeItem( prefix + key );
                return [];
            }
            else
            {
                result[0] = Date.now();
                global.localStorage.setItem( prefix + key, result.join( ";" ) );
            }

            result[2] = $.parseJSON( result[2] );
            return result;
        };

        cacheSet = function ( key, value, ttl )
        {
            if ( Object.isUndefined( value ) )
            {
                global.localStorage.removeItem( prefix + key );
            }
            else
            {
                var get = global.localStorage.getItem( prefix + key ),
                    now = Date.now(), lastHit = 0;

                if ( ! Object.isUndefined( get ) )
                {
                    get = get.split( ";", 3 );
                    lastHit = get[0];
                }

                global.localStorage.setItem( prefix + key, lastHit + ";" +
                    ( now + ttl ) + ";" + $.toJSON( value ) );
            }
            return value;
        };

        cacheIterator = function ( callback )
        {
            var i, l = global.localStorage.length;
            for ( i = 0; i < l; ++i )
            {
                var key = global.localStorage.key(),
                    value = global.localStorage.getItem( key ).split( ";", 3 );

                if ( key.indexOf( prefix ) === 0 )
                {
                    callback( key.substr( prefix.length ), value[0], value[1] );
                }
            }
        };
    }
    else if ( typeof global.globalStorage !== "undefined" ) // older ff
    {
        cacheGet = function ( key )
        {
            var result = global.globalStorage[global.location.hostname].
                    getItem( prefix + key );

            if ( Object.isUndefined( result ) ) { return []; }

            result = result.split( ";", 3 );
            if ( parseInt( result[1], 10 ) < Date.now() )
            {
                global.globalStorage[global.location.hostname].
                    removeItem( prefix + key );
                return [];
            }
            else
            {
                result[0] = Date.now();
                global.globalStorage[global.location.hostname].
                    setItem( prefix + key, result.join( ";" ) );
            }

            result[2] = $.parseJSON( result[2] );
            return result;
        };

        cacheSet = function ( key, value, ttl )
        {
            if ( Object.isUndefined( value ) )
            {
                global.globalStorage[global.location.hostname].
                    removeItem( prefix + key );
            }
            else
            {
                var get = global.globalStorage[global.location.hostname].
                        getItem( prefix + key ),
                    now = Date.now(), lastHit = 0;

                if ( ! Object.isUndefined( get ) )
                {
                    get = get.split( ";", 3 );
                    lastHit = get[0];
                }

                global.globalStorage[global.location.hostname].setItem(
                    prefix + key, lastHit + ";" +
                    ( now + ttl ) + ";" + $.toJSON( value )
                );
            }
            return value;
        };

        cacheIterator = function ( callback )
        {
            var i, l = global.globalStorage[global.location.hostname].length;
            for ( i = 0; i < l; ++i )
            {
                var key = global.globalStorage[global.location.hostname].key(),
                    value = global.globalStorage[global.location.hostname].
                        getItem( key ).split( ";", 3 );

                if ( key.indexOf( prefix ) === 0 )
                {
                    callback( key.substr( prefix.length ), value[0], value[1] );
                }
            }
        };
    }
    else if ( typeof global.document.createElement( "input" ).
        addBehavior !== "undefined" )
    {
        var _localStorage = storageDoc.createElement(
            '<input id="html5LocalStorage" type="hidden" />'
        );

        _localStorage.addBehavior( "#default#userData" );
        storageDoc.body.appendChild( _localStorage );

        if ( typeof _localStorage.save !== "undefined" &&
             typeof _localStorage.load !== "undefined" )
        {
            cacheGet = function ( key )
            {
                _localStorage.load( _localStorage.id );

                var result = _localStorage.getAttribute( prefix + key );

                if ( Object.isUndefined( result ) ) { return []; }

                result = result.split( ";", 3 );
                if ( parseInt( result[1], 10 ) < Date.now() )
                {
                    _localStorage.removeAttribute( prefix + key );
                    return [];
                }
                else
                {
                    result[0] = Date.now();
                    _localStorage.setAttribute( prefix + key,
                        result.join( ";" ) );
                }

                result[2] = $.parseJSON( result[2] );
                return result;
            };

            cacheSet = function ( key, value, ttl )
            {
                _localStorage.load( _localStorage.id );

                var keys = ( _localStorage.
                        getAttribute( "keys" ) || "").split( ";" ),
                    keyInsert = String( key ).
                        replace( "\\", "\\\\" ).replace( ";", "\\59" ),
                    index = keys.indexOf( keyInsert );

                if ( Object.isUndefined( value ) )
                {
                    _localStorage.removeAttribute( prefix + key );
                    if ( index >= 0 )
                    {
                        keys.splice( index, 1 );
                        _localStorage.setAttribute( "keys", keys.join( ";" ) );
                    }
                }
                else
                {
                    var get = _localStorage.geAttribute( prefix + key ),
                        now = Date.now(), lastHit = 0;

                    if ( ! Object.isUndefined( get ) )
                    {
                        get = get.split( ";", 3 );
                        lastHit = get[0];
                    }

                    _localStorage.setAttribute( prefix + key, lastHit + ";" +
                        ( now + ttl ) + ";" + $.toJSON( value ) );

                    if ( index < 0 )
                    {
                        keys.push( keyInsert );
                        _localStorage.setAttribute( "keys", keys.join( ";" ) );
                    }
                }

                _localStorage.save( _localStorage.id );
                return value;
            };

            cacheIterator = function ( callback )
            {
                _localStorage.load( _localStorage.id );
                var keys = ( _localStorage.
                        getAttribute( "keys" ) || "").split( ";" ),
                    i, l = keys.length;

                for ( i = 0; i < l; ++i )
                {
                    var key = keys[i].
                            replace( "\\59", ";" ).replace( "\\\\", "\\" ),
                        value = _localStorage.
                            getAttribute( prefix + key ).split( ";", 3 );
                    callback( key, value[0], value[1] );
                }
            };
        }
    }

    /**
     * Get/set session item by key
     *
     * @function
     * @param {String} key
     * @param {Object} value
     * @type Object
     */
    global.Zork.Store.prototype.session = function ( key, value )
    {
        if ( typeof value === "undefined" )
        {
            return sessionGet( key );
        }
        
        return sessionSet( key, value );
    };

    /**
     * Get/set session item by key
     *
     * @function
     * @param {String} key
     * @param {Object} value
     * @param {Number} ttl time to live (in milliseconds)
     * @type Object
     */
    global.Zork.Store.prototype.cache = function ( key, value, ttl )
    {
        if ( cacheIterator === null )
        {
            js.console.error( "cache not supported" );
            return null;
        }
        
        if ( typeof value === "undefined" )
        {
            var get = cacheGet( key );
            if ( get.length > 0 ) { return get[2]; }

            return null;
        }

        ttl = ttl || this.cache.defaultTtl;
        
        cacheSet( key, value, ttl );
        
        if ( ( value !== null ) && ( cacheGet( key ).length < 1 ) && ttl > 0 )
        {
            var now = Date.now(),
                obsoletes = [];

            cacheIterator( function ( key, lastHit, maxLive )
            {
                if ( maxLive < now ) { obsoletes.push( key ); }
            } );

            obsoletes.forEach( function ( key )
            {
                cacheSet( key, null );
            } );

            cacheSet( key, value, ttl );

            var minKey,
                minHit = null,
                iterate = function ( key, lastHit, maxLive )
                {
                    if ( ( minHit === null ) || ( lastHit < minHit ) )
                    {
                        minHit = lastHit;
                        minKey = key;
                    }
                };

            while ( cacheGet( key ).length < 1 )
            {
                minHit = null;
                minKey = null;
                cacheIterator( iterate );
                
                if ( minKey === null )
                {
                    return null;
                }

                cacheSet( minKey, null );
                cacheSet( key, value, ttl );
            }
        }

        return value;
    };

    /**
     * Default cache ttl (time-to-live in milliseconds)
     */
    global.Zork.Store.prototype.cache.defaultTtl = 86400000; // a day

} ( window, jQuery, zork ) );
