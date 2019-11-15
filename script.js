// PHP TEST:  https://ptsv2.com/t/zbqq5-1572855256/post

var $BTN = $('#export_btn');
var $OUTPUT = $('#output');
var $FIRST = $('.first_cell');

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
    $parent_table.append($clone);
  }
};

//--- Remove row on click ---//
$('.table-remove').click(function () {
  $(this).parents('tr').detach();
});

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

//--- On focusout on first cell call database and fill tables ---//
$FIRST.focusout(function(event) {
  var exp_arr = {};
  var cod_art = $FIRST.text();
  var attr = $FIRST.attr('id');
  exp_arr[attr] = cod_art;
//  $.post('./connessioneDB.php',exp_arr, function(msg){
  var msg = JSON.parse(test);
    $OUTPUT.text(JSON.stringify(msg));
    fill_table(msg);
    if ($('#dialog-confirm').length) {
      open_dialog(); // Open dialog for mod or refresh
    }
//  },'json');
});

//--- Function that get a JSON and fill all the tables ---//
function fill_table(json){
  var $tables = $('.container').find('table');
  //if(msg.length != $tables.length){
    //console.log('Wrong number of element received');
    //return false;
  //}
  $tables.each(function(tnum) { // Loop through tables
    var table_name = $(this).attr('class');
    var container_name = $('.container').attr('id');
    var import_rows = json[container_name][table_name];  // Get corresponding table values from JSON
    var headers = [];
    var $rows = $(this).find('tr:not(:hidden)');

    $(this).find('th:not(.control)').each(function() {  // Get table headers id
      headers.push($(this).attr('id'));
    });
    $rows.shift();

    if($rows.length < import_rows.length){
      add_row($(this),import_rows.length-$rows.length); // Add rows is JSON has more
    }else{
        $rows.each(function(index, el) {
          if (index > import_rows.length-1) { // Remove rows if JSON has more
            $(this).detach();
          }
        });
    }
    var $rows = $(this).find('tr:not(:hidden)');  // Find rows
    $rows.shift();  // Remove first row
    $rows.each(function(index, el) {  // Pass every row and add value to table
      var $td = $(this).find('td');
      headers.forEach(function(h,i){
        $td.eq(i).text(import_rows[index][h])
      });
    });
  });
};

$('.check_comp').on('focusout', function(event) {
  event.preventDefault();
  var $table = $(this).parents('#tableComp');
  var headers = [];
  $table.find('th:not(.control)').each(function() {
    headers.push($(this).attr('id'));
  });
  var val = $(this).text();
  var $cells = $(this).siblings('td');
  $.post('./connessioneDB.php', {componente: val}, function(msg) {
    headers.forEach(function(h, i){
      $td.eq(i).text(msg[h]);
    });
  });
});

// Aautocomplete cod articolo
$( function() {
    var availableTags = [
      "ActionScript",
      "AppleScript",
      "Asp",
      "BASIC",
      "C",
      "C++",
      "Clojure",
      "COBOL",
      "ColdFusion",
      "Erlang",
      "Fortran",
      "Groovy",
      "Haskell",
      "Java",
      "JavaScript",
      "Lisp",
      "Perl",
      "PHP",
      "Python",
      "Ruby",
      "Scala",
      "Scheme",
      "asda",
      "adeha",
      "attedhd",
      "khag",
      "lakjhsdaa",
      "alsdhja",
      "asidhal",
      "asdafafg",
      "oasugfa",
      "asfgagaa",
      "oaugflja",
      "aewruear",
      "agiaohua",
      "apghahai",
      "òakhsbfòa",
      "kjagfoa",
      "ljaga",
      "ujgafja",
      "asdkthd",
      "asdklhkhd",
      "asdhbsbv0",
      "asdasgg"
    ];
    $( "#articolo" ).autocomplete({
      source: availableTags,
      minLength: 3,
      select: function(event, ui){
        console.log(event);
      }
    });
  } );

// Open box if cod articolo exist
function open_dialog(){
  $( "#dialog-confirm" ).dialog({
    resizable: false,
    height: "auto",
    width: 400,
    modal: true,
    buttons: {
      "Modifica": function() {
        var container_id = $('.container').attr('id');
        if (container_id == "newArticolo"){
          $('.container').attr('id', 'modArticolo');
        }
        $( this ).dialog( "close" );
      },
      Cancel: function() {
        $( this ).dialog( "close" );
        location.reload();
      }
    }
  });
};

var test = '{"newArticolo":'+
'{"t_art":[{"cod_art":"a","desc_art":"b","cli_art":"c","cod_cli_art":"d"}],'+
'"t_comp":['+
'{"cod_comp":"e","desc_comp":"f","dim_comp":"g","mat_comp":"h","qt_comp":"i"},'+
'{"cod_comp":"l","desc_comp":"m","dim_comp":"n","mat_comp":"o","qt_comp":"o"}]}}';
/*{"newArticolo":
            {"t_art":[
              {"cod_art":"a","desc_art":"b","cli_art":"c","cod_cli_art":"d"}
            ],
            "t_comp":[
              {"cod_comp":"e","desc_comp":"f","dim_comp":"g","mat_comp":"h","qt_comp":"i"},
              {"cod_comp":"l","desc_comp":"m","dim_comp":"n","mat_comp":"o","qt_comp":"o"}
            ]}
          };*/
