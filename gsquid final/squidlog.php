<?php
	include("includes/valida_sessao.php");
	include("includes/valida_nivel.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Squidlog</title>
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
					<h1><a href='squidlog.php'>Squidlog</a> </h1>
											
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span>"; ?></h3>
				</header>
					<article id="cache">						
						<div>
						<div class="bloqueiosFormulario">
							<form method="POST" action="" id="cadastraSiteLog">
								<label>Inserir site na exceção de logs</label>
								<br/>
								<input type="text" name="site" placeholder="Ex: .live.sekindo.com">								
								<button type="submit">Cadastrar</button>
							</form>
						</div>

						<table class='tabela3'>
						<tr><th colspan='2'>Sites na exceção de logs</th></tr>
						<tr><th>Site</th><th>Ação</th></tr>
					
						<?php
					include("includes/conexao.php");
					$conexao = new db();
					$squidlog_excecoes = $conexao->lista("squidlog_excecoes");
					
					if(isset($_GET['acao']) AND isset($_GET['squidlog_excecoes'])) {
						if($_GET['acao'] == "alterar" AND isset($_GET['id'])) {							
							foreach ($squidlog_excecoes as $key => $value) {
								if($value['id'] == $_GET['id']){
									echo "<form method='POST' action='' id='alteraSiteSquidlog'>
									<tr>
									<input type='hidden' name='id' value='{$value['id']}'>	
									<td><input type='text' name='site' value='{$value['site']}'></td>
									<input type='hidden' name='acao' value='alterar'>		
									<input type='hidden' name='squidlog_excecoes' value='1'>
									
									<td>
										<button type='submit'>Salvar alterações</button>
									</td>
									</tr>
									</form>";
								}  else {
									echo "<tr><td>{$value['site']}</td>";
									echo "<td>
									<div class='blocoAcoes2'>
										<a title='alterar' href='?acao=alterar&id={$value['id']}&squidlog_excecoes=1'><div class='alterar'></div></a>
										<a title='excluir' class='excluirSiteLog' href='?acao=excluir&id={$value['id']}&squidlog_excecoes=1'><div class='excluir'></div></a>
									</div>

									</td>
									</tr>";
								}
							}

						} 
					} else {
						foreach ($squidlog_excecoes as $key => $value) {
							echo "<tr><td>{$value['site']}</td>";
							echo "<td>
							<div class='blocoAcoes2'>
								<a title='alterar' href='?acao=alterar&id={$value['id']}&squidlog_excecoes=1'><div class='alterar'></div></a>
								<a title='excluir' class='excluirSiteLog' href='?acao=excluir&id={$value['id']}&squidlog_excecoes=1'><div class='excluir'></div></a>
							</div>

							</td>
							</tr>";
						}
					}
					
					?>
					</table>
				

			
				</div>
					</article>
			</section>

			</main>
		</div>
		<div id="rodapeFundo">
			<footer id="rodape"></footer>
		</div>

		<script>
			document.getElementById('cadastraSiteLog').onsubmit = function(event){
			event.preventDefault();
			
			var site = this.elements[0].value;

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
			 				  
			xhttp.open("POST", "acoes/configuraSquidLog.php", true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("acao=inserir&" + "site=" + site);
		}		



		if(document.getElementById('alteraSiteSquidlog')) {
			document.getElementById('alteraSiteSquidlog').onsubmit = function(event){
				event.preventDefault();
				
				var id = this.elements[0].value;
				var site = this.elements[1].value;
				

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
				 				  
				xhttp.open("POST", "acoes/configuraSquidLog.php", true);
				xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xhttp.send("acao=alterar&" + "site=" + site + "&id=" + id);
			}
		}











		
		var excluirSiteLog = document.getElementsByClassName("excluirSiteLog");
		if(excluirSiteLog) {
			for(var i = 0; i < excluirSiteLog.length; i++) {					
				excluirSiteLog[i].onclick = function(event){
					event.preventDefault();
					var parametros = this.href.split("?");
					var infos = parametros[1].split("&");				
					var id = infos[1].split("=");
					
					var confirma = confirm("Tem certeza que deseja excluir este site?");
					if(confirma) {
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
					
					xhttp.open("POST", "acoes/configuraSquidLog.php", true);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhttp.send("acao=excluir&site_cache=1&id=" + id[1]);
					}
				}
			}
		}
		
		








		function atualizaSquid(){
			var xhttp = new XMLHttpRequest();
		  	xhttp.onreadystatechange=function() {
			    if (xhttp.readyState == 4 && xhttp.status == 200) {	
			      	window.location.href = "squidlog.php";
				}
			}				  
		  	xhttp.open("GET", "acoes/atualizaTudo.php", true);
		  	xhttp.send();
		}	
		</script>
		<script type='text/javascript' src='js/reloadSquid.js'></script>


</body>
</html>
