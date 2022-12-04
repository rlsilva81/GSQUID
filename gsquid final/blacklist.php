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
					<h1><a href='blacklist.php'>Blacklist</a></h1>
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span>"; ?></h3>
				</header>
					<article id="blacklist">
					<?php				
					
						include("includes/conexao.php");
						$conexao = new db();
						$blacklist = $conexao->lista("blacklist");
						

						echo "<table class='tabela3'><tr><th colspan='6'>Computadores na Blacklist</th></tr>";
						echo "<tr><th>Hostname</th><th>MAC</th><th>IP</th><th>Perfil</th><th>Sala</th><th>Retirar da blacklist</th>";
						
						foreach ($blacklist as $key => $value) {	
								$computadores = $conexao->listaWhere("computadores","ip",$value['ip']);
								if(isset($computadores[0])) {
									$sala = $conexao->listaWhere("salas","cod_sala",$computadores[0]['cod_sala']);
									echo "<tr>
									<input type='hidden' name='cod_computador' class='cod_computador' value='{$computadores[0]['cod_computador']}'>
									<td>{$computadores[0]['hostname']}</td><td>{$computadores[0]['mac']}</td><td><a href='estatisticasDetalhes.php?ip={$computadores[0]['ip']}'>{$computadores[0]['ip']}</a></td><td>{$computadores[0]['perfil']}</td><td class='liberado'>{$sala[0]['nome']}</td><td><a class='blacklist' href='blacklist.php?ip={$value['ip']}&acao=liberar'><img width='30px' title='liberar' src='imagens/liberado.svg'></a></td></tr>";
								} else {
									echo "<tr>
									<td><span class='bloqueado'>Não cadastrado</span></td><td><span class='bloqueado'>Não cadastrado</span></td><td><a href='estatisticasDetalhes.php?ip={$value['ip']}'>{$value['ip']}</a></td><td><span class='bloqueado'>Não cadastrado</span></td><td><span class='bloqueado'>Não cadastrado</span></td><td><a class='blacklist' href='blacklist.php?ip={$value['ip']}&acao=liberar'><img width='30px' title='liberar' src='imagens/liberado.svg'></a></td></tr>";
								}
								
							
						}
						echo "</table>";
					
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
				insereBlacklistForm();

				function insereBlacklistForm() {
					if(document.getElementsByClassName("blacklist")) {
						var blacklist = document.getElementsByClassName("blacklist");
						for(var i = 0; i < blacklist.length; i++) {					
							blacklist[i].onclick = function(event){
								event.preventDefault();	
								var parametros = this.href.split("?");
								parametros = parametros[1].split("=");	
								ip = parametros[1].split("&");			
								var acao = parametros[2];	

								document.getElementById("telaEscura").style.display = "block";
								var log = document.getElementById("aviso");

								if(acao == "bloquear") {
									log.innerHTML = "<form method='POST' id='formulario' action='acoes/insereBlacklist.php'> <input type='hidden' id='ip' name='ip' value='" + ip[0] + "'> <textarea id='mensagem' name='mensagem' placeholder='Insira a mensagem que aparecerá para o usuário'></textarea> <button type='submit'>Confirmar</button> </form>";	
									enviaBlacklistForm();
								} else {
									enviaBlacklistForm2(ip[0]);									
								}

								


							}
						}
					}
				}				



				function enviaBlacklistForm(){
					document.getElementById('formulario').onsubmit = function(event){
						event.preventDefault();
						var ip = document.getElementById("ip").value;
						var mensagem = document.getElementById("mensagem").value;

						var log = document.getElementById("aviso");
						log.innerHTML = "";
						log.style.backgroundImage = "url('imagens/loading.gif')";
						var xhttp = new XMLHttpRequest();
					  	xhttp.onreadystatechange=function() {
						    if (xhttp.readyState == 4 && xhttp.status == 200) {
						    	log.style.backgroundImage = "";
						    		log.innerHTML = xhttp.responseText;
						    		atualizaSquid();
						    		log.innerHTML = log.innerHTML + "<button id='fecharTelaEscura'>Fechar</button>";
						    		document.getElementById("fecharTelaEscura").focus();
						    		document.getElementById('fecharTelaEscura').onclick = function(){					
										log.innerHTML = "";
										telaEscura.style.display = "none";											
										location.reload();
									}
						    }
						};	
						  				  
						xhttp.open("POST", "acoes/insereBlacklist.php", true);
						xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						xhttp.send("ip=" + ip + "&mensagem=" + mensagem + "&acao=bloquear");
					}
				
				}

				function enviaBlacklistForm2(ip){				

						var log = document.getElementById("aviso");
						log.innerHTML = "";
						log.style.backgroundImage = "url('imagens/loading.gif')";
						var xhttp = new XMLHttpRequest();
					  	xhttp.onreadystatechange=function() {
						    if (xhttp.readyState == 4 && xhttp.status == 200) {
						    	log.style.backgroundImage = "";
						    		log.innerHTML = xhttp.responseText;
						    		atualizaSquid();
						    		log.innerHTML = log.innerHTML + "<button id='fecharTelaEscura'>Fechar</button>";
						    		document.getElementById("fecharTelaEscura").focus();
						    		document.getElementById('fecharTelaEscura').onclick = function(){					
										log.innerHTML = "";
										telaEscura.style.display = "none";		
										location.reload();

									}
						    }
						};	
						  				  
						xhttp.open("POST", "acoes/insereBlacklist.php", true);
						xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						xhttp.send("ip=" + ip + "&acao=liberar");
					
				
				}

				

				function atualizaSquid(){
					var xhttp = new XMLHttpRequest();
				  	xhttp.onreadystatechange=function() {
					    if (xhttp.readyState == 4 && xhttp.status == 200) {	
					      	
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
