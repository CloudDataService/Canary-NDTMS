(function($, window, klass) {

	window.ec.substance = window.ec.pie_chart.extend({

		options: {
			height: 300,
			pieSliceText: "value",
			chartArea: { height: "80%", width: "80%" },
			backgroundColor: "transparent",
			legend: { position: "right", alignment: "center" },
		},

		initialize: function(params) {
			this.supr(params);
			this.options.title = this.title;
		},


		build_data: function() {

			var idx, d, row, label, value, total = 0;

			d = [["Substance", "Journeys"]];

			for (idx in this.raw_data) {
				row = this.raw_data[idx];
				value = row.journeys;
				label = row.substance + " (" + value + ")";
				d.push([label, value]);

				total += parseInt(value, 10);
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
