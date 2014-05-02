function zeroFill( number, width )
{
  width -= number.toString().length;
  if ( width > 0 )
  {
    return new Array( width + (/\./.test( number ) ? 2 : 1) ).join( '0' ) + number;
  }
  return number + ""; // always return a string
}

function updateWeather(data)
{
	var d = new Date(data['Date']);
	var temp = Math.round(parseFloat(data['Temp'])*9.0/5.0+32);
	var pressure = Math.round(parseFloat(data['Pressure']) * 0.295299801*100)/100;
	var precip = Math.round(parseFloat(data['Precip'])*3.93701)/100;
	var wind_dir_deg = parseFloat(data['WindDir']);
	var windSp = Math.round(parseFloat(data['WindSpeed']) * 2.2369*100)/100;
	
	var wind_dir = "";
	if(wind_dir_deg >= 292.5 && wind_dir_deg < 67.5)
		wind_dir = 'North';
	else if (wind_dir_deg >= 112.5 && wind_dir_deg < 247.5)
		wind_dir = 'South';
	
	if(wind_dir_deg >= 22.5 && wind_dir_deg < 157.5)
		wind_dir = wind_dir + 'East';
	else if(wind_dir_deg >= 202.5 && wind_dir_deg < 337.5)
		wind_dir = wind_dir + 'West';
	
	document.getElementById("date").innerHTML = "As of " + (d.getMonth()+1) + '/' + d.getDate() + '/'+ d.getFullYear() + ' ' + 
    d.getHours()%12 + ":" + zeroFill(d.getMinutes(),2) + (d.getHours() > 12 ? " PM":" AM");
	document.getElementById("temp").innerHTML = "<b>" + temp + " &deg;F</b>";
	document.getElementById("Humidity_val").innerHTML = data['Humidity'] + '%';
	document.getElementById("Pressure_val").innerHTML = pressure + ' inHg';
	document.getElementById("Precipitation").innerHTML = precip + ' in';
	document.getElementById("Wind_val").innerHTML = windSp + ' mph ' + wind_dir;
}

function imageClicked(args)
{
  var tbl = $('<table class="BigCamTbl"></table>');
  var trow1 = $('<tr></tr>');
  var trow2 = $('<tr></tr>');
  var tdata = $('<td></td>');
  
	var img = $('<img class="modal" src="'+ args.data.URL +'" width="552" height="414"/>');
	img.appendTo(tdata);
  
  tdata.appendTo(trow1);
  tdata = $('<td>'+args.data.Desc+' ('+args.data.Direction+')'+'</td>');
  tdata.appendTo(trow2);
  trow1.appendTo(tbl);
  trow2.appendTo(tbl);

  tbl.appendTo($('#blackout'));

	$('#blackout').fadeIn();
	$('#blackout').click(function(){
		tbl.remove();
		$('#blackout').fadeOut();
	})
}
function genCamEle(cams)
{
  var outer = $('<table class="camframe"></table>');
  var row = $('<tr></tr>');
	//No more than 3 on the front page
	for(var i=0;i<cams.length && i<3;i++)
	{
    var tdata = $('<td></td>');
		var img = $('<img class="camImage" src="'+cams[i].URL+'" width="190" height="142"/>');
    img.click(cams[i], imageClicked);
    img.appendTo(tdata);
    tdata.appendTo(row);
	}
  row.appendTo(outer);
  
  row = $('<tr></tr>');
  for(var i=0;i<cams.length && i<3;i++)
    $('<td>'+cams[i].Desc+' ('+cams[i].Direction+')'+'</td>').appendTo(row);
    
  row.appendTo(outer);
  outer.appendTo($('#cams'));
}

function updateTempGraph() {
  startD = new Date();
  startD.setDate(startD.getDate() - 1);
  endD = new Date();
  
  //format start and end strings
  start = (startD.getMonth()+1)+'/'+startD.getDate()+'/'+startD.getFullYear();
  end = (endD.getMonth()+1)+'/'+endD.getDate()+'/'+endD.getFullYear();
  
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
  });
}

function init()
{
	$.getJSON("weather_now.php", updateWeather);
	window.setInterval(function(){
		$.getJSON("weather_now.php", updateWeather);
	},60000);
	
	//generate cam list
	$.getJSON("working.php", genCamEle);
  
  updateTempGraph();
}