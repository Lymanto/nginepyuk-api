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


if(empty($data->paymentMethod) ||
empty($data->virtualAccount) ||
empty($data->noVirtualAccount) ||
empty($data->cardOwner) ||
empty($data->cardNumber) ||
empty($data->cardExpired)){
      if(empty($data->paymentMethod)){
        $error['paymentMethod'] = "payment method belum terisi";
      } 
      if(empty($data->virtualAccount)){
        $error['virtualAccount'] = "Virtual Account belum terisi";
      } 
      if(empty($data->noVirtualAccount)){
        $error['noVirtualAccount'] = "No Virtual Account belum terisi";
      } 
      if(empty($data->cardOwner)){
        $error['cardOwner'] = "Card Owner belum terisi";
      } 
      if(empty($data->cardNumber)){
        $error['cardNumber'] = "Card Number belum terisi";
      } 
      if(empty($data->cardExpired)){
        $error['cardExpired'] = "Card Expired belum terisi";
      } 
    http_response_code(400);    
    echo json_encode(array("message" => "Unable to create item. Data is incomplete.","error"=>$error));
}else{
    if(!empty($data->paymentMethod) &&
    !empty($data->virtualAccount) &&
    !empty($data->noVirtualAccount) &&
    !empty($data->cardOwner) &&
    !empty($data->cardNumber) &&
    !empty($data->cardExpired)){    
        $booking->paymentMethod = $data->paymentMethod;
		$booking->virtualAccount = $data->virtualAccount;
		$booking->noVirtualAccount = $data->noVirtualAccount;
		$booking->cardOwner = $data->cardOwner;
		$booking->cardNumber = $data->cardNumber;
		$booking->cardExpired = $data->cardExpired;
        if($booking->payment()){                
            http_response_code(201);         
            echo json_encode(array("message" => "Payment detail berhasil ditambahkan."));
        } else{         
            http_response_code(503);        
            echo json_encode(array("message" => "Unable to create item."));
        }
    }
}

?>