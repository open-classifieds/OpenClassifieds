//big map in /map/ (displayed on item.php link location) 
var geocoder = null;
var map = null;
function load() {//loading the map
	  if (GBrowserIsCompatible()) {
			map = new GMap2(document.getElementById("map"));
			map.enableScrollWheelZoom();
			geocoder = new GClientGeocoder();
			
			if (init_street!=""){
				geocoder.getLatLng(init_street,function(point) {//set center point in map
					if (point){
						map.setCenter(point, zoom);
						map.addOverlay(createMarker(point,init_street));
						map.openInfoWindow(point,init_street);
					}
				});
			}
			
			map.addControl(new GLargeMapControl());
			map.setMapType(G_NORMAL_MAP);			
	  }
	}