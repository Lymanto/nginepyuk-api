<?php

class RoomImages{
    private $roomImagesTable = 'room_images';
    public $file;
    public $imageUrl;
    public $category;

    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }
	
    function create($category,$file){
        foreach($file['name'] as $key =>$image){
            $fileName = $file['name'][$key];
            $tmpName = $file['tmp_name'][$key];
            $upload_path = '../upload/room/'; // set upload folder path 
            $uniqueName = time().uniqid(rand());
            $fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); // get image extension
            $destFile = $upload_path . $uniqueName .'.' .$fileExt;
            // valid image extensions
            move_uploaded_file($tmpName, $destFile); // move file from system temporary path to our upload folder path 
            $stmt = $this->conn->prepare("
                INSERT INTO ".$this->roomImagesTable."(`imageUrl`, `category`)
                VALUES(?,?)");
    
            $this->imageUrl =  $_SERVER['HTTP_HOST'].'/upload/room/' . $uniqueName .'.' .$fileExt;
            $this->category = $category;
    
            $stmt->bind_param("si", $this->imageUrl, $this->category);
    
            $stmt->execute();
        }
        // return true;
    }
}

?>