/**
 * Hash functionalities
 * @package zork
 * @subpackage hash
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.hash.php !== "undefined" )
    {
        return;
    }

    /**
     * @class Php
     * @constructor
     * @memberOf Zork.Hash
     */
    global.Zork.Hash.Php = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "hash", "php" ];
    };

    global.Zork.Hash.prototype.php = new global.Zork.Hash.Php();

    /**
     * Encode to php-serialized
     * @param {String} input
     * @type String
     */
    global.Zork.Hash.Php.prototype.encode =
    global.Zork.Hash.Php.prototype.serialize = function ( input )
    {
        var _getType = function ( inp )
        {
            var type = typeof inp, match;
            var key;
            if ( type === "object" && ! inp ) { return "null"; }
            if ( type === "object" )
            {
                if ( ! inp.constructor ) { return "object"; }
                var cons = inp.constructor.toString();
                match = cons.match( /(\w+)\(/ );
                if ( match ) { cons = match[1].toLowerCase(); }
                var types = [ "boolean", "number", "string", "array" ];
                for ( key in types )
                {
                    if ( typeof types[key] !== "undefined" && cons === types[key] )
                    {
                        type = types[key];
                        break;
                    }
                }
            }
            return type;
        };

        var type = _getType( input );
        var val = "";

        switch ( type )
        {
            case "function":
                val = "";
                break;
            case "boolean":
                val = "b:" + ( input ? "1" : "0" );
                break;
            case "number":
                val = ( Math.round( input ) === input ? "i" : "d" ) + ":" + input;
                break;
            case "string":
                val = "s:" + encodeURIComponent( input ).replace( /%../g, 'x' ).
                    length + ":\"" + input + "\"";
                break;
            case "array":
            case "object":
                val = "a";
                var count = 0,
                    vals = "",
                    key;
                for ( key in input )
                {
                    if ( typeof input[key] !== "undefined" )
                    {
                        var ktype = _getType( input[key] );
                        if ( ktype === "function" ) { continue; }
                        var okey = ( key.match( /^[0-9]+$/ ) ?
                            parseInt( key, 10 ) : key );
                        vals += this.serialize( okey ) +
                                this.serialize( input[key] );
                        count++;
                    }
                }
                val += ":" + count + ":{" + vals + "}";
                break;
            case "undefined":
            default:
                val = "N";
                break;
        }
        if ( type !== "object" && type !== "array" )
        {
            val += ";";
        }
        return val;
    };

    /**
     * Decode from php-serialized (unserialize)
     * @param {String} input
     * @type String
     */
    global.Zork.Hash.Php.prototype.decode =
    global.Zork.Hash.Php.prototype.unserialize = function ( input )
    {
        var error = function ( type, msg, filename, line )
        {
            throw new this.window[type]( msg, filename, line );
        };
        var read_until = function ( data, offset, stopchr )
        {
            var buf = [];
            var chr = data.slice( offset, offset + 1 );
            var i = 2;
            while ( chr !== stopchr )
            {
                if ( ( i + offset ) > data.length )
                {
                    error( "Error", "Invalid" );
                }
                buf.push( chr );
                chr = data.slice( offset + ( i - 1 ), offset + i );
                i += 1;
            }
            return [ buf.length, buf.join( "" ) ];
        };
        var read_chrs = function ( data, offset, length )
        {
            var buf = [], i;
            for ( i = 0; i < length; i++ )
            {
                var chr = data.slice( offset + (i - 1), offset + i );
                buf.push( chr );
            }
            return [ buf.length, buf.join( "" ) ];
        };
        var _unserialize = function ( data, offset )
        {
            var readdata;
            var readData;
            var chrs = 0;
            var ccount;
            var stringlength;
            var keyandchrs;
            var keys;

            if ( ! offset ) { offset = 0; }
            var dtype = ( data.slice( offset, offset + 1 ) ).toLowerCase();

            var dataoffset = offset + 2;
            var typeconvert = new Function( 'x', 'return x' );

            switch ( dtype )
            {
                case 'i':
                    typeconvert = function ( x ) { return parseInt( x, 10 ); };
                    readData = read_until( data, dataoffset, ';' );
                    chrs = readData[0];
                    readdata = readData[1];
                    dataoffset += chrs + 1;
                break;
                case 'b':
                    typeconvert = function ( x ) { return parseInt( x, 10 ) !== 0; };
                    readData = read_until( data, dataoffset, ';' );
                    chrs = readData[0];
                    readdata = readData[1];
                    dataoffset += chrs + 1;
                break;
                case 'd':
                    typeconvert = function ( x ) { return parseFloat( x ); };
                    readData = read_until( data, dataoffset, ';' );
                    chrs = readData[0];
                    readdata = readData[1];
                    dataoffset += chrs + 1;
                break;
                case 'n':
                    readdata = null;
                break;
                case 's':
                    ccount = read_until( data, dataoffset, ':' );
                    chrs = ccount[0];
                    stringlength = ccount[1];
                    dataoffset += chrs + 2;
                    readData = read_chrs( data, dataoffset + 1, parseInt( stringlength, 10 ) );
                    chrs = readData[0];
                    readdata = readData[1];
                    dataoffset += chrs + 2;
                    if ( chrs !== parseInt( stringlength, 10 ) && chrs !== readdata.length )
                    {
                        error( 'SyntaxError', 'String length mismatch' );
                    }
                break;
            case 'a':
                    readdata = {};

                    keyandchrs = read_until( data, dataoffset, ':' );
                    chrs = keyandchrs[0];
                    keys = keyandchrs[1];
                    dataoffset += chrs + 2;

                    var i, pk = parseInt( keys, 10 );
                    for ( i = 0; i < pk; i++ )
                    {
                        var kprops = _unserialize( data, dataoffset );
                        var kchrs = kprops[1];
                        var key = kprops[2];
                        dataoffset += kchrs;

                        var vprops = _unserialize( data, dataoffset );
                        var vchrs = vprops[1];
                        var value = vprops[2];
                        dataoffset += vchrs;

                        readdata[key] = value;
                    }

                    dataoffset += 1;
                break;
                default:
                    error( 'SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype );
                break;
            }
            return [ dtype, dataoffset - offset, typeconvert(readdata) ];
        };

        return _unserialize( String( input ), 0 )[2];
    };

} ( window, jQuery, zork ) );
