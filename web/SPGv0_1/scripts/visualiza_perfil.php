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
	$email = $_GET['email'];
	
	/*----BANCO DE DADOS E CONEXÃO COM O MYSQL-------*/
	$con = mysqli_connect("us-cdbr-iron-east-01.cleardb.net", "b4374046414e9f", "05e528e1") or die  ("Sem conexão com o servidor");
	$select = mysqli_select_db($con, "heroku_7d1bac14eb9e1ae") or die("Sem acesso ao DB, Entre em contato com o Administrador.");
	
	/*----------------CONSULTASQL---------*/
	$nome = mysqli_query($con, "SELECT perfil.nome FROM perfil NATURAL JOIN usuario WHERE email = '$email' AND perfil.id = usuario.perfil_id");
	$data = mysqli_query($con, "SELECT data_admissao FROM perfil NATURAL JOIN usuario WHERE email = '$email' AND perfil.id = usuario.perfil_id");
	$git = mysqli_query($con, "SELECT github FROM perfil NATURAL JOIN usuario WHERE email = '$email' AND perfil.id = usuario.perfil_id");
	$linkedin = mysqli_query($con, "SELECT linkedin FROM perfil NATURAL JOIN usuario WHERE email = '$email' AND perfil.id = usuario.perfil_id");
	$type = mysqli_query($con, "SELECT id FROM tipousuario JOIN usuario WHERE email = '$email' AND tipousuario.id = usuario.tipo");
	$result_foto = mysqli_query($con, "SELECT foto FROM perfil NATURAL JOIN usuario WHERE email = '$email' AND perfil.id = usuario.perfil_id");
	$foto = mysqli_fetch_object($result_foto);
	$profile = mysqli_query($con, "SELECT id FROM perfil NATURAL JOIN usuario WHERE email = '$email' AND perfil.id = usuario.perfil_id");
	$profile_id = mysqli_fetch_array($profile)[0];
	if (mysqli_fetch_array(mysqli_query($con, "SELECT tipo FROM tipousuario NATURAL JOIN usuario WHERE email = '$login' AND tipousuario.id = 1"))[0] == "1"){$mostrar = "mostrar";}else{ $mostrar = "nao_mostrar";}
		
	/*-------------------RECUPERA LISTA DE TIPOS DE USUARIOS-------------------*/
	$opt_block = "<select id='tipo' name='tipo' class='form-control' {disabled}>\n";
	$tiposUsers = mysqli_query($con, "SELECT * FROM tipousuario");
	$tipo = mysqli_fetch_array($type)[0];
	while($reg = mysqli_fetch_array($tiposUsers))	{
		if($reg['id'] == $tipo){
			$opt_block .= "<option value='".$reg['id']."' selected>".$reg['id']." - ".$reg['descricao']."</option>\n";
		}else{
			$opt_block .= "<option value='".$reg['id']."'>".$reg['id']." - ".$reg['descricao']."</option>\n";
		}
	}
	$opt_block .= "</select>\n";
	
	/*GERA O TEMPLATE*/
	$template = new Template("../template.html");
	$template2 = new Template("html_template/visualiza.html");
	$template2->nome = mysqli_fetch_array($nome)[0];
	$template2->github = mysqli_fetch_array($git)[0];
	$template2->data = mysqli_fetch_array($data)[0];
	$template2->linkedin = mysqli_fetch_array($linkedin)[0];
	$template2->email = $email;
	$template2->OPT_BLOCK = $opt_block;
	$template2->foto = "getImage.php?PicNum=$profile_id";
	$template2->disabled = "disabled";
	$template2->TITLE = "Visualização de Membro";
	$template2->voltar = "visualiza_membros.php";
	$template2->altera = "inv-total";
	$template2->labelbtn = "Alterar membro";
	$template2->buttao = "button";
	$template2->exclusion = "alterar_este.php?email=$email";
	$template->ende = "../";
	$template->active = "letra";
	$template->painel_active = "letra";
	$template->membros = "membros";
	$template2->somenor = "somenor";
	if(mysqli_fetch_array(mysqli_query($con, "SELECT tipo FROM usuario WHERE email = '$login'"))[0] != 1){
		$template->mostrar = "inv_no_change";
		$template2->visibility = "vis-total";
		$template2->visibil = "inv-total";
	}else{
		$template->mostrar = "vis_no_change";
		$template2->visibility = "inv-total";
		$template2->visibil = "vis-total";
	}
	$template->CONTENT = $template2->parse();
	$template->so_este = "valor";
	$template->show();
}
?>