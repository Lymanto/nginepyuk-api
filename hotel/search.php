<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/Database.php';
include_once '../class/Hotel.php';

$database = new Database();
$db = $database->getConnection();
 
$hotel = new Hotel($db);

$hotel->city = (isset($_GET['kotaTujuan']) && $_GET['kotaTujuan']) ? $_GET['kotaTujuan'] : '0';
$hotel->guest = (isset($_GET['guest']) && $_GET['guest']) ? $_GET['guest'] : '0';
$result = $hotel->search();

if($result->num_rows > 0){    
    $hotelRecords=array();
    $hotelRecords["room"]=array(); 
	while ($hotel = $result->fetch_assoc()) { 	
        extract($hotel); 
        $hotelDetails=array(
            "id" => $id,
            "title" => $title,
            "address" => $address,
			"star" => $star,
            "city" => $city,            
			"country" => $country,
            "price" => $price,
            "imageUrl" => $imageUrl
        ); 
       array_push($hotelRecords["room"], $hotelDetails);
    }    
    http_response_code(200);     
    echo json_encode($hotelRecords);
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No hotel found.")
    );
} 