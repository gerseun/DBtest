// PHP TEST:  https://ptsv2.com/t/zbqq5-1572855256/post

var $BTN = $('#export_btn');
var $OUTPUT = $('#output');
var $FIRST = $('.first_cell');

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

  var exp_arr = {};
  var cod_art = $FIRST.text();
  var attr = $FIRST.attr('id');
  exp_arr[attr] = cod_art;
  $.post('./connessioneDB.php',exp_arr, function(msg){
    $OUTPUT.text(JSON.stringify(msg));
    var $tables = $('.container').find('table');
    //if(msg.length != $tables.length){
      //console.log('Wrong number of element received');
      //return false;
    //}
    $tables.each(function() {
      var t_name = $(this).attr('class');
      var val = msg[t_name];
      var headers = [];
      var $rows = $(this).find('tr:not(:hidden)');

      if(val.length != $rows.length-1){
        console.log('Wrong number of rows passed');
        return false;
      };
      $(this).find('th:not(.control)').each(function() {
        headers.push($(this).attr('id'));
      });
      $rows.shift();
      $rows.each(function(index, el) {
        var $td = $(this).find('td');
        //if($td.length != headers.length){
          //console.log('Wrong number of value passed');
          //return false;
        //};
        headers.forEach(function(h, i){
          $td.eq(i).text(val[index][h]);
        });
      });
    });
  },'json');
});
