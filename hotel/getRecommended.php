<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/Database.php';
include_once '../class/Hotel.php';

$database = new Database();
$db = $database->getConnection();
 
$hotel = new Hotel($db);


$result = $hotel->recommended();

if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords["hotel"]=array(); 
	while ($hotel = $result->fetch_assoc()) { 	
        extract($hotel); 
        $itemDetails=array(
            "id" => $id,
            "title" => $title,
            "address" => $address,
			"star" => $star,
            "city" => $city,            
			"country" => $country,
            "price" => $price,
            "imageUrl" => $imageUrl			
        ); 
       array_push($itemRecords["hotel"], $itemDetails);
    }    
    http_response_code(200);     
    echo json_encode($itemRecords);
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No item found.")
    );
} 