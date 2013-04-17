/**
 * Form functionalities
 * @package zork
 * @subpackage form
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.form.element.output !== "undefined" )
    {
        return;
    }

    /**
     * Time form element
     *
     * @memberOf Zork.Form.Element
     */
    global.Zork.Form.Element.prototype.output = function ( element )
    {
        element = $( element );

        if ( element.is( ":input" ) )
        {
            element.attr( "readonly", true );
        }

        var form = element.attr( "form" );
        if ( Object.isElement( form ) ) { form = $( form ); }
        else if ( form ) { form = $( "#" + form ); }
        else { form = element.parents( "form:first" ); }

        var id = form.attr( "id" );
        if ( ! id ) { form.attr( "id", id = js.generateId() ) ;}

        if ( form.size() === 1 )
        {
            var eventFunc = element.attr( "onforminput" );
            if ( ! Object.isUndefined( eventFunc ) &&
                ! Function.isFunction( eventFunc ) )
            {

                eventFunc = new Function( "event",
                    "with ( this ) { " + eventFunc + "; }" );

                element.on( "forminput", function ( event ) {
                    var formData = {};

                    form.find( ":input[name], output[name]" ).each( function ()
                    {
                        var self = $( this ), d = {};
                            d.value = self.val() || self.text();

                        if ( self.attr( "type" ) === "number" )
                        {
                            d.valueAsNumber = parseFloat( d.value ) ||
                                parseInt( d.value, 10 );
                        }

                        formData[self.attr( "name" )] = d;
                    } );

                    formData.form = formData;
                    formData.value = element.attr( "value" ) || "";

                    var result = eventFunc.call( formData, event );

                    if ( element.is( ":input" ) )
                    {
                        element.attr( "value", formData.value );
                    }
                    else
                    {
                        element.html( formData.value );
                    }

                    if ( ! Object.isUndefined( result ) )
                    {
                        return result;
                    }
                } );
            }

            var trigger = function ()
            {
                $( '#' + id + ' :input, #' + id + ' output, :input[form="' + id
                        + '"], output[form="' + id + '"]' ).andSelf().
                    trigger( "forminput" );
                return true;
            };

            if ( ! form.data( "event.forminput" ) )
            {
                form.data( "event.forminput", true );

                $( '#' + id + ' :radio' ).live( "click", trigger );
                $( '#' + id + ' textarea' ).live( "keyup", trigger );
                $( '#' + id + ' :checkbox' ).live( "click", trigger );
                $( ':radio[form="' + id + '"]' ).live( "click", trigger );
                $( 'textarea[form="' + id + '"]' ).live( "keyup", trigger );
                $( ':checkbox[form="' + id + '"]' ).live( "click", trigger );

                $( '#' + id + ' :input:not(:checkbox):not(:radio)' +
                    ':not(:file):not(:image)' +
                    ':not([data-js-type~="zork.form.element.output"])' +
                    ':not([data-js-type~="js.form.element.output"])' )
                        .on( "keyup", trigger );

                $( ':input[form="' + id + '"]:not(:checkbox):not(:radio)' +
                    ':not(:file):not(:image)' +
                    ':not([data-js-type~="zork.form.element.output"])' +
                    ':not([data-js-type~="js.form.element.output"])' )
                        .on( "keyup", trigger );

                $( '#' + id + ' :input:not([data-js-type~=' +
                    '"zork.form.element.output"])' +
                    ':not([data-js-type~="js.form.element.output"])' )
                        .on( "change", trigger );

                $( ':input[form="' + id + '"]:not([data-js-type~=' +
                    '"zork.form.element.output"])' +
                    ':not([data-js-type~="js.form.element.output"])' )
                        .on( "change", trigger );
            }

            trigger();
        }
    };

    global.Zork.Form.Element.prototype.output.isElementConstructor = true;

} ( window, jQuery, zork ) );
