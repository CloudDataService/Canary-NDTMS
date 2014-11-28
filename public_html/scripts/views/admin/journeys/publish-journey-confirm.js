$('.publish-journey-confirm').click(function (event) {
	event.preventDefault();

	var $url = $(this).attr('href');
	var $modalUrl = "/admin/journeys/modal/publish-journey-confirm";

	$("#publish-journey-modal").dialog('open');

	var $div = $('<div id="publish-journey-modal" title="Publish Journey" style="display:none"></div>').appendTo('body');

	$div.load($modalUrl, function() {
		// open dialog
		$div.dialog({
			width: 450,
			resizable: false,
			modal: true,
			zIndex: 1,
			close: function () {
				$div.remove();
			}
		});

		$div.on('click', '#yes', function(event) {
			event.preventDefault();
			window.location = $url;
		});

		$div.on('click', '#no', function(event) {
			event.preventDefault();
			$div.remove();
		});
	});
});
