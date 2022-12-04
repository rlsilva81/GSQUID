<?php
include("../includes/valida_sessao.php");
include("../includes/squid-class.php");
include("../includes/config.php");
include("../includes/conexao.php");

// Percorre a pasta das acls e excluir todos os arquivos
foreach(glob("{$acls_pasta}*") as $file) {
    unlink($file);        
}

$conexao = new db();



/********************** Cria arquivos das ACls dos sites bloqueados **********************/
$acl_nome = "sites_bloqueados";
$dados = $conexao->lista($acl_nome);
$arquivo = fopen($acls_pasta . $acl_nome, "w") or die ("Não foi possível abrir o arquivo!");
foreach ($dados as $key => $value) {	
	fwrite($arquivo, $value['site'] . "\n") or die ("Não foi possível escrever no arquivo!");
}
fclose($arquivo);

/********************** Cria arquivos das ACls dos sites liberados **********************/
$acl_nome = "sites_liberados";
$dados = $conexao->lista($acl_nome);
$arquivo = fopen($acls_pasta . $acl_nome, "w") or die ("Não foi possível abrir o arquivo!");
foreach ($dados as $key => $value) {	
	fwrite($arquivo, $value['site'] . "\n") or die ("Não foi possível escrever no arquivo!");
}
fclose($arquivo);

/********************** Cria arquivos das ACls das palavras bloqueadas **********************/
$acl_nome = "palavras_bloqueadas";
$dados = $conexao->lista($acl_nome);
$arquivo = fopen($acls_pasta . $acl_nome, "w") or die ("Não foi possível abrir o arquivo!");
foreach ($dados as $key => $value) {	
	fwrite($arquivo, $value['palavra'] . "\n") or die ("Não foi possível escrever no arquivo!");
}
fclose($arquivo);

/********************* Cria o arquivo de ACL do professor ************************/
// É possível otimizar gravando tudo em um único laço de repetição
$perfil = "professor";
$dados = $conexao->listaWhere("computadores", "perfil", $perfil); 
$arquivo = fopen($acls_pasta . $perfil, "w") or die ("Não foi possível abrir o arquivo!");
foreach ($dados as $key => $value) {
	fwrite($arquivo, $value['ip'] . "\n") or die ("Não foi possível escrever no arquivo!");  
}
fclose($arquivo);

/********************* Cria os arquivos das ACLs das salas **********************/

// Primeiro é criado os arquivo das acls das salas todos em branco (caso alguma sala não tem computador)
$dados = $conexao->lista("salas");
foreach ($dados as $key => $value) {
	$arquivo = fopen($acls_pasta . $value['nome'], "w") or die ("Não foi possível abrir o arquivo!");
}
fclose($arquivo);
// Preenche os arquivos de ACL com os IPs
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


/************************ Cria ACLs e http_access na ordem correta ************************/
$acls[] = "";
$acls[] = "####### ACLS PERSONALIZADAS ########";
$http_access[] = "";
$http_access[] = "####### HTTP_ACCESS #######";

// Cria ACLs e http_access para endereços IP na blacklist
// Deve estar acima das demais pois hosts na blacklist possuem seu acesso totalmente negado, incluindo professores
$acl_nome = "blacklist";
$dados = $conexao->lista($acl_nome);
foreach ($dados as $key => $value) {	
	// Cria ACL para o endereço ip específico
	$acls[] = "acl ip_". $value['ip'] ." src ". $value['ip'];
	// A opção deny info permite utilizar uma página personalizada de erro (Essa página é criada pela aplicação ao inserir um host na blacklist)
	$http_access[] = "deny_info " . $mensagens_url . $value['ip'] . ".html ip_" . $value['ip']; 
	// Nega totalmente o acesso ao endereço IP
	$http_access[] = "http_access deny ip_" . $value['ip'];	
}

// Cria as primeiras ACLs 
$acls[] = "acl professor src \"". $acls_pasta ."professor\"";
$acls[] = "acl sites_liberados url_regex -i \"". $acls_pasta ."sites_liberados\"";
$acls[] = "acl sites_bloqueados url_regex -i \"". $acls_pasta ."sites_bloqueados\"";
$acls[] = "acl palavras_bloqueadas url_regex -i \"". $acls_pasta ."palavras_bloqueadas\"";

// Criar o http_access para o professor liberando acesso total sempre, exceto se estiver na blacklist
$http_access[] = "http_access allow professor";

//Cria as ACLs e http_access das salas 
$dados = $conexao->lista("salas");
foreach ($dados as $key => $value) {
	$acls[] = "acl " . $value['nome'] . " src \"" . $acls_pasta . $value['nome'] . "\"";
	if($value['bloqueio_total']){
		$http_access[] = "http_access deny " . $value['nome'];
	} elseif($value['acl_sites_bloqueados']) {
		$http_access[] = "#http_access allow " . $value['nome'];
	} else {
		$http_access[] = "http_access allow " . $value['nome'];
	}	
}

// Últimos http_access
$http_access[] = "http_access allow sites_liberados";
$http_access[] = "http_access deny sites_bloqueados";
$http_access[] = "http_access deny palavras_bloqueadas"; 


/*************  Exceções do cache *******************/
// Cria o arquivo com as exceções
$acl_nome = "cache_excecoes";
$dados = $conexao->lista($acl_nome);
$arquivo = fopen($acls_pasta . $acl_nome, "w") or die ("Não foi possível abrir o arquivo!");
foreach ($dados as $key => $value) {	
	fwrite($arquivo, $value['site'] . "\n") or die ("Não foi possível escrever no arquivo!");
}
fclose($arquivo);
// Cria a ACL de exceções
$cache_excecoes[] = "acl nocache url_regex -i \"". $acls_pasta ."cache_excecoes\"";
$cache_excecoes[] = "cache allow all !nocache";
/*************  Exceções do log *******************/
// Cria o arquivo com as exceções
$acl_nome = "squidlog_excecoes";
$dados = $conexao->lista($acl_nome);
$arquivo = fopen($acls_pasta . $acl_nome, "w") or die ("Não foi possível abrir o arquivo!");
foreach ($dados as $key => $value) {	
	fwrite($arquivo, $value['site'] . "\n") or die ("Não foi possível escrever no arquivo!");
}
fclose($arquivo);
// Cria a ACL de exceções
$squidlog_excecoes[] = "acl squidlog_excecoes url_regex -i \"". $acls_pasta ."squidlog_excecoes\"";
$squidlog_excecoes[] = "log_access deny squidlog_excecoes";


/****************** Organiza a configuração e cria o arquivo squid.conf ************************/

// Une todos os arrays com a configuração do squid.conf na ordem correta
$arquivo_completo_squid = array_merge_recursive($default, $squidlog_excecoes, $acls, $http_access, $ultimas_linhas1, $cache_excecoes, $ultimas_linhas2);

// Cria finalmente o arquivo squid.conf
$arquivo = fopen($arquivo_squid, "w") or die ("Não foi possível abrir o arquivo!");
$return = 0;
foreach ($arquivo_completo_squid as $key => $linha) {
	fwrite($arquivo, $linha . "\n") or die ("Não foi possível escrever no arquivo!"); 
	$return = 1; 	
}
fclose($arquivo);

if($return) {
	echo "A configuração do squid foi atualizada com sucesso!";
} else {
	echo "Não foi possível atualizar a configuração do squid";
}

