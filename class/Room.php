<?php
class Room{       
    private $roomTable = "room";      
    private $hotelTable = "hotel";      
    private $roomImageTable = "room_images";      
    public $id;
    public $title;
    public $description;
    public $bedType;
    public $category;   
    public $guest; 
	public $price; 
    public $discount;
    public $hotelId;
    public $file;
    private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	
	function findBy(){
		$stmt = $this->conn->prepare("SELECT room.id as roomId,room.title as roomTitle, room.bedType, room.price, room.discount, room.guest, room.hotelId, hotel.title as hotelName, room_images.imageUrl FROM " .$this->roomTable . " INNER JOIN ". $this->hotelTable ." ON room.hotelId = hotel.id INNER JOIN " . $this->roomImageTable . " ON room.category = room_images.categoryId WHERE room.id = ? AND room_images.imageUrl = (SELECT room_images.imageUrl from room_images where room_images.categoryId = room.category LIMIT 1)");
		$stmt->bind_param("i", $this->id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	function create(){
		$stmt = $this->conn->prepare("
			INSERT INTO ".$this->roomTable."(`title`, `description`, `bedType` , `category`, `guest`, `price`, `discount`,`hotelId`)
			VALUES(?,?,?,?,?,?,?,?)");

		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->description = htmlspecialchars(strip_tags($this->description));
		$this->bedType = htmlspecialchars(strip_tags($this->bedType));
		$this->category = htmlspecialchars(strip_tags($this->category));
		$this->guest = htmlspecialchars(strip_tags($this->guest));
		$this->price = htmlspecialchars(strip_tags($this->price));
		$this->discount = htmlspecialchars(strip_tags($this->discount));
		$this->hotelId = htmlspecialchars(strip_tags($this->hotelId));

		$stmt->bind_param("sssssddi", $this->title, $this->description, $this->bedType,$this->category, $this->guest, $this->price,$this->discount, $this->hotelId);
		
		if($stmt->execute()){
			return true;
		}
		return false;
	}
    
}
?>