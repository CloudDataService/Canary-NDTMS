// JavaScript Document
$(document).ready(function() {
						
	
	$('form#admin_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			email: {
				required: true,
				email: true,
				remote: "/admin/options/admin_email/" + $('#admin_id').val()
			},
			email_confirmed: {
				required: true,
				equalTo: "#email"
			},
			fname: {
				required: true
			},
			sname: {
				required: true
			},
			password: {
				password_restrict: true
			},
			password_confirmed: {
				equalTo: "#password"
			}
		},
		messages: {
			email: {
				remote: "This email address is already registered."
			},
			email_confirmed: {
				equalTo: "Please confirm your email address."
			},
			password_confirmed: {
				equalTo: "Please confirm your password."
			}
		},
		submitHandler: function (form) {
			if($('#master:checked').length)
			{
				if(confirm('You are currently the master administrator. Making the following administrator master will log you out.'))
				{
					form.submit();
				}
				
				return false;
			}
			else
			{
				form.submit();
			}
		}
	});
	
	
});