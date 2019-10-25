$("p").css('border', '1px solid red');

// Filter table from input
$(document).ready(function(){
  $("#input").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

// Checkbox for table editability
function checkBox(){
  if ($("#myCheck").prop('checked')){
    $('[contenteditable]').attr('contenteditable','true');
    //$('#update-btn').removeClass('hide')
    $('#update-btn').show();
  } else {
    $('[contenteditable]').attr('contenteditable','false');
    //$('#update-btn').addClass('hide','true');
    $('#update-btn').hide();
  }
}

// Add new row on click
$('.table-add').click(function(){
  var $clone = $('#table').find('tr.hide').clone(true).removeClass('hide table-line');
  $('#table').find('table').append($clone);
  //checkBox();
});

// Remove row on click
$('.table-remove').click(function () {
  $(this).parents('tr').detach();
});

// A few jQuery helpers for exporting only
jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;
jQuery.fn.reverse = [].reverse;

// Export table data
$('#export-btn').click(function(event) {
  var $rows = $('#table').find('tr:not(:hidden)');
  var headers = [];
  var data = [];
  // Get the headers (add special header logic here)
  $('.header').find('th:not(.control)').each(function (index) {
    headers.push($(this).text()/*.toLowerCase()*/);
  });
  $rows.shift();
  // Turn all existing rows into a loopable array
  $rows.each(function (row,x) {
    var $td = $(this).find('td');
    var h = {};

    // Use the headers from earlier to name our hash keys
    headers.forEach(function (header, i) {
      h[header] = $td.eq(i).text();
      // Check input format
      if(!validateInput(h[header],i)){
        alert("Formato sbagliato " + header + " riga " + x.rowIndex);
      }
    });

  data.push(h);
  });

  // Output the result
  //$('#output').text(JSON.stringify(data));
  //$('#output').text('export');
  // Posting to server
  $.post('https://ptsv2.com/t/jde4z-1571982762/post', data, function(msg){
    // Printing reply
    $('#output').html(msg);
  });
});

// Import from json
test_arr = [
  {"Impegno":"1","Articolo":"2","Q.tà Articolo":"3","Componente":"4","Q.tà Componente":"5","Stato":"6"},
  {"Impegno":"a","Articolo":"b","Q.tà Articolo":"c","Componente":"d","Q.tà Componente":"e","Stato":"f"}
];

// Import table
$('#import-btn').click(function(event) {
  var $rows = $('#table').find('tr:not(:hidden)');
  var headers = [];
  var data = [];

  // Check JSON length and add rows
  if($rows.length <= test_arr.length){
  for (var i = 1; i < test_arr.length; i++){
    var $clone = $('#table').find('tr.hide').clone(true).removeClass('hide table-line');
    $('#table').find('table').append($clone);
  }}else{
    $rows.each(function(index){
      if(index > test_arr.length){
        $(this).detach();
      }
    });
  }
  $rows = $('#table').find('tr:not(:hidden)');
  // Get the headers (add special header logic here)
  $('.header').find('th:not(.control)').each(function (index) {
    headers.push($(this).text()/*.toLowerCase()*/);
  });
  $rows.shift();
  // Turn all existing rows into a loopable array
  $rows.each(function (index) {
    var $td = $(this).find('td');
      var h = {};
    var obj = test_arr[index];

    // Use the headers from earlier to name our hash keys
    headers.forEach(function (header, i) {
      $td.eq(i).text(obj[header]);
    });

  data.push(h);
  });

  //$EXPORT.text($rows.length);

  $('#output').text(JSON.stringify(test_arr));
});


$('#update-btn').click(function(event) {
  $('#output').text('update');
});

// Check input format
function validateInput(value, column){
  /*
  var impegnoRGEX = /^[0-9]{0,1}[0-9]{0,1}[0-9]{0,1}[0-9]{0,1}[-\s\/]{1}[0-9]{2}$/;
  var articoloRGEX = /^[0-9]{1}[A-Z]{3}[0-9]{5}/;
  var quantitaRGEX = /^[0-9]{1}[0-9]{0,1}[0-9]{0,1}[0-9]{0,1}$/;
  // Validate Impegno format. Accepted format (1111-11) (1111 11) (1111/11)
  if(column==0){
    var checkResult = impegnoRGEX.test(value);
    if(!checkResult){
      return false;
    }
  }
  // Validate Articolo format. Accepted format (1AAA11111...)
  if(column==1){
    var checkResult = articoloRGEX.test(value);
    if(!checkResult){
      return false;
    }
  }
  // Validate Quantità format. Accepted format (1111)
  if (column==2 || column==4) {
    var checkResult = quantitaRGEX.test(value);
    if(!checkResult){
      return false;
    }
  }*/
  return true;
}
