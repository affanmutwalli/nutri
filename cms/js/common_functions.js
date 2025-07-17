
function HideSuccessMessage() {
	$(function() {
		$('#SuccessMessage').delay(2000).fadeOut('slow');
	});
};
function HideUpdateMessage() {
	$(function() {
		$('#UpdateMessage').delay(2000).fadeOut('slow');
	});
};


$(document).ready(function() {
HideSuccessMessage();
});
$(document).ready(function() {
HideUpdateMessage();
});

function get_items_list(value)
{
	
	$.get("getFolder/getItemListFromCategories.php",
	  {
	  	CategoryId:value
	  },
	  function(data,status){
		$('#ItemDIV').html(data);
	  });
}
function get_items_list_with_unit(value)
{
	
	$.get("getFolder/getItemListFromCategoriesWithUnit.php",
	  {
	  	CategoryId:value
	  },
	  function(data,status){
		$('#ItemDIV').html(data);
		NeedToStopTimer=true;
	  });
}
function get_items_list_with_unit_for_edit(value,type)
{
	
	$.get("getFolder/getItemListFromCategoriesWithUnit1.php",
	  {
	  	CategoryId:value
	  },
	  function(data,status){
		$('#ItemDIV').html(data);
	  });
}

function sortTable() {
  var table, rows, switching, i, x, y, shouldSwitch;
  table = document.getElementById("example1");
  switching = true;
  /* Make a loop that will continue until
  no switching has been done: */
  while (switching) {
    // Start by saying: no switching is done:
    switching = false;
    rows = table.rows;
    /* Loop through all table rows (except the
    first, which contains table headers): */
    for (i = 1; i < (rows.length - 1); i++) {
      // Start by saying there should be no switching:
      shouldSwitch = false;
      /* Get the two elements you want to compare,
      one from current row and one from the next: */
      x = rows[i].getElementsByTagName("TD")[0];
      y = rows[i + 1].getElementsByTagName("TD")[0];
      // Check if the two rows should switch place:
      if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
        // If so, mark as a switch and break the loop:
        shouldSwitch = true;
        break;
      }
    }
    if (shouldSwitch) {
      /* If a switch has been marked, make the switch
      and mark that a switch has been done: */
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
    }
  }
}


/*function setInputFilter(textbox, inputFilter) {
  ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
    textbox.addEventListener(event, function() {
      if (inputFilter(this.value)) {
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      }
    });
  });
}*/
/*
<p>There is also a <a href="https://jsfiddle.net/emkey08/tvx5e7q3" target="_blank">jQuery version</a> of this.</p>
<table>
  <tr><td>Integer (both positive and negative):</td><td><input id="intTextBox"></td></tr>
  <tr><td>Integer (positive only):</td><td><input id="uintTextBox"></td></tr>
  <tr><td>Integer (positive and &lt;= 500):</td><td><input id="intLimitTextBox"></td></tr>
  <tr><td>Floating point (use . or , as decimal separator):</td><td><input id="floatTextBox"></td></tr>
  <tr><td>Currency (at most two decimal places):</td><td><input id="currencyTextBox"></td></tr>
  <tr><td>Hexadecimal:</td><td><input id="hexTextBox"></td></tr>
</table>
// Install input filters.
setInputFilter(document.getElementById("intTextBox"), function(value) {
  return /^-?\d*$/.test(value); });
setInputFilter(document.getElementById("uintTextBox"), function(value) {
  return /^\d*$/.test(value); });
setInputFilter(document.getElementById("intLimitTextBox"), function(value) {
  return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 500); });
setInputFilter(document.getElementById("floatTextBox"), function(value) {
  return /^-?\d*[.,]?\d*$/.test(value); });
setInputFilter(document.getElementById("currencyTextBox"), function(value) {
  return /^-?\d*[.,]?\d{0,2}$/.test(value); });
setInputFilter(document.getElementById("hexTextBox"), function(value) {
  return /^[0-9a-f]*$/i.test(value); });
*/


