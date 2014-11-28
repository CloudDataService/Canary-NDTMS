// Load the Visualization API and the columnchart package.
google.load('visualization', '1.0', {'packages':['corechart']});

var csop_chart = (function($, google) {

	var data = {};


	/**
	 * Build the summary data
	 */
	 /*
	var build_datatable = function() {

		// Build data
		var data = [
			['Time', 'Income', 'Outgoing']
		];

		$(".tab[data-age]").each(function() {
			var age = parseInt($(this).data("age"), 10);
			var total_income = parseInt($(this).find("[name='category[Income]']").val(), 10);
			var total_expense = parseInt($(this).find("[name='category[Expense]']").val(), 10);
			age = $(this).data("title");

			data.push([age, total_income, total_expense]);
		});

		data.push([
			"Future",	//parseInt($('[name=end_age]').val(), 10),
			parseInt($('.tab[data-id="full"] [name="category[Income]"]').val(), 10),
			parseInt($('.tab[data-id="full"] [name="category[Expense]"]').val(), 10)
		]);

		// Convert the data and return
		return {
			data: google.visualization.arrayToDataTable(data),
			series: [
				{ color: '#d95c00' },
				{ color: '#437cb7' }
			]
		};
	};
	*/

	var draw_chart = function(id, dt) {

		// Set chart options
		var options = {
			backgroundColor: "transparent",
			width: "100%",
			height: 300,
			chartArea: { width: "90%" },
			pointSize: 0,
			hAxis: {
				textStyle: {
					color: "#000",
					fontName: "Verdana",
					fontSize: 12,
					bold: true
				}
			},
			vAxis: {
				ticks: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
				minValue: 0,
				maxValue: 10,
				textStyle: {
					color: "#000",
					fontName: "Verdana",
					fontSize: 12,
					bold: true
				}
			},
			legend: {
				position: "bottom",
				alignment: "center"
			}
		};

		// Instantiate and draw our chart, passing in some options.
		var chart = new google.visualization.LineChart(document.getElementById(id));
		chart.draw(dt, options);
	};



	var init = function(_data) {

		var k,
			$el,
			dt;

		data = _data;

		// Iterate through the different categories of outcomes
		for (k in data) {

			// Container element where the chart will be drawn
			$el = $(".js-csop-chart[data-title='" + k + "']");

			// DataTable for google to use
			dt = google.visualization.arrayToDataTable(data[k]);

			// Draw the chart with the parsed data into a datatable
			draw_chart($el.attr("id"), dt);
		}
	};


	return {
		init: init
	}


})(jQuery, google);

$(document).ready(function() {
	csop_chart.init(chart_data);
});