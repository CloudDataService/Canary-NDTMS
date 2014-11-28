var ass_criteria = (function($) {
	
	var table_sel = null;		// jquery selector for the table
	var $table = null;		// table of items as jquery object
	
	var select_sel = null;
	var $select = null;
	
	var o_count = 0;		// outcome count
	
	
	// Clear all rows in the table
	clear_items = function() {
		var rows = $(table_sel + " tr.current, " + table_sel + " tr.dynamic").remove();
		o_count = 1;
	}
	
	
	// Add new row
	add_row = function() {
		// Find static blank row and clone it to the table
		$(table_sel + " tfoot tr#blank-row")
			.clone()
			// Show it
			.fadeIn()
			// Add the outcome count to the number text box
			.find("td.num input").val(o_count).end()
			// Remove blank-row ID
			.attr("id", "")
			// add class
			.addClass("dynamic")
			// Add the row just before the blank row (near the end)
			.appendTo(table_sel + " tbody")
			// Focus the text box
			.find("td.outcome input").focus();
		// Increment the outcome question count
		o_count++;
	}
	
	
	// Remove a single row
	remove_row = function(row) {
		row.remove();
		o_count--;
	}
	
	
	// Populate the table of outcomes from a selected list of detaults
	/* populate_list = function(acl_id) {
		
		clear_items();
		
		if (window.acls[acl_id]) {
			var criteria = window.acls[acl_id];
			// Loop through the default outcomes
			$.each(criteria, function(idx, o){
				// Clone the blank row
				$(table_sel + " tfoot tr#blank-row")
					.clone()
					// Set the values
					.find("td.num input").val(idx).end()
					.find("td.outcome input").val(o).end()
					// Remove blank-row ID
					.attr("id", "")
					.addClass("dynamic")
					// Add the row just before the blank row (near the end)
					.appendTo(table_sel + " tbody")
					.show();
				// Increment the outcome question count
				o_count++;
			});
		}
	} */
	
	
	// Set the outcome counter
	set_o_count = function(num) {
		o_count = num;
	}
	
	
	// Initialise
	init = function(table, select) {
		
		table_sel = table;
		$table = $(table_sel);
		
		// Set up events
		
		// Add row
		$table.on("click", "a#add_row", function(e) {
			e.preventDefault();
			add_row();
		});
		
		// Remove row
		$table.on("click", "a.remove_row", function(e) {
			remove_row($(this).parents("tr"));
		});
		
		// Tab entered in input box
		$(table_sel + " tbody td.outcome input").live("keydown", function(e){
			var last_row = $(this).parents("tr").is(":last-child");
			var keyCode = e.keyCode || e.which; 
			if (keyCode == 9 && last_row) {
				e.preventDefault();
				add_row();
			}
		});
		
		// Default - add new row
		if (o_count === 1) add_row();
	}
	
	
	return {
		init: init,
		set_o_count: set_o_count
	}
	
	
})(jQuery);


$(document).ready(function() {
	
	$('#assessment_criteria_form').validate();
	
	// add rules to datepickers	
	$('#assessment_criteria_form input.datepicker').each(function () {
		$(this).rules("add", {
			british_date: true
		});
	});
	
	$('#assessment_form').validate({
		rules: {
			"jas[jas_date]": {
				required: true,
				british_date: true
			}
		}
	});
	
		
	// open up assessment criteria form
	$('#assessment_criteria_btn').click(function () {
		$("#assessment_criteria").dialog({
			width: 500,
			resizable: false,
			modal: true,
			zIndex: 1,
			position: ["center", 100],
		});
		return false;
	});
	
	
	$("table#criteria_outcomes").on("focus", "input.datepicker", function() {
		$(this).datepicker();
	});
	
	
	// open up assessment form
	$('#assessment_btn').click(function () {
		$( "#assessment" ).dialog({
			width: 800,
			maxHeight: "75%",
			resizable: false,
			modal: false,
			zIndex: 1,
			position: ["center", 100],
			open: function() {
				$("table#assessment_form_table input.datepicker").datepicker();
			}
		});
		return false;
	});
	
	
	// populate outcomes based on criteria list selection
	$("select#acl_id").on("change", function() {
		var acl_id = $(this).val();
		var dest = $("table#assessment_form_table tbody.outcomes");
		dest.html("");
		var group = $("table#all_outcomes tbody[data-acl_id='" + acl_id + "'] tr");
		group.clone().appendTo(dest);
	});
	
	
	
	ass_criteria.set_o_count(o_count);
	ass_criteria.init("table#criteria_outcomes");
	
});