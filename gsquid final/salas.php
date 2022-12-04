<?php
	include("includes/valida_sessao.php");
	include("includes/valida_nivel.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Salas</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/estilo.css">
	
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
					<?php
						include("includes/conexao.php");
						$conexao = new db();
						if(isset($_GET['sala'])) {
							$cod_sala = $conexao->listaWhere("salas", "nome", $_GET['sala']);
							$computadores = $conexao->listaWhereNumero("computadores", "cod_sala", $cod_sala[0]['cod_sala']);
							echo "<h1><a href='salas.php'>Salas</a> >> ". htmlspecialchars($_GET['sala']) ." ({$computadores[0]['numero']} computadores)</h1>";							

						} else {
							$computadores = $conexao->pesquisaPersonalizada("SELECT COUNT(*) AS numero FROM computadores");
							echo "<h1><a href='salas.php'>Salas</a>&nbsp; ({$computadores[0]['numero']} computadores)</h1>";
						}

						
					?>
					
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span>"; ?></h3>
					<?php
					$salas = $conexao->pesquisaPersonalizada("SELECT * FROM salas ORDER BY nome");
						echo "<div style='clear: both;'><form method='GET' action=''><select name='sala'>
								<option value='{$salas[0]['nome']}'>--- Selecione uma sala ---</option>
								";
						foreach ($salas as $key => $value) {
							echo "<option name='{$value['nome']}'>{$value['nome']}</option>";
						}
						echo "</select><button type='submit'>Filtrar</button></form></div>";
					?>
				</header>
					<br/>
					<article id="salas">
					<?php
					
					
					if(isset($_GET['sala'])) {
						$cod_sala = $conexao->listaWhere("salas", "nome", $_GET['sala']);
						
						//$computadores = $conexao->listaWhere("computadores", "cod_sala", $cod_sala[0]['cod_sala']);

						$computadores = $conexao->listaWhereOrder("computadores", "cod_sala", $cod_sala[0]['cod_sala'], "hostname");

						echo "<table class='tabela1'><tr><th colspan='5'>Computadores de " . htmlspecialchars($_GET['sala']) . "<a href='modifica_salas.php?sala=" . htmlspecialchars($_GET['sala']) . "'> (+) </a></th></tr>";
						echo "<tr><th>Hostname</th><th>MAC</th><th>IP</th><th>Perfil</th><th>Ação</th>";
						
						foreach ($computadores as $key => $value) {
							if(isset($_GET['destaque'])) {
								if($_GET['destaque'] == $value['ip']) {
									echo "<tr style='background-color: orange;'>";
								} else {
									echo "<tr>";
								}
								
							} else {
								echo "<tr>";
							}
							echo "
							<input type='hidden' name='cod_computador' class='cod_computador' value='{$value['cod_computador']}'>
							<td>{$value['hostname']}</td><td>{$value['mac']}</td><td><a target='_blank' href='estatisticasDetalhes.php?ip={$value['ip']}'>{$value['ip']}</a></td><td>{$value['perfil']}</td>";	
								echo "<td><div class='blocoAcoes2'><a title='alterar' href='valida_salas.php?sala=". htmlspecialchars($_GET['sala']) ."&acao=alterar&cod_computador={$value['cod_computador']}'><div class='alterar'></div></a>
								<a title='excluir' class='excluirHost' href='valida_salas.php?acao=excluir&cod_computador={$value['cod_computador']}'><div class='excluir'></div></a></div></td></tr>";
							
						}
						echo "</table>";
					} else {

					
						$cod_salas = $conexao->pesquisaPersonalizada("SELECT DISTINCT(cod_sala),nome FROM salas");						
						foreach ($cod_salas as $key => $value) {
							$computadores = $conexao->pesquisaPersonalizada("SELECT * FROM computadores WHERE cod_sala = {$value['cod_sala']} ORDER BY hostname");		
							
							echo "<table class='tabela1'><tr><th colspan='5'>Computadores de {$value['nome']} <a href='modifica_salas.php?sala={$value['nome']}'> (+) </a> </th></tr>";
							echo "<tr><th>Hostname</th><th>MAC</th><th>IP</th><th>Perfil</th><th>Ação</th>";
							
							foreach ($computadores as $key => $dados) {
								echo "<tr>
								<input type='hidden' name='sala' class='sala' value='{$value['nome']}'>
								<input type='hidden' name='cod_computador' class='cod_computador' value='{$dados['cod_computador']}'>
								<td>{$dados['hostname']}</td><td>{$dados['mac']}</td><td><a target='_blank' href='estatisticasDetalhes.php?ip={$dados['ip']}'>{$dados['ip']}</a></td><td>{$dados['perfil']}</td>";					
								echo "<td><div class='blocoAcoes2'><a title='alterar' href='valida_salas.php?sala=". htmlspecialchars($value['nome']) ."&acao=alterar&cod_computador={$dados['cod_computador']}'><div class='alterar'></div></a>
								<a title='excluir' class='excluirHost' href='valida_salas.php?acao=excluir&cod_computador={$dados['cod_computador']}'><div class='excluir'></div></a></div></td></tr>";
							}
							echo "</table>";
						}
						
					}
				?>
				
			</article>
			</section>

			</main>
		</div>
		<div id="rodapeFundo">
			<footer id="rodape"></footer>
		</div>

		<script>
			window.onload = function(){
								

				var acao = document.getElementsByClassName("excluirHost");
				for(var i = 0; i < acao.length; i++) {					
					acao[i].onclick = function(event){
						event.preventDefault();
						var confirma = confirm("Tem certeza que deseja excluir este item?");
						if(confirma) {
							var parametros = this.href.split("?");	

							var telaEscura = document.getElementById("telaEscura");
							telaEscura.style.display = "block";
							var log = document.getElementById("aviso");
							log.style.backgroundImage = "url('imagens/loading.gif')";
							
							var xhttp = new XMLHttpRequest();
						  	xhttp.onreadystatechange=function() {
							    if (xhttp.readyState == 4 && xhttp.status == 200) {	
							      	log.style.backgroundImage = "";
							    	log.innerHTML = xhttp.responseText;
					      			log.innerHTML = log.innerHTML + "<button id='fecharTelaEscura'>Fechar</button>";
							    	document.getElementById("fecharTelaEscura").focus();
						    		document.getElementById('fecharTelaEscura').onclick = function(){								
										log.innerHTML = "";
										telaEscura.style.display = "none";	
										atualizaSquid();							
									}					      	
								}
							}
						  
						  xhttp.open("GET", "acoes/confirmaValidaHosts.php?" + parametros[1], true);
						  xhttp.send();

					}
				}
			}

			function atualizaSquid(){
				var xhttp = new XMLHttpRequest();
			  	xhttp.onreadystatechange=function() {
				    if (xhttp.readyState == 4 && xhttp.status == 200) {	
				      	location.reload();
					}
				}				  
			  	xhttp.open("GET", "acoes/atualizaTudo.php", true);
			  	xhttp.send();
			}
		}
		</script>
		<script type='text/javascript' src='js/reloadSquid.js'></script>

</body>
</html>
