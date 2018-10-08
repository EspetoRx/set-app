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
	
	/*-------CONSULTA PERMISSÃO---------*/
	$prioridade = mysqli_query($con, "SELECT tipo FROM usuario WHERE usuario.email = '$login'");
	$tipo = mysqli_fetch_array($prioridade)[0];
	
	/*---Dados de template---*/
	$tpl = new Template("../template.html");
	$template2 = new Template("html_template/altera_senha.html");
	$tpl->active = "active";
	$tpl->ende = "../";
	$tpl->painel_color = "color: #ffffff;";
	$tpl->perfil_active = "color: #000000;";
	if($tipo != 1){
		$tpl->mostrar = "nao_mostrar";
	}
	$template2->visibil = "vis-total";
	$tpl->l_perfil = " - Alterando a senha";
	$tpl->CONTENT = $template2->parse();
	//$tpl->value = "anti-valor";
	$tpl->show();
}
?>