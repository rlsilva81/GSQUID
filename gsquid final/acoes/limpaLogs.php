<?php
include("../includes/valida_sessao.php");
include("../includes/valida_nivel.php");
include("../includes/conexao.php");



if(isset($_POST['log'])) {
	$conexao = new db();
	if($_POST['log'] == "log_logins") {
		if($conexao->deletaTudo("log_logins")) {
			echo "<span> Logs deletados com sucesso! </span>";
		} else {
			echo "<span>Erro ao deletar os logs! </span>";
		}
	} elseif ($_POST['log'] == "log_tentativas") {
		if($conexao->deletaTudo("log_tentativas")) {
			echo "<span> Logs deletados com sucesso! </span>";
		} else {
			echo "<span>Erro ao deletar os logs! </span>";
		}
	} elseif ($_POST['log'] == "log_dashboard") {
		if($conexao->deletaTudo("log_dashboard")) {
			echo "<span> Logs deletados com sucesso! </span>";
		} else {
			echo "<span>Erro ao deletar os logs! </span>";
		}
	}
}
