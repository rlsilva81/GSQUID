<?php

include('includes/config.php');

if(isset($_POST['hosts']) AND isset($_POST['salas']) AND isset($_POST['squid_log']) AND isset($_POST['titulo'])) {
	// Cria o nome do relatório (mesmo nome do log com extensão .html)
	$relatorio = explode("/", $_POST['squid_log']);
	$relatorio = end($relatorio) . ".html";

	// Se o relatório não existir então é criado
	if(!file_exists($pastaTemp . "relatorios/" . $relatorio)){
		$arquivo = fopen($pastaTemp . "relatorios/{$relatorio}", "w") or die("Não é possível escrever no arquivo");
		fwrite($arquivo, $_POST['titulo']);
		fwrite($arquivo, "<div id='estatisticaHosts'>");
		fwrite($arquivo, $_POST['hosts']);
		fwrite($arquivo, "</div>");
		fwrite($arquivo, "<div id='estatisticaSalas'>");
		fwrite($arquivo, $_POST['salas']);
		fwrite($arquivo, "</div>");
		fclose($arquivo);
		
	}	
	// Retorna o conteúdo do relatório
	include($pastaTemp . "relatorios/" . $relatorio);

	
	
}
