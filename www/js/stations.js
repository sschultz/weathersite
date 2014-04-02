function genCheckboxes(data)
{
  //create table with header
  tbl = $('<table><tr align="left"><th><span>Select fields to include</span><button onclick="selAll()">Select All</button></th></tr></table>');
  
  //generate table rows
  n = data.names.length;
  for(i=0;i<n;i++) {
    //create our checkbox for table row
    check = $('<input type="checkbox" class="checkboxes" id="'+i+'" name="'+i+'">');
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
  
  btn = $('<tr><td><button type="button">Download</button></td></tr>');
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

function init()
{
  updateTable()
}