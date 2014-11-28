window.ec = window.ec || {};		// namespace for Escape Charts

(function($, window, klass) {

	/**
	 * Base escape chart class that others extend.
	 */
	window.ec.chart = klass({

		// Name of report
		name: null,

		// Title of report
		title: null,

		// element ID that the chart will be contained in (typically same as name, but can be different)
		el: null,

		// jQuery handle to element
		$el: null,

		// google chart object
		chart: null,

		// google datatable/view (what the chart renders with)
		data: [],

		// raw json or array of raw data (query result)
		raw_data: [],

		// report configuration. can be empty.
		config: null,

		// google chart options
		options: {},


		/**
		 * Initialise the class with some config options
		 *
		 * params can be:
		 * - [string] name		Name of chart
		 * - [string] el		Element ID of container for chart (if it needs to differ from "name")
		 * - [object] config		Configuration param object (can be empty)
		 * - [array|object] data		Source data
		 */
		initialize: function(params) {

			// Capture and store params locally
			this.name = params.name || null;
			this.title = params.title || null;
			this.el = params.el || params.name || null;
			this.$el = $("#" + this.el) || null;
			this.raw_data = params.data;

			// Prepare the data
			this.build_data();
		},


		/**
		 * Parse the raw data and do any processing required.
		 *
		 * By default, just pass it to a google datatable.
		 * This isn't usually sufficient, so each chart should override this function and do its own thing.
		 */
		build_data: function() {
			console.log("ec.chart -> build_data()");
			this.data = google.visualization.arrayToDataTable(this.raw_data);
		},


		/**
		 * Render the chart - combining the data and options to produce something visually beautiful using google charts.
		 *
		 * This could probably be used as-is by charts.
		 * But usually overwritten to allow a dataview to be customised (to provide "annotations" in google-speak)
		 */
		render: function() {
			console.log("ec.chart -> render()");
			this.chart.draw(this.data, this.options);
		},


		/**
		 * Update the total value for this chart in the header.
		 */
		set_total: function(value) {
			$("span." + this.el).text(" (Total: " + value + ")");
		}

	});




	/**
	 * Standard column chart (vertical)
	 */
	window.ec.column_chart = window.ec.chart.extend({

		// Default chart options
		options: {
			height: 350,
			chartArea: { width: "80%" },
			backgroundColor: "transparent",
			legend: { position: "none" }
		},

		initialize: function(params) {
			console.log("Initialising new ec.column_chart");
			this.supr(params);
			this.chart = new google.visualization.ColumnChart(document.getElementById(this.el));
		},

		build_data: function() {
		},

		render: function() {
		}

	});




	/**
	 * Standard bar chart (horizontal)
	 */
	window.ec.bar_chart = window.ec.chart.extend({

		// Default chart options
		options: {
			height: 450,
			chartArea: { height: "80%" },
			backgroundColor: "transparent",
			legend: { position: "none" }
		},

		initialize: function(params) {
			console.log("Initialising new ec.bar_chart");
			this.supr(params);
			this.chart = new google.visualization.BarChart(document.getElementById(this.el));
		},

	});




	/**
	 * Standard pie chart. Mmmmpie.
	 */
	window.ec.pie_chart = window.ec.chart.extend({

		// Default chart options
		options: {
			height: 450,
			pieSliceText: "value",
			chartArea: { height: "80%" },
			backgroundColor: "transparent",
			legend: { position: "right", alignment: "center" }
		},

		initialize: function(params) {
			console.log("Initialising new ec.pie_chart");
			this.supr(params);
			this.chart = new google.visualization.PieChart(document.getElementById(this.el));
		},

	});


})(jQuery, window, klass);
