/**
 * Javascript code for outcome star default outcomes (in options)
 */

$(document).ready(function(){
	
	// Add a new row to the table by copying hidden blank row
	$("a#add_row").click(function(e){
		e.preventDefault();
		// Find static blank row and clone it to the table
		$("table#criteria_outcomes tfoot tr#blank-row")
			.clone()
			// Show it
			.fadeIn('fast')
			// Add the outcome count to the number text box
			.find("td.num input").val(q_count).end()
			// Remove blank-row ID
			.attr("id", "")
			// Add the row just before the blank row (near the end)
			.appendTo("table#criteria_outcomes tbody")
			// Focus the text box
			.find("td.outcome input").focus();
		// Increment the outcome question count
		q_count++;
	});
	
	
	// No existing criteria? Trigger the adding of a row
	if (q_count == 1) {
		$("a#add_row").trigger("click");
	}
	
	
	// Add new row if tab is pressed on the last input box in a row
	$("table#criteria_outcomes tbody td.outcome input").live("keydown", function(e){
		console.log($(this).closest("tr"));
		var last_row = $(this).closest("tr").is(":last-child");
		var keyCode = e.keyCode || e.which; 
		if (keyCode == 9 && last_row) {
			e.preventDefault();
			$("a#add_row").trigger("click");
		}
	});
	
	
	// Remove a row
	$("td.remove").on("click", "a", function(e){
		e.preventDefault();
		var row = $(this).parent().parent();
		row.remove();
		q_count--;
	});
	
	
})