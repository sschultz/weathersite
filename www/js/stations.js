function genCheckboxes(data)
{
  //create table with header
  tbl = $('<table><tr><th>Column Names</th><th>Column Description</th></tr></table>');
  
  //generate table rows
  n = data.names.length;
  for(i=0;i<n;i++) {
    //create our checkbox for table row
    check = $('<input type="checkbox" name="'+i+'">');
    //create row tag and data tag
    row = $('<tr></tr>');
    tdata = $('<td></td>');
    //put check box into table data tag
    tdata.append(check);
    tdata.append(data.names[i]);
    //put data tag into table row
    row.append(tdata);

    //append table column description to same row
    row.append($('<td>'+data.desc[i]+'</td>'));
    tbl.append(row);
  }
  
  btn = $('<tr><td><button type="button">Download</button></td></tr>');
  tbl.append(btn);
  
  //add our table to the checkboxes div tag in stations.html file
  $('#checkboxes').append(tbl);
}

function init()
{
  $.getJSON("tblCols.php", genCheckboxes);
}