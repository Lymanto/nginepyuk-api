<?php
declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/Database.php';
include_once '../class/Booking.php';

$database = new Database();
$db = $database->getConnection();
 
$booking = new Booking($db);
 
$data = json_decode(file_get_contents("php://input"));
$error = array();


if(empty($data->fullName) ||
empty($data->phoneNumber) ||
empty($data->email) ||
empty($data->visitorName)){
        if(empty($data->fullName)){
        $error['fullName'] = "Full Name belum terisi";
        } 
        if(empty($data->phoneNumber)){
        $error['phoneNumber'] = "Phone Number belum terisi";
        } 
        if(empty($data->email)){
        $error['email'] = "Email belum terisi";
        } 
        if(empty($data->visitorName)){
        $error['visitorName'] = "Visitor Name belum terisi";
        } 
    http_response_code(400);    
    echo json_encode(array("message" => "Unable to create item. Data is incomplete.","error"=>$error));
}else{
    if(!empty($data->fullName) &&
    !empty($data->phoneNumber) &&
    !empty($data->email) &&
    !empty($data->visitorName)){    
        $booking->fullName = $data->fullName;
        $booking->phoneNumber = $data->phoneNumber;
        $booking->email = $data->email;
        $booking->visitorName = $data->visitorName;
        if($booking->booking_detail()){                
            http_response_code(201);         
            echo json_encode(array("message" => "booking detail berhasil ditambahkan."));
        } else{         
            http_response_code(503);        
            echo json_encode(array("message" => "Unable to create item."));
        }
    }
}

?>