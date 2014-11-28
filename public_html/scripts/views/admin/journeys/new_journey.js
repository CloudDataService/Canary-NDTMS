// JavaScript Document
$(document).ready(function () {
	
	$(".datepicker").datepicker();
			
	$('#new_journey_form').validate({
		rules: {
			c_fname: {
				required: true
			},
			c_sname: {
				required: true
			},
			c_gender: {
				required: true
			},
			c_date_of_birth: {
				required: true,
				british_date: true
			},
			c_address: {
				required: true
			},
			c_post_code: {
				required: true
			},
			j_date_of_referral: {
				required: true,
				british_date: true
			},
			j_family_or_carer_involved: {
				required: true
			}
		}
	});
	
	// make ajax request
	function search_clients()
	{
		// make ajax request
		$.ajax({
			url: "/admin/ajax/search_clients",
			global: false,
			data: ({c_fname : $('#c_fname').val(),
					c_sname : $('#c_sname').val(),
					c_date_of_birth : $('#c_date_of_birth').val(),
					c_post_code : $('#c_post_code').val(),
					j_type: $("input[name='j_type']").val(),
					url: '/admin/journeys/new-journey/'
			}),
			dataType: "html",
			success: function(data) {
				
				// if some data is returned
				if(data != '')
				{
					// show results
					$('#search_results').html(data)
										.fadeIn('slow');
				}
				else
				{
					$('#search_results').fadeOut('slow', function () { $(this).html('') });
				}
			}
		}).responseText;
	}
	
	$('#search_results').hide();	
	
	if ( ! $('#c_id').length)
	{
		$('#c_fname, #c_sname, #c_date_of_birth, #c_post_code').change(function () {
			search_clients();																 
		});
	}
});