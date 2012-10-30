//small map used in /new/ and /manage/ 
var geocoder = null;
var map = null;
var marker = null;

function load() {//loading the map
  if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("map"));
		map.addControl(new GSmallMapControl());
		map.setMapType(G_NORMAL_MAP);
		
		
		geocoder = new GClientGeocoder();
		geocoder.getLatLng(init_street,function(point) {
			if (point){
				map.setCenter(point, 13);//set center point in map
				//draggable marker
				marker = new GMarker(point, {icon:G_DEFAULT_ICON, draggable: true}); 
				map.addOverlay(marker);
				marker.enableDragging();
				//drag event returns address where was dragged
				GEvent.addListener(marker, "drag", function(){
					geocoder.getLocations(marker.getPoint(), showPointAddress);});
			}
		});
			
		
  }
}
load();