// PHP TEST:  https://ptsv2.com/t/zbqq5-1572855256/post

var $BTN = $('#export_btn');
var $OUTPUT = $('#output');
var $FIRST = $('#first_cell');

jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;

// Add new row on click
$('.table-add').click(function(){
  var $clone = $('#table').find('tr.hide').clone(true).removeClass('hide table-line');
  $('#table').find('table').append($clone);
});

// Remove row on click
$('.table-remove').click(function () {
  $(this).parents('tr').detach();
});

// Export tables value on button click
$BTN.click(function(event) {
  var exp_arr = {};
  var v_arr = {};
  var $tables = $('.container').find('table');
  // Loop through tables
  $tables.each(function(index, el) {
    var val = getTable($(this));
    // Get table class as attr. of json
    var t_class = $(this).attr('class');
    v_arr[t_class] = val;
  });
  var attr = $('.container').attr('id');
  exp_arr[attr] = JSON.stringify(v_arr)
  $.post( './connessioneDB.php', exp_arr, function(msg){
    $('#output').html(msg);
  });
  $OUTPUT.text(JSON.stringify(exp_arr));
});

// Function to get table values
function getTable($table){
  var arr = [];
  var headers = [];
  var $rows = $table.find('tr:not(:hidden)');
  // Get headers id
  $table.find('th:not(.control)').each(function(index, el) {
    headers.push($(this).attr('id'));
  });
  $rows.shift(); // Remove first row(headers)
  // Loop through rows
  $rows.each(function(index, el) {
    var $td = $(this).find('td');
    var val = {};
    // Get cell value and connect to headers id
    headers.forEach(function(header,i){
      val[header] = $td.eq(i).text();
    });
    arr.push(val);
  });
  return (arr);
};

// On focusout check value to database and fill tables
$FIRST.focusout(function(event) {
  /* Act on the event */
});
