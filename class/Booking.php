<?php
class Booking{
    private $bookingTable = "booking";      
    private $hotelTable = "hotel";      
    private $bookingDetailTable = "booking_detail";      
    private $paymentTable = "payment";      
    private $roomTable = "room";      
    public $id;
    public $title;
    public $status;
    public $quantity;
    public $roomPrice;
    public $taxPrice;   
    public $discount; 
	public $total; 
    public $bookingStartDate;
    public $bookingDetailId;
    public $paymentId;
    public $userId;
    public $hotelId;
    public $roomId;
    public $created_at;
    public $updated_at;
    
    //booking detail
    public $fullName;
    public $phoneNumber;
    public $email;
    public $visitorName;
    
    //booking detail
    public $paymentMethod;
    public $virtualAccount;
    public $noVirtualAccount;
    public $cardOwner;
    public $cardNumber;
    public $cardExpired;
    
    private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	
	
    function create(){
        $this->booking_detail();
        $this->payment();
        if($this->booking()){
            return true;
        }else{
            $this->paymentDelete();
            $this->bookingDetailDelete();
            return false;
        }
    }

    function paymentDelete(){
        $stmt = $this->conn->prepare("
        DELETE FROM ".$this->paymentTable." 
        WHERE id = ?");
            
        $stmt->bind_param("i", $this->paymentId);
    
        $stmt->execute();
         
    }
    
    function bookingDetailDelete(){
        $stmt = $this->conn->prepare("
        DELETE FROM ".$this->bookingDetailTable." 
        WHERE id = ?");
            
        $stmt->bind_param("i", $this->bookingDetailId);
    
        $stmt->execute();
    }

    function booking_detail(){
        $stmt = $this->conn->prepare("
			INSERT INTO ".$this->bookingDetailTable."(`fullName`, `phoneNumber`, `email`,`visitorName`)
			VALUES(?,?,?,?)");
		$this->fullName = htmlspecialchars(strip_tags($this->fullName));
		$this->phoneNumber = htmlspecialchars(strip_tags($this->phoneNumber));
		$this->email = htmlspecialchars(strip_tags($this->email));
		$this->visitorName = htmlspecialchars(strip_tags($this->visitorName));

		$stmt->bind_param("ssss", $this->fullName, $this->phoneNumber, $this->email, $this->visitorName);
		
		$stmt->execute();
        $this->bookingDetailId = $this->conn->insert_id;
    }

    function payment(){
        $stmt = $this->conn->prepare("
			INSERT INTO ".$this->paymentTable."(`paymentMethod`, `virtualAccount`, `noVirtualAccount`,`cardOwner`, `cardNumber`,`cardExpired`)
			VALUES(?,?,?,?,?,?)");
		$this->paymentMethod = htmlspecialchars(strip_tags($this->paymentMethod));
		$this->virtualAccount = htmlspecialchars(strip_tags($this->virtualAccount));
		$this->noVirtualAccount = htmlspecialchars(strip_tags($this->noVirtualAccount));
		$this->cardOwner = htmlspecialchars(strip_tags($this->cardOwner));
		$this->cardNumber = htmlspecialchars(strip_tags($this->cardNumber));
		$this->cardExpired = htmlspecialchars(strip_tags($this->cardExpired));

		$stmt->bind_param("ssssss", $this->paymentMethod, $this->virtualAccount, $this->noVirtualAccount, $this->cardOwner, $this->cardNumber, $this->cardExpired);
		
		$stmt->execute();
        $this->paymentId = $this->conn->insert_id;
    }
    
    function booking(){
        $roomPrice = $this->checkRoomPrice();
    
        $stmt = $this->conn->prepare("
			INSERT INTO ".$this->bookingTable."(`title`, `status`,`quantity`, `roomPrice`,`taxPrice`, `discount`, `total`, `bookingStartDate`, `bookingEndDate`, `bookingDetailId`, `paymentId`, `userId`, `hotelId`, `roomId`)
			VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$this->title = $roomPrice['title'];
		$this->status = htmlspecialchars(strip_tags($this->status));
		$this->quantity = htmlspecialchars(strip_tags($this->quantity));
		$this->roomPrice = $roomPrice['price'] * $this->quantity;
		$this->discount = $roomPrice['discount'] * $this->quantity;
		$this->taxPrice = ($roomPrice['price'] - $roomPrice['discount']) * 11 / 100 * $this->quantity;
		$this->total = $this->roomPrice - $this->discount + $this->taxPrice;
		$this->bookingStartDate = htmlspecialchars(strip_tags($this->bookingStartDate));
		$this->bookingEndDate = htmlspecialchars(strip_tags($this->bookingEndDate));
		$this->bookingDetailId = htmlspecialchars(strip_tags($this->bookingDetailId));
		$this->paymentId = htmlspecialchars(strip_tags($this->paymentId));
		$this->userId = htmlspecialchars(strip_tags($this->userId));
		$this->hotelId = $roomPrice['hotelId'];
		$this->roomId = htmlspecialchars(strip_tags($this->roomId));

		$stmt->bind_param("ssiddddssiiiii", $this->title, $this->status,$this->quantity, $this->roomPrice, $this->taxPrice, $this->discount, $this->total, $this->bookingStartDate, $this->bookingEndDate, $this->bookingDetailId, $this->paymentId, $this->userId, $this->hotelId, $this->roomId);
		
		if($stmt->execute()){
			return true;
		}
		return false;
    }
    
    function checkRoomPrice(){
		$stmt = $this->conn->prepare("SELECT price,discount,hotelId,title FROM ".$this->roomTable." WHERE id = ?");
		$stmt->bind_param("i", $this->roomId);					
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result->fetch_assoc();
    }

    
}
?>