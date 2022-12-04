<?php		
ini_set('memory_limit', '1024M');		
include("../includes/squid-class.php");
include("../includes/config.php");

if(isset($_GET['squid_log'])) {
	$arquivo_log = $_GET['squid_log'];

	$relatorio = explode("/", $_GET['squid_log']);
	$relatorio = end($relatorio) . ".html";
	if(file_exists($pastaTemp . "relatorios/" . $relatorio)){
		include($pastaTemp . "relatorios/" . $relatorio);
		exit;
	}
}



$logs = new squid($arquivo_log);
// Lista todas as linhas do arquivo access.log e retorna cada linha em um indice do array
$resultado = $logs->listaTudo();
// Classe utilizada para extrair as linhas que possuem determinada range de IPs
$estatisticas = new estatisticas($range_ips_regex, $resultado);
/* Método que retorna todas a infromações organizadas das linhas que possuem GET e CONNECT
Exemplo de retorno (url,data,bytes,flag):
$acessos[172.16.0.2][0] => 'http://www.senacrs.com.br,19/03/2016 20:12:07,2655,TCP_MISS/200'
$acessos[172.16.0.2][1] => ...
*/
$acessos = $estatisticas->capturaInfo();



/* Método utilizado para calcular a banda utilzada por cada host e a banda total
Exemplo de retorno:
$host[172.16.0.2] => 27.15 MBytes
$host[172.16.0.3] => 17.81 KBytes
[banda_total] => 27.17 MBytes
*/

include("../includes/conexao.php");
$conexao = new db();

$host = $estatisticas->calculaBanda($acessos);							
echo "<div id='estatisticaHosts'>";
echo "<table class='tabela3 ordenadoBanda'><tr><th colspan='4'>Consumo de banda por host</th></tr><tr><th>Endereço IP</th><th>Banda</th><th>Sala</th><th>Acessos</th><th>Ação</th></tr>";

$contador = 0;
foreach ($host as $ip => $banda_consumida) {	
	if($ip != "banda_total") {
		
		$computador = $conexao->listaWhere("computadores","ip",$ip);	
		$contador++;

		if(isset($computador[0]['cod_sala'])) {
			$sala = $conexao->listaWhere("salas","cod_sala", $computador[0]['cod_sala']);
			$link = "<a target='_blank' href='salas.php?destaque={$ip}&sala=" . $sala[0]['nome'] . "'>{$ip}</a>";
			$sala = "<span class='liberado'>{$sala[0]['nome']}</span>";
		} else {
			$link = $ip;
			$sala = "<span class='bloqueado'>Não cadastrado</span>";
		}
		if($blacklist = $conexao->listaWhere("blacklist","ip",$ip)) {
			$link_blacklist = "<a class='blacklist' href='blacklist.php?ip={$ip}&acao=liberar'><img width='30px' title='liberar' src='imagens/liberado.svg'></a>";
			echo "<tr class='bloqueado'>";
		} else {
			$link_blacklist = "<a class='blacklist' href='blacklist.php?ip={$ip}&acao=bloquear'><img width='30px' title='bloquear' src='imagens/bloqueado.svg'></a>";
			echo "<tr>";
		}
		
		echo "<td>{$link}</td>
				<td>{$banda_consumida}</td>
				<td>" . $sala ."</td>
				<td><a target='_blank' href='estatisticasDetalhes.php?ip={$ip}

				";

		if(isset($_GET['squid_log'])) {
			echo "&squid_log=" . $_GET['squid_log'] . "'>Ver detalhes</a></td><td>{$link_blacklist}</td></tr>";
		} else {
			echo "'>Ver detalhes</a></td><td>{$link_blacklist}</td></tr>";
		}
					
	}						
}	
echo "<tr><th colspan='4'>Banda total: {$estatisticas->calculaBytes($host['banda_total'])} &nbsp; Total de computadores: {$contador}</th></tr>";
echo "</table></div>";
?>

<?php				

  
/*
$resultado = $conexao->lista("salas");
echo "<div id='estatisticaSalas'><table class='tabela1'><tr><th colspan='2'>Consumo de banda por sala</th></tr><tr><th>Sala</th><th>Banda</th></tr>";
foreach ($resultado as $key => $value) {							
	$sala = $value['nome'];
	$acls_salas= new squid($acls_pasta . $sala);
	$ips = $acls_salas->listaTudo();
	echo "<tr><td>{$sala}</td>";
	
	$banda[$sala] = 0;
	foreach ($ips as $key => $value) {
		if(isset($host[$value])) {
			$banda[$sala] += $host[$value];
		}
		
	}


	echo "<td>{$estatisticas->calculaBytes($banda[$sala])}</td>			
		</tr>";

}
echo "</table>";
*/


$resultado = $conexao->lista("salas");
foreach ($resultado as $key => $value) {							
	$sala = $value['nome'];
	$acls_salas= new squid($acls_pasta . $sala);
	$ips = $acls_salas->listaTudo();
		
	$banda[$sala] = 0;
	foreach ($ips as $key => $value) {
		if(isset($host[$value])) {
			$banda[$sala] += $host[$value];
		}
		
	}

}

arsort($banda);
echo "<div id='estatisticaSalas'><table class='tabela1'><tr><th colspan='2'>Consumo de banda por sala</th></tr><tr><th>Sala</th><th>Banda</th></tr>";

foreach ($banda as $key => $value) {	
	echo "<tr><td>{$key}</td>";
	echo "<td>{$estatisticas->calculaBytes($value)}</td>			
		</tr>";
}
echo "</table>";