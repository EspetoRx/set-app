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
	
	/*----BANCO DE DADOS E CONEXÃO COM O MYSQL-------*/
	$con = mysqli_connect("us-cdbr-iron-east-01.cleardb.net", "b4374046414e9f", "05e528e1") or die  ("Sem conexão com o servidor");
	$select = mysqli_select_db($con, "heroku_7d1bac14eb9e1ae") or die("Sem acesso ao DB, Entre em contato com o Administrador.");
	
	
	/*-------CONSULTA PERMISSÃO---------*/
	$prioridade = mysqli_query($con, "SELECT tipo FROM usuario WHERE usuario.email = '$login'");
	$tipo = mysqli_fetch_array($prioridade)[0];
	if($tipo==1){
	
		
	/*---------COLSULTA USUARIOS----------*/
	$retorno = "<div class='col-md-12'><h3><center>Alteração de Membros</center></h3><br></div>";
	$contador = 1;
	$sql = 	"SELECT * FROM usuario JOIN perfil WHERE usuario.perfil_id = id ORDER BY perfil.nOme ASC";
	$usuarios = mysqli_query($con, $sql);
	while($reg = mysqli_fetch_array($usuarios)){
		if($contador%6 != 4){
			$retorno .= "<div class='col-md-3'>\n
				<div class='col-md-10 main-login'>
				<a href='alterar_este.php?email=".$reg['email']."'>
				<br>
				<center><img class='foto' src='upload/".$reg['arquivo']."' width=\"100%\" /></center>\n
				<p><center><strong>".$reg['nome']."</strong></center></p><br>
				</a>
				</div>
			</div>\n";
			$contador++;
		}else{
			$retorno .= "<div class='col-md-3'>\n
				<div class='col-md-10 main-login'>
				<br>
				<a href='alterar_este.php?email=".$reg['email']."'>
				<center><img class='foto' src='upload/".$reg['arquivo']."' width=\"100%\" /></center>\n
				<p><center><strong>".$reg['nome']."</strong></center></p><br>
				</a>
				</div>
			</div><div class='col-md-12 vis'>&nbsp;</div>\n";
			$contador++;
		}
	}
	$retorno .= "<div class=\"col-md-12\">
				<center><a href=\"painel_adm.php\"><button type=\"button\" class=\"btn btn-lg float-center bg-light\">Voltar</button></a></center
			</div>";
		//echo($retorno);
		
	/*---Dados de template---*/
	$tpl = new Template("../template.html");
	$tpl2 = new Template("html_template/visualiza_exlui.html");
	$tpl->painel_active = "active";
	$tpl->ende = "../";
	$tpl->painel_color = "color: #000000;";
	$tpl->perfil_active = "color: #ffffff;";
	$tpl->labeltitle = " - Alterar Membro";
	$tpl2->USUARIOS = $retorno;
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