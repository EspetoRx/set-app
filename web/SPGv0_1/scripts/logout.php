<?php
header("Content-type: text/html; charset=utf-8");
session_start();
require_once("raelgc/view/Template.php");
use raelgc\view\Template;

if((!isset ($_SESSION['login']) == true) and (!isset ($_SESSION['senha']) == true))
{
  unset($_SESSION['login']);
  unset($_SESSION['senha']);
  header('location:../index.php');
}

if(isset($_SESSION['login'])){
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}
    session_destroy();
    
	$tpl = new Template("../index.html");

	$tpl->erro = "<center><p style='color: black;'>Logout realizado com sucesso.</center>";
	$tpl->allert= "";
	$tpl->ende = "../";
	$tpl->rodape = "rodape";

	$tpl->show();
}
?>