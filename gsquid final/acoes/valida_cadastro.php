<?php

include("../includes/valida_sessao.php");
include("../includes/valida_nivel.php");


if(isset($_POST)){
	if(isset($_POST['cadastroComputador'])) {
		$hostname = $_POST['hostname'];
		$mac = $_POST['mac'];
		$ip = $_POST['ip'];
		$sala = $_POST['sala'];
		if($sala == NULL OR $sala == "") {
			echo "A sala não pode estar vazia";
			exit;
		}
		$perfil = $_POST['perfil'];
		include("../includes/conexao.php");

		$conexao = new db();
		
		$cod_sala = $conexao->listaWhere("salas","nome",$sala);
		
		$dados = array (
			'hostname' => $hostname,
			'mac' => $mac,
			'ip' => $ip,
			'cod_sala' => $cod_sala[0]['cod_sala'],
			'perfil' => $perfil,
		);

		if($conexao->insere('computadores', $dados)) {
			echo "<h4>Computador cadastrado com sucesso!</h4>";
		} else {
			echo "Erro";
		}

	} elseif(isset($_POST['cadastroSala'])) {
		$sala = array(
			'nome' => $_POST['sala'],
			'acl_sites_bloqueados' => $_POST['sitesbloqueados']
		);
		include("../includes/conexao.php");
		$conexao = new db();	

		if($conexao->insere('salas', $sala)) {
			echo "<h4>Sala cadastrada com sucesso!</h4>";
		} else {
			echo "Erro";
		}
	}
	

}