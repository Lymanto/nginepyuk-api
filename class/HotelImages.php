<?php

class HotelImages{
    private $hotelImagesTable = 'hotel_images';
    public $file;
    public $imageUrl;
    public $hotelId;

    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }	
	
    function create(){
        foreach($this->file['name'] as $key =>$image){
            $fileName = $this->file['name'][$key];
            $tmpName = $this->file['tmp_name'][$key];
            $upload_path = '../upload/hotel/'; // set upload folder path 
            $uniqueName = time().uniqid(rand());
            $fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); // get image extension
            $destFile = $upload_path . $uniqueName .'.' .$fileExt;
            // valid image extensions
            move_uploaded_file($tmpName, $destFile); // move file from system temporary path to our upload folder path 
            $stmt = $this->conn->prepare("
                INSERT INTO ".$this->hotelImagesTable."(`imageUrl`, `hotelId`)
                VALUES(?,?)");
    
            $this->imageUrl =  $_SERVER['HTTP_HOST'].'/upload/hotel/' . $uniqueName .'.' .$fileExt;
            $this->hotelId = htmlspecialchars(strip_tags($this->hotelId));
    
            $stmt->bind_param("si", $this->imageUrl, $this->hotelId);
    
            $stmt->execute();
        }
        return true;
    }
}

?>