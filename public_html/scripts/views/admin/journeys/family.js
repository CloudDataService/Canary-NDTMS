// Function to make AJAX request to search for client
function search_clients()
{
	// make ajax request
	$.ajax({
		url: "/admin/ajax/search_clients",
		global: false,
		data: ({not_c_id: $("input#j_c_id").val(),
				c_id: $("input#c_id").val(),
				c_fname: $('#f_fname').val(),
				c_sname: $('#f_sname').val(),
				c_date_of_birth: $('#f_date_of_birth').val(),
				url: '/admin/family/add-client/' + $("input#j_id").val() + '/'
		}),
		dataType: "html",
		success: function(data) {
			// if some data is returned
			if(data != '')
			{
				// show results
				$('#search_results').css("visibility", "visible")
									.html(data)
									.fadeIn('slow');
			}
			else
			{
				$('#search_results').fadeOut('slow', function () { $(this).html('') });
			}
		}
	}).responseText;
}
	

$(document).ready(function() {
	
	// Handle AJAX deletion of family members
	$("body").on("click", "#family_members a.delete", function(e){
		
		e.preventDefault();
		e.stopPropagation();
		
		var sure = confirm("Are you sure you want to remove this family member?");
		if ( ! sure) return false;
		
		// Handle to the link tag
		var $a = $(this);
		
		// Send AJAX request to action the delete in the database
		$.ajax({
			url: href = $a.attr("href"),
			global: false,
			success: function(html) {
				$a.parent('td').parent('tr').remove();
			}
		});
	});
	
	
	// Client AJAX search for adding member
	$('#search_results')
		.hide()
		.css({
			"position": "relative",
			"margin-left": "7px",
			"width": "100%",
		});
	
	//$('#c_id, #f_fname, #f_sname, #f_date_of_birth').change(function() {
	$('body').on('change', '#c_id, #f_fname, #f_sname, #f_date_of_birth', function(){
		search_clients();						 
	});
	
});