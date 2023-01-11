<?php
declare(strict_types=1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once('../vendor/autoload.php');
use Firebase\JWT\JWT;
include_once '../config/Database.php';
include_once '../class/Users.php';


$database = new Database();
$db = $database->getConnection();
 
$users = new Users($db);

$data = json_decode(file_get_contents("php://input"));
$error = array();
if(empty($data->email) || empty($data->password)){    
    if(empty($data->email)){
        $error['email'] = "Email anda belum terisi";
    }
    if(empty($data->password)){
        $error['password'] = "Password anda belum terisi";
    }
    http_response_code(400);    
    echo json_encode(array("message" => "Unable to create item. Data is incomplete.","error"=>$error));
}else{
    if(!empty($data->email) && !empty($data->password)){    

        $users->email = $data->email;
        $users->password = $data->password;
        $result = $users->login();
        $user = $users->verify($data->email, $data->password);

        if ($user===false) { 
            http_response_code(503);        
            echo json_encode(array("message" => "Email atau password salah"));
        }else{
            $now = strtotime("now");
            if($user['role'] == "hotel"){
                $jwt= JWT::encode([
                    "iat" => $now, // ISSUED AT - TIME WHEN TOKEN IS GENERATED
                    "nbf" => $now, // NOT BEFORE - WHEN THIS TOKEN IS CONSIDERED VALID
                    "exp" => $now + 36000 , // EXPIRY - 1 HR (3600 SECS) FROM NOW IN THIS EXAMPLE
                    "jti" => base64_encode(random_bytes(16)), // JSON TOKEN ID
                    "iss" => JWT_ISSUER, // ISSUER
                    "aud" => JWT_AUD, // AUDIENCE
                    // WHATEVER USER DATA YOU WANT TO ADD
                    "data" => [
                        "fullName" => $user["fullName"],
                        "email" => $user["email"],
                        "role" => $user["role"],
                        "hotelId" => $user["hotelId"],
                        "userId" => $user['id']
                    ]
                ], JWT_SECRET, 'HS512');
            }else{
                $jwt= JWT::encode([
                    "iat" => $now, // ISSUED AT - TIME WHEN TOKEN IS GENERATED
                    "nbf" => $now, // NOT BEFORE - WHEN THIS TOKEN IS CONSIDERED VALID
                    "exp" => $now + 36000 , // EXPIRY - 1 HR (3600 SECS) FROM NOW IN THIS EXAMPLE
                    "jti" => base64_encode(random_bytes(16)), // JSON TOKEN ID
                    "iss" => JWT_ISSUER, // ISSUER
                    "aud" => JWT_AUD, // AUDIENCE
                    // WHATEVER USER DATA YOU WANT TO ADD
                    "data" => [
                        "fullName" => $user["fullName"],
                        "email" => $user["email"],
                        "role" => $user["role"],
                        "userId" => $user["id"],
                    ]
                ], JWT_SECRET, 'HS512');
            }
            http_response_code(200);     
            echo json_encode(array("message" => "login berhasil.","jwt"=>$jwt));
        }
            
    }
}