<?php
	// error_reporting(E_ALL);
	ini_set("display_errors", 0);
	include_once("./Config/Config.php");
	include_once("./Config/Database.php");
	include_once("./App/Controller/Home.php");
	
	if(empty($_GET['method'])){
		$Controller = new Home();
		$Controller->index();
	}else{
		$Controller	= $_GET['class'];
		$Controller = new $Controller();
		$method		= $_GET['method'];
		$Controller->$method();
	}
?>