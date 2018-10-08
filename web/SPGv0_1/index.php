<?php
require_once("scripts/raelgc/view/Template.php");
use raelgc\view\Template;
session_start();
if(!isset($_SESSION['login'])){
	$tpl = new Template("index.html");

	$tpl->erro = "<br>";
	$tpl->allert = "";
	
	$tpl->show();
}else{
	header("Location: session.php");
}


?>