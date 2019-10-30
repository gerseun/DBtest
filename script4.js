// POST testing:   https://ptsv2.com/t/jde4z-1571982762/post

$("p").css('border', '1px solid red');

// Filter table from input
$(document).ready(function(){
  $("#input").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });

/*
  $('#codArticolo').focusout(function(event) {
    var art = $('#codArticolo').text();
    $.post( './connessioneDB.PHP', {articolo : art}, function(msg){
      $('#output').text(JSON.stringify(msg));

      var art = [];
      var comp = [];
      art = msg.aa;
      comp = msg.ac;

      $('#codArticolo').text(art.ca);
      $('#descrizione').text(art.d);
      $('#cliente').text(art.c);
      $('#codCliente').text(art.cc);

      var $rows = $('#table').find('tr:not(:hidden)');
      var headers = [];
      if($rows.length <= comp.length){
      for (var i = 1; i < comp.length; i++){
        var $clone = $('#table').find('tr.hide').clone(true).removeClass('hide table-line');
        $('#table').find('table').append($clone);
      }}else{
        $rows.each(function(index){
          if(index > comp.length){
            $(this).detach();
          }
        });
      }
      $rows = $('#table').find('tr:not(:hidden)');
      $('.header').find('th:not(.control)').each(function (index) {
        headers.push($(this).text());
      });
      $rows.shift();
      // Turn all existing rows into a loopable array
      $rows.each(function (index) {
        var $td = $(this).find('td');
        var h = {};
        var obj = comp[index];
        $('#output').text(JSON.stringify(obj));
        headers = ["c1","c2","c3","c4"];
        // Use the headers from earlier to name our hash keys
        headers.forEach(function (header, i) {
          $td.eq(i).text(obj[header]);
        });
      });
    },"json");
  });*/
});


// Add new row on click
$('.table-add1').click(function(){
  var $clone = $('#tableArticolo').find('tr.hide').clone(true).removeClass('hide table-line');
  $('#tableArticolo').find('table').append($clone);
  //checkBox();
});
// Add new row on click
$('.table-add2').click(function(){
  var $clone = $('#tableComponente').find('tr.hide').clone(true).removeClass('hide table-line');
  $('#tableComponente').find('table').append($clone);
  //checkBox();
});

// Remove row on click
$('.table-remove').click(function () {
  $(this).parents('tr').detach();
});

// A few jQuery helpers for exporting only
jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;
jQuery.fn.reverse = [].reverse;

// Export table data
$('#export-btn-ni').click(function(event) {
  // Creo array Impegno
  var i1 = $('#codImpegno').text();
  var i2 = $('#cliente').text();
  var i3 = $('#date').val();
  var i4 = $('#ordine').text();
  var imp = {i1,i2,i3,i4};

  // Creo array articoli
  var $rowsArt = $('#tableArticolo').find('tr:not(:hidden)');
  var art = [];

  var headersArt = ["ca", "d", "c", "cc"];
  var colonArt = $('#headerAr').find('th:not(.control)')
  if (colonArt.length != headersArt.length){
    console.log("Numero Header tabella Articoli sbagliato");
    return false;
  }
  $rowsArt.shift();
  $rowsArt.each(function(row,x) {
    var $td = $(this).find('td');
    var h = {};
    headersArt.forEach(function(header,i){
      h[header] = $td.eq(i).text();
    });
    art.push(h);
  });

  // Creo array Componenti
  var $rowsComp = $('#tableComponente').find('tr:not(:hidden)');
  var comp = [];

  var headersComp = ["c1", "c2", "c3", "c4"];
  $rowsComp.shift();
  $rowsComp.each(function(row,x) {
    var $td = $(this).find('td');
    var h = {};
    headersComp.forEach(function(header,i){
      h[header] = $td.eq(i).text();
    });
    comp.push(h);
  });

  var arr = {imp, art, comp};
  $('#output').text(JSON.stringify(arr));

  $.post( './connessioneDB.PHP', { newImpegno:JSON.stringify(arr)}, function(msg){
    $('#output').html(msg);
  });
});


// Check input format
function validateInput(value, column){
  /*
  var impegnoRGEX = /^[0-9]{0,1}[0-9]{0,1}[0-9]{0,1}[0-9]{0,1}[-\s\/]{1}[0-9]{2}$/;
  var articoloRGEX = /^[0-9]{1}[A-Z]{3}[0-9]{5}/;
  var quantitaRGEX = /^[0-9]{1}[0-9]{0,1}[0-9]{0,1}[0-9]{0,1}$/;
  // Validate Impegno format. Accepted format (1111-11) (1111 11) (1111/11)
  if(column==0){
    var checkResult = impegnoRGEX.test(value);
    if(!checkResult){
      return false;
    }
  }
  // Validate Articolo format. Accepted format (1AAA11111...)
  if(column==1){
    var checkResult = articoloRGEX.test(value);
    if(!checkResult){
      return false;
    }
  }
  // Validate Quantità format. Accepted format (1111)
  if (column==2 || column==4) {
    var checkResult = quantitaRGEX.test(value);
    if(!checkResult){
      return false;
    }
  }*/
  return true;
}
