<?php
	include("includes/valida_sessao.php");
	include("includes/valida_nivel.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Logs</title>
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
					<h1>Logs</h1>
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span>"; ?></h3>
				</header>
				<article id="corpoLogs">
					<?php
						include("includes/conexao.php");
						$conexao = new db();

						echo "<table class='tabela2'>
						<tr>
							<th colspan='5'>Ações realizadas no sistema </th>
						</th>
						<tr>
							<th>Usuário</th><th>Ação</th><th>Sala</th><th>Hora</th><th>IP</th>
						</tr>";
						$resultado = $conexao->pesquisaPersonalizada("SELECT u.login, l.acao, l.sala, l.hora, l.ip FROM usuarios u JOIN log_dashboard l ON u.id = l.id_usuario ORDER BY l.hora DESC");
						foreach ($resultado as $key => $value) {
							if($value['acao'] == 'bloquear_internet') 
								$value['acao'] = "<span class='bloqueado'>Bloqueou</span>";
							if($value['acao'] == 'filtrar') 
								$value['acao'] = "<span class='filtrado'>Filtrou</span>";
							if($value['acao'] == 'liberar') 
								$value['acao'] = "<span class='liberado'>Liberou</span>";
							if($value['acao'] == 'squid reload') {
								echo "<tr style='background-color: #f50;'><td>{$value['login']}</td><td>{$value['acao']}</td><td>{$value['sala']}</td><td>" . date("Y/m/d H:i:s", $value['hora']) . "</td><td>{$value['ip']}</td></tr>";
							} else {
								echo "<tr><td>{$value['login']}</td><td>{$value['acao']}</td><td>{$value['sala']}</td><td>" . date("Y/m/d H:i:s", $value['hora']) . "</td><td>{$value['ip']}</td></tr>";
							}
							
						}

						echo "<tr><td colspan='5'><a class='limpaLogs' href='acoes/limpaLogs.php?log=log_dashboard'>Limpar logs</a></td></tr></table>";
						

						echo "<table class='tabela1'>
						<tr>
							<th colspan='3'>Logins realizados</th>
						</th>
						<tr>
							<th>Usuário</th><th>Horário de login</th><th>IP</th>
						</tr>";
						
						$resultado = $conexao->pesquisaPersonalizada("SELECT l.logou, l.ip, u.login FROM log_logins l JOIN usuarios u ON l.id_usuario = u.id ORDER BY l.logou DESC");
						foreach ($resultado as $key => $value) {
							echo "<tr><td>{$value['login']}</td><td>" . date("Y/m/d H:i:s", $value['logou']) . "</td><td>{$value['ip']}</td></tr>";
						}

						echo "<tr><td colspan='3'><a class='limpaLogs' href='acoes/limpaLogs.php?log=log_logins'>Limpar logs</a></td></tr></table>";
						

						echo "<table class='tabela1'>
						<tr>
							<th colspan='3'>Logins sem sucesso</th>
						</th>
						<tr>
							<th>Login utilizado</th><th>Horário da tentativa</th><th>IP</th>
						</tr>";
						
						$resultado = $conexao->lista("log_tentativas");
						foreach ($resultado as $key => $value) {
							echo "<tr><td>{$value['login']}</td><td>" . date("Y/m/d H:i:s", $value['hora']) . "</td><td>{$value['ip']}</td></tr>";
						}

						echo "<tr><td colspan='3'><a class='limpaLogs' href='acoes/limpaLogs.php?log=log_tentativas'>Limpar logs</a></td></tr></table>";

						

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
				limpaLogs();

				function limpaLogs() {
					if(document.getElementsByClassName("limpaLogs")) {
						var limpaLogs = document.getElementsByClassName("limpaLogs");
						for(var i = 0; i < limpaLogs.length; i++) {					
							limpaLogs[i].onclick = function(event){
								event.preventDefault();	
								var parametros = this.href.split("?");
																			
								
								var log = document.getElementById("aviso");	
								document.getElementById("telaEscura").style.display = "block";
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
											location.reload();
										}
								    }
								};	
						  				  
								xhttp.open("POST", "acoes/limpaLogs.php", true);
								xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
								xhttp.send(parametros[1]);


							}
						}
					}
				}	






			}

		</script>

		<script type='text/javascript' src='js/reloadSquid.js'></script>

</body>
</html>
