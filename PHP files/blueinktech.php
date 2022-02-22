<!-- 
    This code was designed by Daniel Grass
    for Blue Ink Tech.

    Purpose of this code is to track the miles
    a semi truck completed per state based off
    of Lat/Lon points.
 -->
<?php

//Yidas SDK import
require __DIR__ . '/vendor/autoload.php';
use yidas\googleMaps\Client;

//Database variables
$serverName = "localhost";
$userName = "root";
$password = "root";
$dbName = "blueinktech";
$dbName2 = "geoname";

//create connection
$con = mysqli_connect($serverName, $userName, $password, $dbName);
if(mysqli_connect_errno()){
    echo 'failed to connect';
    exit();
}
echo "Connected succuess." . "<br><br>";

//Querying Lat/Lon Points from Database
$sql = "SELECT lat, lon FROM `track_points` GROUP BY lat, lon HAVING COUNT(*) >= 1";
$result = $con->query($sql);

class Geocode{
//This function processes the Lat/Lon from SQL doc
  public function getState($lat2, $lng2){
    
    //Homebase Lat/Lon
    $lat1 = 38.413551;
    $lon1 = -82.57708;
    $lat2 = $lat2;
    $lon2 = $lng2;
    $hM = 25;
    
    //Variables to pass to API
    $thislat = $lat2;
    $thislng = $lng2;
  
    //Calculating distance between two lon/lat points
    //Used to calculate the total distance a truck has traveled
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    
    /*
      Code Below is used to fetch a state name based off of Lat/Lon points.
      SDK Yidas & Google Developer Reverse Geocode API -- MAX 10,000 calls per day.

      Code silenced after SQL doc processed.

    */
    
    //API call -- Reverse Geocode 
    //$gmaps = new \yidas\googleMaps\Client(['key'=>'AIzaSyCr8DNNe8caOzVQjibKIBHcdl4pPzUM6u4']);
    // Look up an address with reverse geocoding
    //$reverseGeocodeResult = $gmaps->reverseGeocode([$lat2, $lng2]);
    //echo '<pre>'; print_r($reverseGeocodeResult); echo '</pre>';
    //echo "lat: " . $lat2 . " lon: " . $lng2 . "State/County: " . $reverseGeocodeResult[0]['address_components'][4]['long_name'] . " State/County2: " . $reverseGeocodeResult[0]['address_components'][5]['long_name']. " miles: " . $miles ."<br>";
    
    //Used to view array structure to grab specific objects
    // foreach($reverseGeocodeResult as $mark) {
    //   echo $mark['formatted_address']."<br>"; 
    //}

    //Insert Lat/Lon into new database table
    //This code is muted after all new info has been stored to databased
    //dBInsert($lat2, $lng2, $state, $miles);

  }
}

class ReverseGeocode extends Geocode
{
  //insert info into database, query results, output a clean format of State/Miles per state.
  public function dBInsert($thisLat,  $thisLng, $thisState, $thisMiles){

    $insert = "INSERT INTO `mileage_tracker`(lat, lon, state_name, miles) VALUES ('$thisLat', '$thisLng', '$thisState', '$thisMiles')";

    if ($con->query($insert) === TRUE) {
     echo "New record created successfully";
    } else {
      echo "Error: " . $insert . "<br>" . $con->error;
    }

  }
}
//Fetching SQL Data
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
      
      $lat = $row["lat"];
      $lng = $row["lon"];
      //Sending SQL Doc data to function
      $instance = new Geocode ();
      $instance->getState($lat, $lng);
    }
  } else {
    echo "0 results";
  }
  //Querying Lat/Lon Points from Database
  $sql2 = "SELECT * FROM `mileage_tracker_update` WHERE state_name='West Virginia' ORDER BY miles";
  $result2 = $con->query($sql2);
  $sum;
  $count = 1;
  if (mysqli_num_rows($result2) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result2)) {
      $count++;
      $latMT = $row["lat"];
      $lngMT = $row["lon"];
      $currentState = $row["state_name"];
      $mileage = $row["miles"];
      $sum += $mileage;

      $wvLatStart[] = $row["lat"];
      $wvLonStart[] = $row["lon"];

    }
  } else {
    echo "0 results";
  }
  //Output Lat/Lon for WV
  $lon1 = $wvLonStart[0];
  $lon2 = end($wvLonStart);
  $lat1 = $wvLatStart[0];
  $lat2 = end($wvLatStart);

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $wvMiles = $dist * 60 * 1.1515;

  $sql3 = "SELECT * FROM `mileage_tracker_update` WHERE state_name='North Carolina' ORDER BY miles";
  $result3 = $con->query($sql3);
  $sum;
  $count = 1;
  if (mysqli_num_rows($result3) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result3)) {
      $count++;
      $latMT = $row["lat"];
      $lngMT = $row["lon"];
      $currentState = $row["state_name"];
      $mileage = $row["miles"];
      $sum += $mileage;

      $ncLatStart[] = $row["lat"];
      $ncLonStart[] = $row["lon"];

    }
  } else {
    echo "0 results";
  }
  //Output Lat/Lon for NC
  $lon1 = $ncLonStart[0];
  $lon2 = end($ncLonStart);
  $lat1 = $ncLatStart[0];
  $lat2 = end($ncLatStart);

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $ncMiles = $dist * 60 * 1.1515;

  $sql4 = "SELECT * FROM `mileage_tracker_update` WHERE state_name='Virginia' ORDER BY miles";
  $result4 = $con->query($sql4);
  $sum;
  $count = 1;
  if (mysqli_num_rows($result4) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result4)) {
      $count++;
      $latMT = $row["lat"];
      $lngMT = $row["lon"];
      $currentState = $row["state_name"];
      $mileage = $row["miles"];
      $sum += $mileage;

      $vaLatStart[] = $row["lat"];
      $vaLonStart[] = $row["lon"];

    }
  } else {
    echo "0 results";
  }
  //Output Lat/Lon for VA
  $lon1 = $vaLonStart[0];
  $lon2 = end($vaLonStart);
  $lat1 = $vaLatStart[0];
  $lat2 = end($vaLatStart);

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $vaMiles = $dist * 60 * 1.1515;

  $sql5 = "SELECT * FROM `mileage_tracker_update` WHERE state_name='South Carolina' ORDER BY miles";
  $result5 = $con->query($sql5);
  $sum;
  $count = 1;
  if (mysqli_num_rows($result5) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result5)) {
      $count++;
      $latMT = $row["lat"];
      $lngMT = $row["lon"];
      $currentState = $row["state_name"];
      $mileage = $row["miles"];
      $sum += $mileage;

      $scLatStart[] = $row["lat"];
      $scLonStart[] = $row["lon"];

    }
  } else {
    echo "0 results";
  }
  //Output Lat/Lon for SC
  $lon1 = $scLonStart[0];
  $lon2 = end($scLonStart);
  $lat1 = $scLatStart[0];
  $lat2 = end($scLatStart);

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $scMiles = $dist * 60 * 1.1515;

  $sql6 = "SELECT * FROM `mileage_tracker_update` WHERE state_name='Ohio' ORDER BY miles";
  $result6 = $con->query($sql6);
  $sum;
  $count = 1;
  if (mysqli_num_rows($result6) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result6)) {
      $count++;
      $latMT = $row["lat"];
      $lngMT = $row["lon"];
      $currentState = $row["state_name"];
      $mileage = $row["miles"];
      $sum += $mileage;

      $ohLatStart[] = $row["lat"];
      $ohLonStart[] = $row["lon"];

    }
  } else {
    echo "0 results";
  }
  //Output Lat/Lon for OH
  $lon1 = $ohLonStart[0];
  $lon2 = end($ohLonStart);
  $lat1 = $ohLatStart[0];
  $lat2 = end($ohLatStart);

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $ohMiles = $dist * 60 * 1.1515;

  $sql7 = "SELECT * FROM `mileage_tracker_update` WHERE state_name='Kentucky' ORDER BY miles";
  $result7 = $con->query($sql7);
  $sum;
  $count = 1;
  if (mysqli_num_rows($result7) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result7)) {
      $count++;
      $latMT = $row["lat"];
      $lngMT = $row["lon"];
      $currentState = $row["state_name"];
      $mileage = $row["miles"];
      $sum += $mileage;

      $kyLatStart[] = $row["lat"];
      $kyLonStart[] = $row["lon"];

    }
  } else {
    echo "0 results";
  }
  //Output Lat/Lon for MD
  $lon1 = $kyLonStart[0];
  $lon2 = end($kyLonStart);
  $lat1 = $kyLatStart[0];
  $lat2 = end($kyLatStart);

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $kyMiles = $dist * 60 * 1.1515;

  $sql8 = "SELECT * FROM `mileage_tracker_update` WHERE state_name='Maryland' ORDER BY miles";
  $result8 = $con->query($sql8);
  $sum;
  $count = 1;
  if (mysqli_num_rows($result8) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result8)) {
      $count++;
      $latMT = $row["lat"];
      $lngMT = $row["lon"];
      $currentState = $row["state_name"];
      $mileage = $row["miles"];
      $sum += $mileage;

      $mdLatStart[] = $row["lat"];
      $mdLonStart[] = $row["lon"];

    }
  } else {
    echo "0 results";
  }
  //Output Lat/Lon for MD
  $lon1 = $mdLonStart[0];
  $lon2 = end($mdLonStart);
  $lat1 = $mdLatStart[0];
  $lat2 = end($mdLatStart);

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $mdMiles = $dist * 60 * 1.1515;
  
  //Clean Output
  echo "Kentucky: " . $kyMiles . " miles" . "<br>";
  echo "Ohio: " . $ohMiles . " miles" . "<br>";
  echo "West Virginia: " . $wvMiles . " miles" . "<br>";
  echo "Virgina: " . $vaMiles . " miles" . "<br>";
  echo "Maryland: " . $mdMiles . " miles" . "<br>";
  echo "North Carolina: " . $ncMiles . " miles" . "<br>";
  echo "South Carolina: " . $scMiles . " miles" . "<br>";

  //Closing DB connection
  mysqli_close($con);

?>