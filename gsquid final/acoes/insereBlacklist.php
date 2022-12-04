<?php

include("../includes/config.php");

$ip =  $_POST['ip'];




if(isset($_POST['acao'])) {

	include("../includes/conexao.php");
	$conexao = new db();

	if($_POST['acao'] == "liberar") {
		if($conexao->deleta("blacklist", "ip", $ip)) {
			unlink("{$mensagens_pasta}" . $ip . ".html");		    
			echo "<span>Endereço {$ip} liberado com sucesso!</span>";
		}
	} elseif ($_POST['acao'] == "bloquear") {
		$mensagem = $_POST['mensagem'];
		$resultado = $conexao->lista("blacklist");

		foreach ($resultado as $key => $value) {
			if($value['ip'] == $ip) {
				echo "<span>O endereço {$ip} já consta na blacklist!</span>";
				exit;
			}
		}



		$pagina = "
			<!DOCTYPE html>
			<html lang='pt-br'>
			<head>
				<title>Estatísticas Detalhes</title>
				<meta charset='utf-8'>
				<style>
					body {
						background-color: #eee;
					}
					header {
						max-width: 900px;
						height: 100px;
						margin: auto;
						margin-top: 50px;
						text-align: center;
					}
					header img {
						width: 100px;
					}
					#container {
						max-width: 900px;
						height: 500px;
						margin: auto;
					}
					h1 {
						font-size: 35px;
						color: red;
						text-align: center;
					}
				</style>
			</head>
			<body>
				<header>
					<img src='". $mensagens_url ."squid.png'>
					<img src='". $mensagens_url ."senac.png'>			
				</header>
				<div id='container'>
					<h1>" . htmlspecialchars($mensagem) . "</h1>
				</div>
			</body>
			</html>

		";




		$arquivo = fopen($mensagens_pasta . $ip . ".html", "w") or die ("Não foi possível abrir o arquivo!");
		fwrite($arquivo, $pagina) or die ("Não foi possível escrever no arquivo!");
		fclose($arquivo);

		$dados = array(
			'ip' => $ip
			);

		if($conexao->insere('blacklist', $dados)) {	
			echo "<span>Endereço {$ip} inserido na blacklist com sucesso!</span>";
		}
	}
}
