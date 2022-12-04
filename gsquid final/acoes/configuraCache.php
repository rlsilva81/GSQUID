<?php
include("../includes/conexao.php");
$conexao = new db();

if(isset($_POST['acao']) AND isset($_POST['site_cache'])) {
	if($_POST['acao'] == "alterar") {							
		$dados = array(
			'site' => $_POST['site']
		);

		$id = $_POST['id'];
		if($conexao->altera("cache_excecoes", $dados, "id", $id)){
			echo "<h4>Site alterado com sucesso!</h4>";
		} else {
			echo "<h1>Erro</h1>";
		}
	} elseif($_POST['acao'] == "inserir") {
		$dados = array(
			'site' => $_POST['site']
		);

		if($conexao->insere("cache_excecoes", $dados)) {
			echo "<h4>Site inserido com sucesso!</h4>";
		} else {
			echo "<h1>Erro</h1>";
		}
	} elseif($_POST['acao'] == "excluir") {
		
		if($conexao->deleta("cache_excecoes", "id", $_POST['id'])) {
			echo "<h4>Site excluído com sucesso!</h4>";
		} else {
			echo "<h1>Erro</h1>";
		}
	}	
} else {
	echo "Nenhum parâmetro";
}
