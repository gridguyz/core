/**
 * CSS object model
 * @package zork
 * @subpackage cssom
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";
    
    if ( typeof js.cssom !== "undefined" )
    {
        return;
    }
    
    var propertyAliases = {
            "border-radius" : [
                "-webkit-border-radius",
                "-moz-border-radius",
                "-ms-border-radius"
            ],
            "border-top-left-radius" : [
                "-webkit-border-top-left-radius",
                "-moz-border-radius-topleft",
                "-ms-border-top-left-radius"
            ],
            "border-top-right-radius" : [
                "-webkit-border-top-right-radius",
                "-moz-border-radius-topright",
                "-ms-border-top-right-radius"
            ],
            "border-bottom-left-radius" : [
                "-webkit-border-bottom-left-radius",
                "-moz-border-radius-bottomleft",
                "-ms-border-bottom-left-radius"
            ],
            "border-bottom-right-radius" : [
                "-webkit-border-bottom-right-radius",
                "-moz-border-radius-bottomright",
                "-ms-border-bottom-right-radius"
            ]
        },
        propertyGetters = {
            "background-position-x": function () {
                var pos = this.get( "background-position" ),
                    result;

                if ( pos )
                {
                    pos = pos.split( /\s+/g, 2 );

                    if ( 0 in pos )
                    {
                        result = pos[0];
                    }
                }

                return result;
            },
            "background-position-y": function () {
                var pos = this.get( "background-position" ),
                    result;

                if ( pos )
                {
                    pos = pos.split( /\s+/g, 2 );

                    if ( 1 in pos )
                    {
                        result = pos[1];
                    }
                }

                return result;
            }
        },
        propertySetters = {
            "background-position-x": function ( value, important ) {
                var pos     = this.get( "background-position" ),
                    other   = '0%';

                if ( pos )
                {
                    pos = pos.split( /\s+/g, 2 );

                    if ( 1 in pos )
                    {
                        other = pos[1];
                    }
                }

                return this.set( "background-position", value + " " + other, important );
            },
            "background-position-y": function ( value, important ) {
                var pos     = this.get( "background-position" ),
                    other   = '0%';

                if ( pos )
                {
                    pos = pos.split( /\s+/g, 2 );

                    if ( 0 in pos )
                    {
                        other = pos[0];
                    }
                }

                return this.set( "background-position", other + " " + value, important );
            }
        };
    
    /**
     * @class User module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Cssom = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "cssom" ];
    };
    
    global.Zork.prototype.cssom = new global.Zork.Cssom();
    
    var getElement = function ( sheet )
        {
            if ( typeof sheet.owningElement !== "undefined" )
            {
                return sheet.owningElement;
            }
            
            if ( typeof sheet.ownerNode !== "undefined"  )
            {
                return sheet.ownerNode;
            }
            
            throw new Error( "Css rules not supported" );
        };
    
    /**
     * @class Represents a style-sheet
     * @constructor
     * @memberOf Zork.Cssom
     */
    global.Zork.Cssom.Sheet = function ( url )
    {
        var i, l, element, sUrl;
        this._sheet = null;
        
        if ( ! Object.isUndefined( url ) && Number.isNumber( url ) )
        {
            this._sheet = global.document.styleSheets[url];
            return;
        }
        
        if ( ! Object.isUndefined( url ) )
        {
            sUrl = String( url );
            
            if ( "#" == sUrl.charAt( 0 ) )
            {
                sUrl    = sUrl.substr( 1 );
                element = global.document.getElementById( sUrl );
                
                if ( element )
                {
                    if ( ! Object.isUndefined( element.sheet ) )
                    {
                        this._sheet = element.sheet;
                        return;
                    }
                    
                    if ( ! Object.isUndefined( element.styleSheet ) )
                    {
                        this._sheet = element.styleSheet;
                        return;
                    }
                    
                    for ( i = 0, l = global.document.styleSheets; i < l; ++i )
                    {
                        this._sheet = global.document.styleSheets[i];

                        if ( getElement( this._sheet ).id == url )
                        {
                            return;
                        }

                        url = null;
                        this._sheet = null;
                    }
                }
            }
        }
        
        if ( typeof global.document.createStyleSheet !== "undefined" )
        {
            try
            {
                this._sheet = global.document.createStyleSheet( url );
            }
            catch( e )
            { }
        }
        
        if ( ! this._sheet )
        {
            if ( url )
            {
                this._sheet = global.document.createElement( "link" );
                this._sheet.setAttribute( "rel", "stylesheet" );
                this._sheet.setAttribute( "type", "text/css" );
                this._sheet.setAttribute( "href", url );
            }
            else
            {
                this._sheet = global.document.createElement( "style" );
                this._sheet.setAttribute( "type", "text/css" );
            }
            
            $( "head" ).append( this._sheet );
            
            if ( typeof this._sheet.sheet !== "undefined" )
            {
                this._sheet = this._sheet.sheet;
            }
            else
            {
                this._sheet = global.document.styleSheets[
                    global.document.styleSheets.length - 1
                ];
            }
        }
    };
    
    /**
     * Disable / enable / get the disabled status of the sheet
     */
    global.Zork.Cssom.Sheet.prototype.disable = function ( set )
    {
        if ( Object.isUndefined( set ) )
        {
            return this._sheet.disabled;
        }
        
        this._sheet.disabled = !! set;
        return this;
    };
    
    /**
     * Get the sheet element (which owning the rules)
     * @type HTMLElement
     */
    global.Zork.Cssom.Sheet.prototype.element = function ()
    {
        return getElement( this._sheet );
    };
    
    /**
     * Destroys the Sheet object
     */
    global.Zork.Cssom.Sheet.prototype.destroy = function ()
    {
        this._sheet.disabled = true;
        $( this.element() ).remove();
        this._sheet = null;
    };
    
    /**
     * Create a new sheet
     * @type Zork.Cssom.Sheet
     */
    global.Zork.Cssom.prototype.sheet = function ( url )
    {
        return new global.Zork.Cssom.Sheet( url );
    };
    
    /**
     * @class Represents style-rules
     * @constructor
     * @memberOf Zork.Cssom
     */
    global.Zork.Cssom.Rules = function ( sheet, indexes )
    {
        this._sheet = sheet;
        this._indexes = ( indexes || [] ).clone();
    };
    
    var getRules = function ( sheet )
        {
            if ( typeof sheet.cssRules !== "undefined" )
            {
                return sheet.cssRules;
            }
            else if ( typeof sheet.rules !== "undefined" )
            {
                return sheet.rules;
            }
            else
            {
                throw new Error( "Css rules not supported" );
            }
        },
        selectorText = function ( selector )
        {
            return selector.replace( /\s+/, " " ).
                            replace( /^ /, "" ).
                            replace( / $/, "" ).
                            replace( /\s?,\s?/, ", " ).
                            toLowerCase();
        };
    
    /**
     * Create a new rule (within a sheet)
     * @param {String} selectors
     * @type Zork.Cssom.Rules
     */
    global.Zork.Cssom.Sheet.prototype.rules = function ( selectors )
    {
        selectors = String( selectors );
        
        var i, l,
            indexes     = [],
            rules       = getRules( this._sheet ),
            firstIndex  = rules.length,
            selText     = selectorText( selectors ),
            selList     = selectors.split( "," ).map( selectorText ),
            curText     = "";
        
        for ( i = 0; i < firstIndex; ++i )
        {
            curText = selectorText( rules[i].selectorText );
            
            if ( curText == selText || selList.indexOf( curText ) >= 0 )
            {
                indexes.push( i );
            }
        }
        
        if ( indexes.length < 1 )
        {
            if ( typeof this._sheet.insertRule !== "undefined" )
            {
                this._sheet.insertRule( selectors + "{}", firstIndex );
            }
            else if ( typeof this._sheet.addRule !== "undefined" )
            {
                this._sheet.addRule( selectors, "zoom: 1" );
            }
            else
            {
                throw new Error( "Css rules not supported" );
            }
            
            for ( i = firstIndex, l = rules.length; i < l; ++i )
            {
                indexes.push( i );
            }
        }
        
        return new global.Zork.Cssom.Rules( this._sheet, indexes );
    };
    
    /**
     * Remove the rules from the sheet
     */
    global.Zork.Cssom.Rules.prototype.remove = function ()
    {
        var self = this;
        
        if ( typeof this._sheet.deleteRule !== "undefined" )
        {
            this._indexes.forEach( function ( rule )
            {
                self._sheet.deleteRule( rule );
            } );
        }
        else if ( typeof this._sheet.removeRule !== "undefined" )
        {
            this._indexes.forEach( function ( rule )
            {
                self._sheet.removeRule( rule );
            } );
        }
        else
        {
            throw new Error( "Css rules not supported" );
        }
        
        this._indexes = [];
    };
    
    /**
     * Get a property value by name within a rule
     * @param {String} name f.ex.: "background-color"
     * @return String|null
     */
    global.Zork.Cssom.Rules.prototype.get = function ( name )
    {
        if ( this._indexes.length < 1 )
        {
            return undefined;
        }
        
        var style = getRules( this._sheet )[this._indexes[0]].style,
            result;
        
        if ( ! style )
        {
            throw new Error( "Css rules not supported" );
        }
        
        if ( typeof style.getPropertyValue !== "undefined" )
        {
            result = style.getPropertyValue( name );
        }
        else
        {
            result = style[ name.replace(
                /-([a-z])/, function ( _, letter )
                {
                    return letter.toUpperCase();
                }
            ) ];
        }
        
        if ( ! result )
        {
            if ( name in propertyGetters )
            {
                result = propertyGetters[name].call( this );
            }
        }
        else
        {
            result = String( result ).replace( /\s*!important$/, "" );
        }
        
        return result;
    };
    
    var isImportantByText = function ( txt, name )
    {
        var result = undefined;
        
        txt.replace( /\([^\)]+\)/g, "()" ).
            replace( /"([^"]*|\\")*"/g, "\"\"" ).
            split( ";" ).
            forEach( function ( t )
            {
                var n, p = t.split( ":" );
                
                if ( 0 in p )
                {
                    n = p[0].replace( /^\s+/, "" )
                            .replace( /\s+$/, "" )
                            .toLowerCase();
                    
                    if ( n == name && 1 in p )
                    {
                        result = ( /\s+!important\s*$/ ).test( p[1] );
                    }
                }
            } );
        
        return result;
    };
    
    /**
     * Is a property !important
     * @param {String} name f.ex.: "background-color"
     * @return Boolean|null
     */
    global.Zork.Cssom.Rules.prototype.isImportant = function ( name )
    {
        if ( this._indexes.length < 1 )
        {
            return undefined;
        }
        
        var style = getRules( this._sheet )[this._indexes[0]].style;
        
        if ( ! style )
        {
            throw new Error( "Css rules not supported" );
        }
        
        if ( typeof style.getPropertyPriority !== "undefined" )
        {
            return style.getPropertyPriority( name ) === "important";
        }
        else if ( style.cssText )
        {
            return isImportantByText( style.cssText, name );
        }
        
        var raw = style[ name.replace(
            /-([a-z])/, function ( match, letter )
            {
                return letter.toUpperCase();
            }
        ) ];
        
        return ( /!important$/ ).test( raw );
    };
    
    /**
     * Get a property value by name within a rule
     * @param {String} name f.ex.: "background-color"
     * @param {String} value f.ex.: "#abcdef"
     * @param {Boolean} important [optional]
     */
    global.Zork.Cssom.Rules.prototype.set = function ( name, value, important )
    {
        var self = this;
        
        if ( name in propertyAliases )
        {
            $.each( propertyAliases[name], function ( _, alias ) {
                self.set( alias, value, important );
            } );
        }
        
        var rules = getRules( this._sheet ),
            rawName = null;
        
        if ( Object.isUndefined( value ) )
        {
            this._indexes.forEach( function ( idx )
            {
                var style = rules[idx].style;
                
                if ( typeof style.removeProperty !== "undefined" )
                {
                    style.removeProperty( name );
                }
                else
                {
                    if ( rawName === null )
                    {
                        rawName = name.replace(
                            /-([a-z])/, function ( match, letter )
                            {
                                return letter.toUpperCase();
                            }
                        );
                    }
                    
                    style[rawName] = null;
                }
            } );
        }
        else
        {
            this._indexes.forEach( function ( idx )
            {
                var style = rules[idx].style;
                
                if ( typeof style.setProperty !== "undefined" )
                {
                    try
                    {
                        style.setProperty( name, value,
                            important ? "important" : null );
                    }
                    catch ( e )
                    { }
                }
                else if ( typeof style.cssText !== "undefined" )
                {
                    try
                    {
                        style.cssText += ";" + name + ": " + value +
                            ( important ? " !important" : "" );
                    }
                    catch ( e )
                    { }
                }
                else
                {
                    if ( rawName === null )
                    {
                        rawName = name.replace(
                            /-([a-z])/, function ( match, letter )
                            {
                                return letter.toUpperCase();
                            }
                        );
                    }
                    
                    try
                    {
                        style[rawName] = value + ( important ? " !important" : "" );
                    }
                    catch ( e )
                    { }
                }
            } );
        }
        
        if ( name in propertySetters )
        {
            propertySetters[name].call( this, value, important );
        }
        
        return this;
    };
    
} ( window, jQuery, zork ) );
