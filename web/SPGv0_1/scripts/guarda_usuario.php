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
	
	/*------------PEGA VARIÁVEIS-------------*/
	$nome = $_POST['nome'];
	$data_admissao = $_POST['data'];
	$email = $_POST['email'];
	$senha = $_POST['senha'];
	$resenha = $_POST['resenha'];
	$github = $_POST['github'];
	$linkedin = $_POST['linkedin'];
	$tipo = $_POST['tipo'];	
		
	/*-------------------RECUPERA LISTA DE TIPOS DE USUARIOS-------------------*/
	$opt_block = "<select id='tipo' name='tipo' class='form-control' {disabled}>\n";
	$tiposUsers = mysqli_query($con, "SELECT * FROM tipousuario");
	while($reg = mysqli_fetch_array($tiposUsers))	{
		if($reg['id'] == $tipo){
			$opt_block .= "<option value='".$reg['id']."' selected>".$reg['id']." - ".$reg['descricao']."</option>\n";
		}else{
			$opt_block .= "<option value='".$reg['id']."'>".$reg['id']." - ".$reg['descricao']."</option>\n";
		}
	}
	$opt_block .= "</select>\n";
		
	/*-ARQUIVOS DE IMAGEM-*/
	$msg = false;
	$novo_nome = "";
	$image;
	if(isset($_FILES['file'])){
		$extensao = strtolower(substr($_FILES['file']['name'], -4));
		$novo_nome2 = md5(time()).$extensao;
		$novo_nome = $novo_nome2;
		$diretorio = "upload/";
		$nome_completo = $diretorio.$novo_nome2;
		move_uploaded_file($_FILES['file']['tmp_name'], $nome_completo);
		$tamanhoImg = filesize($nome_completo); 
    	$mysqlImg = addslashes(fread(fopen($nome_completo, "r"), $tamanhoImg));
    	$image = $mysqlImg; 
	}
	/*- FIM ARQUIVOS DE IMAGEM-*/

	/*-------------CHECA SE JÁ EXISTE---------------*/
	$checa = mysqli_query($con, "SELECT email FROM usuario WHERE email = '$email'");
	if(mysqli_num_rows($checa)>0){
		$tpl = new Template("../template.html");
		$tpl->painel_active = "active";
		$tpl->ende = "../";
		$tpl->painel_color = "color: #000000;";
		$tpl->perfil_active = "color: #ffffff;";
		$tpl->CONTENT = "<h2>Error 303<h2><h3>E-mail já existe</h3>";
		//$tpl->value = "anti-valor";
		$tpl->show();
		return;
	}else if($senha != $resenha){
		$tpl = new Template("../template.html");
		$tpl->painel_active = "active";
		$tpl->ende = "../";
		$tpl->painel_color = "color: #000000;";
		$tpl->perfil_active = "color: #ffffff;";
		$tpl->CONTENT = "<h2>Error 303<h2><h3>Senhas não batem</h3>";
		//$tpl->value = "anti-valor";
		$tpl->show();
		return;
	}else{
		$sql1 = "INSERT INTO usuario (email,senha,tipo) VALUES ('$email','$senha',$tipo)";
		$sql2 = "INSERT INTO perfil (nome, data_admissao, linkedin, github, arquivo, data, foto) VALUES ('$nome', '$data_admissao', '$linkedin', '$github', '$novo_nome', NOW(), '$image')";
		$sql3 = "SELECT id FROM perfil WHERE nome = '$nome'";
		$user = mysqli_query($con, $sql1);
		if($nome != '')	$profile = mysqli_query($con, $sql2);
		$profileid = mysqli_query($con, $sql3);
		$sql4 = "UPDATE usuario SET perfil_id = '".mysqli_fetch_array($profileid)[0]."' WHERE email= '$email'";
		$done = mysqli_query($con,$sql4);
	}
		
	
	$result_foto = mysqli_query($con, "SELECT foto FROM perfil NATURAL JOIN usuario WHERE email = '$email' AND perfil.id = usuario.perfil_id");
	$foto = mysqli_fetch_object($result_foto);
	$profile = mysqli_query($con, "SELECT id FROM perfil NATURAL JOIN usuario WHERE email = '$email' AND perfil.id = usuario.perfil_id");
	$profile_id = mysqli_fetch_array($profile)[0];
	if (mysqli_fetch_array(mysqli_query($con, "SELECT tipo FROM tipousuario NATURAL JOIN usuario WHERE email = '$email' AND tipousuario.id = 1"))[0] == "1"){$mostrar = "mostrar";}else{ $mostrar = "nao_mostrar";}


	
	/*---Dados de template---*/
	$tpl = new Template("../template.html");
	$tpl2 = new Template("html_template/visualiza.html");
	$tpl2->data = date("Y-m-d");
	$tpl->painel_active = "active";
	$tpl->ende = "../";
	$tpl->painel_color = "color: #000000;";
	$tpl->perfil_active = "color: #ffffff;";
	$tpl->labeltitle = " - Membro Adicionado";
	$tpl2->OPT_BLOCK = $opt_block;
	$tpl2->nome = $nome;
	$tpl2->data = $data_admissao;
	$tpl2->email = $email;
	$tpl2->github = $github;
	$tpl2->linkedin = $linkedin;
	$tpl2->foto = "getImage.php?PicNum=$profile_id";
	$tpl2->disabled = "disabled";
	$tpl2->visibility = "vis-total";
	$tpl2->visibil = "inv-total";
	$tpl2->voltar = "painel_adm.php";
	$tpl2->TITLE = "Usuário novo adicionado.";
	$tpl->CONTENT = $tpl2->parse();
	$tpl->so_este = "valor";

	/*---Carregamento do modelo---*/


	$tpl->show();
	}else{
		$tpl = new Template("../template.html");
		$tpl->painel_active = "active";
		$tpl->ende = "../";
		$tpl->painel_color = "color: #000000;";
		$tpl->perfil_active = "color: #ffffff;";
		$tpl->CONTENT = "<h2>Error 404<h2><h3>Você não tem nível de permissão suficiente para acessar esta página.</h3>";
		//$tpl->value = "anti-valor";
		$tpl->show();
	}
	
}
?>