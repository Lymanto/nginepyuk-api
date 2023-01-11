<?php
class Hotel{       
    private $hotelTable = "hotel";      
    private $roomTable = "room";      
    private $hotelImageTable = "hotel_images";      
    private $roomImageTable = "room_images";      
    private $categoryTable = "category";      
    public $id;
    public $title;
    public $star;
    public $city;
    public $country;   
    public $postalCode; 
	public $imageUrl; 
    public $facilities;
    public $guest;
    
    public $created_at;
    public $updated_at;
    private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	
	
	function search(){
		$stmt = $this->conn->prepare("SELECT hotel.id ,hotel.title, hotel.address, hotel.star , hotel.city, hotel.country, room.price, hotel_images.imageUrl FROM " .$this->hotelTable . " INNER JOIN ". $this->roomTable ." ON hotel.id = room.hotelId INNER JOIN " . $this->hotelImageTable . " ON hotel.id = hotel_images.hotelId WHERE room.price = (SELECT MIN(room.price) from room where hotelId = hotel.id AND room.guest=?) AND hotel_images.imageUrl = (SELECT hotel_images.imageUrl from hotel_images where hotelId = hotel.id LIMIT 1) AND hotel.city=?");
		$stmt->bind_param("ss", $this->guest,$this->city);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	function recommended(){
		$stmt = $this->conn->prepare("SELECT hotel.id ,hotel.title, hotel.address, hotel.star , hotel.city, hotel.country, room.price, hotel_images.imageUrl FROM " .$this->hotelTable . " INNER JOIN ". $this->roomTable ." ON hotel.id = room.hotelId INNER JOIN " . $this->hotelImageTable . " ON hotel.id = hotel_images.hotelId WHERE room.price = (SELECT MIN(room.price) from room where hotelId = hotel.id) AND hotel_images.imageUrl = (SELECT hotel_images.imageUrl from hotel_images where hotelId = hotel.id LIMIT 1) LIMIT 3");
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	
	function detailHotel(){
		$stmt = $this->conn->prepare("SELECT hotel.id ,hotel.title, hotel.address, hotel.star , hotel.city, hotel.country, room.price FROM " .$this->hotelTable . " INNER JOIN ". $this->roomTable ." ON hotel.id = room.hotelId WHERE room.price = (SELECT MIN(room.price) from room where hotelId = hotel.id) AND hotel.id = ?");
		$stmt->bind_param("i", $this->id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	function detailHotelImage(){
		$stmt = $this->conn->prepare("SELECT imageUrl FROM " .$this->hotelImageTable . " WHERE hotelId = ?");
		$stmt->bind_param("i", $this->id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	function detailHotelCategory(){
		$stmt = $this->conn->prepare("SELECT category FROM " . $this->categoryTable . " WHERE hotelId = ? GROUP BY category");
		$stmt->bind_param("i", $this->id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	function detailHotelCategoryDetail(){
		$stmt = $this->conn->prepare("SELECT category.id,category.category, room_images.imageUrl FROM " . $this->categoryTable . " INNER JOIN " . $this->roomImageTable . " ON category.id = room_images.categoryId WHERE category.hotelId = ?");
		$stmt->bind_param("i", $this->id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	
	function detailHotelRoom(){
		$stmt = $this->conn->prepare("SELECT room.id, room.title, room.description, room.bedType, category.category, room.guest, room.price, room.discount, room.hotelId FROM " . $this->roomTable . " INNER JOIN ". $this->categoryTable ." ON room.category = category.id WHERE room.hotelId = ?");
		$stmt->bind_param("i", $this->id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

	function update(){
		$stmt = $this->conn->prepare("
			UPDATE " . $this->hotelTable . " SET address= ?, star= ?, city= ?, country= ?, postalCode= ? 
			WHERE id = ?");
		$this->address = htmlspecialchars(strip_tags($this->address));
		$this->star = htmlspecialchars(strip_tags($this->star));
		$this->city = htmlspecialchars(strip_tags($this->city));
		$this->country = htmlspecialchars(strip_tags($this->country));
		$this->postalCode = htmlspecialchars(strip_tags($this->postalCode));

		$stmt->bind_param("sssssi", $this->address, $this->star, $this->city, $this->country, $this->postalCode, $this->id);

		if($stmt->execute()){
			return true;
		}
		return false;
	}
}
?>