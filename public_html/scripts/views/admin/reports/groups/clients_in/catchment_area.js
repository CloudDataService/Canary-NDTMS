(function($, window, klass) {

	window.ec.catchment_area = window.ec.pie_chart.extend({


		build_data: function() {

			var idx, d, row, label, value, total = 0;

			d = [["Area", "Journeys"]];

			for (idx in this.raw_data) {
				row = this.raw_data[idx];
				value = row.journeys;
				label = row.area + " (" + value + ")";
				d.push([label, value]);

				total += parseInt(row.journeys, 10);
			}

			this.total = total;

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
