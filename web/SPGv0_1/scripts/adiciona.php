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

	$login = $_SESSION['login'];
	
	$con = mysqli_connect("us-cdbr-iron-east-01.cleardb.net", "b4374046414e9f", "05e528e1") or die  ("Sem conexão com o servidor");
	$select = mysqli_select_db($con, "heroku_7d1bac14eb9e1ae") or die("Sem acesso ao DB, Entre em contato com o Administrador.");
	
	$prioridade = mysqli_query($con, "SELECT tipo FROM usuario WHERE usuario.email = '$login'");
	$tipo = mysqli_fetch_array($prioridade)[0];
	if($tipo==1){
	
	/*-------------------RECUPERA LISTA DE TIPOS DE USUARIOS-------------------*/
	$opt_block = "<select id='tipo' name='tipo' class='form-control'>\n";
	$tiposUsers = mysqli_query($con, "SELECT * FROM tipousuario");
	while($reg = mysqli_fetch_array($tiposUsers))	{
		$opt_block .= "<option value='".$reg['id']."'>".$reg['id']." - ".$reg['descricao']."</option>\n";
	}
	$opt_block .= "</select>\n";

	/*---Dados de template---*/
	$tpl = new Template("../template.html");
	$tpl2 = new Template("html_template/adiciona.html");
	$tpl2->data = date("Y-m-d");
	$tpl->painel_active = "active";
	$tpl->ende = "../";
	$tpl->painel_color = "color: #000000;";
	$tpl->perfil_active = "color: #ffffff;";
	$tpl->labeltitle = " - Adicionar Membro";
	$tpl2->OPT_BLOCK = $opt_block;
	$tpl->CONTENT = $tpl2->parse();
	$tpl->so_este = "valor";

	/*---Carregamento do modelo---*/


	$tpl->show();
	}else{
		$tpl = new Template("../template.html");
		//$tpl2 = new Template("html_template/adiciona.html");
		//$tpl2->data = date("Y-m-d");
		$tpl->painel_active = "active";
		$tpl->ende = "../";
		$tpl->painel_color = "color: #000000;";
		$tpl->perfil_active = "color: #ffffff;";
		$tpl->CONTENT = "<h2>Error 404<h2><h3>Você não tem nível de permissão suficiente para acessar esta página.</h3>";
		header("Location: logout.php");
		//$tpl->value = "anti-valor";
		
		$tpl->show();
	}
	
}
?>