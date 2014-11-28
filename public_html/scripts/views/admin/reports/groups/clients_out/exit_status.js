(function($, window, klass) {

	window.ec.exit_status = window.ec.column_chart.extend({

		cols: [],

		initialize: function(params) {
			this.supr(params);

			this.options.isStacked = true;
			this.options.legend = {
				position: "bottom", alignment: "center"
			};
		},

		build_data: function() {

			var idx,
				idy,
				d = [],
				row,
				tmp,
				col = 1;

			// Header row with all statuses
			row = ["Status"];

			// Set up cols. The first item should refer to the Month, no difference here.
			this.cols = [0];

			// Iterate through the statuses

			for (idx in this.raw_data.statuses) {

				// Add the status to the first row (header)
				row.push(this.raw_data.statuses[idx]);

				// Add the *actual* value to the cols array
				this.cols.push(col);

				// Now the annotation for this col
				this.cols.push({
					role: "annotation",
					type: "string",
					calc: "stringify",
					sourceColumn: col
				});

				col++;
			}

			d.push(row);

			// All the data rows

			for (idx in this.raw_data.data) {
				tmp = this.raw_data.data[idx];
				row = [idx];

				// Push values for all the statuses
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