<?php

/*
Template Name: Maps API
*/
require 'config.php';
// Create connection
$conn = mysqli_connect($servername,$dbuser,$dbpass,$dbname);

// Check connection
if (!$conn) {
    die("Connection faiiled: " . mysqli_connect_error());
}
/*
if (isset($_POST['sprt']) && $_POST['sprt'] != 'top') {
 $sql = "SELECT FacilityName, Longitude, Latitude from rec_facilities where SportsPlayed = '".$_POST['sprt']."'";
} else {
 $sql = "SELECT FacilityName, Longitude, Latitude from rec_facilities";
}

*/

if (isset($_POST['subrb']) && $_POST['subrb'] != 'stop' && isset($_POST['sprt']) && $_POST['sprt'] != 'top') {
  $sql = "SELECT FacilityName, Longitude, Latitude from rec_facilities where SuburbTown = '".$_POST['subrb']."' AND SportsPlayed = '".$_POST['sprt']."'";
} else if (isset($_POST['subrb']) && $_POST['subrb'] != 'stop') {
  $sql = "SELECT FacilityName, Longitude, Latitude from rec_facilities where SuburbTown = '".$_POST['subrb']."'";
} else if (isset($_POST['sprt']) && $_POST['sprt'] != 'top') {
  $sql = "SELECT FacilityName, Longitude, Latitude from rec_facilities where SportsPlayed = '".$_POST['sprt']."'";
} else {
  $sql = "SELECT FacilityName, Longitude, Latitude from rec_facilities";
}
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        $facilitynames[] = $row['FacilityName'];
        $longitudes[] = $row['Longitude'];
        $latitudes[] = $row['Latitude'];
    }
}

$sql1 = "SELECT SportsPlayed from sports_played";
$sports = mysqli_query($conn,$sql1);
  
$sql2 = "SELECT SuburbTown from suburb";
$suburb = mysqli_query($conn,$sql2);

if (isset($_POST['subrb'])&& $_POST['subrb'] != 'stop'){
  $sql3 = "SELECT Postcode from suburb where SuburbTown = '".$_POST['subrb']."'";
  $postcode = mysqli_query($conn,$sql3);
  if (mysqli_num_rows($postcode) > 0) {
    while($row4 = mysqli_fetch_assoc($postcode)) {
        $post = $row4['Postcode'];
  }


// output data of each row
    }

}else { $post = '3000';
}
mysqli_close($conn);
?>

<html>
    <head>

<style type="text/css">
  html { height: 100% }
  body { height: 100%; margin: 0; padding: 0 }
  #map_canvas { height: 100% }
</style>
<script type="text/javascript"
  src=
"http://maps.googleapis.com/maps/api/js?key=<?php echo $apikey; ?>&sensor=false">
</script>
<script type="text/javascript">
var infos = [];

var locations =
[
<?php
$length = count($facilitynames);
for ($i = 0; $i < $length; $i++) {
echo "[";
echo json_encode($facilitynames[$i]);
echo ", ";
echo $latitudes[$i];
echo ", ";
echo $longitudes[$i];
echo "],";
}
?>
]

  function initialize() {

    var myOptions = {
      center: new google.maps.LatLng(-37.8136,144.9631),
      zoom: 10,
      mapTypeId: google.maps.MapTypeId.ROADMAP

    };

    var map = new google.maps.Map(document.getElementById("default"),
        myOptions);

    setMarkers(map,locations)
  
}



  function setMarkers(map,locations){

      var marker, i

for (i = 0; i < locations.length; i++)
 {  

 var addr = locations[i][0]
 var lat = locations[i][1]
 var long = locations[i][2]

 latlngset = new google.maps.LatLng(lat,long);
var image = 'http://18.188.137.186/wp-content/themes/twentyseventeen/assets/images/gold.png';

  var marker = new google.maps.Marker({ 
          map: map, title: addr, position: latlngset, icon: image  
        });
        map.setCenter(marker.getPosition())


	var content = "<h3>Treasure Chest<br /></h3>" + "Location: " + addr

  var infowindow = new google.maps.InfoWindow()
        
       
google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){ 
        return function() {
        
        /* close the previous info-window */
       closeInfos();
        
           infowindow.setContent(content);
           infowindow.open(map,marker);
        
        /* keep the handle, in order to close it on next click event */
   infos[0]=infowindow;
        
        };
    })(marker,content,infowindow)); 

  }
  }

function closeInfos(){
 
   if(infos.length > 0){
 
      /* detach the info-window from the marker ... undocumented in the API docs */
      infos[0].set("marker", null);
 
      /* and close it */
      infos[0].close();
 
      /* blank the array */
      infos.length = 0;
   }
}

</script> 
  </head>
 <body onload="initialize()">
  <br />
<center>

<form action="" method="post" name="sports">
  <select name="sprt">
    "<option value='top'>Select an Activity...</option>
      <?php
if (mysqli_num_rows($sports) > 0) {
 while($row = mysqli_fetch_assoc($sports)) {
if (isset($_POST['sprt']) && $_POST['sprt']==$row['SportsPlayed']){
  echo "<option value='".$row['SportsPlayed']."' selected>".$row['SportsPlayed']."</option>";
  }  else {
echo "<option value='".$row['SportsPlayed']."'>".$row['SportsPlayed']."</option>";
}
}
} else {
  echo "<option value='none'>No Data</option>";
}
      ?>
</select>


 <select name="subrb">
    "<option value='stop'>Select a Suburb...</option>
      <?php
if (mysqli_num_rows($suburb) > 0) {
 while($row1 = mysqli_fetch_assoc($suburb)) {
if (isset($_POST['subrb']) && $_POST['subrb']==$row1['SuburbTown']){
  echo "<option value='".$row1['SuburbTown']."' selected>".$row1['SuburbTown']."</option>";
  }  else {
echo "<option value='".$row1['SuburbTown']."'>".$row1['SuburbTown']."</option>";
}
}
} else {
  echo "<option value='none'>No Data</option>";
}
      ?>
</select>

<input type="submit" value="Go">
</form>
  <div id="default" style="width:50%; height:50%"></div>
<br />

<script type="text/javascript" src="https://www.weatherzone.com.au/woys/graphic_current.jsp?postcode=<?php
echo $post;
?>
"></script> <br />
</center>
 </body>
  </html>
