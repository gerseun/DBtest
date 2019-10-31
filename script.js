var $BTN = $('#export-btn');
var $OUTPUT = $('#output');
var possibleTables = {
  'tableImp': { 'name':   'imp',
                'headers': {'Impegno' :             'i1',
                            'Cliente':              'i2',
                            'Data consegna':        'i3',
                            'Numero ordine':        'i4'}},
  'tableArt': { 'name':     'art',
                'headers': {'Codice Articolo':      'a1',
                            'Descrizione':          'a2',
                            'Cliente':              'a3',
                            'Codice Cliente':       'a4',
                            'Quantità':             'a5'}},
  'tableArtImp': { 'name':     'art',
                'headers': {'Codice Articolo':      'a1',
                            'Descrizione':          'a2',
                            'Quantità':             'a5'}},
  'tableComp': { 'name':    'comp',
                'headers': {'Codice Componente':    'c1',
                            'Descrizione':          'c2',
                            'Dimensione':           'c3',
                            'Materiale':            'c4',
                            'Quantità':             'c5'}};

jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;

$BTN.click(function(event) {
  var exportArr = {};
  // Check tables number
  var tables = $('.container').find('table');

  tables.each(function(index, el) {
    var val = getTable($(this));
    var tableClass = $(this).attr('class');
    exportArr[possibleTables[tableClass].name] = val;
    //var val = $(this).attr('class');
    //var attrName = tablesNames[val];
    //console.log("Valore: "+index+" Nome: "+attrName);
    //exportArr[attrName] = index;
  });
  $OUTPUT.text(JSON.stringify(exportArr));
});

function getTable($table){
  var arr = [];
  var tableClass = $table.attr('class');
  var tableRows = $table.find('tr:not(:hidden)');
  var tableHeaders = possibleTables[tableClass].headers;
  console.log("1 - " + tableClass);
  console.log("2 - " + possibleTables);
  console.log("3 - " + possibleTables[tableClass]);
  console.log("4 - " + tableHeaders.length);
  console.log("5 - " + $table.find('th:not(.control)').length);

  tableRows.shift();
  tableRows.each(function(index, el) {
    var $td = $(this).eq(index).text();
    var val = {};
    tableHeaders.forEach(function (header, i){
      val[header] = $td.eq(i).text();
    });
    arr.push(val);
  });

  return (arr);
};
