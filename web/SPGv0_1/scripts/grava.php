<?php 
/* esse bloco de código em php verifica se existe a sessão, pois o usuário pode
 simplesmente não fazer o login e digitar na barra de endereço do seu navegador 
o caminho para a página principal do site (sistema), burlando assim a obrigação de 
fazer um login, com isso se ele não estiver feito o login não será criado a session, 
então ao verificar que a session não existe a página redireciona o mesmo
 para a index.php.*/
header("Content-type: text/html; charset=utf-8");
session_start();
if((!isset ($_SESSION['login']) == true) and (!isset ($_SESSION['senha']) == true))
{
  unset($_SESSION['login']);
  unset($_SESSION['senha']);
  header('location:../index.php');
  }

require_once("raelgc/view/Template.php");
use raelgc\view\Template;

$login = $_SESSION['login'];

$tpl = new Template("../template.html");


$con = mysqli_connect("us-cdbr-iron-east-01.cleardb.net", "b4374046414e9f", "05e528e1") or die  ("Sem conexão com o servidor");
$select = mysqli_select_db($con, "heroku_7d1bac14eb9e1ae") or die("Sem acesso ao DB, Entre em contato com o Administrador.");

/*-ARQUIVOS DE IMAGEM-*/
$msg = false;

if(isset($_FILES['file'])){
	$extensao = strtolower(substr($_FILES['file']['name'], -4));
	$novo_nome = md5(time()).$extensao;
	$diretorio = "upload/";
	$nome_completo = $diretorio.$novo_nome;
	move_uploaded_file($_FILES['file']['tmp_name'], $nome_completo);
	
	$sql_code = "UPDATE perfil NATURAL JOIN usuario SET arquivo = '$novo_nome', data = NOW() WHERE usuario.email = '$login' AND perfil.id = usuario.perfil_id";

	if(mysqli_query($con, $sql_code)){
		$msg = "Arquivo enviado com sucesso!";
	}else{
		$msg = "Falha ao enviar o arquivo";
	}
}

/*- FIM ARQUIVOS DE IMAGEM-*/

$nome = mysqli_query($con, "SELECT perfil.nome FROM perfil NATURAL JOIN usuario WHERE email = '$login' AND perfil.id = usuario.perfil_id");

$data = mysqli_query($con, "SELECT data_admissao FROM perfil NATURAL JOIN usuario WHERE email = '$login' AND perfil.id = usuario.perfil_id");
$git = mysqli_query($con, "SELECT github FROM perfil NATURAL JOIN usuario WHERE email = '$login' AND perfil.id = usuario.perfil_id");
$linkedin = mysqli_query($con, "SELECT linkedin FROM perfil NATURAL JOIN usuario WHERE email = '$login' AND perfil.id = usuario.perfil_id");
$type = mysqli_query($con, "SELECT descricao FROM tipousuario NATURAL JOIN usuario WHERE email = '$login' AND tipousuario.id = usuario.perfil_id");
$foto = mysqli_query($con, "SELECT arquivo FROM perfil NATURAL JOIN usuario WHERE email = '$login' AND perfil.id = usuario.perfil_id");

if (mysqli_fetch_array(mysqli_query($con, "SELECT tipo FROM tipousuario NATURAL JOIN usuario WHERE email = '$login' AND tipousuario.id = 1"))[0] == "1"){$mostrar = "mostrar";}else{ $mostrar = "nao_mostrar";}

mysqli_close($con);

$tpl->CONTENT = "<div class=\"row\">
				<div class=\"col-md-2\">
					<center><img class=\"foto\" src='upload/".mysqli_fetch_array($foto)[0]."' width=\"100%\" /></center>
					<form action='grava.php' method='post' enctype='multipart/form-data'>
					<center><input type=\"file\" name=\"file\" id=\"file\" class=\"inputfile\" onChange='this.form.submit()'/> 
						<label for=\"file\">Alterar avatar</label></center>
					</form>
					<br><center>".(($msg!=false)?$msg:"")."</center>
				</div>
				<div class=\"col-md-5\">
					<div class=\"row\">
						<div class=\"col-md-12\">
							<h4>Nome</h4>
						</div>
						<div class=\"col-md-12\">
							<p class=\"campos\">&nbsp;".mysqli_fetch_array($nome)[0]."</p>
						</div>
						<br>
						<div class=\"col-md-12\">
							<h4>Data de admissão</h4>
						</div>
						<div class=\"col-md-12\">
							<p class=\"campos\">&nbsp;".date('d/m/Y', strtotime(mysqli_fetch_array($data)[0]))."</p>
						</div>
						<div class=\"col-md-12\">
							<h4>E-mail</h4>
						</div>
						<div class=\"col-md-12\">
							<p class=\"campos\">&nbsp;$login</p>
						</div>
					</div>
				</div>
				<div class=\"col-md-5\">
					<div class=\"row\">
						<div class=\"col-md-12\">
							<h4>Github</h4>
						</div>
						<div class=\"col-md-12\">
							<p class=\"campos\">&nbsp;".mysqli_fetch_array($git)[0]."</p>
						</div>
						<br>
						<div class=\"col-md-12\">
							<h4>Linked-in</h4>
						</div>
						<div class=\"col-md-12\">
							<p class=\"campos\">&nbsp;".mysqli_fetch_array($linkedin)[0]."</p>
						</div>
						<div class=\"col-md-12\">
							<h4>Tipo de Usuario</h4>
						</div>
						<div class=\"col-md-12\">
							<p class=\"campos\">&nbsp;".mysqli_fetch_array($type)[0]."</p>
						</div>
						<div class=\"col-md-12\">
							<form method=\"post\" action=\"logout.php\">
								<button type=\"submit\" class=\"btn btn-lg float-right\" style=\"background-color: #285273; color: #ffffff;\" align=\"left\">Logout</button>
							</form>
						</div>
					</div>
				</div>
			</div>";

$tpl->mostrar = $mostrar;
$tpl->active = "active";
$tpl->perfil_active = "color: #000000;";
$tpl->painel_color = "color: #ffffff;";
$tpl->ende = "../";

$tpl->show();

?>
