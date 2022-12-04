<?php
include("../includes/valida_sessao.php");
include("../includes/conexao.php");
include("../includes/config.php");
include("../includes/squid-class.php");

$conexao = new db();
/********************* Cria o arquivo de ACL do professor ************************/
// É possível otimizar gravando tudo em um único laço de repetição
$perfil = "professor";
$dados = $conexao->listaWhere("computadores", "perfil", $perfil); 
foreach ($dados as $key => $value) {	
	$ips_professores[] = $value['ip'] . "\n"; 
}
$arquivo = fopen($acls_pasta . $perfil, "w") or die ("Não foi possível abrir o arquivo!");
foreach ($ips_professores as $key => $ip) {
	fwrite($arquivo, $ip) or die ("Não foi possível escrever no arquivo!");  	
}
fclose($arquivo);

/********************* Cria os arquivos das ACLs das salas **********************/

$dados = $conexao->pesquisaPersonalizada("SELECT c.hostname, c.mac, c.ip, c.perfil, s.nome FROM computadores c JOIN salas s ON c.cod_sala = s.cod_sala");
foreach ($dados as $key => $value) {	
	$salas[$value['nome']][] = $value['ip'] . "\n"; 
}
foreach ($salas as $sala => $ips_array) {	
	$arquivo = fopen($acls_pasta . $sala, "w") or die ("Não foi possível abrir o arquivo!");
	foreach ($ips_array as $key => $ip) {
		fwrite($arquivo, $ip) or die ("Não foi possível escrever no arquivo!");  	
	}
	fclose($arquivo);
}

