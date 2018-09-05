/*function myMap() {
  var mapCanvas = document.getElementById("map");
  var mapOptions = {
    center: new google.maps.LatLng(-23.579520, -47.524148), zoom: 10
  };
  var map = new google.maps.Map(mapCanvas, mapOptions);
}


src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBu-916DdpKAjTmJNIgngS6HL_kDIKU0aU&callback=myMap"*/

 <script>
                       function calcRouteArtessa() {
                            var start = document.getElementById("startAddressArtessa").value;
                            var end = "14111 74th Court NE, Kirkland, WA 98034";
                           // var end = document.getElementById("endAddressArtessa").value;

                            var request = {
                                origin: start,
                                destination: end,
                                travelMode: google.maps.DirectionsTravelMode.DRIVING
                            };

                            directionsService.route (request, function (result, status) {
                                if (status == google.maps.DirectionsStatus.OK) {
                                      directionsDisplay.setDirections(result);
                                } else {
                                alert ("Directions were not successful because the starting location was " + status);
                                }        // end IF
                            });       // fnd Function

                      }       // end calcRoute


           </script>


                  <script>
                      $(document).ready(function(){
                          $('#directionsButtonArtessa').click(function(){
                                calcRouteArtessa();
                          });
                      });
                    </script>