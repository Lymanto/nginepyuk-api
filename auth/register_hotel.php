<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once '../config/Database.php';
include_once '../class/Users.php';
 
$database = new Database();
$db = $database->getConnection();
 
$users = new Users($db);
 
$data = json_decode(file_get_contents("php://input"));
$error = array();
if(empty($data->fullName) || empty($data->email) ||
empty($data->password)){    
    if(empty($data->fullName)){
        $error['fullName'] = "Nama anda belum terisi";
    }
    if(empty($data->email)){
        $error['email'] = "Email anda belum terisi";
    }
    if(empty($data->password)){
        $error['password'] = "Password anda belum terisi";
    }
    if(empty($data->title)){
        $error['title'] = "Nama hotel anda belum terisi";
    }
    http_response_code(400);    
    echo json_encode(array("message" => "Unable to create item. Data is incomplete.","error"=>$error));
}else{
    if(!empty($data->fullName) && !empty($data->email) &&
    !empty($data->password)){    
    
        $users->fullName = $data->fullName;
        $users->email = $data->email;
        $users->password = $data->password;
        $users->title = $data->title;
        
        if($users->register_hotel()){         
            http_response_code(201);         
            echo json_encode(array("message" => "Register berhasil."));
        } else{         
            http_response_code(503);        
            echo json_encode(array("message" => "Register gagal."));
        }
    }
}
?>