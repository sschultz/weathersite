function genCheckboxes(data)
{
  //create table with header
  tbl = $('<table><tr align="left"><th><span>Select fields to include</span><button onclick="selAll()">Select All</button></th></tr></table>');
  
  //generate table rows
  n = data.names.length;
  for(i=0;i<n;i++) {
    //create our checkbox for table row
    check = $('<input type="checkbox" class="checkboxes" name="'+(i+1)+'">');
    //create row tag and data tag
    row = $('<tr></tr>');
    tdata = $('<td></td>');
    
    label = $('<label></label>')
    label.append(check)
    label.append($('<span>'+data.desc[i]+'</span>'))
    
    tdata.append(label)
    row.append(tdata);
    tbl.append(row);
  }
  
  btn = $('<tr><td><button onclick="download()" type="button">Download</button></td></tr>');
  tbl.append(btn);
  
  //add our table to the checkboxes div tag in stations.html file
  $('#checkboxes').append(tbl);
}

function updateTable()
{
  $('#checkboxes').empty();
  var stationId = $('#selStation').val();
  $.getJSON("tblCols.php?t="+stationId, genCheckboxes);
}

function selAll()
{
  $('#checkboxes input').each( function () {
    $(this).prop('checked', true);
  })
}

String.prototype.replaceAt=function(index, character) {
    return this.substr(0, index) + character + this.substr(index+character.length);
}

function download()
{
  var stationId = $('#selStation').val();
  var after = $('#startdate').val().toString();
  var before = $('#enddate').val().toString();
  var flaghexstr = "";
  var flagbinstr = "";
  //convert checkboxes into hex flags string
  $('#checkboxes input').each( function () {
    var isChecked = $(this).prop('checked');

    if(isChecked == true) {
      var colNo = $(this).prop('name');
      //padd left of string with '0'
      for(var i = flagbinstr.length; i < colNo; i++)
      {
        flagbinstr = "0" + flagbinstr;
      }
      
      flagbinstr = flagbinstr.replaceAt(flagbinstr.length - colNo,"1");
    }
  })
  
  while(flagbinstr.length % 4 != 0) {
    flagbinstr = "0" + flagbinstr;
  }
  
  //convert binhexstr to hex string
  for(var i = flagbinstr.length-4; i >= 0; i-=4)
  {
    var binchar = flagbinstr.substr(i, 4);
    var val = parseInt(binchar, 2);
    flaghexstr = val.toString(16) + flaghexstr;
  }
  //alert("binstr: " + flagbinstr);
  //alert("Flag: " + flaghexstr);
  if(flaghexstr == "")
  {
    alert("No columns selected");
    exit();
  }
  document.location.href="data.php?dset="+stationId+"&cols="+flaghexstr+"&before="+before+"&after="+after;
}

function downloadAll()
{
  var stationId = $('#selStation').val();
  var after = $('#startdate').val().toString();
  var before = $('#enddate').val().toString();
  document.location.href="data.php?dset="+stationId+"&cols=0"+"&before="+before+"&after="+after;
}

function init()
{
  //Initialize date picker controls
  start = new Date("2013/7/22");
  end = new Date();
  $('#startdate').datepicker();
  $('#enddate').datepicker();
  $('#startdate').val((start.getMonth()+1)+'/'+start.getDate()
+'/'+start.getFullYear());
  $('#enddate').val((end.getMonth()+1)+'/'+end.getDate()+'/'+end.getFullYear());
  
  $('#dAll').append($('<button onclick="downloadAll()" type="button">Download All Fields</button>'));
  
  //fill table to pick fields with
  updateTable();
}