var ewGoogleMaps = [];

function ew_ShowGoogleMapByAddress(id, maptype, address, zoom, title, desc) {
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode( {'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			ew_ShowGoogleMap(id, maptype, results[0].geometry.location, zoom, title, desc);
		} else {
			alert("Geocode was not successful for the following reason: " + status);
		}
	});
}

function ew_ShowGoogleMap(id, maptype, latlng, zoom, title, desc) {
	var typeid = google.maps.MapTypeId.ROADMAP; // default
	switch (maptype.toLowerCase()) {
		case "roadmap": typeid = google.maps.MapTypeId.ROADMAP; break;
		case "satellite": typeid = google.maps.MapTypeId.SATELLITE; break;
		case "hybrid": typeid = google.maps.MapTypeId.HYBRID; break;
		case "terrain": typeid = google.maps.MapTypeId.TERRAIN; break;
	}
	var mapOptions = {
		zoom: parseInt(zoom),
		center: latlng,
		mapTypeId: typeid
	};
	var map = new google.maps.Map(document.getElementById(id), mapOptions);
	if (desc) {
		var infowindow = new google.maps.InfoWindow({
			content: desc
		});
		var marker = new google.maps.Marker({
			position: latlng,
			map: map,
			title: title
		});
		google.maps.event.addListener(marker, "click", function() {
			infowindow.open(map, marker);
		});
	}
}
jQuery(function($) {
	$.each(ewGoogleMaps, function(i, map) {
		var id = map["id"], latitude = map["latitude"], longitude = map["longitude"],
			address = map["address"], maptype = map["type"], zoom = map["zoom"],
			title = map["title"], desc = map["description"];
		if (address && $.trim(address) != "") {
			ew_ShowGoogleMapByAddress(id, maptype, address, zoom, title, desc);
		} else if (latitude && !isNaN(latitude) && longitude && !isNaN(longitude)) {
			var latlng = new google.maps.LatLng(latitude, longitude);
			ew_ShowGoogleMap(id, maptype, latlng, zoom, title, desc);
		} else {
			$("#" + id).hide();
		}
	});
});
