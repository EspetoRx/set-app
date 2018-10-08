<?php
header("Content-type: text/html; charset=utf-8");

/*---Template---*/
require_once("raelgc/view/Template.php");
use raelgc\view\Template;

/*---Sessão---*/
session_start();

/*CHECA SESSÃO*/
if((!isset ($_SESSION['login']) == true) and (!isset ($_SESSION['senha']) == true))
{
  unset($_SESSION['login']);
  unset($_SESSION['senha']);
  header('location:../index.php');
}

if(isset($_SESSION['login'])){
	
	/*PEGANDO VARIÁVEIS DE SESSÃO*/
	$login = $_SESSION['login'];
	
	/*----BANCO DE DADOS E CONEXÃO COM O MYSQL-------*/
	$con = mysqli_connect("us-cdbr-iron-east-01.cleardb.net", "b4374046414e9f", "05e528e1") or die  ("Sem conexão com o servidor");
	$select = mysqli_select_db($con, "heroku_7d1bac14eb9e1ae") or die("Sem acesso ao DB, Entre em contato com o Administrador.");
	
	
	$template = new Template("../template.html");
	$template2 = new Template("html_template/carta.html");
	$template->ende = "../";
	$template->active = "letra";
	$template->painel_active = "letra";
	$template->carta_serv = "membros";
	$template->CONTENT = $template2->parse();
	if(mysqli_fetch_array(mysqli_query($con, "SELECT tipo FROM usuario WHERE email = '$login'"))[0] != 1){
		$template->mostrar = "inv_no_change";
	}else{
		$template->mostrar = "vis_no_change";
	}
	$template->value = "always_static";
	$template->show();
}
?>