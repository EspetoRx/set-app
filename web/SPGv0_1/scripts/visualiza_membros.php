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
	
	/*---------COLSULTA USUARIOS----------*/
	$retorno = "<div class='col-md-12'><h3><center>Visualização de Membros</center></h3><br></div>";
	$contador = 1;
	$sql = 	"SELECT * FROM usuario JOIN perfil WHERE usuario.perfil_id = id ORDER BY perfil.nOme ASC";
	$usuarios = mysqli_query($con, $sql);
	while($reg = mysqli_fetch_array($usuarios)){
		if($contador%6 != 4){
			$retorno .= "<div class='col-md-3'>
					<div class='col-md-10 main-login'>
						<br>
						<a href='visualiza_perfil.php?email=".$reg['email']."'>
						<center><img class='foto' src=\"getImage.php?PicNum=".$reg['perfil_id']."\" width=\"100%\" /></center>
						<p><center><strong>".$reg['nome']."</strong></center></p>
						</a>
					</div>
			</div>\n";
			$contador++;
		}else{
			$retorno .= "<div class='col-md-3'>
				<div class='col-md-10 main-login'>
				<br>
				<a href='visualiza_perfil.php?email=".$reg['email']."'>
				<center><img class='foto' src=\"getImage.php?PicNum=".$reg['perfil_id']."\" width=\"100%\" /></center>
				<p><center><strong>".$reg['nome']."</strong></center></p>
				</a>
				</div>
			</div><div class='col-md-12 vis'>&nbsp;</div>\n";
			$contador++;
		}
	}
	$retorno .= "<div class=\"col-md-12\">
				<center><a href=\"../session.php\"><button type=\"button\" class=\"btn btn-lg float-center bg-light\">Voltar</button></a></center
			</div>";
	
	/*GERA O TEMPLATE*/
	$template = new Template("../template.html");
	$template2 = new Template("html_template/visualiza_exlui.html");
	$template2->USUARIOS = $retorno;
	$template->l_membros = " - Visualizando membros";
	$template->ende = "../";
	$template->active = "letra";
	$template->painel_active = "letra";
	$template->membros = "membros";
	$template->CONTENT = $template2->parse();
	if(mysqli_fetch_array(mysqli_query($con, "SELECT tipo FROM usuario WHERE email = '$login'"))[0] != 1){
		$template->mostrar = "inv_no_change";
	}else{
		$template->mostrar = "vis_no_change";
	}
	//$template->value = "valor";
	$template->show();
}
?>