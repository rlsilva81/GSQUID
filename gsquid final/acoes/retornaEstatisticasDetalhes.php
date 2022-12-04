<?php	
ini_set('memory_limit', '1024M');
if(isset($_GET['ip'])) {
	$ip = $_GET['ip'];
	include("../includes/squid-class.php");
	include("../includes/config.php");
	if(isset($_GET['squid_log'])) {
		$arquivo_log = $_GET['squid_log'];
	}
	$logs = new squid($arquivo_log);
	// Lista todas as linhas do arquivo access.log e retorna cada linha em um indice do array
	$resultado = $logs->listaTudo();
	// Classe utilizada para extrair as linhas que possuem determinada range de IPs
	$estatisticas = new estatisticas("/{$ip}/", $resultado);
	/* Método que retorna todas a infromações organizadas das linhas que possuem GET e CONNECT
	Exemplo de retorno (url,data,bytes,flag):
	$acessos[172.16.0.2][0] => 'http://www.senacrs.com.br,19/03/2016 20:12:07,2655,TCP_MISS/200'
	$acessos[172.16.0.2][1] => ...
	*/

	// ((GET)|(CONNECT)|(HEAD))
	$acessos = $estatisticas->capturaInfo();
	if($acessos) {
							
		if(isset($_GET['ordenar'])) {
			if($_GET['ordenar'] == 'bytes') {
				$i = 0;
				foreach ($acessos[$ip] as $key => $value) {
					$linha = explode("|", $value);
					$novoAcessos[$i]['site'] = $linha[0];
					$novoAcessos[$i]['data'] = $linha[1];
					$novoAcessos[$i]['bytes'] = $linha[2];
					$novoAcessos[$i]['flag'] = $linha[3];
					$i++;
				}
				usort($novoAcessos, function($a, $b) {
				    return $b['bytes'] - $a['bytes'];
				});			

				unset($acessos[$ip]);
				foreach ($novoAcessos as $key => $value) {
					$acessos[$ip][] = implode("|", $value);		
				}	
			}
		} else {
			// Ordena por data
			$acessos[$ip] = array_reverse($acessos[$ip]);
		}



		if(isset($_GET['squid_log'])) {
			$arquivo = explode(".", $_GET['squid_log']);
			if(isset($arquivo[2])) {
				$data = $arquivo[1];
				$turno = $arquivo[2];
				$titulo = "<h1>O endereço {$ip} realizou " . count($acessos[$ip]) . " acessos em {$data} pela {$turno}</h1><br/>";
			} else {
				$titulo = "<h1>O endereço " . $ip . " realizou " . count($acessos[$ip]) . " acessos</h1><br/>";
			}
			
		} else {
			include("../includes/conexao.php");
			$conexao = new db();
			if($blacklist = $conexao->listaWhere("blacklist","ip",$ip)) {
			$link_blacklist = "<a class='blacklist' href='blacklist.php?ip={$ip}&acao=liberar'><img width='30px' title='liberar' src='imagens/liberado.svg'></a>";			
			} else {
				$link_blacklist = "<a class='blacklist' href='blacklist.php?ip={$ip}&acao=bloquear'><img width='30px' title='bloquear' src='imagens/bloqueado.svg'></a>";				
			}
			$titulo = "<h1>O endereço " . $ip . " realizou " . count($acessos[$ip]) . " acessos</h1><div style='float: right; margin-right: 20px;'>{$link_blacklist}</div><br/>";
		}
		


		/**************************** Paginação *****************************************/
		$acessos_pagina = 1000;	
		$n_pages = ceil(count($acessos[$ip]) / $acessos_pagina);
		echo $titulo;
		for ($i=0; $i < $n_pages; $i++) { 
			$inicio = $i * $acessos_pagina;
			$link = $i + 1;
			$url = "?ip={$_GET['ip']}";
			if(isset($_GET['squid_log'])) {
				$url .= "&squid_log={$_GET['squid_log']}";
			}
			if(isset($_GET['ordenar'])) {
				if($_GET['ordenar'] == "bytes") {
					$url .= "&ordenar=bytes";
				}
				
			}
			if(isset($_GET['inicio'])) {
				if($_GET['inicio'] == $inicio) {
					echo "<a href='{$url}&inicio={$inicio}'>{$link}</a> &nbsp;";
				} else {
					echo "<a style='text-decoration: none;' href='{$url}&inicio={$inicio}'>{$link}</a> &nbsp;";
				}
			} else {
				echo "<a style='text-decoration: none;' href='{$url}&inicio={$inicio}'>{$link}</a> &nbsp;";
			}									
		}
		$acessos_total = count($acessos[$ip]); // 1500
		if(isset($_GET['inicio'])) {									
			$contador = $_GET['inicio'];	// 200								
			if(($acessos_total - $contador) > $acessos_pagina) {
				$acessos_total = $contador + $acessos_pagina; // 300
			} 
		} else {
			$contador = 0;	
			$acessos_total = $acessos_pagina;																	
		}
		/*********************************************************************************/

		
		echo "<div id='estatisticaDetalhes'><table class='tabela3 ordenadoBanda'><tr><th colspan='4'>Acessos de {$ip}</th></tr><tr><th>Site</th><th><a id='organizaPorData' href='?ip={$ip}&squid_log={$arquivo_log}'>Data</a></th><th><a id='organizaPorBytes' href='?ip={$ip}&squid_log={$arquivo_log}&ordenar=bytes'>Bytes</a></th><th>Flag</th></tr>";
		for ($i=$contador; $i < $acessos_total; $i++) { 

			if(!isset($acessos[$ip][$i])) {
				continue;
			}

			$linha = explode("|", $acessos[$ip][$i]);

			if(strlen($linha[0]) > 10) {
				//$linha[0] = substr($linha[0], 0, 100);
			}
			if(strlen($linha[1]) > 64) {
				//$linha[1] = "Sem data";
			}								
			if(stripos($linha[3], "DENIED") !== FALSE) {
				echo "<tr ><td><a href='{$linha[0]}'>{$linha[0]}</a></td><td>{$linha[1]}</td><td>". $estatisticas->calculaBytes($linha[2]) . "</td><td class='bloqueado'>{$linha[3]}</td></tr>";
			} else {
				echo "<tr><td><a href='{$linha[0]}'>{$linha[0]}</a></td><td>{$linha[1]}</td><td>". $estatisticas->calculaBytes($linha[2]) . "</td><td>{$linha[3]}</td></tr>";
			}
			
		}
		echo "</table></div>";
		for ($i=0; $i < $n_pages; $i++) { 
			$inicio = $i * $acessos_pagina;
			$link = $i + 1;
			$url = "?ip={$_GET['ip']}";
			if(isset($_GET['squid_log'])) {
				$url .= "&squid_log={$_GET['squid_log']}";
			}
			if(isset($_GET['inicio'])) {
				if($_GET['inicio'] == $inicio) {
					echo "<a href='{$url}&inicio={$inicio}'>{$link}</a> &nbsp;";
				} else {
					echo "<a style='text-decoration: none;' href='{$url}&inicio={$inicio}'>{$link}</a> &nbsp;";
				}
			} else {
				echo "<a style='text-decoration: none;' href='{$url}&inicio={$inicio}'>{$link}</a> &nbsp;";
			}									
		}

	} else {
		echo "<h1>Nenhum tráfego</h1>";
	}

} 
