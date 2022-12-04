<?php
include("../includes/config.php");
include("../includes/conexao.php");
$conexao = new db();
if(isset($_POST['acao'])) {
	if($_POST['acao'] == 'alterar') {
		
		$_POST['hostname'] = preg_replace('/\s+/', '', $_POST['hostname']);
		
		$dados = array(
			'hostname' => $_POST['hostname'],
			'mac' => $_POST['mac'],
			'ip' => $_POST['ip'],
			'perfil' => $_POST['perfil'],
		);
		$sala = $_POST['sala'];
		$cod_computador = $_POST['cod_computador'];
		
		if($conexao->altera('computadores', $dados, 'cod_computador', $cod_computador)) {
			//header("Location: acoes/atualizaTudo.php?voltar=salas&sala={$sala}");
			echo "Computador alterado com sucesso!";
			
		} else {
			echo "Nenhuma alteração feita";
			//print_r($cod_computador);
		}
	}
}

	//echo "<h1>Teste</h1>";

if(isset($_GET['acao'])) {

	if($_GET['acao'] == 'excluir') {

		if($conexao->deleta("computadores","cod_computador", $_GET['cod_computador'])) {
			//header("Location: acoes/atualizaTudo.php?voltar=salas");	
			echo "Computador excluído com sucesso!";									
		} else {
			echo "Nenhum computador excluído";
		}
	}
}