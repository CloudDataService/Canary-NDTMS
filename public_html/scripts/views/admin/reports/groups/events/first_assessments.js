(function($, window, klass) {

	window.ec.first_assessments = window.ec.column_chart.extend({

		build_data: function() {

			var idx, d, row;

			d = [["Month", "First Assessments"]];

			for (idx in this.raw_data) {
				row = this.raw_data[idx];
				d.push([row.month, row.first_assessments]);
			}

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