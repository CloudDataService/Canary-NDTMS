(function($, window) {

	var live_charts = {},
		init_charts,
		report_class;


	init_charts = function() {

		for (report in window.report_data) {

			var report_class = report_data[report].config.js_class || report;

			console.log("** Chart " + report + " using class " + report_class);

			if (window.ec[report_class] === undefined) {

				// Error - could not create because the class was not found
				console.log("Could not load class " + report_class);
				$("#" + report).html("<p class='report_error'>Error: No javascript class found " + report_class + ".");

			} else {

				// Create a new instance of the report class
				live_charts[report] = new window.ec[report_class]({
					name: report,
					title: report_data[report].report.title,
					config: report_data[report].config,
					data: report_data[report].result.data
				});

				live_charts[report].render();
			}

		}

	}

	// Load the Visualization API. When loaded, call init_charts().
	// We have to call init_charts AFTER google has loaded, because initialising the charts builds the datatables and creates google objects.
	google.load('visualization', '1.0', {'packages':['corechart'], "callback": init_charts});

})(jQuery, window);