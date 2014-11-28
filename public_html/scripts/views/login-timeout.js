
function session_timer()
{
	// check to see if user's was active less than 10 minutes ago
	$.get("/persist", { check_persist_session: "1" },
		function(data){
						
			// if the user has not been active in the last 10 minutes
			if(data == 'false')
			{				
				// prompt to log out
				$('body').append('<div id="action"><h2>Your session is about to time out.</h2><p>You have been inactive for 15 minutes. If you would like to continue working please click "Continue working" otherwise you will be logged out in <span id="seconds">11</span> seconds.</p></div>');

				$( "#action" ).dialog({
					open: count_down(),
					width: 500,
					resizable: false,
					modal: true,
					close: function () {
											
						$('div#action').remove();
						
						clearTimeout(count_down_timeout);
			
						$.ajax({
							url: '/persist'			
						});
						
						// start timeout again
						setTimeout("session_timer()", milliseconds);
						
						return false;
					},
					buttons: {						
						"Continue working": function() {
							$(this).dialog( "close" );
						}
					}
				});
			}
			else
			{
				// start timeout again
				setTimeout("session_timer()", milliseconds);
			}
		}
	);
}

function count_down()
{
	var second = parseInt($('span#seconds').text());
	
	if(second > 0)
	{
		$('span#seconds').text(second-1);
		
		count_down_timeout = setTimeout("count_down()", 1000);
	}
	else
	{
		window.location = '/logout?timeout=1';
	}
}



$(document).ready(function() {			
	setTimeout("session_timer()", milliseconds);	
});