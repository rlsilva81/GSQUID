<?php

if(isset($_GET['acao'])) {
	include("../includes/conexao.php");
	$conexao = new db();
	if($_GET['acao'] == 'salvar' AND isset($_GET['salaOld'])) {
		
		$_GET['sala'] = preg_replace('/\s+/', '', $_GET['sala']);
		$dados = array(
			'nome' => $_GET['sala']
		);
		
		if($conexao->altera("salas", $dados, "nome", $_GET['salaOld'])){
			echo "Sala alterada";
		} else {
			echo "Não foi possível atualizar essa sala";
		}
	}


	if($_GET['acao'] == 'excluir') {
		$resultado = $conexao->listaWhere("salas", "nome", $_GET['sala']);
		$cod_sala = $resultado[0]['cod_sala'];
		if($conexao->listaWhere("computadores", "cod_sala", $cod_sala)) {
			echo "Não é possível excluir esta sala pois existem computadores registrados nela";
		} else {
			if($conexao->deleta("salas", "nome", $resultado[0]['nome'])){
				echo "Sala excluída com sucesso!";
			}
		}	
	}
}

