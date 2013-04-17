/**
 * Hash functionalities
 * @package zork
 * @subpackage hash
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.hash.base64 !== "undefined" )
    {
        return;
    }

    /**
     * @class Base64
     * @constructor
     * @memberOf Zork.Hash
     */
    global.Zork.Hash.Base64 = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "hash", "base64" ];
    };

    global.Zork.Hash.prototype.base64 = new global.Zork.Hash.Base64();

    var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz" +
                 "0123456789+/=",
        utf8_encode = function ( string )
        {
            string = string.replace( /\r\n/g, "\n" );
            var utftext = "", n, l = string.length;
            for ( n = 0; n < l; n++ )
            {
                var c = string.charCodeAt(n);
                if ( c < 128 )
                {
                    utftext += String.fromCharCode( c );
                }
                else if ( ( c > 127 ) && ( c < 2048 ) )
                {
                    utftext += String.fromCharCode( ( c >> 6 ) | 192 );
                    utftext += String.fromCharCode( ( c & 63 ) | 128 );
                }
                else
                {
                    utftext += String.fromCharCode( ( c >> 12 ) | 224 );
                    utftext += String.fromCharCode( ( ( c >> 6 ) & 63 ) | 128 );
                    utftext += String.fromCharCode( ( c & 63 ) | 128 );
                }
            }
            return utftext;
        },
        utf8_decode = function ( utftext )
        {
            var string = "";
            var i = 0;
            var c, c2, c3;
            while ( i < utftext.length )
            {
                c = utftext.charCodeAt( i );
                if ( c < 128 )
                {
                    string += String.fromCharCode( c );
                    i++;
                }
                else if ( ( c > 191 ) && ( c < 224 ) )
                {
                    c2 = utftext.charCodeAt( i + 1 );
                    string += String.fromCharCode( ( ( c & 31 ) << 6 ) |
                        ( c2 & 63 ) );
                    i += 2;
                }
                else
                {
                    c2 = utftext.charCodeAt( i + 1 );
                    c3 = utftext.charCodeAt( i + 2 );
                    string += String.fromCharCode( ( ( c & 15 ) << 12 ) |
                        ( ( c2 & 63 ) << 6 ) | ( c3 & 63 ) );
                    i += 3;
                }
            }
            return string;
        };

    /**
     * Encode to base64
     * @param {String} input
     * @type String
     */
    global.Zork.Hash.Base64.prototype.encode = function ( input )
    {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;
        input = utf8_encode( input );
        while ( i < input.length )
        {
            chr1 = input.charCodeAt( i++ );
            chr2 = input.charCodeAt( i++ );
            chr3 = input.charCodeAt( i++ );
            enc1 = chr1 >> 2;
            enc2 = ( ( chr1 & 3 ) << 4 ) | ( chr2 >> 4 );
            enc3 = ( ( chr2 & 15 ) << 2 ) | ( chr3 >> 6 );
            enc4 = chr3 & 63;
            if ( isNaN( chr2 ) ) { enc3 = enc4 = 64; }
            else if ( isNaN( chr3 ) ) { enc4 = 64; }
            output = output +
                keyStr.charAt( enc1 ) + keyStr.charAt( enc2 ) +
                keyStr.charAt( enc3 ) + keyStr.charAt( enc4 );
        }
        return output;
    };

    /**
     * Decode from base64
     * @param {String} input
     * @type String
     */
    global.Zork.Hash.Base64.prototype.decode = function ( input )
    {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;
        input = input.replace( /[^A-Za-z0-9\+\/\=]/g, "" );
        while ( i < input.length )
        {
            enc1 = keyStr.indexOf( input.charAt( i++ ) );
            enc2 = keyStr.indexOf( input.charAt( i++ ) );
            enc3 = keyStr.indexOf( input.charAt( i++ ) );
            enc4 = keyStr.indexOf( input.charAt( i++ ) );
            chr1 = ( enc1 << 2 ) | ( enc2 >> 4 );
            chr2 = ( ( enc2 & 15 ) << 4 ) | ( enc3 >> 2 );
            chr3 = ( ( enc3 & 3 ) << 6 ) | enc4;
            output = output + String.fromCharCode( chr1 );
            if ( enc3 !== 64 ) { output = output + String.fromCharCode( chr2 ); }
            if ( enc4 !== 64 ) { output = output + String.fromCharCode( chr3 ); }
        }
        output = utf8_decode( output );
        return output;
    };

} ( window, jQuery, zork ) );
