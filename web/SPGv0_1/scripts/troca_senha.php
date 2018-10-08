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
	
	/*RECEBE VARIÁVEIS DO FORMULARIO*/
	$senha_antiga  = $_POST['senha_antiga'];
	$senha_nova = $_POST['senha_nova'];
	$re_senha_nova = $_POST['re_senha_nova'];
	
	/*CHECA SENHA ANTIGA*/
	$sql = "SELECT senha FROM usuario WHERE email='$login'";
	$senha = mysqli_query($con, $sql);
	$valor_senha = mysqli_fetch_array($senha)[0];
	
	
	if($valor_senha != $senha_antiga){
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
		$template2->ERRO_SENHA = "<center><spam style='color: red'>Erro ao confirmar a senha antiga.</spam></center>";
		$tpl->l_perfil = " - Alterando a senha";
		$tpl->CONTENT = $template2->parse();
		//$tpl->value = "anti-valor";
		$tpl->show();
	}else{
		if($senha_nova == $re_senha_nova){
			$sql = "UPDATE usuario SET senha='$senha_nova' WHERE email='$login'";
			mysqli_query($con, $sql);
			if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
			session_destroy();
    
			$tpl = new Template("../index.html");

			$tpl->erro = "<center><p style='color: black;'>Senha alterada com sucesso.<br>Faça login novamente.</center>";
			$tpl->allert= "";
			$tpl->ende = "../";

			$tpl->show();
			}
		}else{
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
			$template2->ERRO_SENHA = "<center><spam style='color: red'>Senhas novas não conferem.</spam></center>";
			$tpl->l_perfil = " - Alterando a senha";
			$tpl->CONTENT = $template2->parse();
			//$tpl->value = "anti-valor";
			$tpl->show();	
		}
	}
}
?>
    
	