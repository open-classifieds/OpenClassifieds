//common functions gogole maps, load first

//from address returns point
function showAddress(address) {
  if (geocoder) {//+', '+init_street
		geocoder.getLatLng(address,
			function(point) {
					if (!point) {
					  document.getElementById("place").value="not found";
					  //alert(address + " not found");
					} else {
					//	document.getElementById("place").value=point.y.toFixed(4) + "," + point.x.toFixed(4);
					  	map.setCenter(point, 16);
						marker.setPoint(point);
						//marker.openInfoWindowHtml(address);
					}
		  		}
		);
  	}
}

//from a point returns and address!
function showPointAddress(response) {
	  if (!response || response.Status.code != 200) {//not found
	    //alert("Status Code:" + response.Status.code);
		  document.getElementById("place").value="not found";
	  } 
	  else {//found
		  	map.setCenter(marker.getPoint(), 16);
			place = response.Placemark[0];
			document.getElementById("place").value=place.address;
			//document.getElementById("place").value=marker.getPoint().toUrlValue();
	  }
}


// Creates a marker at the given point with the given number icon and text
function createMarker(p,text) {
	var marker = new GMarker(p);
	if (text!=""){
		GEvent.addListener(marker, "click", function() {
			marker.openInfoWindowHtml(text);});
	}
	return marker;
}