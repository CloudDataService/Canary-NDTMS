// JavaScript Document
$(document).ready(function () {
	
	$(".datepicker").datepicker();
	
	$('#journey_form').validate({
		rules: {
			j_date_of_referral: {
				british_date: true
			},
			ji_date_referral_received: {
				british_date: true
			},
			ji_date_rc_allocated: {
				british_date: true
			},
			j_closed_date: {
				british_date: true
			}
		}
	});
	
	autosave.init("#journey_form");
	
});