/**
 * Color functionalities
 * @package zork
 * @subpackage color
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";
    
    if ( typeof js.color !== "undefined" )
    {
        return;
    }
    
    /**
     * @class Color module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Color = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "color" ];
    };
    
    global.Zork.prototype.color = new global.Zork.Color();
    
    /**
     * r, g, b[, a] in [0.0, 1.0]
     * @class rgb
     * @param {Number} r
     * @param {Number} g
     * @param {Number} b
     * @param {Number} a [optional]
     */
    global.Zork.Color.Rgb = function ( r, g, b, a )
    {
        this.r = r;
        this.g = g;
        this.b = b;
        this.a = a;
    };
    
    /**
     * c, m, y[, a] in [0.0, 1.0]
     * @class cmy
     * @param {Number} c
     * @param {Number} m
     * @param {Number} y
     * @param {Number} a [optional]
     */
    global.Zork.Color.Cmy = function ( c, m, y, a )
    {
        this.c = c;
        this.m = m;
        this.y = y;
        this.a = a;
    };
    
    /**
     * h in [0.0, 360.0]; s, v[, a] in [0.0, 1.0]
     * @class hsv
     * @param {Number} h
     * @param {Number} s
     * @param {Number} v
     * @param {Number} a [optional]
     */
    global.Zork.Color.Hsv = function ( h, s, v, a )
    {
        this.h = h;
        this.s = s;
        this.v = v;
        this.a = a;
    };
    
    /**
     * h in [0.0, 360.0]; s, l[, a] in [0.0, 1.0]
     * @class hsl
     * @param {Number} h
     * @param {Number} s
     * @param {Number} l
     * @param {Number} a [optional]
     */
    global.Zork.Color.Hsl = function ( h, s, l, a )
    {
        this.h = h;
        this.s = s;
        this.l = l;
        this.a = a;
    };
    
    /**
     * r, g, b[, a] in [0.0, 1.0]
     * @class rgb
     * @param {Number} r
     * @param {Number} g
     * @param {Number} b
     * @param {Number} a [optional]
     * @type {Zork.Color.Rgb}
     */
    global.Zork.Color.prototype.rgb = function ( r, g, b, a )
    {
        return new global.Zork.Color.Rgb( r, g, b, a );
    };
    
    /**
     * c, m, y[, a] in [0.0, 1.0]
     * @class cmy
     * @param {Number} c
     * @param {Number} m
     * @param {Number} y
     * @param {Number} a [optional]
     * @type {Zork.Color.Cmy}
     */
    global.Zork.Color.prototype.cmy = function ( c, m, y, a )
    {
        return new global.Zork.Color.Cmy( c, m, y, a );
    };
    
    /**
     * h in [0.0, 360.0]; s, v[, a] in [0.0, 1.0]
     * @class hsv
     * @param {Number} h
     * @param {Number} s
     * @param {Number} v
     * @param {Number} a [optional]
     * @type {Zork.Color.Hsv}
     */
    global.Zork.Color.prototype.hsv = function ( h, s, v, a )
    {
        return new global.Zork.Color.Hsv( h, s, v, a );
    };
    
    /**
     * h in [0.0, 360.0]; s, l[, a] in [0.0, 1.0]
     * @class hsl
     * @param {Number} h
     * @param {Number} s
     * @param {Number} l
     * @param {Number} a [optional]
     * @type {Zork.Color.Hsl}
     */
    global.Zork.Color.prototype.hsl = function ( h, s, l, a )
    {
        return new global.Zork.Color.Hsl( h, s, l, a );
    };
    
    /**
     * Convert rgb to cmy
     * @param {Zork.Color.Rgb} c1
     * @type {Zork.Color.Cmy}
     */
    global.Zork.Color.prototype.rgb2cmy = function ( c1 /** r, g, b */ )
    {
        return new global.Zork.Color.Cmy( 1 - c1.r, 1 - c1.g, 1 - c1.b, c1.a );
    };
    
    /**
     * Convert rgb to cmy
     * @param {Zork.Color.Cmy} c1
     * @type {Zork.Color.Rgb}
     */
    global.Zork.Color.prototype.cmy2rgb = function ( c1 /** c, m, y */ )
    {
        return new global.Zork.Color.Rgb(1 - c1.c, 1 - c1.m, 1 - c1.y, c1.a);
    };
    
    /**
     * Convert rgb to cmy
     * @param {Zork.Color.Rgb} c1
     * @type {Zork.Color.Hsv}
     */
    global.Zork.Color.prototype.rgb2hsv = function ( c1 /** r, g, b */ )
    {
        var themin = Math.min( c1.r, c1.g, c1.b );
        var themax = Math.max( c1.r, c1.g, c1.b );
        var delta = themax - themin;
        var c2 = new global.Zork.Color.Hsv( 0, 0, themax );
        if ( themax > 0 ) {c2.s = delta / themax;}
        if ( delta > 0 )
        {
            if ( themax === c1.r && themax !== c1.g )
            {c2.h += ( c1.g - c1.b ) / delta;}
            if ( themax === c1.g && themax !== c1.b )
            {c2.h += ( 2 + ( c1.b - c1.r ) / delta);}
            if ( themax === c1.b && themax !== c1.r )
            {c2.h += ( 4 + ( c1.r - c1.g ) / delta);}
            c2.h *= 60;
        }
        c2.a = c1.a;
        return c2;
    };
    
    /**
     * Convert rgb to cmy
     * @param {Zork.Color.Hsv} c1
     * @type {Zork.Color.Rgb}
     */
    global.Zork.Color.prototype.hsv2rgb = function ( c1 /** h, s, v */ )
    {
        var c2 = new global.Zork.Color.Rgb();
        var sat = new global.Zork.Color.Rgb();
        
        while ( c1.h < 0 ) {c1.h += 360;}
        while ( c1.h > 360 ) {c1.h -= 360;}
        
        if ( c1.h < 120 )
        {
            sat.r = ( 120 - c1.h ) / 60.0;
            sat.g = c1.h / 60.0;
            sat.b = 0;
        }
        else if ( c1.h < 240 )
        {
            sat.r = 0;
            sat.g = ( 240 - c1.h ) / 60.0;
            sat.b = ( c1.h - 120 ) / 60.0;
        }
        else
        {
            sat.r = ( c1.h - 240 ) / 60.0;
            sat.g = 0;
            sat.b = ( 360 - c1.h ) / 60.0;
        }
        sat.r = Math.min( sat.r, 1 );
        sat.g = Math.min( sat.g, 1 );
        sat.b = Math.min( sat.b, 1 );
        
        c2.r = ( 1 - c1.s + c1.s * sat.r ) * c1.v;
        c2.g = ( 1 - c1.s + c1.s * sat.g ) * c1.v;
        c2.b = ( 1 - c1.s + c1.s * sat.b ) * c1.v;
        c2.a = c1.a;
        
        return c2;
    };
    
    /**
     * Convert rgb to cmy
     * @param {Zork.Color.Rgb} c1
     * @type {Zork.Color.Hsl}
     */
    global.Zork.Color.prototype.rgb2hsl = function ( c1 /** r, g, b */ )
    {
        var themin = Math.min( c1.r, c1.g, c1.b );
        var themax = Math.max( c1.r, c1.g, c1.b );
        var delta = themax - themin;
        var c2 = new global.Zork.Color.Hsl( 0, 0, ( themin + themax ) / 2 );
        if ( c2.l > 0 && c2.l < 1 )
        {
            c2.s = delta / ( c2.l < 0.5 ? ( 2 * c2.l ) : ( 2 - 2 * c2.l ) );
        }
        if ( delta > 0 )
        {
            if ( themax === c1.r && themax !== c1.g )
            {c2.h += ( c1.g - c1.b ) / delta;}
            if ( themax === c1.g && themax !== c1.b )
            {c2.h += ( 2 + ( c1.b - c1.r ) / delta );}
            if ( themax === c1.b && themax !== c1.r )
            {c2.h += ( 4 + ( c1.r - c1.g ) / delta );}
            c2.h *= 60;
        }
        c2.a = c1.a;
        return c2;
    };
    
    /**
     * Convert rgb to cmy
     * @param {Zork.Color.Hsl} c1
     * @type {Zork.Color.Rgb}
     */
    global.Zork.Color.prototype.hsl2rgb = function ( c1 /** h, s, l */ )
    {
        var c2 = new global.Zork.Color.Rgb();
        var sat = new global.Zork.Color.Rgb();
        var ctmp = new global.Zork.Color.Rgb();
        
        while ( c1.h < 0 ) {c1.h += 360;}
        while ( c1.h > 360 ) {c1.h -= 360;}
        
        if ( c1.h < 120 )
        {
            sat.r = (120 - c1.h) / 60.0;
            sat.g = c1.h / 60.0;
            sat.b = 0;
        }
        else if ( c1.h < 240 )
        {
            sat.r = 0;
            sat.g = ( 240 - c1.h ) / 60.0;
            sat.b = ( c1.h - 120 ) / 60.0;
        }
        else
        {
            sat.r = ( c1.h - 240 ) / 60.0;
            sat.g = 0;
            sat.b = ( 360 - c1.h ) / 60.0;
        }
        sat.r = Math.min( sat.r, 1 );
        sat.g = Math.min( sat.g, 1 );
        sat.b = Math.min( sat.b, 1 );
        
        ctmp.r = 2 * c1.s * sat.r + ( 1 - c1.s );
        ctmp.g = 2 * c1.s * sat.g + ( 1 - c1.s );
        ctmp.b = 2 * c1.s * sat.b + ( 1 - c1.s );
        
        if (c1.l < 0.5)
        {
            c2.r = c1.l * ctmp.r;
            c2.g = c1.l * ctmp.g;
            c2.b = c1.l * ctmp.b;
        }
        else
        {
            c2.r = ( 1 - c1.l ) * ctmp.r + 2 * c1.l - 1;
            c2.g = ( 1 - c1.l ) * ctmp.g + 2 * c1.l - 1;
            c2.b = ( 1 - c1.l ) * ctmp.b + 2 * c1.l - 1;
        }
        
        c2.a = c1.a;
        return c2;
    };
    
    /**
     * parse css string to color
     * @param {String} str
     * @type {Zork.Color.Rgb|Zork.Color.Hsl}
     */
    global.Zork.Color.prototype.parse = function ( str )
    {
        var m;
        m = str.match( /#([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})/ );
        if ( m )
        {
            return new global.Zork.Color.Rgb(
                parseInt( m[1], 16 ) / 255,
                parseInt( m[2], 16 ) / 255,
                parseInt( m[3], 16 ) / 255,
                1
            );
        }
        m = str.match( /#([0-9a-fA-F])([0-9a-fA-F])([0-9a-fA-F])/ );
        if ( m )
        {
            return new global.Zork.Color.Rgb(
                parseInt( m[1], 16 ) / 15,
                parseInt( m[2], 16 ) / 15,
                parseInt( m[3], 16 ) / 15,
                1
            );
        }
        m = str.match( /rgba?\(\s*([0-9]{1,3})%\s*,\s*([0-9]{1,3})%\s*,\s*?([0-9]{1,3})%,?\s*([0-9\.]+)?/ );
        if ( m )
        {
            return new global.Zork.Color.Rgb(
                parseInt( m[1], 10 ) / 100,
                parseInt( m[2], 10 ) / 100,
                parseInt( m[3], 10 ) / 100,
                Object.notUndefined( parseFloat( m[4] ), 1 )
            );
        }
        m = str.match( /rgba?\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*?([0-9]{1,3}),?\s*([0-9\.]+)?/ );
        if ( m )
        {
            return new global.Zork.Color.Rgb(
                parseInt( m[1], 10 ) / 255,
                parseInt( m[2], 10 ) / 255,
                parseInt( m[3], 10 ) / 255,
                Object.notUndefined( parseFloat( m[4] ), 1 )
            );
        }
        m = str.match( /rgba?\(\s*([0-9\.]+)\s*,\s*([0-9\.]+)\s*,\s*?([0-9\.]+),?\s*([0-9\.]+)?/ );
        if ( m )
        {
            return new global.Zork.Color.Rgb(
                parseFloat( m[1] ),
                parseFloat( m[2] ),
                parseFloat( m[3] ),
                Object.notUndefined( parseFloat( m[4] ), 1 )
            );
        }
        m = str.match( /hsla?\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})%\s*,\s*?([0-9]{1,3})%,?\s*([0-9\.]+)?/ );
        if ( m )
        {
            return new global.Zork.Color.Hsl(
                parseInt( m[1], 10 ),
                parseInt( m[2], 10 ) / 100,
                parseInt( m[3], 10 ) / 100,
                Object.notUndefined( parseFloat( m[4] ), 1 )
            );
        }
        if ( str === "transparent" )
        {
            return new global.Zork.Color.Rgb( 0, 0, 0, 0 );
        }
        return null;
    };
    
    /**
     * Get as rgb
     * @type {Zork.Color.Rgb}
     */
    global.Zork.Color.Rgb.prototype.toRgb = function ()
    {
        return this;
    };
    
    /**
     * Get as cmy
     * @type {Zork.Color.Cmy}
     */
    global.Zork.Color.Rgb.prototype.toCmy = function ()
    {
        return global.Zork.Color.prototype.rgb2cmy( this );
    };
    
    /**
     * Get as hsv
     * @type {Zork.Color.Hsv}
     */
    global.Zork.Color.Rgb.prototype.toHsv = function ()
    {
        return global.Zork.Color.prototype.rgb2hsv( this );
    };
    
    /**
     * Get as hsl
     * @type {Zork.Color.Hsl}
     */
    global.Zork.Color.Rgb.prototype.toHsl = function ()
    {
        return global.Zork.Color.prototype.rgb2hsl( this );
    };
    
    /**
     * Get as rgb
     * @type {Zork.Color.Rgb}
     */
    global.Zork.Color.Cmy.prototype.toRgb = function ()
    {
        return global.Zork.Color.prototype.cmy2rgb( this );
    };
    
    /**
     * Get as rgb
     * @type {Zork.Color.Rgb}
     */
    global.Zork.Color.Hsv.prototype.toRgb = function ()
    {
        return global.Zork.Color.prototype.hsv2rgb( this );
    };
    
    /**
     * Get as rgb
     * @type {Zork.Color.Rgb}
     */
    global.Zork.Color.Hsl.prototype.toRgb = function ()
    {
        return global.Zork.Color.prototype.hsl2rgb( this );
    };
    
    /**
     * Get invert
     * @type {Zork.Color.Rgb}
     */
    global.Zork.Color.Rgb.prototype.invert = function ()
    {
        return new global.Zork.Color.Rgb( 1 - this.r, 1 - this.g, 1 - this.b, this.a );
    };
    
    /**
     * Get invert
     * @type {Zork.Color.Cmy}
     */
    global.Zork.Color.Cmy.prototype.invert = function ()
    {
        return new global.Zork.Color.Cmy( 1 - this.c, 1 - this.m, 1 - this.y, this.a );
    };
    
    /**
     * Get invert
     * @type {Zork.Color.Hsv}
     */
    global.Zork.Color.Hsv.prototype.invert = function ()
    {
        return new global.Zork.Color.Hsv( 360 - this.h, 1 - this.s, 1 - this.v, this.a );
    };
    
    /**
     * Get invert
     * @type {Zork.Color.Hsl}
     */
    global.Zork.Color.Hsl.prototype.invert = function ()
    {
        return new global.Zork.Color.Hsl( 360 - this.h, 1 - this.s, 1 - this.l, this.a );
    };
    
    /**
     * Get as css string
     * @type {String}
     */
    global.Zork.Color.Rgb.prototype.toString = function ()
    {
        if ( Object.isUndefined( this.a ) || this.a === 1 )
        {
            var rs = parseInt( this.r * 255, 10 ).toString( 16 );
            var gs = parseInt( this.g * 255, 10 ).toString( 16 );
            var bs = parseInt( this.b * 255, 10 ).toString( 16 );
            if ( rs.length === 1 ) { rs = '0' + rs; }
            if ( gs.length === 1 ) { gs = '0' + gs; }
            if ( bs.length === 1 ) { bs = '0' + bs; }
            return '#' + ( rs + gs + bs ).toLowerCase();
        }
        else
        {
            return 'rgba(' + parseInt( this.r * 255, 10 ) + ', ' +
                parseInt( this.g * 255, 10 ) + ', ' +
                parseInt( this.b * 255, 10 ) + ', ' +
                parseFloat( this.a ) + ')';
        }
    };
    
    /**
     * Get as css string
     * @type {String}
     */
    global.Zork.Color.Cmy.prototype.toString =
    global.Zork.Color.Hsv.prototype.toString =
    global.Zork.Color.Hsl.prototype.toString = function ()
    {
        return this.toRgb().toString();
    };
    
} ( window, jQuery, zork ) );
