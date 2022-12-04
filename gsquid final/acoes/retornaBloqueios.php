<?php
include("../includes/conexao.php");
$conexao = new db();


if(isset($_POST['acao']) AND isset($_POST['sites_bloqueados'])) {
	if($_POST['acao'] == "alterar") {							
		$dados = array(
			'site' => $_POST['site']
		);

		$id = $_POST['id'];
		if($conexao->altera("sites_bloqueados", $dados, "id", $id)){
			echo "<h4>Site alterado com sucesso!</h4>";
		} else {
			echo "<h1>Erro</h1>";
		}
	} elseif($_POST['acao'] == "inserir") {
		$dados = array(
			'site' => $_POST['site'],
			'tipo' => 'url_regex'

		);

		if($conexao->insere("sites_bloqueados", $dados)) {
			echo "<h4>Site inserido com sucesso!</h4>";
		} else {
			echo "<h1>Erro</h1>";
		}
	}	
}


if(isset($_POST['acao']) AND isset($_POST['palavras_bloqueadas'])) {	
	if($_POST['acao'] == "alterar") {							
		$dados = array(
			'palavra' => $_POST['palavra']
		);

		$id = $_POST['id'];
		if($conexao->altera("palavras_bloqueadas", $dados, "id", $id)){
			echo "<h4>Palavra alterada com sucesso!</h4>";
		} else {
			echo "<h1>Erro</h1>";
		}
	} else {
		$dados = array(
			'palavra' => $_POST['palavra'],
			'tipo' => 'url_regex'

		);

		if($conexao->insere("palavras_bloqueadas", $dados)) {
			echo "<h4>palavra inserida com sucesso!</h4>";
		} else {
			echo "<h1>Erro</h1>";
		}
	}	
}


if(isset($_POST['acao']) AND isset($_POST['sites_liberados'])) {	
	if($_POST['acao'] == "alterar") {							
		$dados = array(
			'site' => $_POST['site']
		);

		$id = $_POST['id'];
		if($conexao->altera("sites_liberados", $dados, "id", $id)){
			echo "<h4>Palavra alterada com sucesso!</h4>";
		} else {
			echo "<h1>Erro</h1>";
		}
	} else {
		$dados = array(
			'site' => $_POST['site'],
			'tipo' => 'url_regex'

		);

		if($conexao->insere("sites_liberados", $dados)) {
			echo "<h4>Site inserido com sucesso!</h4>";
		} else {
			echo "<h1>Erro</h1>";
		}
	}	
}




if(isset($_GET['acao'])) {
	if($_GET['acao'] == "excluir" AND isset($_GET['palavras_bloqueadas']) AND isset($_GET['id'])) {			
		if($conexao->deleta("palavras_bloqueadas", "id", $_GET['id'])){			
			echo "<h4>Palavra excluída com sucesso!</h4>";
		} else {
			echo "<h4>Erro</h4>";
		}		
	} elseif ($_GET['acao'] == "excluir" AND isset($_GET['sites_bloqueados']) AND isset($_GET['id'])) {
		if($conexao->deleta("sites_bloqueados", "id", $_GET['id'])){
			echo "<h4>Site excluído com sucesso!</h4>";
		} else {
			echo "<h4>Erro</h4>";
		}
	} elseif ($_GET['acao'] == "excluir" AND isset($_GET['sites_liberados']) AND isset($_GET['id'])) {
		if($conexao->deleta("sites_liberados", "id", $_GET['id'])){
			echo "<h4>Site excluído com sucesso!</h4>";
		} else {
			echo "<h4>Erro</h4>";
		}
	}

}
	