
var header_art = {
  'home': {
    titolo: '<p>News: On year 2020 vers: 2.0.1 for beta tester.</p>'
  },
  'about': {
    titolo: '<p><h1>Everything works fine!</h1><i style="font-size: 5em;" class="fa fa-thumbs-up"></i></p>'
  },
  'newArticolo': {
    titolo: '<h3>CREAZIONE ARTICOLO - CILINDRO</h3>',
    tabelle: [{
        table_title: '<p>Inserire i dati relativi al cilindro.</p>',
        table_id: 't_art',
        headers: [{
            text: 'Codice Articolo',
            id: 'cod_art',
            editabile: true,
            datepicker: false
          },
          {
            text: 'Descrizione',
            id: 'desc_art',
            editabile: true,
            datepicker: false
          },
          {
            text: 'Cliente',
            id: 'cli_art',
            editabile: true,
            datepicker: false
          },
          {
            text: 'Codice Cliente',
            id: 'cod_cli_art',
            editabile: true,
            datepicker: false
          },
          {
            text: 'id articolo',
            id: 'id_art',
            editabile: false,
            datepicker: false
          }
        ],
        search: true,
        control: false,
        first_line: true
      },
      {
        table_title: '<br><p>Compilare le righe dei componenti.</p>',
        table_id: 't_comp',
        headers: [{
            text: 'Codice Componente',
            id: 'cod_comp',
            editabile: true,
            datepicker: false
          },
          {
            text: 'Descrizione',
            id: 'desc_comp',
            editabile: true,
            datepicker: false
          },
          {
            text: 'Dimensione',
            id: 'dim_comp',
            editabile: true,
            datepicker: false
          },
          {
            text: 'Materiale',
            id: 'mat_comp',
            editabile: true,
            datepicker: false
          },
          {
            text: 'Qt Componente',
            id: 'qt_comp',
            editabile: true,
            datepicker: false
          },
          {
            text: 'id comp',
            id: 'id_comp',
            editabile: false,
            datepicker: false
          }

        ],
        search: true,
        control: true,
        first_line: true
      }
    ]
  }
};
page_id='';
jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;
var PHP_LINK = "../connessioneDB.php";
var $OUTPUT = $('#output_text');
var $EXPORT = $('#export_btn');
var $MODIFY = $('#modify_btn');
var $TEST = $('#test_btn');

var ADMIN = false;

$('.login').click(function(event) {
  if(!ADMIN){
    if ($('.login_name').val() == 'admin') {
      if ($('.login_password').val() == 'password') {
        ADMIN = true;
        $('.login_name').hide();
        $('.login_password').hide();
        $('.login').text('Logout');
        isAdmin(ADMIN);
      }
    }
  }else{
    ADMIN = false;
    $('.login_name').show();
    $('.login_password').show();
    $('.login').text('Login');
    isAdmin(ADMIN);
  }
});

function isAdmin(bool){
  if (bool) {
    //$OUTPUT.show();
    $TEST.show();
  }else {
    //$OUTPUT.hide();
    $TEST.hide();
  }
};

$(document).ready(function() {
  $('a').click(function(event) {
    // Clean the page
    $('#title').children().remove();
    $('#page').children().remove();
    $OUTPUT.text('');
    $OUTPUT.html('');
    // Get page class
    page_id = $(this).attr('id');
    // Populate page
    $('#title').append(header_art[page_id].titolo);
    if (page_id == 'home' || page_id == 'about') {
      $EXPORT.hide();
      $OUTPUT.hide();
      return false;
    }else{
      $EXPORT.show();
      $OUTPUT.show();
      create_table(header_art[page_id].tabelle);
    }

  });
});

function create_table(table){
  $container = $('<div id="container"></div>');
  table.forEach(function(value, index){
    $container.append(value.table_title);
    var $table = $('<table></table>');
    $table.attr('id', value.table_id);
    var $thead = $('<thead></thead>');
    var $tbody = $('<tbody></tbody>');
    var $tr = $('<tr></tr>');
    value.headers.forEach(function(h, i) {
      var $th = $('<th></th>');
      var $td = $('<td></td>');
      $th.attr('id', h.id);
      $th.text(h.text);
      $thead.append($th);
      if (h.editabile) {
        $td.attr('contenteditable', true);
        if(value.search && i == 0){
          $td.addClass('search');
        }
      }else{
        $td.css('color', '#8c8c8c');
      }
      if (h.datepicker) {
        var $i = $('<input type="text" class="datepicker">');
        $td.append($i);
      }
      if (i == 0 && index == 0 && $('#page') && page_id != 'newComponente') {
        $td.attr('id', 'first_cell');
      }
      $tr.append($td);
    });
    if (value.first_line) {
      $tbody.append($tr);
    }
    if (value.control) {
      $th_c = $('<th class="control"></th>');
      $th_c.append('<i class="table-add fa fa-plus"></i>');
      $td_c = $('<td class="control"></td>');
      $td_c.append('<i class="table-remove fa fa-times"></i>');
      $thead.append($th_c);
      $tr.append($td_c);
      var $clone = $tr.clone();
      $clone.attr('class', 'hide');
      $tbody.append($clone);
    }

    $table.append($thead);
    $table.append($tbody);
    $container.append($table);
  });
  $('#page').append($container);

  $('.table-add').click(function() { ///----- Add row on click
    var $parent_table = $(this).parents('table');
    add_row($parent_table, 1);
  });
  $('.table-remove').click(function(event) {
    $(this).parents('tr').detach();
  });
};

function add_row($table, n) { ///----- Add new row
  for (var i = 0; i < n; i++) {
    var $clone = $table.find('tr.hide').clone(true, true).removeClass('hide');
    $table.append($clone);
  }
};
