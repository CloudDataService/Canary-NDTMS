// JavaScript Document
$(document).ready(function () {

	multi_add.init({
		"select": "select#disabilities",
		"add": ".action-add-disability",
		"list": "ul.disabilities",
		"hidden_field": "d_id"
	});

	$(".datepicker").datepicker();

	$("#ci_gp_code").autocomplete({
		source: "/admin/ajax/get_gps"
	});

	$('#client_form').validate({
		rules: {
			j_date_of_referral: {
				required: true,
				british_date: true
			},
			ji_date_referral_received: {
				required: false,
				british_date: true
			},
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


	function risk_flag($risk_flag_code)
	{
		if($risk_flag_code == '1')
		{
			//they are a risk
			$('#ji_flagged_risk_summary_row').show();
		}
		else
		{
			$('#ji_flagged_risk_summary_row').hide();
		}
	}
	//check risk flag on load
	risk_flag($('#ji_flagged_as_risk').val())
	//check risk when flag changes
	$('#ji_flagged_as_risk').change(function() {
		risk_flag($('#ji_flagged_as_risk').val());
	});

	//certain fields trigger flagging the journey as a risk.
	$('#ci_previous_offender').change(function() {
		if( $('#ci_previous_offender').val() == '1')
		{
			$('#ji_flagged_as_risk').val('1');
			$('#ji_flagged_risk_summary').val( 'Is a previous offender. ' +$('#ji_flagged_risk_summary').val() );
			risk_flag($('#ji_flagged_as_risk').val())
		}
	});
	$('#ci_current_offender').change(function() {
		if( $('#ci_current_offender').val() == '1')
		{
			$('#ji_flagged_as_risk').val('1');
			$('#ji_flagged_risk_summary').val( 'Is a current offender. ' +$('#ji_flagged_risk_summary').val() );
			risk_flag($('#ji_flagged_as_risk').val())
		}
	});
	$('#ci_pregnant').change(function() {
		if( $('#ci_pregnant').val() == '1')
		{
			$('#ji_flagged_as_risk').val('1');
			$('#ji_flagged_risk_summary').val( 'Is pregnant. ' +$('#ji_flagged_risk_summary').val() );
			risk_flag($('#ji_flagged_as_risk').val())
		}
	});

	// Enable autosave
	autosave.init("#client_form");

});
