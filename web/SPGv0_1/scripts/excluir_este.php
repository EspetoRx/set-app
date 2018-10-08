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
	$email = $_GET['email'];
	
	
	/*----BANCO DE DADOS E CONEXÃO COM O MYSQL-------*/
	$con = mysqli_connect("us-cdbr-iron-east-01.cleardb.net", "b4374046414e9f", "05e528e1") or die  ("Sem conexão com o servidor");
	$select = mysqli_select_db($con, "heroku_7d1bac14eb9e1ae") or die("Sem acesso ao DB, Entre em contato com o Administrador.");
	
	
	/*-------CONSULTA PERMISSÃO---------*/
	$prioridade = mysqli_query($con, "SELECT tipo FROM usuario WHERE usuario.email = '$login'");
	$tipo = mysqli_fetch_array($prioridade)[0];
	if($tipo==1){
	
	/*----------------CONSULTASQL---------*/
	$nome = mysqli_query($con, "SELECT perfil.nome FROM perfil NATURAL JOIN usuario WHERE email = '$email' AND perfil.id = usuario.perfil_id");
	$data = mysqli_query($con, "SELECT data_admissao FROM perfil NATURAL JOIN usuario WHERE email = '$email' AND perfil.id = usuario.perfil_id");
	$git = mysqli_query($con, "SELECT github FROM perfil NATURAL JOIN usuario WHERE email = '$email' AND perfil.id = usuario.perfil_id");
	$linkedin = mysqli_query($con, "SELECT linkedin FROM perfil NATURAL JOIN usuario WHERE email = '$email' AND perfil.id = usuario.perfil_id");
	$type = mysqli_query($con, "SELECT id FROM tipousuario JOIN usuario WHERE email = '$email' AND tipousuario.id = usuario.tipo");
	$foto = mysqli_query($con, "SELECT arquivo FROM perfil NATURAL JOIN usuario WHERE email = '$email' AND perfil.id = usuario.perfil_id");
		
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
	
	/*-------------BOTAO REMOVE-----------*/
	
		
	/*---Dados de template---*/
	$tpl = new Template("../template.html");
	$tpl2 = new Template("html_template/visualiza.html");
	$tpl->painel_active = "active";
	$tpl->ende = "../";
	$tpl->painel_color = "color: #000000;";
	$tpl->perfil_active = "color: #ffffff;";
	$tpl->labeltitle = " - Remove Membro";
	$tpl2->nome = mysqli_fetch_array($nome)[0];
	$tpl2->github = mysqli_fetch_array($git)[0];
	$tpl2->data = mysqli_fetch_array($data)[0];
	$tpl2->linkedin = mysqli_fetch_array($linkedin)[0];
	$tpl2->email = $email;
	$tpl2->OPT_BLOCK = $opt_block;
	$tpl2->foto = mysqli_fetch_array($foto)[0];
	$tpl2->disabled = "disabled";
	$tpl2->visibility = "inv-total";
	$tpl2->visibil = "vis-total";
	$tpl2->altera = "inv-total";
	$tpl2->exclusion = "exclui.php?email=".$email;
	$tpl2->labelbtn = "Excluir";
	$tpl2->buttao = "button";
	$tpl2->voltar = "painel_adm.php";
	$tpl2->TITLE = "Exclusão de Membro";
	$tpl->CONTENT = $tpl2->parse();
	$tpl->value = "valor";

		
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
		$tpl->value = "anti-valor";
		/*---Carregamento do modelo---*/
		$tpl->show();
	}
	
}
?>