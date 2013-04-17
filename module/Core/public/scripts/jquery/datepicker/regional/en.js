( function ( $ ) {

    $.datepicker.regional['en'] = {
        closeText: 'close',
        prevText: '&laquo;&nbsp;back',
        nextText: 'next&nbsp;&raquo;',
        currentText: 'today',
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June','July', 'August', 'September', 'October', 'November', 'December'],
        monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        dayNamesMin: ['Su', 'M', 'Tu', 'W', 'Th', 'F', 'Sa'],
        weekHeader: 'Su',
        dateFormat: 'yy-mm-dd',
        altFormat: 'd. MM, yy.',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };

    $.datepicker.setDefaults( $.datepicker.regional['en'] );

} ( jQuery ) );
