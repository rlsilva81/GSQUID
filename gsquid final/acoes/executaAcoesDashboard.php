<?php
include("../includes/valida_sessao.php");
include("../includes/config.php");
include("../includes/conexao.php");
$conexao = new db();	

if(isset($_GET['sala']) && isset($_GET['acao'])) {
	if($_GET['acao'] == "filtrar") {
		$acl_sites_bloqueados = 1;
		$acl_palavras_bloqueadas = 1;
		$bloqueio_total = 0;
	} elseif ($_GET['acao'] == "liberar") {
		$acl_sites_bloqueados = 0;
		$acl_palavras_bloqueadas = 0;
		$bloqueio_total = 0;
	} elseif ($_GET['acao'] == "bloquear_internet") {
		$acl_sites_bloqueados = 1;
		$acl_palavras_bloqueadas = 1;
		$bloqueio_total = 1;
	} else {		
		exit;
	}
	$dados = array(
		'acl_sites_bloqueados' => $acl_sites_bloqueados,
		'acl_palavras_bloqueadas' => $acl_palavras_bloqueadas,
		'bloqueio_total' => $bloqueio_total
	);
	$sala = $conexao->listaWhere("salas", "nome", $_GET['sala']);
	if($sala[0]['acl_sites_bloqueados'] == $acl_sites_bloqueados AND $sala[0]['acl_palavras_bloqueadas'] == $acl_palavras_bloqueadas AND $sala[0]['bloqueio_total'] == $bloqueio_total) {
		echo "Ação já realizada";
		exit;
	}

	if($conexao->altera("salas",$dados,"nome",$_GET['sala'])){			
		$cod_usuario = $conexao->listaWhere("usuarios","login", $_SESSION['login']);		
		$dados = array(
		'id_usuario' => $cod_usuario[0]['id'],
		'acao' => $_GET['acao'],
		'sala' => $_GET['sala'],
		'hora' => time(),
		'ip' => $_SERVER['REMOTE_ADDR']
		);		
		$conexao->insere("log_dashboard",$dados);		
		echo "Ação realizada";
	} else {
		echo "Não foi possível executar a ação";
	}
}