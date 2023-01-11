<?php
declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/Database.php';
include_once '../class/Category.php';

require "../vendor/autoload.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
$database = new Database();
$db = $database->getConnection();
 
$category = new Category($db);
 
$data = json_decode(file_get_contents("php://input"));
$error = array();

// $jwt = substr(getallheaders()["Authorization"], 7);
if (! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)){ 
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
    if($token["role"] == "hotel" && $token['hotelId']){
        if(empty($_FILES['file']) || empty($_POST['category'])){
            if(empty($_POST['category'])){
                $error['category'] = "Category belum terisi";
            }
            if(empty($_FILES['file'])){
                $error['file'] = "Image belum terisi";
            }
            http_response_code(400);    
            echo json_encode(array("message" => "Unable to create item. Data is incomplete.","error"=>$error));
        }else{
            if(!empty($_FILES['file']) && !empty($_POST['category'])){    
                $category->file = $_FILES['file'];
                $category->hotelId = $token['hotelId'];
                $category->category = $_POST['category'];
                if($category->create()){                         
                    http_response_code(201);         
                    echo json_encode(array("message" => "Category room berhasil ditambahkan."));
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