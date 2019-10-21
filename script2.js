$("p").css('border', '1px solid red');

// Filter table from input
$(document).ready(function(){
  $("#input").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

// Checkbox for table editability
function f_checkbox(){
  if ($("#myCheck").prop('checked')){
    $('[contenteditable]').attr('contenteditable','true');
  } else {
    $('[contenteditable]').attr('contenteditable','false');
  }
}

// Add new row on click
$("#table-add").click(function(){
  alert("clicked");
  //var $clone = $('#table').find('tr.hide').clone(true).removeClass('hide table-line');
  //$('#table').find('table').append($clone);
  //f_checkbox();
});

// A few jQuery helpers for exporting only
jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;

$("#export-btn").click(function(event) {
  alert("suka");
});
