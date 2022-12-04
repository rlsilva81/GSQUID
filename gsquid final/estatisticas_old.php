<?php
	include("includes/valida_sessao.php");
	include("includes/valida_nivel.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Estatísticas</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/estilo.css">
	<style type="text/css">
		#estatisticasTurnos {
			/*border: solid 1px;*/			
		}
		#estatisticasTurnos table {
			margin: auto;
			margin-bottom: 50px;
		}
		#estatisticasInfos {
			
			text-align: center;
			padding: 20px;
		}
		#gerarEstatisticaHost {
			width: 200px;
			height: 40px;
			margin: auto;
			cursor: pointer;
			margin-top: 10px;
			margin-bottom: 10px;
		}
		#estatisticasTabelas {
			/*border: solid 1px;*/
			overflow: hidden;
			text-align: center;
		}
		#estatisticasTabelas > h1 {
			font-size: 25px;
			margin-bottom: 20px;
		}

	</style>
</head>
<body>
	<div id="telaEscura">
		<div id="aviso"></div>
	</div>
	<?php
			include("includes/cabecalho.php");
		?>
		<div id="corpoFundo">
			<main id="corpo">
			<?php
				include("includes/menuInferior.php");
			?>
			<section id="conteudo">
				<header>
					<h1><a href='estatisticas.php'>Estatísticas<a/></h1>
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span>"; ?></h3>
				</header>
				<article id="estatisticasCorpo">
					<div id="estatisticasTurnos">
					<div id="estatisticasInfos">
										
					</div>
						
							

					<?php	
						include("includes/config.php");
						foreach(glob($pastaTemp . "*") as $file) {
							if(stripos($file, "squidlog.") !== FALSE) {
								
								$array = explode(".", $file);
								$data = $array[1];
								$nome = explode("/", $file);
								$nome = end($nome);
								
								if(count($array) == 4) {
									$nome = explode(".", $nome);
									$turno = $nome[2];
									$log[$data][$turno]['nome'] = $nome[0] . "." . $nome[1] . "." .$nome[2];
									$log[$data][$turno]['data'] = $data;
									$log[$data][$turno]['turno'] = $nome[2];
									$log[$data][$turno][$array[3]] = $array[3]; 
								} else {
									$turno = end($array);
									$log[$data][$turno]['nome'] = $nome;
									$log[$data][$turno]['data'] = $data;
									$log[$data][$turno]['turno'] = end($array);
								}

							}
						}
						
						if(isset($log)) {
							/*$dias = cal_days_in_month(CAL_GREGORIAN, 4, 2016);
							echo "<h1>{$dias} em Abril</h1>";
							foreach ($log as $indice_data => $arquivo) {
								$data = explode("-", $indice_data);
								$ano = $data[0];
								$mes = $data[1];
								$dia = $data[2];
							}*/
							$meses = array(
								'Janeiro' => 01,
								'Fevereiro' => 02,
								'Março' => 03,
								'Abril' => 04,
								'Maio' => 05,
								'Junho' => 06,
								'Julho' => 07,
								'Agosto' => 08,
								'Setembro' => 09,
								'Outubro' => 10,
								'Novembro' => 11,
								'Dezembro' => 12,
								);
							$anoAnterior = "";
							$mesAnterior = "";
							$rowspan = 0;
							echo "<table class='tabela1'><tr><th>Ano</th><th>Mês</th><th>Dia</th><th>Turno</th></tr>";
							foreach ($log as $indice_data => $arquivo) {							
								echo "<tr>";
								$data = explode("-", $indice_data);

								if($data[0] != $anoAnterior) {
									foreach ($log as $indice_data2 => $arquivo2){
										$data2 = explode("-", $indice_data2);
										if($data2[0] == $data[0]) {
											$rowspan++;
										}
									}
									echo "<td rowspan='{$rowspan}'>{$data[0]}</td>";
									$rowspan = 0;
								} 	
								$anoAnterior = $data[0];		

								if($data[1] != $mesAnterior) {
									foreach ($log as $indice_data3 => $arquivo3){
										$data3 = explode("-", $indice_data3);
										if($data3[1] == $data[1]) {
											$rowspan++;
										}
									}
									foreach ($meses as $key => $value) {
										if($value == $data[1]) {
											echo "<td rowspan='{$rowspan}'>{$key}</td>";
										}
									}
									
									$rowspan = 0;
								} 				
								$mesAnterior = $data[1];								
								echo "<td>{$data[2]}</td>";
								echo "<td>";
								foreach ($arquivo as $indice_turno => $value) {
									if(count($value) > 3){
										foreach ($value as $key2 => $value2) {
											if($key2 != 'nome' AND $key2 != 'data' AND $key2 != 'turno')
											echo "<a class='arquivo_log' href='?" . $pastaTemp . $value['nome'] . "." . $value2 . "'>" . $indice_turno . "(" . ($value2+1) . ")" . "</a><br/>";
										}
									} else {
										echo "<a class='arquivo_log' href='?" . $pastaTemp . $value['nome'] . "'>" . $indice_turno . "</a><br/>";
									}								
								}
								echo "</td>";
								echo "</tr>";
														
							}
						echo "<tr><th colspan='4'><button id='gerarEstatisticaHost'>Gerar estatísticas atuais</button></th></tr>";	
						echo "</table>";
						}

						
						?>	
					</div>
					<div id="estatisticasTabelas">

					</div>


				</article>

			</section>

			</main>
		</div>
		<div id="rodapeFundo">
			<footer id="rodape"></footer>
		</div>

		<script>
			window.onload = function(){


				// Exibe informações de log
				var xhttp = new XMLHttpRequest();
			  	xhttp.onreadystatechange=function() {
				    if (xhttp.readyState == 4 && xhttp.status == 200) {	
				      	var json = JSON.parse(xhttp.responseText);
				      	var info = "<h3> Log atual: " + json["logAtual"]["linhas"] + " linhas (" + json["logAtual"]["tamanho"] + ")</h3>";
				      	info = info + "<h3> Log original: " + json["logOriginal"]["linhas"] + " linhas (" + json["logOriginal"]["tamanho"] + ")</h3>";
				        document.getElementById("estatisticasInfos").innerHTML = info + document.getElementById("estatisticasInfos").innerHTML;
				    }
				}					  
				xhttp.open("GET", "acoes/contaLinhas.php", true);
				xhttp.send();

				// Captura evento de clique para gerar estatísticas atuais
				document.getElementById("gerarEstatisticaHost").onclick = function(event){						
					retornaEstatisticasHost();						
				}


				// Função para gerar estatisticas atuais
				function retornaEstatisticasHost() {
					document.getElementById("telaEscura").style.display = "block";
					var log = document.getElementById("aviso");
					log.style.backgroundImage = "url('imagens/loading.gif')";
					var xhttp = new XMLHttpRequest();
				  	xhttp.onreadystatechange=function() {
				    if (xhttp.readyState == 4 && xhttp.status == 200) {
				      document.getElementById("estatisticasTabelas").innerHTML = "<h1>Estatísticas Atuais</h1>" + xhttp.responseText;			      
				      log.style.backgroundImage = "";
				      document.getElementById("telaEscura").style.display = "none";
					  log.innerHTML = "";
				      organizaEstatisticasHost();
				    }
					};					  
					  xhttp.open("GET", "acoes/retornaEstatisticasHost.php", true);
					  xhttp.send();
				}


					
				eventoClickLogs();

				function eventoClickLogs(){
					var arquivo_log = document.getElementsByClassName("arquivo_log");
					for(var i = 0; i < arquivo_log.length; i++) {					
						arquivo_log[i].onclick = function(event){
							event.preventDefault();	
							var parametros = this.href.split("?");
							var infos = parametros[1].split(".");
							
							var data = infos[1];
							var turno = infos[2];
							if(infos.length > 3) {
								var parte = parseInt(infos[3]);
								parte++;
								turno = turno + " (" + parte + ") ";
							}
							var titulo = "<h1>Estatísticas de " + data + " - " + turno + "</h1>";

							document.getElementById("telaEscura").style.display = "block";
							var log = document.getElementById("aviso");
							log.style.backgroundImage = "url('imagens/loading.gif')";

							var xhttp = new XMLHttpRequest();
						  	xhttp.onreadystatechange=function() {
							    if (xhttp.readyState == 4 && xhttp.status == 200) {	
							    	document.getElementById("estatisticasTabelas").innerHTML = xhttp.responseText;			      
								    log.style.backgroundImage = "";
								    document.getElementById("telaEscura").style.display = "none";
									log.innerHTML = "";
							      	organizaEstatisticasHost(parametros[1], titulo);
								}
							}
						  
						  xhttp.open("GET", "acoes/retornaEstatisticasHost.php?squid_log=" + parametros[1], true);
						  xhttp.send();
							
						}
					}
				}
				

















			function organizaEstatisticasHost(squid_log, titulo){				
				// Captura todas as colunas da tabela com classe ordenadoBanda
				var tds = document.querySelectorAll('.ordenadoBanda td');
				// Captura a div com id estatisticaHosts
				var estatisticaHosts = document.getElementById('estatisticaHosts');
				// Declara variáveis que serão usada dentro do laço de repetição
				var contador = 0;	
				var linhas = new Object();
				/* 
				   Percorre todas as 3 colunas de cada linha da tabela e insere no objeto/array linhas[]
     			   Exemplo de saída:
     			   linhas[0]['ip'] => 172.16.0.10;
     			   linhas[0]['banda'] => 1636201025;
     			   linhas[0]['acao'] => '<a href='http://192.168.0.203/estatisticasDetalhes.php?ip=172.16.0.10'>Ver detalhes</a>;
				   linhas[1]['ip'] => ...
		
				*/
				for (var i=0; i < tds.length; i = i + 4) {
					linhas[contador] = {						
							'ip' : tds[i].innerHTML,
							'banda' : converteBytes(tds[i+1].innerHTML),
							'sala' : tds[i+2].innerHTML,	
							'acao' : tds[i+3].innerHTML						
					};					
					contador++;
				}

				/*
					Percorre o array/objeto linhas e retorna um array multidimensional
					Exemplo de saída:
					sortable[0][1] => 1636201025;
					sortable[1][1] => 281506;
				*/				
				var sortable = new Array();
				for (var value in linhas) {	
					sortable.push([linhas[value].ip, linhas[value].banda, linhas[value].sala, linhas[value].acao]);
				}	
				// Ordena o array sortable em ordem decrescente pela banda
				sortable.sort(function(a, b) {return b[1] - a[1]});	
				
				// Inicia a criação da tabela
				var tabela = "<table class='tabela1'><tr><th colspan='4'>Consumo de banda por host</th></tr><tr><th>Endereço IP</th><th>Banda</th><th>Sala</th><th>Ação</th></tr>";

				//Percorre o array multidimensional sortable montar as colunas da tabela				
				var split = [];
				var bandatotal = 0;
				for (var value in sortable) {	
					tabela = tabela + "<tr>";
					for(var value2 in sortable[value]){
						if(value2 == 1) {
							tabela = tabela + "<td>" + converteBytes2(sortable[value][value2]) + "</td>";
						} else {
							tabela = tabela + "<td>" + sortable[value][value2] + "</td>";
						}			
					}
					tabela = tabela + "</tr>";
					// Soma a banda total 
					bandatotal = parseInt(bandatotal) + parseInt(sortable[value][1]);
				}				
				// Imprime a última linha da tabela com o total
				tabela = tabela + "<tr><th colspan='4'>Total: " + converteBytes2(bandatotal) + "</th></tr></table>";
				
				// Se o parâmetro squid_log for indefinido, essa função foi chamada pelo clique no gerarestatisticasatuais e portanto, não deverá gerar relatório
				if (typeof squid_log == 'undefined') { 
				// Insere a tabela na div estatisticaHosts
				estatisticaHosts.innerHTML = tabela;
				} else {
					geraRelatorio();
				}

				// Função para gerar um relatório em HTML estático
				function geraRelatorio(){
					// Captura a tabela das estatísticas das salas (não precisa ser ordenada)
					var salas = encodeURIComponent(document.getElementById("estatisticaSalas").innerHTML);
					// Captura a tabela das estatísticas dos hosts ordenados anteriormente
					var hosts = encodeURIComponent(tabela);
					var xhttp = new XMLHttpRequest();
				  	xhttp.onreadystatechange=function() {
				    if (xhttp.readyState == 4 && xhttp.status == 200) {
				    	// Insere o resultado na div estatisticasTabelas
				    	document.getElementById("estatisticasTabelas").innerHTML = xhttp.responseText;				    		
				    }
					};					  
					xhttp.open("POST", "geraRelatorioHTML.php", true);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					// Envia o parâmetro titulo que contém o titulo do relatório com data e turno, a tabela hosts, salas e o caminho do log
					xhttp.send("titulo=" + titulo + "&hosts=" + hosts + "&salas=" + salas + "&squid_log=" + squid_log);									
					
				}

				

				
			

			}
			
				


			




				
			}
		</script>
		<script type="text/javascript" src="js/script.js"></script>
		<script type='text/javascript' src='js/reloadSquid.js'></script>

</body>
</html>
