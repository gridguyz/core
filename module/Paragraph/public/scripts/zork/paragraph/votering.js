/**
 * Paragraph functionalities
 * @package zork
 * @subpackage paragraph
 * @author Sipos Zoltán <sipos.zoltan@megaweb.hu>
 */
( function ( global, $, js )
{
    "use strict";

    if ( typeof js.paragraph.votering !== "undefined" )
    {
        return;
    }

    /**
     * Vote
     *
     * @param {HTMLElement|$} element
     */
    global.Zork.Paragraph.prototype.votering = function ( element )
    {
        element = $( element );

        var titleTranslation,
            chartElement,
            loadChart = function()
            {
                var chart = Object( $(element).find('table div').data('jsChartSeries') );

                titleTranslation = $(element).find('.vote-container table div').data('jsChart');
                element.find('.vote-container table div').html('');

                chart.tooltip =
                {
                    "formatter": function() {
                        return '<b>'+ this.point.name +'</b>: ' + this.point.y + ' ' + titleTranslation +' (' + (Math.round(this.percentage*100)/100) + ' %)';
                    }
                };
                chart.plotOptions =
                {
                    "pie":
                    {
                        "allowPointSelect": false,
                        "dataLabels":
                        {
                            "enabled": true,
                            "color": '#000000',
                            "connectorColor": '#000000',
                            "formatter": function()
                            {
                                return '<b>'+ this.point.name +'</b>: ' + this.point.y + ' ' + titleTranslation +' (' + (Math.round(this.percentage*100)/100) + ' %)';
                            }
                        }
                    }
                };
                chart.credits =
                {
                    "enabled": false,
                    "position":
                    {
                        "align": 'left',
                        "x": 10
                    }
                };

                js.script('/scripts/library/highchart/js/highcharts.js', {
                    "success": function()
                    {
//                      js.console.log(chart);
                        chartElement = new Highcharts.Chart(chart);
//                      element.find('tspan').each();
                    }
                });

            };

        if(element.find('table div').length>0 && element.find('table div div').length<=0)
        {
            loadChart();
        }

    };

    global.Zork.Paragraph.prototype.votering.isElementConstructor = true;

} ( window, jQuery, zork ) );
