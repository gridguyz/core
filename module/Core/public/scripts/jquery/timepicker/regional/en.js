( function ( $ ) {

    $.timepicker.regional['en'] = {
        currentText: 'Now',
        ampm: false,
        showSecond: true,
        timeFormat: 'HH:mm:ss',
        timeOnlyTitle: 'Choose Time',
        timeText: 'Time',
        hourText: 'Hour',
        minuteText: 'Minute',
        secondText: 'Second',
        separator: 'T'
    };

    $.timepicker.setDefaults( $.timepicker.regional['en'] );

} ( jQuery ) );
