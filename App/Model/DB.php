<?php 

class DB{
	
	/* protected $conn; */
	
	public function __construct(){  
		$this->conn = pg_connect("host=127.0.0.1 dbname=IC_Automation user=postgres password=Livefor2020@#");
	}
}