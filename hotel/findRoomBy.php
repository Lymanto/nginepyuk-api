<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/Database.php';
include_once '../class/Room.php';

$database = new Database();
$db = $database->getConnection();
 
$room = new Room($db);

$room->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';
$result = $room->findBy();

if($result->num_rows > 0){    
    $roomRecords=array();
    $roomRecords["room"]=array(); 
	while ($room = $result->fetch_assoc()) { 	
        extract($room); 
        $roomDetails=array(
            "roomTitle" => $roomTitle,
            "bedType" => $bedType,
            "price" => $price,
			"discount" => $discount,
            "guest" => $guest,            
			"hotelName" => $hotelName,
			"hotelId" => $hotelId,
			"roomId" => $roomId,
			"imageUrl" => $imageUrl,
        ); 
       array_push($roomRecords["room"], $roomDetails);
    }    
    http_response_code(200);     
    echo json_encode($roomRecords);
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No room found.")
    );
} 