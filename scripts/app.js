var header_art={
  'home':{
    titolo:'<p>News: On year 2020 vers: 2.0.1 for beta tester.</p>'
  },
  'newArticolo':{
    titolo:'<h3>CREAZIONE ARTICOLO - CILINDRO</h3>',
    tabelle:[
      { table_title:'<p>Inserire i dati relativi al cilindro.</p>',
        table_id:'tableArticolo',
        table_class:'t_art',
        headers:[
          {text:'Codice Articolo', id:'cod_art', editabile:true, other:'search_art'},
          {text:'Descrizione', id:'desc_art', editabile:true},
          {text:'Cliente', id:'cli_art', editabile:true},
          {text:'Codice Cliente', id:'cod_cli_art', editabile:true}
        ],
        hascontrol:false
      },
      { table_title:'<br><p>Compilare le righe dei componenti.</p>',
        table_id:'tableComp',
        table_class:'t_comp',
        headers:[
          {text:'Codice Componente', id:'cod_comp', editabile:true, other:'search_comp'},
          {text:'Descrizione', id:'desc_comp', editabile:true},
          {text:'Dimensione', id:'dim_comp', editabile:true},
          {text:'Materiale', id:'mat_comp', editabile:true},
          {text:'Qt Componente', id:'qt_comp', editabile:true}
        ],
        hascontrol:true
       }
    ]
  },
  'newComponente':{
    titolo:'<h3>CREAZIONE COMPONENTE SINGOLO PER PRODUZIONE</h3>',
    tabelle:[
      { table_title:'<p>Inserire i dati relativi ai componenti da produrre.</p>',
        table_id:'tableComp',
        table_class:'t_comp',
        headers:[
          {text:'Codice Componente', id:'cod_comp', editabile:true, other:'search_comp'},
          {text:'Descrizione', id:'desc_comp', editabile:true},
          {text:'Dimensione', id:'dim_comp', editabile:true},
          {text:'Materiale', id:'mat_comp', editabile:true}
        ],
        hascontrol:true
       }
    ]
  },
   'newImpegno':{
     titolo:'<h3>CREAZIONE IMPEGNO </h3>',
     tabelle:[
       { table_title:'<p>Inserire i dati relativi all\'ordine.</p>',
         table_id:'tableImpegno',
         table_class:'t_imp',
         headers:[
           {text:'Codice Impegno', id:'cod_imp', editabile:true, other:'search_imp'},
           {text:'Cliente', id:'cliente', editabile:true},
           {text:'Num. ord. cliente', id:'cod_ord_cli', editabile:true},
           {text:'Data ordine', id:'data_ord', datepicker:true}
         ],
         hascontrol:false
       },
        { table_title:'<br><p>Compilare le righe degli ARTICOLI.</p>',
          table_id:'tableArt',
          table_class:'t_art',
          headers:[
            {text:'Codice Articolo', id:'cod_art', editabile:true, other:'search_art'},
            {text:'Descrizione', id:'desc_art', editabile:true},
            {text:'Quantita\'', id:'qt_art', editabile:true},
            {text:'Data consegna', id:'data_cons_art', datepicker:true}
          ],
          hascontrol:true
        },
        { table_title:'<br><p>Compilare le righe dei COMPONENTI o KIT per la produzione.</p>',
          table_id:'tableComp',
          table_class:'t_comp',
          headers:[
            {text:'Codice Componente', id:'cod_comp', editabile:true, other:'search_comp'},
            {text:'Descrizione', id:'desc_comp', editabile:true},
            {text:'Quantita\'', id:'qt_comp', editabile:true},
            {text:'Data consegna', id:'data_cons_comp', datepicker:true}
          ],
          hascontrol:true
         }
     ]
   }
};

var list_art = [];
var list_comp = [];
var list_imp = [];
var page_class = "";
var PHP_LINK = "./connessioneDB.php";
var $OUTPUT = $('#output_field');
var $EXPORT = $('#export_btn');

jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;

$(document).ready(function() {
  $('a').click(function(event) { ///----- Load page on menu click
    $('#title').children().remove(); // Clean the title field
    $('.container').children().remove(); // Clean the container field
    page_class = $(this).attr('class'); // Get menu button class
    $('#title').append(header_art[page_class].titolo) // Retrieve html from json and append it on title
    if(page_class == 'home'){
      $EXPORT.hide();
      $OUTPUT.hide();
      return false; // If page home exit the function
    }
    create_table(header_art[page_class].tabelle); // Pass table info to function
    $EXPORT.show();
    $OUTPUT.show();
  });
  first_call();
});

function create_table(json){
  json.forEach(function(value, index){  // Loop through table
    $('.container').append(value.table_title); // Table title
    var $table = $('<table></table>'); // Create table obj
    $table.attr('class', value.table_class); // Add class to table
    $table.attr('id', value.table_id); // Add id to table
    var $thead = $('<thead></thead>'); // Create table head obj
    var $tbody = $('<tbody></tbody>'); // Create table body obj
    var $tr = $('<tr></tr>'); // Create row obj
    $tbody.append($tr); // Append row ro table body
    value.headers.forEach(function(h, i){ // Loop through headers to create columns
      var $th = $('<th></th>'); // Create header cell obj
      $th.attr('id', h.id); // Set header id
      $th.text(h.text); // Set header text
      $thead.append($th); // Append header to table head
      var $td = $('<td></td>'); // Create table cell obj
      $td.attr('contenteditable', h.editabile); // Add editability to cell
      $td.attr('class', h.other); // Add class to cell
      if(i==0 && index==0){$td.attr('id', 'first_cell');}
      if (h.datepicker) { // Check if a date field is required
        var $i = $('<input type="text" class="datepicker">'); // Create input obj
        $td.append($i);
      }
      $tr.append($td); // Append cell to row
    });

    if(value.hascontrol){
      $th_c = $('<th class="control"></th>'); // Create new header obj
      $th_c.append('<i class="table-add fa fa-plus"></i>'); // Add Icon
      $td_c = $('<td class:"control"></td>'); // Create new cell obj
      $td_c.append('<i class="table-remove fa fa-times"></i>'); // Add icon
      $thead.append($th_c); // Append new header to head
      $tr.append($td_c); // Append new cell to row
      var $clone = $tr.clone(); // Copy row
      $clone.attr('class', 'hide'); // Add class to copy
      $tbody.append($clone); // Append new row to table
    }
    $table.append($thead); // Append head to table
    $table.append($tbody); // Append body to table
    $('.container').append($table); // Append table to container
  });
  post_init();
};

function post_init(){
  $('.table-add').click(function(){ ///----- Add row on click
    var $parent_table = $(this).parents('table');
    add_row($parent_table,1);
  });
  $('.table-remove').click(function(event) {
    $(this).parents('tr').detach();
  });
  $('.datepicker').each(function(index, $el) {
    add_datepicker($(this));
  });
  add_search($('.search_art'), list_art);
  add_search($('.search_comp'), list_comp);
  add_search($('.search_imp'), list_imp);
};


function add_row($table, n){ ///----- Add new row
  for (var i = 0; i < n; i++) {
    var $clone = $table.find('tr.hide').clone(true, true).removeClass('hide');
    $table.append($clone);
    if (!$clone.find('input').hasClass('.hasDatepicker')) {
      add_datepicker($clone.find('input'));
    }
    var $first_cell = $clone.find('td').first();
    if($first_cell.hasClass('search_art')){
      add_search($first_cell, list_art);
    }else if ($first_cell.hasClass('search_comp')) {
      add_search($first_cell, list_comp);
    }else if ($first_cell.hasClass('search_imp')) {
      add_search($first_cell, list_imp);
    }
  }
};

function add_datepicker($el){
  if ($el.parents('tr').hasClass('hide')) {
    return false;
  }
  $el.datepicker();
  $el.datepicker( "option", "dateFormat", "yy-mm-dd" );
};

function first_call(){
  var val = {};
  var name = 'firstCall';
  val[name] = page_class;
  //$.post(PHP_LINK, val, function(data, textStatus, xhr) {
    var data = JSON.parse(test1);
    $OUTPUT.html(JSON.stringify(data));
    if(data.hasOwnProperty('first_call')){
      if(data['first_call'].hasOwnProperty('list_art')){
        list_art = data['first_call']['list_art'];
      }
      if (data['first_call'].hasOwnProperty('list_comp')){
        list_comp = data['first_call']['list_comp'];
      }
      if (data['first_call'].hasOwnProperty('list_imp')){
        list_imp = data['first_call']['list_imp'];
      }
    }else{
      console.log('Func: first_call, Error: json wrong structure');
    }
  //},'JSON').fail(function(){
    //console.log('Func: first_call, Error: server call fail');
  //});
};

function add_search($el, arr){
  $el.each(function(index, el) {
    if ($(this).parent('tr').hasClass('hide')) {
      return true;
    }
    var container_id = page_class;
    if($(this).attr('id') == 'first_cell'){
      $(this).autocomplete({
        source: arr,
        minLength: 1,
        select: function(event, ui){
          var exp_arr = {};
          var value = ui.item.value;
          var el_class = $(this).attr('class').split(' ')[0];
          exp_arr[el_class] = value;
          //$.post(php_link, exp_arr, function(data, textStatus, xhr) {
          var data = JSON.parse(test2);
          $OUTPUT.text(JSON.stringify(data));
            var $tables = $('.container').find('table');
            $tables.each(function(index, el) {
              var table_name = $(this).attr('class');
              if(data[container_id].hasOwnProperty(table_name)){
                fill_table($(this), data[container_id][table_name]);
              }
            });
          //},'JSON');
        }
      });
    }else{
      $(this).autocomplete({
        source: arr,
        minLength: 1,
        select: function(event, ui){
          var exp_arr = {};
          var value = ui.item.value;
          var el_class = $(this).attr('class').split(' ')[0];
          exp_arr[el_class] = value;

          var data = JSON.parse(test3);
          $OUTPUT.text(JSON.stringify(data));
          //$.post(php_link, exp_arr, function(data, textStatus, xhr) {
            var $row = $el.parent('tr');
            var $table = $el.parents('table');
            var table_name = $table.attr('class');
            fill_row($row, data[container_id][table_name]);
          //},'JSON');
        }
      });
    }
    console.log('search added');
  });
};

function fill_table($table, arr){
  var table_name = $table.attr('class');
  var headers = get_tableHeaders($table);
  var $rows = $table.find('tr:not(:hidden)');

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

  $rows.each(function(index, el) {  // Pass every row and add value to table
    var $td = $(this).find('td');
    headers.forEach(function(h,i){
      if ($td.eq(i).children('input').length) {
        $td.eq(i).children('input').val(arr[index][h]);
      } else {
        $td.eq(i).text(arr[index][h]);
      }
    });
  });
};

function fill_row ($row, arr){
  var headers = get_tableHeaders($row);
  var $td = $row.find('td');
  headers.forEach(function(h,i){
    if ($td.eq(i).children('input').length) {
      $td.eq(i).children('input').val(arr[0][h]);
    } else {
      $td.eq(i).text(arr[0][h]);
    }
  });
};

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

var test1 = '{"first_call":'+
'{"list_imp":["123","321","145","167"],'+
'"list_art":["1ABC00100","1ABC00200","1BCA00100","1BCA00200"],'+
'"list_comp":["1ABC00102","1ABC00110","1ABC00201","1BCA00120","1BCA00230"]}}';

var test2 = '{"newArticolo":'+
'{"t_art":[{"cod_art":"1ABC00100","desc_art":"cilindro","cli_art":"Asd","cod_cli_art":"123456"}],'+
'"t_comp":['+
'{"cod_comp":"1ABC00102","desc_comp":"camicia","dim_comp":"100","mat_comp":"S355","qt_comp":"1"},'+
'{"cod_comp":"1ABC00110","desc_comp":"stelo","dim_comp":"50","mat_comp":"C45","qt_comp":"2"}]}}';

var test3 = '{"newArticolo":'+
'{"t_art":[],'+
'"t_comp":['+
'{"cod_comp":"1ABC00110","desc_comp":"stelo","dim_comp":"50","mat_comp":"C45","qt_comp":"2"}]}}';
