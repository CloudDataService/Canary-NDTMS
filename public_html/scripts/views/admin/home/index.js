// JavaScript Document
$(window).ready(function() {

	$('.quick_links p').click(function () {
		window.location = $(this).children('a').attr('href');
	});

	$('.quick_links p').hover(function () {
		$(this).css({'cursor' : 'pointer'});
	});

	// get map div
	var mapDiv = document.getElementById('map');

	// set options for map
	var options = {
		center: new google.maps.LatLng(51, 0),
		zoom: 6,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	// declare new google map object
	var map = new google.maps.Map(mapDiv, options);

	// declare new bounds object
	var bounds = new google.maps.LatLngBounds();

	// declare array to add markers to
	var markers = [];

	// ajax call to get json object of coords
	$.getJSON("/admin/home/getJson", function(coords){

		// loop over each set of coords
		for(i = 0; i < coords.length; i++)
		{
			// declare latlng object
			var latlng = new google.maps.LatLng(coords[i].lat, coords[i].lng);

			// crate new marker
			var marker = new google.maps.Marker({
				position: latlng
			});

			// push marker to markers array
			markers.push(marker);

			// extend the boundary object to include current coords
			bounds.extend(latlng);
		}

		// fit bounds to our map
		map.fitBounds(bounds);

		var markerclusterer = new MarkerClusterer(map, markers);

    });

});
