<?php
header("Content-type: text/html; charset=utf-8");
/*---Template---*/
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
	
	/*RECUPERAÇÃO DE DADOS DE SESSÃO*/
	$login = $_SESSION['login'];
	
	/*----BANCO DE DADOS E CONEXÃO COM O MYSQL-------*/
	$con = mysqli_connect("us-cdbr-iron-east-01.cleardb.net", "b4374046414e9f", "05e528e1") or die  ("Sem conexão com o servidor");
	$select = mysqli_select_db($con, "heroku_7d1bac14eb9e1ae") or die("Sem acesso ao DB, Entre em contato com o Administrador.");
	
	$nome = $_POST['nome'];
	$git = $_POST['github'];
	$linkedin = $_POST['linkedin'];
	
	$sql = "UPDATE perfil JOIN usuario SET nome = '$nome', github = '$git', linkedin='$linkedin' WHERE usuario.email='$login' AND id = usuario.perfil_id";
	mysqli_query($con, $sql);
	header("Location: ../session.php");
}
?>