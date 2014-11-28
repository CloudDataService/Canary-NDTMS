// JavaScript Document
$(document).ready(function () {
	
	$(".datepicker").datepicker();
	
	$("#ci_gp_code").autocomplete({
		source: "/admin/ajax/get_gps"
	});
			
	$('#client_form').validate({
		rules: {
			ci_fname: {
				required: true
			},
			ci_sname: {
				required: true
			},
			ci_gender: {
				required: true
			},
			ci_date_of_birth: {
				required: true,
				british_date: true
			},
			ci_address: {
				required: true
			},
			ci_post_code: {
				required: true
			},
			ci_no_of_children: {
				digits: true
			}
		}
	});
	
	function male_client($gender_code)
	{
		// if client is male
		if($gender_code == '1')
		{
			// set pregnant value to No
			$('#ci_pregnant').val('2');
		}
	}
	
	// check client's gender on load
	male_client($('#ci_gender').val());
	
	// check client's gender on change
	$('#ci_gender').change(function () {
		male_client($(this).val())
	});
	
	function has_children($parental_status_code)
	{
		if($parental_status_code in ({'' : '', '7' : ''}))
		{
			$('#ci_no_of_children').val('').attr('disabled', 'disabled');
		}
		else
		{
			$('#ci_no_of_children').removeAttr('disabled');
		}
	}
	
	// checks if client has children on load
	has_children($('#ci_parental_status').val());
	
	// checks if client has children on change
	$('#ci_parental_status').change(function () {
		has_children($(this).val());
	});
	
	// Enable autosave
	autosave.init("#client_form");
	
});