function getData(chartDataJSON) {
    return {
        data: chartDataJSON,
        xkey: ['hour'],
        ykeys: ['netto'],
        labels: ['nett gross'],
        hideHover: 'auto',
        behaveLikeLine: true,
        parseTime: false,
        resize: true,
        pointFillColors: ['white'],
        pointStrokeColors: ['black'],
        lineColors: ['#28a745']
    };
}

config2x2 = getData(chart2x2JSON);
config2x2.element = 'productionChart2x2';
Morris.Line(config2x2);

config3x2 = getData(chart3x2JSON);
config3x2.element = 'productionChart3x2';
Morris.Line(config3x2);

config4x2 = getData(chart4x2JSON);
config4x2.element = 'productionChart4x2';
Morris.Line(config4x2);
