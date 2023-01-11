<?php

class Users{       
    private $itemsTable = "users";      
    private $hotelTable = "hotel";      
    public $id;
    public $title;
    public $fullName;
    public $email;
    public $password;
    public $phoneNumber;   
    public $role; 
	public $address; 
    public $gender;
    public $idCard;
    public $idBank;
    public $hotelId;
    public $created_at;
    public $updated_at;
    private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	
	
	function login(){	
		$stmt = $this->conn->prepare("SELECT id ,fullName, email,role , hotelId, password FROM " .$this->itemsTable . " WHERE email = ? LIMIT 0,1");
		$stmt->bind_param("s", $this->email);					
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result->fetch_assoc();	
	}
	function verify ($email, $password) {
		// (F1) GET USER
		$user = $this->login($email);
		$valid = is_array($user);
	 
		// (F2) CHECK PASSWORD
		if ($valid) {
		  $valid = password_verify($password, $user["password"]);
		}
	 
		// (F3) RETURN RESULT (FALSE IF INVALID, USER ARRAY IF VALID)
		if ($valid) { return $user; }
		else {
		  $this->error = "Invalid user/password";
		  return false;
		}
	  }
	function register(){
		$stmt = $this->conn->prepare("
			INSERT INTO ".$this->itemsTable."(`fullName`, `email`, `password`)
			VALUES(?,?,?)");

		$this->fullName = htmlspecialchars(strip_tags($this->fullName));
		$this->email = htmlspecialchars(strip_tags($this->email));
		$this->password = password_hash($this->password, PASSWORD_BCRYPT);

		$stmt->bind_param("sss", $this->fullName, $this->email, $this->password);
		
		if($stmt->execute()){
			return true;
		}
		return false;
	}
	function register_hotel(){
		
		$stmt = $this->conn->prepare("
		INSERT INTO ".$this->itemsTable."(`fullName`, `email`, `password`, `role`, `hotelId`)
		VALUES(?,?,?,?,?)");
		
		$this->fullName = htmlspecialchars(strip_tags($this->fullName));
		$this->email = htmlspecialchars(strip_tags($this->email));
		$this->password = password_hash($this->password, PASSWORD_BCRYPT);
		$this->role = "hotel";
		$this->hotelId = $this->conn->insert_id;
		$this->title = htmlspecialchars(strip_tags($this->title));
		$stmt->bind_param("ssssi", $this->fullName, $this->email, $this->password,$this->role,$this->createHotel($this->title));
		
		
		if($stmt->execute()){
			return true;
		}
		return false;
	}
	function createHotel($title){
		$stmt = $this->conn->prepare("
			INSERT INTO ". $this->hotelTable ."(`title`) VALUES(?)");
			
		$stmt->bind_param("s", $title);
		
		$stmt->execute();
		return $this->conn->insert_id;
	}
}
?>