(function($, window, klass) {

	window.ec.month_referrals = window.ec.column_chart.extend({

		build_data: function() {

			var idx, d, row, total = 0;

			d = [["Month", "Journeys"]];

			for (idx in this.raw_data) {
				row = this.raw_data[idx];
				d.push([row.month, row.journeys]);

				total += parseInt(row.journeys, 10);
			}

			this.set_total(total);

			this.data = google.visualization.arrayToDataTable(d);
		},

		render: function() {

			var view = new google.visualization.DataView(this.data);
			view.setColumns([0, 1, { calc: "stringify",
				sourceColumn: 1,
				type: "string",
				role: "annotation"
			}]);

			this.chart.draw(view, this.options);
		}

	});

})(jQuery, window, klass);
