(function($, window, klass) {

	window.ec.event_types = window.ec.bar_chart.extend({

		cols: [],

		initialize: function(params) {
			this.supr(params);

			this.options.height = 800;
			this.options.width = "100%";
			this.options.chartArea.width = "60%";
			this.options.chartArea.height = "90%";
			this.options.isStacked = true;
			this.options.legend = {
				position: "right", alignment: "top", maxLines: 3
			};
		},

		build_data: function() {

			var idx,
				idy,
				d = [],
				row,
				tmp,
				col = 1;

			// Header row with all types
			row = ["Type"];

			// Set up cols. The first item should refer to the Month, no difference here.
			this.cols = [0];

			// Iterate through the types

			for (idx in this.raw_data.types) {

				// Add the status to the first row (header)
				row.push(this.raw_data.types[idx]);

				// Add the *actual* value to the cols array
				this.cols.push(col);

				col++;
			}

			d.push(row);

			// All the data rows

			for (idx in this.raw_data.data) {
				tmp = this.raw_data.data[idx];
				row = [idx];

				// Push values for all the types
				for (idy in tmp) {
					row.push(tmp[idy]);
				}

				d.push(row);
			}

			this.data = google.visualization.arrayToDataTable(d);
		},

		render: function() {

			var view = new google.visualization.DataView(this.data);
			view.setColumns(this.cols);
			this.chart.draw(view, this.options);
		}

	});

})(jQuery, window, klass);