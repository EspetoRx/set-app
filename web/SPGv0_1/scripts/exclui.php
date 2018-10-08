<?php
header("Content-type: text/html; charset=utf-8");/*---Template---*/
require_once("raelgc/view/Template.php");
use raelgc\view\Template;

/*---Sessão---*/
session_start();

if((!isset ($_SESSION['login']) == true) and (!isset ($_SESSION['senha']) == true))
{
  unset($_SESSION['login']);
  unset($_SESSION['senha']);
  header('location:../index.php');
}

if(isset($_SESSION['login'])){
	
	$login = $_SESSION['login'];
	$email = $_GET['email'];
	
	
	/*----BANCO DE DADOS E CONEXÃO COM O MYSQL-------*/
	$con = mysqli_connect("us-cdbr-iron-east-01.cleardb.net", "b4374046414e9f", "05e528e1") or die  ("Sem conexão com o servidor");
	$select = mysqli_select_db($con, "heroku_7d1bac14eb9e1ae") or die("Sem acesso ao DB, Entre em contato com o Administrador.");
	
	
	/*-------CONSULTA PERMISSÃO---------*/
	$prioridade = mysqli_query($con, "SELECT tipo FROM usuario WHERE usuario.email = '$login'");
	$tipo = mysqli_fetch_array($prioridade)[0];
	if($tipo==1){
	
	$sql = "SELECT id FROM perfil JOIN usuario WHERE email='$email' AND id=usuario.perfil_id";
	$idperfil = mysqli_fetch_array(mysqli_query($con, $sql))[0];
	
	$sql = "DELETE FROM usuario WHERE email = '$email'";
	$deleta_usuario = mysqli_query($con, $sql);
		
	$sql = "DELETE FROM perfil WHERE id='$idperfil'";
	$deleta_perfil = mysqli_query($con, $sql);
	
		
	/*---Dados de template---*/
	header('Location: remove_usuario.php');
		
	/*---Carregamento do modelo---*/
	$tpl->show();
	}else{
		/*---Dados de template---*/
		$tpl = new Template("../template.html");
		$tpl->painel_active = "active";
		$tpl->ende = "../";
		$tpl->painel_color = "color: #000000;";
		$tpl->perfil_active = "color: #ffffff;";
		$tpl->CONTENT = "<h2>Error 404<h2><h3>Você não tem nível de permissão suficiente para acessar esta página.</h3>";
		//$tpl->value = "anti-valor";
		/*---Carregamento do modelo---*/
		$tpl->show();
	}
	
}
?>