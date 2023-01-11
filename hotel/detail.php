<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/Database.php';
include_once '../class/Hotel.php';

$database = new Database();
$db = $database->getConnection();
 
$hotel = new Hotel($db);

$hotel->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';
$result1 = $hotel->detailHotel();
$result2 = $hotel->detailHotelImage();
$result3 = $hotel->detailHotelCategoryDetail();
$result4 = $hotel->detailHotelRoom();
$result5 = $hotel->detailHotelCategory();

$detailRecords=array();

if($result1->num_rows > 0){    
    $detailRecords["hotel"]=array(); 
    while ($fetch = $result1->fetch_assoc()) { 	
        extract($fetch); 
        $hotelDetails1=array(
            "id" => $id,
            "title" => $title,
            "address" => $address,
			"star" => $star,
            "city" => $city,            
			"country" => $country,
            "price" => $price
        ); 
       array_push($detailRecords["hotel"], $hotelDetails1);
    }    
}
if($result2->num_rows > 0){    
    $detailRecords["images"]=array(); 
	while ($fetch = $result2->fetch_assoc()) { 	
        extract($fetch); 
        $hotelDetails2=array(
            "imageUrl" => $imageUrl,
        ); 
       array_push($detailRecords["images"], $hotelDetails2);
    }    
}
if($result4->num_rows > 0){    
    $detailRecords["room"]=array(); 
	while ($fetch = $result4->fetch_assoc()) { 	
        extract($fetch); 
        $hotelDetails4=array(
            "roomId" => $id,
            "category" => $category,
            "roomTitle" => $title,
            "roomDescription" => $description,
            "bedType" => $bedType,
            "guest" => $guest,
            "price" => $price,
            "discount" => $discount,
            "hotelId" => $hotelId,
        ); 
       array_push($detailRecords["room"], $hotelDetails4);
    }    
    
}
if($result5->num_rows > 0){    
    $detailRecords["category"]=array(); 
	while ($fetch = $result5->fetch_assoc()) { 	
        extract($fetch); 
        $hotelDetails5=array(
            "category" => $category,
        ); 
       array_push($detailRecords["category"], $hotelDetails5);
    }    
    
}
if($result3->num_rows > 0){    
    $detailRecords["categoryDetail"]=array(); 
	while ($fetch = $result3->fetch_assoc()) { 	
        extract($fetch); 
        $hotelDetails3=array(
            "categoryId" => $id,
            "category" => $category,
            "imageUrl" => $imageUrl,
        ); 
       array_push($detailRecords["categoryDetail"], $hotelDetails3);
    }    
    http_response_code(200);     
    echo json_encode($detailRecords);
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No data found.")
    );
}