header_art={'newArticolo':
  [
    {name:'Codice Articolo',id:'cod_art'},
    {name:'Descrizione',id:'desc_art'},
    {name:'Cliente',id:'cli_art'},
    {name:'Codice Cliente',id:'cod_cli_art'}],
   'newComponente':
   [
     {table_name:'t_comp',
      }
   ],
   'newImpegno':
   []
};

$('a').click(function(event) {
  create_table(header_art);
});

function create_table(headers){
  var $table = $('<table></table>');
  var $thead = $('<thead></thead>');
  var $tbody = $('<tbody></tbody>');
  headers.forEach(function(item,index){
    var $cell = $('<th></th>');
    $cell.text(item.name);
    $cell.attr('id', item.id);
    $thead.append($cell);
  });
  headers.forEach(function(item,index){
    var $cell = $('<td></td>');
    $cell.attr('contenteditable', true);
    $tbody.append($cell);
  });
  $table.append($thead);
  $table.append($tbody);
  $('#title').append($table);
  //$('#title').after($table);
}
