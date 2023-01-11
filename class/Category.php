<?php
class Category{       
    private $categoryTable = "category";      
    private $roomImagesTable = 'room_images';
    public $id;
    public $category;
    public $hotelId;
    public $imageUrl;
    public $file;
    private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	
	
	function create(){
        $stmt = $this->conn->prepare("
			INSERT INTO ".$this->categoryTable."(`category`, `hotelId`)
			VALUES(?,?)");

		$this->category = htmlspecialchars(strip_tags($this->category));
		$this->hotelId = htmlspecialchars(strip_tags($this->hotelId));

		$stmt->bind_param("si", $this->category, $this->hotelId);
        $stmt->execute();
        $this->addImage($this->conn->insert_id,$this->file);
        return true;
	}

    function addImage($category,$file){
        foreach($file['name'] as $key =>$image){
            $fileName = $file['name'][$key];
            $tmpName = $file['tmp_name'][$key];
            $upload_path = '../upload/room/';
            $uniqueName = time().uniqid(rand());
            $fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); // get image extension
            $destFile = $upload_path . $uniqueName .'.' .$fileExt;

            move_uploaded_file($tmpName, $destFile);
            $stmt = $this->conn->prepare("
                INSERT INTO ".$this->roomImagesTable."(`imageUrl`, `categoryId`)
                VALUES(?,?)");

            $this->imageUrl =  $_SERVER['HTTP_HOST'].'/upload/room/' . $uniqueName .'.' .$fileExt;

            $stmt->bind_param("si", $this->imageUrl, $category);

            $stmt->execute();
        }
    }
}
?>