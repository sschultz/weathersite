function genCheckboxes(data)
{
  //create table with header
  tbl = $('<table><tr><th>Include?</th></tr></table>');
  
  //generate table rows
  n = data.names.length;
  for(i=0;i<n;i++) {
    //create our checkbox for table row
    check = $('<input type="checkbox" name="'+i+'">');
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

function init()
{
  updateTable()
}