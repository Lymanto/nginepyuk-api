<?php
define("JWT_SECRET", "gHfKxh%zjqC7ZMKAcY@B(fC(aC0Opv9Q");
define("JWT_ISSUER", "nginepyuk");
define("JWT_AUD", "nginepyuk.com");
define("JWT_ALGO", "HS512");
class Database{
	
	private $host  = 'localhost';
    private $user  = 'root';
    private $password   = "root";
    private $database  = "nginepyuk"; 
    
    public function getConnection(){		
		$conn = new mysqli($this->host, $this->user, $this->password, $this->database);
		if($conn->connect_error){
			die("Error failed to connect to MySQL: " . $conn->connect_error);
		} else {
			return $conn;
		}
    }
}
?>