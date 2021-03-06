/**
 * User interface functionalities
 * @package zork
 * @subpackage user
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.user !== "undefined" )
    {
        return;
    }

    /**
     * @class User module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.User = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "user" ];
    };

    global.Zork.prototype.user = new global.Zork.User();

    /**
     * Grant rights section
     */
    global.Zork.User.prototype.grantRights = function ( element )
    {
        element = $( element );

        var page    = element.parents( ".page" ),
            panel   = $( "<div />", {
                "class": "panel panel-right"
            } ),
            groups  = $( "<ul />", {
                "class": "right-group-list"
            } );

        page.before(
            panel.append(
                     $( "<h3 />", {
                         "text": js.core.translate( "user.right.groups" )
                     } )
                 )
                 .append( groups )
        );

        element.find( ".right-group" )
               .each( function () {
                   var group  = $( this ),
                       title  = group.find( "h4" ),
                       active = !! group.find(
                                       "input[type=checkbox]:checked, " +
                                       "input.required[type=hidden][value=1]"
                                   ).length;

                   groups.append(
                       $( "<li />", {
                           "text": title.text(),
                           "class": active ? "selected" : "",
                           "click": function () {
                               if ( active = ! active )
                               {
                                   $( this ).addClass( "selected" );
                                   group.show( "fast" );

                                   group.find( "input[type=checkbox]" )
                                        .attr( "disabled", null )
                                        .prop( "disabled", false );

                                   group.find( "input.required[type=hidden]" )
                                        .attr( "value", "1" );
                               }
                               else
                               {
                                   $( this ).removeClass( "selected" );
                                   group.hide( "fast" );

                                   group.find( "input[type=checkbox]" )
                                        .attr( "disabled", "disabled" )
                                        .prop( "disabled", true );

                                   group.find( "input.required[type=hidden]" )
                                        .attr( "value", "" );
                               }
                           }
                       } )
                   );

                   group.find( "dt" )
                        .each( function () {
                            var dt   = $( this ),
                                dd   = dt.nextUntil( "dt" ),
                                chks = dd.find( "input[name][type=checkbox]" ),
                                some = !! dd.find( "input[name][type=checkbox]:checked" ).length,
                                all  = ! dd.find( "input[name][type=checkbox]:not(:checked)" ).length,
                                grp  = $( "<input type='checkbox' />" );

                            dt.prepend(
                                grp.prop( {
                                       "checked": all,
                                       "indeterminate": some && ! all
                                   } )
                                   .on( "change", function () {
                                       if ( this.checked )
                                       {
                                           chks.prop( "checked", true );
                                       }
                                       else
                                       {
                                           chks.prop( "checked", false );
                                       }
                                   } )
                            );

                            chks.on( "change", function () {
                                some = this.checked
                                    || !! dd.find( "input[name][type=checkbox]:checked" ).length;

                                all  = this.checked
                                    && ! dd.find( "input[name][type=checkbox]:not(:checked)" ).length;

                                grp.prop( {
                                    "checked": all,
                                    "indeterminate": some && ! all
                                } );
                            } );
                        } );

                   var chks   = group.find( "input[type=checkbox]" ),
                       some   = !! group.find( "input[name][type=checkbox]:checked" ).length,
                       all    = ! group.find( "input[name][type=checkbox]:not(:checked)" ).length,
                       chkAll = $( "<input type='checkbox' />" ).prop( {
                           "checked": all,
                           "indeterminate": some && ! all
                       } ).on( "change", function () {
                            if ( this.checked )
                            {
                                chks.prop( {
                                    "checked": true,
                                    "indeterminate": false
                                } );
                            }
                            else
                            {
                                chks.prop( {
                                    "checked": false,
                                    "indeterminate": false
                                } );
                            }
                        } );

                   group.find( "dl:first" )
                        .prepend(
                            $( "<dt />", {
                                "class": "check-all",
                                "text": " " + js.core.translate( "default.checkAll" )
                            } ).prepend( chkAll )
                        );

                   chks.on( "change", function () {
                       if ( this === chkAll[0] )
                       {
                           return;
                       }

                       some = this.checked
                           || !! group.find( "input[name][type=checkbox]:checked" ).length;

                       all  = this.checked
                           && ! group.find( "input[name][type=checkbox]:not(:checked)" ).length;

                       chkAll.prop( {
                           "checked": all,
                           "indeterminate": some && ! all
                       } );
                   } );

                   group.accordion( {
                       "header": "h4",
                       "collapsible": true,
                       "heightStyle": "content",
                       "active": group.find( "input[type=checkbox]:checked, " +
                           "input.required[type=hidden][value=1]" ).length ? 0 : false,
                       "icons": {
                           "header": "ui-icon-carat-1-s",
                           "activeHeader": "ui-icon-carat-1-n"
                       }
                   } );

                   if ( ! active )
                   {
                       group.hide();
                   }
               } );

    };

    global.Zork.User.prototype.grantRights.isElementConstructor = true;

    /**
     * User select form-element
     * @param {HTMLElement} element
     * @type undefined
     */
    global.Zork.User.prototype.select = function ( element )
    {
        js.require( "jQuery.fn.autocompleteicon" );
        element = $( element );

        var opened      = false,
            toggle      = element.data( "jsUserselectToggle" ) != false,
            minLength   = element.data( "jsUserselectMinLength" ) || 1,
            placeholder = element.data( "jsAutocompletePlaceholder" ) ||
                          js.core.translate( "default.autoCompletePlaceholder" ),
            selected    = element.find( ":selected" ),
            input       = $( "<input type='text' />" ),
            change      = function ( _, ui ) {
                var val, lab;

                if ( ui.item )
                {
                    val = ui.item.value;
                    lab = ui.item.label;
                }
                else
                {
                    val = "";
                    lab = "";
                }

                input.val( lab );
                element.val( val )
                       .trigger( "change" );
            };

        if ( selected.length )
        {
            input.val( selected.text() );
        }

        input.attr( "placeholder", placeholder );

        element.removeAttr( "multiple" )
               .addClass( "ui-helper-hidden" )
               .prop( "multiple", false )
               .after( input );

        input.autocompleteicon( {
            "minLength": minLength,
            "position": {
                "my": "left top",
                "at": "left bottom",
                "collision": "flip"
            },
            "source": function ( request, response ) {
                var result = [],
                    term = request.term
                                  .toLowerCase()
                                  .replace( /^\s+/, "" )
                                  .replace( /\s+$/, "" )
                                  .replace( /\s+/, " " );

                element.find( "option" ).each( function () {
                    var self    = $( this ),
                        val     = self.val(),
                        text    = self.text(),
                        email   = String( self.data( "email" ) || "" ),
                        search  = String( text + " " + email )
                                    .toLowerCase()
                                    .replace( /^\s+/, "" )
                                    .replace( /\s+$/, "" )
                                    .replace( /\s+/, " " );

                    if ( ~search.indexOf( term ) )
                    {
                        result.push( {
                            "value": val,
                            "label": text,
                            "description": email,
                            "icon": self.data( "avatar" )
                                ? js.core.thumbnail( self.data( "avatar" ), {
                                      "width": 25,
                                      "height": 25
                                  } )
                                : null
                        } );
                    }
                } );

                response( result );
            },
            "change": change,
            "select": function ( event, ui ) {
                event.preventDefault();
                change.call( this, event, ui );
            },
            "search": function () {
                opened = true;
            },
            "close": function () {
                opened = false;
            }
        } );

        if ( toggle )
        {
            input.after(
                $( '<button type="button" />' )
                    .button( {
                        "text": false,
                        "icons": {
                            "primary": "ui-icon-triangle-1-s"
                        }
                    } )
                    .click( function () {
                        if ( minLength )
                        {
                            input.autocompleteicon( "option", "minLength", 0 );
                        }

                        if ( opened )
                        {
                            input.autocompleteicon( "close" );
                        }
                        else
                        {
                            input.focus()
                                 .autocompleteicon( "search", "" );
                        }

                        if ( minLength )
                        {
                            input.autocompleteicon( "option", "minLength", minLength );
                        }
                    } )
            );

            element.parent()
                   .inputset();
        }
    };

    global.Zork.User.prototype.select.isElementConstructor = true;

    /**
     * User multi-select form-element
     * @param {HTMLElement} element
     * @type undefined
     */
    global.Zork.User.prototype.multiSelect = function ( element )
    {
        js.require( "jQuery.fn.autocompleteicon" );
        element = $( element );

        var opened      = false,
            toggle      = element.data( "jsUserselectToggle" ) != false,
            minLength   = element.data( "jsUserselectMinLength" ) || 1,
            selected    = element.find( ":selected" ),
            input       = $( "<input type='search' />" ),
            udiv        = $( '<div class="js-tag-list ui-widget" />' ),
            add         = function ( value, text ) {
                var label = $( "<label />", {
                                "text": text,
                                "class": "js-tag ui-state-default ui-widget-content ui-corner-all"
                            } ),
                    close = $( '<button type="button" />' )
                                .button( {
                                    "text": false,
                                    "icons": {
                                        "primary": "ui-icon-close"
                                    }
                                } )
                                .click( function () {
                                    var vals  = element.val() || [],
                                        index = vals.indexOf( value );

                                    vals.splice( index, 1 );
                                    element.val( vals )
                                           .trigger( "change" );

                                    label.hide( "fast", function () {
                                        label.remove();
                                    } );
                                } );

                udiv.append( label.append( close ) );

                var vals  = element.val() || [];
                vals.push( value );

                element.val( vals )
                       .trigger( "change" );
            },
            change      = function ( _, ui ) {
                var val, lab, original = element.val() || [];

                if ( ui.item )
                {
                    val = ui.item.value;
                    lab = ui.item.label;
                }
                else
                {
                    val = "";
                    lab = "";
                }

                input.val( "" );

                if ( val && 0 > original.indexOf( val ) )
                {
                    add( val, lab );
                }
            };

        element.attr( "multiple", "multiple" )
               .addClass( "ui-helper-hidden" )
               .prop( "multiple", true )
               .after( udiv )
               .after( input );

        selected.each( function () {
            var $this = $( this );
            add( $this.val(), $this.text() );
        } );

        input.autocompleteicon( {
            "minLength": minLength,
            "position": {
                "my": "left top",
                "at": "left bottom",
                "collision": "flip"
            },
            "source": function ( request, response ) {
                var result = [],
                    term = request.term
                                  .toLowerCase()
                                  .replace( /^\s+/, "" )
                                  .replace( /\s+$/, "" )
                                  .replace( /\s+/, " " );

                element.find( "option" ).each( function () {
                    var self    = $( this ),
                        val     = self.val(),
                        text    = self.text(),
                        email   = String( self.data( "email" ) || "" ),
                        search  = String( text + " " + email )
                                    .toLowerCase()
                                    .replace( /^\s+/, "" )
                                    .replace( /\s+$/, "" )
                                    .replace( /\s+/, " " );

                    if ( ~search.indexOf( term ) )
                    {
                        result.push( {
                            "value": val,
                            "label": text,
                            "description": email,
                            "icon": self.data( "avatar" )
                                ? js.core.thumbnail( self.data( "avatar" ), {
                                      "width": 25,
                                      "height": 25
                                  } )
                                : null
                        } );
                    }
                } );

                response( result );
            },
            "change": change,
            "select": function ( event, ui ) {
                event.preventDefault();
                change.call( this, event, ui );
            },
            "search": function () {
                opened = true;
            },
            "close": function () {
                opened = false;
            }
        } );

        if ( toggle )
        {
            input.after(
                $( '<button type="button" />' )
                    .button( {
                        "text": false,
                        "icons": {
                            "primary": "ui-icon-triangle-1-s"
                        }
                    } )
                    .click( function () {
                        if ( minLength )
                        {
                            input.autocompleteicon( "option", "minLength", 0 );
                        }

                        if ( opened )
                        {
                            input.autocompleteicon( "close" );
                        }
                        else
                        {
                            input.focus()
                                 .autocompleteicon( "search", "" );
                        }

                        if ( minLength )
                        {
                            input.autocompleteicon( "option", "minLength", minLength );
                        }
                    } )
            );

            element.parent()
                   .inputset();
        }
    };

    global.Zork.User.prototype.multiSelect.isElementConstructor = true;

    /**
     * User-group multi-select form-element
     * @param {HTMLElement} element
     * @type undefined
     */
    global.Zork.User.prototype.multiSelectGroup = function ( element )
    {
        element = $( element );

        var opened      = false,
            toggle      = element.data( "jsUserselectToggle" ) != false,
            loggedOut   = element.data( "jsUserselectLoggedout" ) ||
                          js.core.translate( "paragraph.form.content.userGroups.loggedOut" ),
            minLength   = element.data( "jsUserselectMinLength" ) || 0,
            selected    = element.find( ":selected" ),
            input       = $( "<input type='search' />" ),
            udiv        = $( '<div class="js-tag-list ui-widget" />' ),
            add         = function ( value, text ) {
                var label = $( "<label />", {
                                "text": text,
                                "class": "js-tag ui-state-default ui-widget-content ui-corner-all"
                            } ),
                    close = $( '<button type="button" />' )
                                .button( {
                                    "text": false,
                                    "icons": {
                                        "primary": "ui-icon-close"
                                    }
                                } )
                                .click( function () {
                                    var vals  = element.val() || [],
                                        index = vals.indexOf( value );

                                    vals.splice( index, 1 );
                                    element.val( vals )
                                           .trigger( "change" );

                                    label.hide( "fast", function () {
                                        label.remove();
                                    } );
                                } );

                udiv.append( label.append( close ) );

                var vals  = element.val() || [];
                vals.push( value );

                element.val( vals )
                       .trigger( "change" );
            },
            change      = function ( _, ui ) {
                var val, lab, original = element.val() || [];

                if ( ui.item )
                {
                    val = ui.item.value;
                    lab = val ? ui.item.label : loggedOut;
                }
                else
                {
                    val = "";
                    lab = "";
                }

                input.val( "" );

                if ( ( val || lab ) && 0 > original.indexOf( val ) )
                {
                    add( val, lab );
                }
            };

        element.attr( "multiple", "multiple" )
               .addClass( "ui-helper-hidden" )
               .prop( "multiple", true )
               .after( udiv )
               .after( input );

        selected.each( function () {
            var $this = $( this ),
                val   = $this.val();
            add( val, val ? $this.text() : loggedOut );
        } );

        input.autocomplete( {
            "minLength": minLength,
            "position": {
                "my": "left top",
                "at": "left bottom",
                "collision": "flip"
            },
            "source": function ( request, response ) {
                var result = [],
                    term = request.term
                                  .toLowerCase()
                                  .replace( /^\s+/, "" )
                                  .replace( /\s+$/, "" )
                                  .replace( /\s+/, " " );

                element.find( "option" ).each( function () {
                    var self    = $( this ),
                        val     = self.val(),
                        text    = self.text(),
                        search  = String( text )
                                    .toLowerCase()
                                    .replace( /^\s+/, "" )
                                    .replace( /\s+$/, "" )
                                    .replace( /\s+/, " " );

                    if ( ~search.indexOf( term ) )
                    {
                        result.push( {
                            "value": val,
                            "label": val ? text : loggedOut
                        } );
                    }
                } );

                response( result );
            },
            "change": change,
            "select": function ( event, ui ) {
                event.preventDefault();
                change.call( this, event, ui );
            },
            "search": function () {
                opened = true;
            },
            "close": function () {
                opened = false;
            }
        } );

        if ( toggle )
        {
            input.after(
                $( '<button type="button" />' )
                    .button( {
                        "text": false,
                        "icons": {
                            "primary": "ui-icon-triangle-1-s"
                        }
                    } )
                    .click( function () {
                        if ( minLength )
                        {
                            input.autocomplete( "option", "minLength", 0 );
                        }

                        if ( opened )
                        {
                            input.autocomplete( "close" );
                        }
                        else
                        {
                            input.focus()
                                 .autocomplete( "search", "" );
                        }

                        if ( minLength )
                        {
                            input.autocomplete( "option", "minLength", minLength );
                        }
                    } )
            );

            element.parent()
                   .inputset();
        }
    };

    global.Zork.User.prototype.multiSelectGroup.isElementConstructor = true;

} ( window, jQuery, zork ) );
