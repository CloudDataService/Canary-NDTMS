// JavaScript Document
$(window).ready(function() {
	
	// get coords
	var coords = $.parseJSON($('#coords').val());
	
	// declare latlng object
	var latlng = new google.maps.LatLng(coords.lat, coords.lng)
	
	// get map div
	var mapDiv = document.getElementById('map');
	
	// set options for map
	var options = {
		center: latlng,
		zoom: 12,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	
	// declare new google map object
	var map = new google.maps.Map(mapDiv, options);

	// crate new marker
	var marker = new google.maps.Marker({
		position: latlng,
		map: map
	});

});