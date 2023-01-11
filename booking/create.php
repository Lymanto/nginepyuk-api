<?php
declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/Database.php';
include_once '../class/Booking.php';

require "../vendor/autoload.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
$database = new Database();
$db = $database->getConnection();
 
$booking = new Booking($db);
 
$data = json_decode(file_get_contents("php://input"));
$error = array();
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  // The request is using the POST method
  header("HTTP/1.1 200 OK");
  return;

}
// $jwt = substr(getallheaders()["Authorization"], 7);
if (!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)){ 
    header('HTTP/1.0 400 Bad Request');
    echo 'Token not found in request';
    exit;
}

$jwt = $matches[1];

if(!$jwt){
    // No token was able to be extracted from the authorization header
    header('HTTP/1.0 400 Bad Request');
    exit;
};

try {
    $token = JWT::decode($jwt, new Key(JWT_SECRET, 'HS512'));
    $token = (array) $token;
    $token = (array) $token["data"];
    if($token["role"] == "user"){
        if(empty($data->status) ||
        empty($data->quantity) ||
        empty($data->bookingStartDate) ||
        empty($data->bookingEndDate) ||
        empty($data->roomId) ||
        empty($data->paymentMethod) ||
        empty($data->virtualAccount) ||
        empty($data->noVirtualAccount) ||
        empty($data->cardOwner) ||
        empty($data->cardNumber) ||
        empty($data->cardExpired) ||
        empty($data->fullName) ||
        empty($data->phoneNumber) ||
        empty($data->email) ||
        empty($data->visitorName)){
            
              if(empty($data->status)){
                $error['status'] = "Status belum terisi";
              } 
              if(empty($data->quantity)){
                $error['quantity'] = "quantity belum terisi";
              } 
              if(empty($data->bookingStartDate)){
                $error['bookingStartDate'] = "Check in belum terisi";
              } 
              if(empty($data->bookingEndDate)){
                $error['bookingEndDate'] = "Checkout belum terisi";
              } 
             
              if(empty($data->roomId)){
                $error['roomId'] = "Room belum terisi";
              } 
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
            if(
            !empty($data->status) &&
            !empty($data->quantity) &&
            !empty($data->bookingStartDate) &&
            !empty($data->bookingEndDate) &&
            !empty($data->roomId) &&
            !empty($data->paymentMethod) &&
            !empty($data->virtualAccount) &&
            !empty($data->noVirtualAccount) &&
            !empty($data->cardOwner) &&
            !empty($data->cardNumber) &&
            !empty($data->cardExpired) &&
            !empty($data->fullName) &&
            !empty($data->phoneNumber) &&
            !empty($data->email) &&
            !empty($data->visitorName)){    
             
                $booking->status = $data->status;
                $booking->quantity = $data->quantity;
                $booking->bookingStartDate = $data->bookingStartDate;
                $booking->bookingEndDate = $data->bookingEndDate;
                $booking->userId = $token['userId'];
                $booking->roomId = $data->roomId;

                $booking->paymentMethod = $data->paymentMethod;
                $booking->virtualAccount = $data->virtualAccount;
                $booking->noVirtualAccount = $data->noVirtualAccount;
                $booking->cardOwner = $data->cardOwner;
                $booking->cardNumber = $data->cardNumber;
                $booking->cardExpired = $data->cardExpired;

                $booking->fullName = $data->fullName;
                $booking->phoneNumber = $data->phoneNumber;
                $booking->email = $data->email;
                $booking->visitorName = $data->visitorName;
                if($booking->create()){                         
                    http_response_code(201);         
                    echo json_encode(array("message" => "transaksi berhasil ditambahkan."));
                } else{         
                    http_response_code(503);        
                    echo json_encode(array("message" => "Unable to create item."));
                }
            }
        }
    }else{
        http_response_code(503);        
        echo json_encode(array("message" => "Unable to create item."));
    }
  } catch (Exception $e) {
    // Bagian ini akan jalan jika terdapat error saat JWT diverifikasi atau di-decode
    echo json_encode(array("message" => "JWT gagal."));
    http_response_code(401);
    exit();
}
?>