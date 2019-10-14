var $TABLE = $('#table');
var $BTN = $('#export-btn');
var $BTN2 = $('#import-btn');
var $EXPORT = $('#export');
var $CHECK = $('#myCheck');
var $TEXT = $('#text');

// Add new row on click
$('.table-add').click(function(){
  var $clone = $TABLE.find('tr.hide').clone(true).removeClass('hide table-line');
  $TABLE.find('table').append($clone);
  f_checkbox();
});

// Remove row on click
$('.table-remove').click(function () {
  $(this).parents('tr').detach();
});

// Move row up
$('.table-up').click(function () {
  var $row = $(this).parents('tr');
  if ($row.index() === 1) return; // Don't go above the header
  $row.prev().before($row.get(0));
});

// move row down
$('.table-down').click(function () {
  var $row = $(this).parents('tr');
  $row.next().after($row.get(0));
});

// A few jQuery helpers for exporting only
jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;

// Check input format
function f_validateinput(value, column){
  var impegnoRGEX = /^[0-9]{0,1}[0-9]{0,1}[0-9]{0,1}[0-9]{0,1}[-\s\/]{1}[0-9]{2}$/;
  var articoloRGEX = /^[0-9]{1}[A-Z]{3}[0-9]{5}/;
  var quantitaRGEX = /^[0-9]{1}[0-9]{0,1}[0-9]{0,1}[0-9]{0,1}$/;

  // Validate Impegno format
  if(column==0){
    var checkResult = impegnoRGEX.test(value);
    if(!checkResult){
      return false;
    }
  }
  // Validate Articolo format
  if(column==1){
    var checkResult = articoloRGEX.test(value);
    if(!checkResult){
      return false;
    }
  }
  // Validate Quantità Formato
  if (column==2 || column==4) {
    var checkResult = quantitaRGEX.test(value);
    if(!checkResult){
      return false;
    }
  }
  return true;
}

// Export commant
$BTN.click(function () {
  var $rows = $TABLE.find('tr:not(:hidden)');
  var headers = [];
  var data = [];

  // Get the headers (add special header logic here)
  $($rows.shift()).find('th:not(:empty)').each(function () {
    headers.push($(this).text()/*.toLowerCase()*/);
  });

  // Turn all existing rows into a loopable array
  $rows.each(function (row,x) {
    var $td = $(this).find('td');
    var h = {};

    // Use the headers from earlier to name our hash keys
    headers.forEach(function (header, i) {
      h[header] = $td.eq(i).text();
      // Check input format
      if(!f_validateinput(h[header],i)){
        alert("Formato sbagliato " + header + " riga " + x.rowIndex);
      }
    });

  data.push(h);
  });

  // Output the result
  $EXPORT.text(JSON.stringify(data));
});
      //# sourceURL=pen.js

// Import from json
test_arr = [
  {"Impegno":"1","Articolo":"2","Q.tà Articolo":"3","Componente":"4","Q.tà Componente":"5","Stato":"6"},
  {"Impegno":"a","Articolo":"b","Q.tà Articolo":"c","Componente":"d","Q.tà Componente":"e","Stato":"f"}
];

// Import command
$BTN2.click(function(){
  var $rows = $TABLE.find('tr:not(:hidden)');
  var headers = [];
  var data = [];

  // Check JSON length and add rows
  if($rows.length <= test_arr.length){
  for (var i = 1; i < test_arr.length; i++){
    var $clone = $TABLE.find('tr.hide').clone(true).removeClass('hide table-line');
    $TABLE.find('table').append($clone);
  }}else{
    $rows.each(function(index){
      if(index > test_arr.length){
        $(this).detach();
      }
    });
  }

  $rows = $TABLE.find('tr:not(:hidden)');

  // Get the headers (add special header logic here)
  $($rows.shift()).find('th:not(:empty)').each(function () {
    headers.push($(this).text()/*.toLowerCase()*/);
  });

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

  $EXPORT.text(JSON.stringify(test_arr));
});

function f_checkbox(){
  var checkBox = document.getElementById("myCheck");
  var text = document.getElementById("text");
  $rows = $TABLE.find('tr:not(:hidden)');
  if (checkBox.checked == true){
    //text.style.display = "block";
    //text.contentEditable = "true";
    $rows.each(function(){
      $(this).find('td').attr('contenteditable','true');
    });

  } else {
     //text.style.display = "none";
     //text.contentEditable = "false";
     $rows.each(function(){
       $(this).find('td').attr('contenteditable','false');
     });
  }
}
