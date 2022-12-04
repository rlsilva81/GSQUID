<?php
	include("includes/valida_sessao.php");
	include("includes/valida_nivel.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Ufdbguard</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/estilo.css">
	<style>
		#corpoUfdbguard table input {
			height: 25px;			
		}
		#corpoUfdbguard table input[type='text'] {
			width: 100%;
		}
		
		#corpoUfdbguard table button {			
			cursor: pointer;
		}
		#corpoUfdbguard table th:last-child {
			padding: 10px 0px 10px 0px;
		}
		#corpoUfdbguard table td {
			padding: 10px 0px 10px 0px;
		}
		#atualizaUfdbguard {
			width: 180px;
			height: 40px;
			font-size: 16px;
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
					<h1><a href='ufdbguard.php'>Ufdbguard</a></h1>
											
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span>"; ?></h3>
				</header>
				<article id="corpoUfdbguard">
					<table class="tabela1">
						<tr>
							<th colspan="5">Gerenciamento manual de blacklists</th>
						</tr>
						<tr>
							<th>Diretório da blacklist</th><th>Domínios</th><th>Procurar domínio</th><th>Inserir domínio</th><th>Excluir domínio</th>
						</tr>
					<?php


						foreach(glob("/var/ufdbguard/blacklists/*") as $file) {		
							if(stripos($file, "security"))
							continue;						
							$qtd = shell_exec("wc -l {$file}/domains | cut -d ' ' -f1");						
						    echo "<tr><td>{$file}/domains</td><td>{$qtd}</td>"; 
						    echo "<td>
						    		<form method='POST' action='' class='procurarDominio'>
						    			<input type='text' name='dominio' placeholder='Digite um domínio' required>
						    			<input type='hidden' name='blacklist' value='". $file ."/domains'>
						    			<button type='submit'>Procurar</button>
						    		</form>
						    	</td>";
						    echo "<td>
						    		<form method='POST' action='' class='inserirDominio'>
						    			<input type='text' name='dominio' placeholder='Digite um domínio' required>
						    			<input type='hidden' name='blacklist' value='". $file ."/domains'>
						    			<button type='submit'>Inserir</button>
						    		</form>
						    	</td>";
						     echo "<td>
						    		<form method='POST' action='' class='excluirDominio'>
						    			<input type='text' name='dominio' placeholder='Digite um domínio' required>						    			
						    			<input type='hidden' name='blacklist' value='". $file ."/domains'>
						    			<button type='submit'>Excluir</button>
						    		</form>
						    	</td></tr>";
						    
						    						
							
						}


					?>
						
						<tr>
							<th colspan="5">
								<button type="submit" id="atualizaUfdbguard">Atualizar Ufdbguard</button>
								
							</th>
						</tr>
					</table>
					<div id="exibeResultado"></div>

					<!--table class="tabela1">
						<tr>
							<th colspan="4">Gerenciamento automatizado de blacklists</th>
						</tr>
						<tr>
							<th>Diretório da blacklist</th><th>Domínios</th><th>Atualização online</th><th>Upload de blacklist</th>
						</tr-->
					<?php
					/*

						foreach(glob("/var/ufdbguard/blacklists/*") as $file) {		
							if(stripos($file, "security"))
							continue;						
							$qtd = shell_exec("wc -l {$file}/domains | cut -d ' ' -f1");						
						    echo "<tr><td>{$file}/domains</td><td>{$qtd}</td>"; 
						  
						    echo "<td>	
						    		<form method='POST' action='' class='atualizarOnline'> 								    			
						    			<input type='hidden' name='blacklist' value='". $file ."/domains'>
						    			<button type='submit'>Atualizar</button>	
					    			</form>					    		
						    	</td>";
						    echo "<td>	
						    		<form method='POST' action='' class='uploadBlacklist' enctype='multipart/form-data'> 								    			
						    			<input type='hidden' name='blacklist' value='". $file ."/domains'>
						    			<input type='file' name='blacklistFile'>
						    			<button type='submit'>Upload</button>	
					    			</form>					    		
						    	</td></tr>";
						    						
							
						}

	*/
					?>
						
					<!--
					</table>
					-->
				</article>
			</section>

			</main>
		</div>
		<div id="rodapeFundo">
			<footer id="rodape"></footer>
		</div>

		<script type='text/javascript' src='js/reloadSquid.js'></script>
		<script>
			var acao = document.getElementsByClassName("procurarDominio");
			for(var i = 0; i < acao.length; i++) {					
				acao[i].onsubmit = function(event){
					event.preventDefault();					
					var dominio = this.children[0].value;
					var blacklist = this.children[1].value;					
					document.getElementById("telaEscura").style.display = "block";
					var log = document.getElementById("aviso");
					log.style.backgroundImage = "url('imagens/loading.gif')";

					var xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange=function() {
						if (xhttp.readyState == 4 && xhttp.status == 200) {								    	
							document.getElementById('exibeResultado').innerHTML = xhttp.responseText;							
							log.style.backgroundImage = "";
							document.getElementById("telaEscura").style.display = "none";
							log.innerHTML = "";
						}
					}
										  
					xhttp.open("POST", "acoes/retornaUfdbguard.php", true);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhttp.send("acao=procurar&" + "dominio=" + dominio + "&blacklist=" + blacklist);
					
				}
			}

			var acao = document.getElementsByClassName("inserirDominio");
			for(var i = 0; i < acao.length; i++) {					
				acao[i].onsubmit = function(event){
					event.preventDefault();					
					var dominio = this.children[0].value;
					var blacklist = this.children[1].value;					
								
					var confirma = confirm("Tem certeza que deseja inserir o domínio " + dominio + "?");
					if(confirma) {					
						document.getElementById("telaEscura").style.display = "block";
						var log = document.getElementById("aviso");
						log.style.backgroundImage = "url('imagens/loading.gif')";	
						var xhttp = new XMLHttpRequest();
						xhttp.onreadystatechange=function() {
							if (xhttp.readyState == 4 && xhttp.status == 200) {								    	
								log.innerHTML = log.innerHTML + xhttp.responseText;
								log.style.backgroundImage = "";
								log.style.marginLeft = "-175px";
								log.style.width = "350px";
								log.innerHTML = log.innerHTML + "<button id='fecharTelaEscura'>Fechar</button>";
								document.getElementById("fecharTelaEscura").focus();
								document.getElementById('fecharTelaEscura').onclick = function(){
									location.reload();
								}
							}
						}						  
						xhttp.open("POST", "acoes/retornaUfdbguard.php", true);
						xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						xhttp.send("acao=inserir&" + "dominio=" + dominio + "&blacklist=" + blacklist);
					}
				}
			}

			var acao = document.getElementsByClassName("excluirDominio");
			for(var i = 0; i < acao.length; i++) {					
				acao[i].onsubmit = function(event){
					event.preventDefault();					
					var dominio = this.children[0].value;
					var blacklist = this.children[1].value;

					var confirma = confirm("Tem certeza que deseja excluir o domínio " + dominio + "?");
					if(confirma) {				
						document.getElementById("telaEscura").style.display = "block";
						var log = document.getElementById("aviso");
						log.style.backgroundImage = "url('imagens/loading.gif')";				
						var xhttp = new XMLHttpRequest();
						xhttp.onreadystatechange=function() {
							if (xhttp.readyState == 4 && xhttp.status == 200) {											
								log.innerHTML = log.innerHTML + xhttp.responseText;
								log.style.backgroundImage = "";
								log.style.marginLeft = "-175px";
								log.style.width = "350px";
								log.innerHTML = log.innerHTML + "<button id='fecharTelaEscura'>Fechar</button>";
								document.getElementById("fecharTelaEscura").focus();
								document.getElementById('fecharTelaEscura').onclick = function(){
									location.reload();
								}

							}
						}						  
						xhttp.open("POST", "acoes/retornaUfdbguard.php", true);
						xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						xhttp.send("acao=excluir&" + "dominio=" + dominio + "&blacklist=" + blacklist);
					}
				}
			}


			document.getElementById("atualizaUfdbguard").onclick = function(){
				var confirma = confirm("Tem certeza que deseja atualizar as blacklist do Ufdbguard? Essa ação pode levar alguns minutos...");
				if(confirma) {				
					document.getElementById("telaEscura").style.display = "block";
					var log = document.getElementById("aviso");
					log.style.backgroundImage = "url('imagens/loading.gif')";	
					log.innerHTML = "Este processo pode demorar alguns minutos... aguarde...";			
					var xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange=function() {
						if (xhttp.readyState == 4 && xhttp.status == 200) {	
							document.getElementById("telaEscura").style.display = "none";
							document.getElementById("exibeResultado").innerHTML = xhttp.responseText;	
							//alert(xhttp.responseText);				
							

						}
					}						  
					xhttp.open("POST", "acoes/retornaUfdbguard.php", true);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhttp.send("acao=atualizarBlacklist");
				}
			}




			/*
			var acao = document.getElementsByClassName("atualizarOnline");
			for(var i = 0; i < acao.length; i++) {					
				acao[i].onsubmit = function(event){
					event.preventDefault();	
					var blacklist = this.children[0].value;			
					document.getElementById("telaEscura").style.display = "block";
					var log = document.getElementById("aviso");
					log.style.backgroundImage = "url('imagens/loading.gif')";				
					var xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange=function() {
						if (xhttp.readyState == 4 && xhttp.status == 200) {	
							log.style.backgroundImage = "";
							log.style.marginLeft = "-175px";
							log.style.width = "350px";
							if(xhttp.responseText == "atualizar") {
								log.innerHTML = log.innerHTML + "<p>Atualização disponível. Deseja atualizar agora?</p>";
								log.innerHTML = log.innerHTML + "<button style='width: 90px; height: 30px; float: left; margin-left: 60px;' id='confirmaAtualizar'>Confirmar</button>";
								log.innerHTML = log.innerHTML + "<button style='width: 90px; height: 30px; float: right; margin-right: 60px;' id='fecharTelaEscura'>Cancelar</button>";
								document.getElementById("confirmaAtualizar").focus();
								document.getElementById('confirmaAtualizar').onclick = function(){
									confirmaAtualizarOnline(blacklist);
								}
							} else if(xhttp.responseText == "atualizado"){
								log.innerHTML = "<p>Blacklist já atualizada!</p>";
								log.innerHTML = log.innerHTML + "<button id='fecharTelaEscura'>Fechar</button>";
								document.getElementById("fecharTelaEscura").focus();								
							} else if(xhttp.responseText == "erro") {
								log.innerHTML = "<p>Não foi possível buscar atualizações!</p>";
								log.innerHTML = log.innerHTML + "<button id='fecharTelaEscura'>Fechar</button>";
								document.getElementById("fecharTelaEscura").focus();								
							} else {
								log.innerHTML = xhttp.responseText;
								log.innerHTML = log.innerHTML + "<button style='width: 90px; height: 30px; float: left; margin-left: 60px;' id='confirmaAtualizar'>Confirmar</button>";
								log.innerHTML = log.innerHTML + "<button style='width: 90px; height: 30px; float: right; margin-right: 60px;' id='fecharTelaEscura'>Cancelar</button>";
								document.getElementById("fecharTelaEscura").focus();
								document.getElementById('confirmaAtualizar').onclick = function(){
									confirmaAtualizarOnline(blacklist);
								}
							}

							document.getElementById('fecharTelaEscura').onclick = function(){
								location.reload();
							}

						}
					}						  
					xhttp.open("POST", "acoes/retornaUfdbguard.php", true);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhttp.send("acao=atualizarOnline&" + "&blacklist=" + blacklist);
					
				}
			}

			function confirmaAtualizarOnline(blacklist) {
				var log = document.getElementById("aviso");
				log.style.backgroundImage = "url('imagens/loading.gif')";
				log.innerHTML = "";
				var xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange=function() {
						if (xhttp.readyState == 4 && xhttp.status == 200) {	
							log.style.backgroundImage = "";
							log.innerHTML = log.innerHTML + xhttp.responseText;
							log.innerHTML = log.innerHTML + "<button id='fecharTelaEscura'>Fechar</button>";
							document.getElementById("fecharTelaEscura").focus();
							document.getElementById('fecharTelaEscura').onclick = function(){
								location.reload();
							}
						}
					}						  
					xhttp.open("POST", "acoes/retornaUfdbguard.php", true);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhttp.send("acao=atualizarOnline&blacklist=" + blacklist + "&confirma=1");
			}


			var acao = document.getElementsByClassName("uploadBlacklist");
			for(var i = 0; i < acao.length; i++) {					
				acao[i].onsubmit = function(event){
					event.preventDefault();	
					var blacklist = this.children[0].value;
					//var blacklistFile = this.children[1].value;	
					var blacklistFile = new FormData(this);

					document.getElementById("telaEscura").style.display = "block";
					var log = document.getElementById("aviso");
					log.style.backgroundImage = "url('imagens/loading.gif')";				
					var xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange=function() {
						if (xhttp.readyState == 4 && xhttp.status == 200) {	
							log.style.backgroundImage = "";
							log.style.marginLeft = "-175px";
							log.style.width = "350px";
							log.innerHTML = xhttp.responseText;
						}
							
					}						  
					xhttp.open("POST", "acoes/retornaUfdbguard.php", true);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhttp.send("acao=uploadBlacklist&" + "&blacklist=" + blacklist + "&blacklistFile=" + blacklistFile);
					
				}
			}
*/


			function exibeSites(){
				if(document.getElementById('botaoSites').innerHTML == 'Mostrar sites') {
					document.getElementById('sitesEncontrados').style.display = 'block';
					document.getElementById('botaoSites').innerHTML = 'Ocultar sites';
				} else {
					document.getElementById('sitesEncontrados').style.display = 'none';
					document.getElementById('botaoSites').innerHTML = 'Mostrar sites';
				}
				

			}


		</script>

</body>
</html>
