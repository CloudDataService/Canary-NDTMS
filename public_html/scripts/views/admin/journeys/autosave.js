/**
 * NERAF Recovery autosave module
 *
 * Submits forms via AJAX with timed buffer to prevent multiple triggers 
 * and submits queuing up.
 *
 * By default, the "change" event of any input, textarea or select within 
 * the given form will trigger the autosave to run.
 *
 * Usage:
 *   autosave.init("#form_id");
 *
 * The form will serialize() the form and send it via POST to the form action URL.
 * After the 1st request, method=autosave will be appended to the data.
 * The Log model is instructed to not save logs when a) this is present and b)
 * the HTTP request is XHR.
 *
 * @author CR
 */

var autosave = (function($){
	
	var $form = null;
	var timer = null;
	var $notification = null;
	var autosave_count = 0;
	
	// Update the text notification on the page
	set_notification = function(text, status) {
		$notification.text(text);
		if (status == "ok") {
			$notification.css("color", "darkgreen");
		} else if (status == "err") {
			$notification.css("color", "darkred");
		} else {
			$notification.css("color", "black");
		}
	}
	
	// Serialize the form data and make AJAX request
	save_form = function() {
		
		if ( ! $form.valid()) {
			return;
		}
		
		// Gather form data
		var data = $form.serialize();
		
		// Append method=autosave to prevent log entries from being written if not first time
		if (autosave_count > 0) {
			data += "&method=autosave";
		}
		
		// Make AJAX request to submit form
		$.ajax({
			type: "POST",
			async: true,
			url: $form.attr("action"),
			data: data,
			success: function() {
				set_notification("Auto-saved.", "ok");
				autosave_count++;
				window.onbeforeunload = null;
			},
			error: function() {
				set_notification("Unable to auto-save details.", "err");
			}
		});
	}
	
	// Function that is called when form is changed or should have the timer triggered
	autosave_triggered = function(e) {
		//console.log(e.currentTarget.className );
		if((e.currentTarget.className == 'multi_add_choice') || 
			(e.currentTarget.className == 'multi_add_choice valid'
			))
		{
			//console.log('ignore');
		}
		else
		{
			//console.log(e);
			clearTimeout(timer);
			if ($form.valid()) {
				// If form validates successfully then set a timer to save the form
				window.onbeforeunload = function () { return "Your changes have not been saved." };
				set_notification("Not saved yet...", "err");
				timer = setTimeout(save_form, 500);
			} else {
				// Form not valid - don't submit
				set_notification("Form is not valid - check details.", "err");
			}
		}
	}
	
	// Initialise autosave
	init = function(form_id){
		
		// Get handle to form
		$form = $(form_id);
		
		// Add notification bar
		$('<div id="autosave_notification"></div>')
			.insertBefore("div.breadcrumbs h1")
			.css({ "position": "absolute", "font-size": "11px"});
		$notification = $("div#autosave_notification");
		
		// Set initial notification status that the feature is enabled.
		set_notification("Auto-save enabled.");
		
		// Attach change event to form elements
		$form.on("change", "input,select,textarea", autosave_triggered);
	}
	
	return {
		init: init
	};
	
})(jQuery);