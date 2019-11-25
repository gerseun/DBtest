// PHP TEST:  https://ptsv2.com/t/zbqq5-1572855256/post

var php_link = "./connessioneDB.php"

var $BTN = $('#export_btn');
var $OUTPUT = $('#output');
var $FIRST = $('.first_cell');
var IS_ADMIN = true;

var test1 = '{"first_call":'+
'{"list_art":["1ABC00100","1ABC00200","1BCA00100","1BCA00200"],'+
'"list_comp":["1ABC00102","1ABC00110","1ABC00201","1BCA00120","1BCA00230"]}}';

var test2 = '{"newArticolo":'+
'{"t_art":[{"cod_art":"1ABC00100","desc_art":"cilindro","cli_art":"Asd","cod_cli_art":"123456"}],'+
'"t_comp":['+
'{"cod_comp":"1ABC00102","desc_comp":"camicia","dim_comp":"100","mat_comp":"S355","qt_comp":"1"},'+
'{"cod_comp":"1ABC00110","desc_comp":"stelo","dim_comp":"50","mat_comp":"C45","qt_comp":"2"}]}}';

var test3 = '{"newArticolo":'+
'{"t_art":[],'+
'"t_comp":['+
'{"cod_comp":"1ABC00110","desc_comp":"stelo","dim_comp":"50","mat_comp":"C45","qt_comp":"2"}]}}';;

var list_art = [];
var list_comp = [];
var list_imp = [];

jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;

//--- Add row on click ---//
$('.table-add').click(function(){
  var $parent_table = $(this).parents('table');
  add_row($parent_table,1);
});

//--- Add n rows to the table ---//
function add_row($parent_table, n){
  for (var i = 0; i < n; i++) {
    var $clone = $parent_table.find('tr.hide').clone(true).removeClass('hide table-line');
    $clone.addClass('search_comp');
    $parent_table.append($clone);

    var $first_cell = $clone.find('td').eq(0);
    var $parent_firstCell = $parent_table.find('td').eq(0);
    var classname = $parent_firstCell.attr('class');
    if(classname.indexOf('search_art') > -1){
      add_searchDialog($first_cell, list_art);
    }else if (classname.indexOf('search_comp') > -1) {
      add_searchDialog($first_cell, list_comp);
    }
  }
};

//--- Remove row on click ---//
$('.table-remove').click(function () {
  $(this).parents('tr').detach();
});

function get_tableHeaders ($el){
  var headers = [];
  if ($el.is('table')){
    $el.find('th:not(.control)').each(function() {  // Get table headers id
      headers.push($(this).attr('id'));
    });
  }else {
    var $table = $el.parents('table');
    $table.find('th:not(.control)').each(function() {  // Get table headers id
      headers.push($(this).attr('id'));
    });
  }
  return headers;
};

$(document).ready(function() {
  var test_firstCall = {};
  var name_Conn = 'firstCall';
  test_firstCall[name_Conn] = $('.container').attr('id');
  $.post(php_link, test_firstCall, function(data, textStatus, xhr) {
    console.log('Server connection success: first_call');
    if (IS_ADMIN) {

//var data = JSON.parse(test1);
//$OUTPUT.html(JSON.stringify(data));
//    }

    if(data.hasOwnProperty('first_call')){
      if(data['first_call'].hasOwnProperty('list_art')){
        list_art = data['first_call']['list_art'];
        add_searchDialog($('.search_art'), list_art);
      }
      if (data['first_call'].hasOwnProperty('list_comp')){
        list_comp = data['first_call']['list_comp'];
        add_searchDialog($('.search_comp'), list_comp);
      }
      if (data['first_call'].hasOwnProperty('list_imp')){
        list_imp = data['first_call']['list_imp'];
        add_searchDialog($('.search_imp'), list_imp);
      }
    }else{
      console.log('json error: first_call');
    }

  }
}, 'JSON').fail(function(){

  console.log('Server connection error: first_call');
});
});

function add_searchDialog ($el, arr){
  var container_id = $('.container').attr('id');
  if($el.attr('id') == 'first_cell'){
    $el.autocomplete({
      source: arr,
      minLength: 1,
      select: function(event, ui){
        var exp_arr = {};
        var value = ui.item.value;
        var el_class = $(this).attr('class').split(' ')[0];
        exp_arr[el_class] = value;
        $.post(php_link, exp_arr, function(data, textStatus, xhr) {
  //var data = JSON.parse(test2);
          //$OUTPUT.text(JSON.stringify(data));
//$OUTPUT.html(JSON.stringify(data));

          var $tables = $('.container').find('table');
          $tables.each(function(index, el) {
            var table_name = $(this).attr('class');
            if(data[container_id].hasOwnProperty(table_name)){
              fill_table($(this), data[container_id][table_name]);
            }
          });
          modify(false);
        },'JSON');
      }
    });
  }else{
    $el.autocomplete({
      source: arr,
      minLength: 1,
      select: function(event, ui){
        var exp_arr = {};
        var value = ui.item.value;
        var el_class = $(this).attr('class').split(' ')[0];
        exp_arr[el_class] = value;

        $.post(php_link, exp_arr, function(data, textStatus, xhr) {
  //var data = JSON.parse(test3);
          //$OUTPUT.text(JSON.stringify(data));

          var $row = $el.parent('tr');
          var $table = $el.parents('table');
          var table_name = $table.attr('class');
          fill_row($row, data[container_id][table_name]);
        },'JSON');
      }
    });
  }
  console.log('search added');
};

function get_value ($el, json, type) {
  var container_id = $('.container').attr('id');
  if (!json.hasOwnProperty(container_id)) {
    console.log('json error: fill_table');
    return false;
  }
};

function modify (bool){
  if(bool){
    $('#modify_btn').hide();
    $('#export_btn').show();
    $('td').attr('contenteditable', 'true');
    $('.control').show();
  }else{
    $('#modify_btn').show();
    $('#export_btn').hide();
    $('td').attr('contenteditable', 'false');
    $('.control').hide();
  }
};

function fill_table($table, arr){
  var table_name = $table.attr('class');
  var headers = get_tableHeaders($table);
  var $rows = $table.find('tr:not(:hidden)');
  $rows.shift();

  if($rows.length < arr.length){
    add_row($table,arr.length-$rows.length); // Add rows is JSON has more
  }else{
      $rows.each(function(index, el) {
        if (index > arr.length-1) { // Remove rows if JSON has more
          $(this).detach();
        }
      });
  }
  var $rows = $table.find('tr:not(:hidden)');  // Find rows
  $rows.shift();  // Remove first row

  $rows.each(function(index, el) {  // Pass every row and add value to table
    var $td = $(this).find('td');
    headers.forEach(function(h,i){
      $td.eq(i).text(arr[index][h])
    });
  });
};

function fill_row ($row, arr){
  var headers = get_tableHeaders($row);
  var $td = $row.find('td');
  headers.forEach(function(h,i){
    $td.eq(i).text(arr[0][h])
  });
};

//--- Export tables value on button click ---//
$BTN.click(function(event) {

  var exp_arr = {};
  var v_arr = {};
  var $tables = $('.container').find('table');

  $tables.each(function(index, el) {  // Loop through tables
    var val = getTable($(this));  // Use function to collect tables values
    var t_class = $(this).attr('class');  // Use table class ass attr. of JSON
    v_arr[t_class] = val;
  });
  var attr = $('.container').attr('id');  // Use container_id ass attr. of JSON

  exp_arr[attr] = JSON.stringify(v_arr)

  $.post( './connessioneDB.php', exp_arr, function(msg){  // Export JSON
    $('#output').html(msg);
  });
  $OUTPUT.text(JSON.stringify(exp_arr));
});

//--- Function to get table values ---//
function getTable($table){
  var arr = [];
  var headers = [];
  var $rows = $table.find('tr:not(:hidden)');

  $table.find('th:not(.control)').each(function(index, el) {  // Get table headers id
    headers.push($(this).attr('id'));
  });
  $rows.shift(); // Remove first row(headers)

  $rows.each(function(index, el) {  // Loop through rows
    var $td = $(this).find('td');
    var val = {};

    headers.forEach(function(header,i){ // Use table header as attr. for cells value in JSON
      val[header] = $td.eq(i).text();
    });
    arr.push(val);
  });
  return (arr); // Return array [{ h1:r1c1, h2:r1c2, ... }, { h1:r2c1, h2:r2c2, ...}, ...]
};
