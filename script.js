var $BTN = $('#export-btn');
var $OUTPUT = $('#output');
var headerImp = [
  'i1', // Impegno
  'i2', // Cliente
  'i3', // Data consegna
  'i4'  // Numero ordine
];
var headerArt = [
  'a1', // Codice Articolo
  'a2', // Descrizione
  'a3', // Cliente
  'a4', // Codice Cliente
  'a5'  // Quantità
];
var headerComp = [
  'c1', // Codice Componente
  'c2', // Descrizione
  'c3', // Dimensione
  'c4', // Materiale
  'c5'  // Quantità
];
var tablesNames =[
  'tableImp',
  'tableArt',
  'tableComp'
];

$BTN.click(function(event) {
  var exportArr = {};
  // Check tables number
  var tables = $('.container').find('table');
  console.log('Number of tables: '+ tables.length);

  tables.each(function(index, el) {
    var val = $(this).attr('class');
    var attrName = tablesNames[index];
    console.log("Valore: "+val+" Nome: "+attrName);
    exportArr[attrName] = val;
  });
  $OUTPUT.text(JSON.stringify(exportArr));
});
