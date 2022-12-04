<?php
session_start();
if(!isset($_POST['login']) OR !isset($_POST['senha']) OR empty($_POST['login']) OR empty($_POST['senha']) OR strtolower(end(explode("/", $_SERVER['HTTP_REFERER']))) != "login.php"){
	exit;
}


$login = $_POST['login'];
$senha = sha1($_POST['senha']);

include("../includes/conexao.php");

$conexao = new db();


$dados = array (
		'login' => $login,
		'senha' => $senha
	);
if($conexao->autenticacao("usuarios", $dados)) {
	echo 1;

	$dados = $conexao->listaWhere("usuarios", "login", $login);
	$_SESSION['nivel'] = $dados[0]['nivel'];
	$_SESSION['login'] = $login;

	include("../includes/log_logins.php");

	if(isset($_SESSION['erro_login'])) {
		unset($_SESSION['erro_login']);
	}

} else {
	if(isset($_SESSION['erro_login'])){
		if($_SESSION['erro_login'] < 2){
			$_SESSION['erro_login']++;
		} else {
			sleep(5);
			unset($_SESSION['erro_login']);
		}
	} else {
		$_SESSION['erro_login'] = 1;
	}
	
	$log = array(
		'ip' => $_SERVER['REMOTE_ADDR'],
		'hora' => time(),
		'login' => $login
	);
	$conexao->insere("log_tentativas", $log);
	echo FALSE;

}
