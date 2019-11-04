var $BTN = $('#export-btn');
var $OUTPUT = $('#output');
var possibleTables = {
  'tableImp': { 'name':     'imp',
                'headers': {'Impegno':              'i1',
                            'Cliente':              'i2',
                            'Data consegna':        'i3',
                            'Ordine':        'i4'}},
  'tableArt': { 'name':     'art',
                'headers': {'Codice Articolo':      'a1',
                            'Descrizione':          'a2',
                            'Cliente':              'a3',
                            'Codice Cliente':       'a4',
                            'Quantità':             'a5'}},
  'tableArtImp': { 'name':  'art',
                'headers': {'Codice Articolo':      'a1',
                            'Descrizione':          'a2',
                            'Quantità':             'a5'}},
  'tableComp': { 'name':    'comp',
                'headers': {'Codice Componente':    'c1',
                            'Descrizione':          'c2',
                            'Dimensione':           'c3',
                            'Materiale':            'c4',
                            'Quantità':             'c5'}}};

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
  var headers = [];
  var className = $table.attr('class');
  var rows = $table.find('tr:not(:hidden)');

  $table.find('th:not(.control)').each(function(index, el) {
    headers.push($(this).text());
  });

  if(headers.length == Object.keys(possibleTables[className].headers).length){
    rows.shift();
    rows.each(function(index, el) {
      var $td = $(this).find('td');
      var val = {};
      headers.forEach(function(header,i){
        val[possibleTables[className]['headers'][header]] = $td.eq(i).text();
      });
      arr.push(val);
    });

  }else{
    console.log('Errore');
    return false;
  }

  return (arr);
};
