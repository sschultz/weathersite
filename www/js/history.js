$(function() {
  start = new Date();
  start.setDate(start.getDate() - 1);
  end = new Date();
  $('#start-date').datepicker();
  $('#start-date').val((start.getMonth()+1)+'/'+start.getDate()+'/'+start.getFullYear());
  $('#end-date').datepicker();
  $('#end-date').val((end.getMonth()+1)+'/'+end.getDate()+'/'+end.getFullYear());
  
  
  eventRange();
});

function updateGraphs(start, end) {
  $.getJSON("graphdata.php",
  { 'start' : start,
    'end' : end },
  function( phpData ) {
    $('#tempgraph').highcharts({
      title: {text: 'Temperature'},
      chart: {zoomType: 'x'},
      xAxis: {
        title: {text: 'Date'},
        //dateTimeLabelFormats: {day: ''}
        type: "datetime"
      },
      yAxis: {
        allowDecimals: false,
        title: {text: 'Temperature (°F)'},
        plotLines: [{
          value: 0,
          width: 1,
          color: '#808080'
      }]},
      tooltip: {valueSuffix: '°F'},
      legend: {enabled: false},
      series: [
        {
          name:'Temperature',
          data: phpData.Temp
        }
      ],
      credits: {enabled: false}
    });
    //HUMIDITY CHART
    $('#humgraph').highcharts({
      title: {text: 'Humidity'},
      chart: {zoomType: 'x'},
      xAxis: {
        title: {text: 'Date'},
        //dateTimeLabelFormats: {day: ''}
        type: "datetime"
      },
      yAxis: {
        allowDecimals: false,
        title: {text: 'Humidity (%)'},
        plotLines: [{
          value: 0,
          width: 1,
          color: '#808080'
      }]},
      tooltip: {valueSuffix: '%'},
      legend: {enabled: false},
      series: [
        {
          name:'Humidity',
          data: phpData.Humidity
        }
      ],
      credits: {enabled: false}
    });
    //PRESSURE CHART
    $('#presgraph').highcharts({
      title: {text: 'Pressure'},
      chart: {zoomType: 'x'},
      xAxis: {
        title: {text: 'Date'},
        //dateTimeLabelFormats: {day: ''}
        type: "datetime"
      },
      yAxis: {
        allowDecimals: false,
        title: {text: 'Pressure (InHg)'},
        plotLines: [{
          value: 0,
          width: 1,
          color: '#808080'
      }]},
      tooltip: {valueSuffix: ' InHg'},
      legend: {enabled: false},
      series: [
        {
          name:'Pressure',
          data: phpData.Pressure
        }
      ],
      credits: {enabled: false}
    });
    //WindSpeed CHART
    $('#windgraph').highcharts({
      title: {text: 'Wind Speed'},
      chart: {zoomType: 'x'},
      xAxis: {
        title: {text: 'Date'},
        //dateTimeLabelFormats: {day: ''}
        type: "datetime"
      },
      yAxis: {
        allowDecimals: false,
        title: {text: 'Wind Speed (mph)'},
        plotLines: [{
          value: 0,
          width: 1,
          color: '#808080'
      }]},
      tooltip: {valueSuffix: '  mph'},
      legend: {enabled: false},
      series: [
        {
          name:'Wind Speed',
          data: phpData.WindSpeed
        }
      ],
      credits: {enabled: false}
    });
  });
}

function eventRange() {
  start = $('#start-date').val();
  end = $('#end-date').val();
  updateGraphs(start, end);
}