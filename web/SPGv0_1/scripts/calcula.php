<?php
header("Content-type: text/html; charset=utf-8");
session_start();
require_once("raelgc/view/Template.php");
use raelgc\view\Template;

if((!isset ($_SESSION['login']) == true) and (!isset ($_SESSION['senha']) == true))
{
  unset($_SESSION['login']);
  unset($_SESSION['senha']);
  header('location:../index.php');
}

if(isset($_SESSION['login'])){
	$con = mysqli_connect("us-cdbr-iron-east-01.cleardb.net", "b4374046414e9f", "05e528e1") or die  ("Sem conexão com o servidor");
	$select = mysqli_select_db($con, "heroku_7d1bac14eb9e1ae") or die("Sem acesso ao DB, Entre em contato com o Administrador.");
	$data = $_POST['data'];
	$rendimentos = $_POST['rendimentos'];
	$taxa = $_POST['taxa'];
	$horas = $_POST['horas'];
	$despesas_ac = $_POST['despesas'];
	$preco = $_POST['preco'];
	$integrantes = $_POST['integrantes'];
	$recebidos = $_POST['recebidos'];
	//msqli_close($con);
	
	// Calcula dias  //
	$data_atual = date("Y-m-d");
	
	$pega_data_Time = new DateTime($data);
	$data_sistema_Time = new DateTime($data_atual);
	
	$pega_diferenca = $data_sistema_Time->diff($pega_data_Time);
	$dias_normais = ($pega_diferenca->y*364.25+$pega_diferenca->m*30+$pega_diferenca->d);
	$dias_uteis = ($pega_diferenca->y*364.25+$pega_diferenca->m*30+$pega_diferenca->d)/7*5;
	$horas_trabalhar = $horas*$integrantes*floor($dias_uteis);
	$valor_hora = number_format(((($rendimentos * (1 + $taxa/100) + (double)$despesas_ac) - (double)$recebidos)/$horas_trabalhar), 2, ',', '');
	// FIM CALCULO DE DIAS//
	
	$vbruto = ((($rendimentos * (1 + $taxa/100) + (double)$despesas_ac) - (double)$recebidos)/$horas_trabalhar);
	
	$sql = "INSERT INTO config_adm (data_fechamento, rendimentos_esperados, taxa_retorno, horas_trabalho, despesas, preco_hora, numero_integrantes, recebidos) VALUES ('$data','$rendimentos', ".(double)($taxa/100).", '$horas', '$despesas_ac', '$vbruto', '$integrantes', '$recebidos')";
	
	$tpl = new Template("../template.html");
	$tpl->painel_active = "active";
	$tpl->ende = "../";
	$tpl->painel_color = "color: #000000;";
	$tpl->perfil_active = "color: #ffffff;";
	$tpl->CONTENT = "
	<form method='post' action='".$_SERVER['PHP_SELF']."'>
	".(($con->query($sql) === TRUE)?"<center><p>Dados gravados com sucesso!</p></center>":"<center><p>Algo deu errado, os dados não foram gravados!</p></center>")."
	<div class='row'>
		<div class='col-md-8'>
				<div class='row'>
					<div class='col-md-6'>
						<div class='row order-sm-1'>
							<div class='col-md-12'>
								<h5>Prazo para fechamento do caixa:</h5>
							</div>
							<div class='col-md-12'>
								<input id='data' name='data' type='date' class='form-control' value='$data'/>
							</div>
						</div>
						<br>
						<div class='row order-sm-3'>
							<div class='col-md-12'>
								<h5>Rendimentos esperados dentro do prazo estabelecido:</h5>
							</div>
							<div class='esconder col-md-2'>
								<p style='padding-top: 7px;'>R$:</p>
							</div>
							<div class='col-md-10'>
								<input id='rendimentos' name='rendimentos' type='number' class='form-control' value='$rendimentos'/>
							</div>
						</div>
						<br>
						<div class='row order-sm-2'>
							<div class='col-md-12'>
								<h5>Valores recebidos:</h5>
							</div>
							<div class='col-md-2 esconder'>
								<p style='padding-top: 7px;'>R$:</p>
							</div>
							<div class='col-md-10'>
								<input id='recebidos' name='recebidos' type='text' class='form-control' value=".$recebidos.">
							</div>
						</div>
						<br>
						<div class='row'>
							<div class='col-md-12'>
								<h5>Preço da hora-trabalhada no ponto de equilíbrio</h5>
							</div>
							<div class='col-md-2 esconder'>
								<p style='padding-top: 7px;'>R$:</p>
							</div>
							<div class='col-md-10'>
								<input id='preco' name='preco' type='text' class='form-control' value=".(($valor_hora<=4.26)?"4,26":$valor_hora).">
							</div>
						</div>
						<br>
						<div class='row'>
							<div class='col-md-12'>
								<h5>Despezas esperadas dentro do prazo estabelecido:</h5>
							</div>
							<div class='col-md-2 esconder'>
								<p style='padding-top: 7px;'>R$:</p>
							</div>
							<div class='col-md-10'>
								<input id='despesas' name='despesas' type='text' class='form-control' value='$despesas_ac'/>
							</div>
						</div>
					</div>
					<div class='col-md-6'>
					<spam class='exibir'><br></spam>
						<div class='row'>
							<div class='col-md-12'>
								<h5>Taxa de retorno:</h5>
							</div>
							<div class='col-md-10'>
								<input id='taxa' name='taxa' type='text' class='form-control' value='$taxa'/>
							</div>
							<div class='col-md-2 esconder'>
								<p style='padding-top: 7px;'>%</p>
							</div>
						</div>
						<br>
						<div class='row'>
							<div class='col-md-12'>
								<h5>Horas-trabalho de membros por dia:</h5>
							</div>
							<div class='col-md-10'>
								<input id='horas' name='horas' type='text' class='form-control' value='$horas'/>
							</div>
							<div class='col-md-2 esconder'>
								<p style='padding-top: 7px;'>Horas/Dia</p>
							</div>
						</div>
						<br>
						<div class='row'>
							<div class='col-md-12'>
								<h5>Número de integrantes da equipe:</h5>
							</div>
							<div class='col-md-6'>
								<input id='integrantes' name='integrantes' type='text' class='form-control' value='$integrantes'/>
							</div>
							<div class='col-md-6 esconder'>
								<p style='padding-top: 7px;'>Integrantes</p>
							</div>
						</div>
						<br>
						<div class='row'>
							<div class='col-md-12 col-sm-push-10'>
								<h5>Dias úteis até o fim do prazo:</h5>
							</div>
							<div class='col-md-12' style='text-align: justify;'>
								A diferença de tempo é de ".$pega_diferenca->y." anos, ".$pega_diferenca->m." meses e ".$pega_diferenca->d." dias (".$dias_normais." dias). A quantidade de dias úteis prevista piso(365d,30d,d) é de ".floor($dias_uteis)." dias úteis (5 dias e não considera feriados). Número de horas a trabalhar é ".$horas_trabalhar.". Nesse pique podemos vender nossas horas por: R$ ".$valor_hora.".
								".
									(($valor_hora<=4.26)?"Este valor-hora se encontra abaixo do piso nacional garantido por lei (R$ 4,26 / 2017), sugiro usar R$ 4,26.":"Este valor-hora se encontra acima do piso nacional garantido por lei (R$ 4,26 / 2017), sugiro usar R$ ".$valor_hora.".")
								."
							</div>
						</div>
						<br>
					</div>
				</div>
			
		</div>
		<div class='col-md-4'>
			<div class='row'>
				<div class='col-md-12'>
					<center>
						<button type=\"submit\" class=\"btn btn-lg\" style=\"background-color: #285273; color: #ffffff;\" align=\"left\">Adicionar Membro</button>
					</center>
				</div>
				<div class='col-md-12'>
					<br>
					<center>
						<button type=\"submit\" class=\"btn btn-lg\" style=\"background-color: #285273; color: #ffffff;\" align=\"left\">Remover Membro</button>
					</center>
				</div>
				<div class='col-md-12'>
					<br>
					<center>
						<button type=\"submit\" class=\"btn btn-lg\" style=\"background-color: #285273; color: #ffffff;\" align=\"left\">Alterar Membro</button>
					</center>
				</div>
				<div class='col-md-12'>
					<br>
					<center>
						<button type=\"submit\" class=\"btn btn-lg\" style=\"background-color: #285273; color: #ffffff;\" align=\"left\">Calcular Valores<br>& Salvar Configurações</button>
					</center>
				</div>
			</div>
		</div>
	</div>
	</form>";
	$tpl->value = "valor";
	$tpl->show();
}
?>