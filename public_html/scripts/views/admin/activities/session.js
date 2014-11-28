// JavaScript Document
$(document).ready(function () {
	
	$(".datepicker").datepicker();
	
	$('#session_form').validate({
		rules: {
			sps_date: {
				required: true,
				british_date: true
			}
		}
	});
	
	// bind validation rules
	$('#client_form').validate({
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
				british_date: true,
				required: true
			},
			c_post_code: {
				required: true
			}
		},
		submitHandler: function (form) {
		
			var $url = $(form).attr('action');
			var $data = $(form).serialize();
			
			$.post('/admin/clients/set', $data,
				function(data) {
					$('#client_div').dialog('close');

					var redirect = $url + '/' + data;
					
					window.location = redirect;
				}
			);		
			
			return false;			
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
					url: $('#client_form').attr('action') + '/'
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
	
	$('#c_fname, #c_sname, #c_date_of_birth, #c_post_code').change(function () {
		search_clients();																 
	});
	
	$('#add_client').click(function () {
		
		// open dialog
		$('#client_dialog').dialog({
			width: 400,
			resizable: false,
			modal: true,
			zIndex: 1
		});
		
		return false;
		
	});
	
	// each set attendance select element
	$('select.set_attendance').change(function () {
		// get select element
		var $select = $(this);
		
		// parent td 
		var $td = $select.parent('td');
		
		// get href
		var $url = $select.val();
		
		// if href is not false
		if($url != '')
		{
			$.ajax({
				url: $url,
				success: function(data){
					// hide select element
					$select.hide(0, function (){
						// append attendance div
						$td.append(data);
						
						$('div.breadcrumbs').action('Attendance set');
					});
				}
			});
		}
	});
	
	$('#register tr.row td').click(function (event) {
				
		// get current target
		var $this = event.target;
		
		// if event bubbling is on attended div
		if($($this).hasClass('attended'))
		{
			$(this).children('div, small').remove();
			$(this).children('select').show();
		}
	});
	
	$('#register tr.row td').each(function () {
		
		// if td has attended child
		if($(this).children('.attended').length)
		{
			// hide select element child
			$(this).children('select').hide();
		}
	});	
	
	$('#register tr.row td').hover(
		function () {
			// if td has attended div child
			if($(this).children('div.attended').length)
			{
				$(this).css({'cursor' : 'pointer'});
				$(this).append(' <small class="attended">change</small>');
			}
		},
		function () {
			$(this).css({'cursor' : 'default'});
			$(this).children('small').remove();
		}
	);	
	
	
	$('#suggest_a_register').click(function () {
		
		var $url = $(this).attr('href');

		var $div = $('<div id="suggested_register" title="Suggested register" style="display:none"></div>').appendTo('body');
		
		$div.load($url, function () {
			
			$('#use_register').click(function () {
				// redirect to use register action		
				window.location = $url + '?use_register=1';
				
				return false;
			});
			
			// open dialog
			$div.dialog({
				width: 450,
				resizable: false,
				modal: true,
				zIndex: 1,
				close: function () {
					$div.remove();
				}
			});
		});
		
		return false;
		
	});
});