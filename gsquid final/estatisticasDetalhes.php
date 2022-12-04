<?php
	include("includes/valida_sessao.php");
	include("includes/valida_nivel.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Estatísticas Detalhes</title>
	<meta charset="utf-8">
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
					<h1><a href='estatisticas.php'>Estatísticas</a> >> detalhes</h1>
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span>"; ?></h3>
					
				</header>				
				<div id="corpoEstatisticasDetalhes"></div>
				

			</section>

			</main>
		</div>
		<div id="rodapeFundo">
			<footer id="rodape"></footer>
		</div>
		<script type="text/javascript" src="js/script.js"></script>
		<script type='text/javascript' src='js/reloadSquid.js'></script>

		<script>
		window.onload = function(){
			
			



			retornaEstatisticasDetalhes();

			function retornaEstatisticasDetalhes() {
				var parametros = window.location.href.split("?");
								
					

					document.getElementById("telaEscura").style.display = "block";
					var log = document.getElementById("aviso");
					log.style.backgroundImage = "url('imagens/loading.gif')";
					var xhttp = new XMLHttpRequest();
				  	xhttp.onreadystatechange=function() {
				    if (xhttp.readyState == 4 && xhttp.status == 200) {
				      document.getElementById("corpoEstatisticasDetalhes").innerHTML = xhttp.responseText;	
				      insereBlacklistForm();		      
				      log.style.backgroundImage = "";
				      document.getElementById("telaEscura").style.display = "none";
					  log.innerHTML = "";		
					  if(document.getElementById("organizaPorData")) {
							document.getElementById("organizaPorData").onclick = function(event){
								event.preventDefault();
								ordenarBytes(document.getElementById("organizaPorData").href);
							}
						}
						
						if(document.getElementById("organizaPorBytes")) {
							document.getElementById("organizaPorBytes").onclick = function(event){
								event.preventDefault();
								ordenarBytes(document.getElementById("organizaPorBytes").href);

							}
						}		  
				    
				    }
					};					  
					  xhttp.open("GET", "acoes/retornaEstatisticasDetalhes.php?" + parametros[1], true);
					  xhttp.send();
				}


				function ordenarBytes(url) {
					var parametros = url.split("?");
					//alert(parametros[1]);							

					document.getElementById("telaEscura").style.display = "block";
					var log = document.getElementById("aviso");
					log.style.backgroundImage = "url('imagens/loading.gif')";
					var xhttp = new XMLHttpRequest();
				  	xhttp.onreadystatechange=function() {
				    if (xhttp.readyState == 4 && xhttp.status == 200) {
				      document.getElementById("corpoEstatisticasDetalhes").innerHTML = xhttp.responseText;	
				      insereBlacklistForm();		      
				      log.style.backgroundImage = "";
				      document.getElementById("telaEscura").style.display = "none";
					  log.innerHTML = "";		
					  if(document.getElementById("organizaPorData")) {
							document.getElementById("organizaPorData").onclick = function(event){
								event.preventDefault();
								ordenarBytes(document.getElementById("organizaPorData").href);
							}
						}
						
						if(document.getElementById("organizaPorBytes")) {
							document.getElementById("organizaPorBytes").onclick = function(event){
								event.preventDefault();							
								ordenarBytes(document.getElementById("organizaPorBytes").href);
							}
						}		  
				    
				    }
					};					  
					  xhttp.open("GET", "acoes/retornaEstatisticasDetalhes.php?" + parametros[1], true);
					  xhttp.send();
				}



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
									log.style.width = "300px";
									log.style.height = "300px";
									log.innerHTML = "<form method='POST' id='formulario' action='acoes/insereBlacklist.php'> <input type='hidden' id='ip' name='ip' value='" + ip[0] + "'> <textarea style='font-size: 20px;' cols='25' rows='7' id='mensagem' name='mensagem' placeholder='Insira a mensagem que aparecerá para o usuário'></textarea> <button type='submit'>Confirmar</button> <button id='cancelarAcao'>Cancelar</button> </form>";	
									enviaBlacklistForm();
								} else {
									enviaBlacklistForm2(ip[0]);									
								}

								


							}
						}
					}
				}				



				function enviaBlacklistForm(){
					var log = document.getElementById("aviso");
					document.getElementById('cancelarAcao').onclick = function(event){
						event.preventDefault();
						document.getElementById("aviso").innerHTML = "";
						document.getElementById("telaEscura").style.display = "none";
						log.style.width = "300px";
						log.style.height = "200px";
					}
					document.getElementById('formulario').onsubmit = function(event){
						event.preventDefault();
						var ip = document.getElementById("ip").value;
						var mensagem = document.getElementById("mensagem").value;	
						log.style.width = "300px";
						log.style.height = "200px";					
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

</body>
</html>
