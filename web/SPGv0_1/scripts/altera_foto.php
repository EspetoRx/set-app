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
	$prioridade = mysqli_query($con, "SELECT tipo FROM usuario WHERE email = '$login'");
	$tipo = mysqli_fetch_array($prioridade)[0];
	if($tipo==1){
	
	/*--------ALTERA FOTO----------------*/
	/*-ARQUIVOS DE IMAGEM-*/
	$msg = false;

	if(isset($_FILES['file'])){
		$extensao = strtolower(substr($_FILES['file']['name'], -4));
		$novo_nome = md5(time()).$extensao;
		$diretorio = "upload/";
		$nome_completo = $diretorio.$novo_nome;
		move_uploaded_file($_FILES['file']['tmp_name'], $nome_completo);

		$tamanhoImg = filesize($nome_completo); 
 
    	$mysqlImg = addslashes(fread(fopen($nome_completo, "r"), $tamanhoImg)); 
	
		$sql_code = "UPDATE perfil NATURAL JOIN usuario SET arquivo = '$novo_nome', data = NOW(), foto = '$mysqlImg' WHERE usuario.email = '$email' AND perfil.id = usuario.perfil_id";

		if(mysqli_query($con, $sql_code)){
			$msg = "Arquivo enviado com sucesso!";
		}else{
			$msg = "Falha ao enviar o arquivo";
		}
	}

	/*- FIM ARQUIVOS DE IMAGEM-*/
		
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
	if (mysqli_fetch_array(mysqli_query($con, "SELECT tipo FROM tipousuario NATURAL JOIN usuario WHERE email = '$email' AND tipousuario.id = 1"))[0] == "1"){$mostrar = "mostrar";}else{ $mostrar = "nao_mostrar";}
		
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
	$tpl2->nome = mysqli_fetch_array($nome)[0];
	$tpl2->github = mysqli_fetch_array($git)[0];
	$tpl2->data = mysqli_fetch_array($data)[0];
	$tpl2->linkedin = mysqli_fetch_array($linkedin)[0];
	$tpl2->email = $email;
	$tpl2->OPT_BLOCK = $opt_block;
	$tpl2->foto = "getImage.php?PicNum=$profile_id";
	$tpl2->disabled = "";
	$tpl2->visibility = "inv-total";
	$tpl2->visibil = "vis-total";
	$tpl2->altera = "vis_no_change";
	$tpl2->act = "grava_alteracoes";
	$tpl2->buttao = "submit";
	$tpl2->labelbtn = "Alterar";
	$tpl2->TITLE = "Alteração de membro - Avatar alterado";
	$tpl2->voltar = "altera.php";
	//$tpl2->emailonly = "Disabled";
	$tpl->labeltitle = " - Avatar alterado";
	$tpl2->somenor = "somenor";
	$tpl2->msg = $msg;
	$tpl->CONTENT = $tpl2->parse();
	//$tpl->value = "valor";

		
	/*---Carregamento do modelo---*/
	$tpl->show();
	}elseif($email == $login){
		/*--------ALTERA FOTO----------------*/
		/*-ARQUIVOS DE IMAGEM-*/
		$msg = false;

		if(isset($_FILES['file'])){
			$extensao = strtolower(substr($_FILES['file']['name'], -4));
			$novo_nome = md5(time()).$extensao;
			$diretorio = "upload/";
			$nome_completo = $diretorio.$novo_nome;
			move_uploaded_file($_FILES['file']['tmp_name'], $nome_completo);
			$tamanhoImg = filesize($nome_completo); 
		 
		    $mysqlImg = addslashes(fread(fopen($nome_completo, "r"), $tamanhoImg)); 
			
			$sql_code = "UPDATE perfil NATURAL JOIN usuario SET arquivo = '$novo_nome', data = NOW(), foto = '$mysqlImg' WHERE usuario.email = '$login' AND perfil.id = usuario.perfil_id";

			if(mysqli_query($con, $sql_code)){
				$msg = "Arquivo enviado com sucesso!";
			}else{
				$msg = "Falha ao enviar o arquivo";
			}
		}

		/*- FIM ARQUIVOS DE IMAGEM-*/

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

		/*-------------------RECUPERA LISTA DE TIPOS DE USUARIOS-------------------*/
		$opt_block = "<select id='tipo' name='tipo' class='form-control' disabled>\n";
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
		$template2 = new Template("html_template/visualiza.html");
		$tpl->active = "active";
		$tpl->ende = "../";
		$tpl->painel_color = "color: #ffffff;";
		$tpl->perfil_active = "color: #000000;";
		if($tipo != 1){
			$tpl->mostrar = "nao_mostrar";
		}
		$template2->visibility = "inv-total";
		$template2->visibil = "vis-total";
		$template2->labelbtn = "Alterar";
		/*VARIAVEIS DE MEMBRO*/
		$template2->nome = mysqli_fetch_array($nome)[0];
		$template2->data = mysqli_fetch_array($data)[0];
		$template2->github = mysqli_fetch_array($git)[0];
		$template2->linkedin = mysqli_fetch_array($linkedin)[0];
		$template2->email = $login;
		$template2->foto = "getImage.php?PicNum=$profile_id";
		$template2->OPT_BLOCK = $opt_block;

		$template2->emailonly = "disabled";
		$template2->data_admin = "disabled";
		$template2->somenor = "somenor";
		$template2->voltar = "../session.php";
		//$template2->visibil = "vis-total";
		$tpl->l_perfil = " - Alterando o perfil";
		$tpl->CONTENT = $template2->parse();
		//$tpl->value = "valor";

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