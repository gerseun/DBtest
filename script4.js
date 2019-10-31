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

  $('#codImpegno').focusout(function(event) {
    var imp = $('#codImpegno').text();
    $.post( './connessioneDB.PHP', {impegno : imp}, function(msg){
      $('#output').text(JSON.stringify(msg));

      var tImp = msg.imp;
      var tArt = msg.art;
      var tComp = msg.comp;
      // Completamento tabella Impegno
      $('#cliente').text(tImp.i2);
      $('#datepicker').val(tImp.i3);
      $('#ordine').val(tImp.i4);
      // Completamento tabella Articoli
      var $rowsArt = $('#tableArt').find('tr:not(:hidden)');
      var headersArt = ["ca", "d", "q"];
      if($rowsArt.length <= tArt.length){
        for(var i = 1;i < tArt.length; i++){
          var $clone = $('#tableArticolo').find('tr.hide').clone(true).removeClass('hide table-line');
          $('#tableArticolo').find('table').append($clone);
        }
      }else{
        $rowsArt.each(function(index, el) {
          if(index > tArt.length){
            $(this).detach();
          }
        });
      }
      var $rowsArt = $('#tableArt').find('tr:not(:hidden)');
      $rowsArt.shift();
      $rowsArt.each(function(index, el) {
        var $td = $(this).find('td');
        var obj = tArt[index];
        headersArt.forEach(function(header, i){
          $td.eq(i).text(obj[header]);
        });
      });
      // Completamento tabella Componenti
      var $rowsComp = $('#tableComp').find('tr:not(:hidden)');
      var headersComp = ["c1", "c2", "c3", "c4","c5"];
      if($rowsComp.length <= tComp.length){
        for(var i = 1;i < tComp.length; i++){
          var $clone = $('#tableComponente').find('tr.hide').clone(true).removeClass('hide table-line');
          $('#tableComponente').find('table').append($clone);
        }
      }else{
        $rowsComp.each(function(index, el) {
          if(index > tComp.length){
            $(this).detach();
          }
        });
      }
      var $rowsComp = $('#tableArt').find('tr:not(:hidden)');
      $rowsComp.shift();
      $rowsComp.each(function(index, el) {
        var $td = $(this).find('td');
        var obj = tComp[index];
        headersComp.forEach(function(header, i){
          $td.eq(i).text(obj[header]);
        });
      });
    },"json");
  });
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
  var i3 = $('#datepicker').val();
  var i4 = $('#ordine').text();
  var imp = {i1,i2,i3,i4};

  // Creo array articoli
  var $rowsArt = $('#tableArticolo').find('tr:not(:hidden)');
  var art = [];

  var headersArt = ["ca", "d", "q"];
  var colonArt = $('#headerAr').find('th:not(.control)');
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

  var headersComp = ["c1", "c2", "c3", "c4","c5"];
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
