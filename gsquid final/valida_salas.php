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
						if(isset($_GET['sala'])) {
							echo "<h1><a href='salas.php'>Salas</a> >> <a href='salas.php?sala={$_GET['sala']}'>". htmlspecialchars($_GET['sala']) ."</a> >> alterar computadores</h1>";
						} else {
							echo "<h1><a href='salas.php'>Salas</a></h1>";
						}
					?>
					
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span>"; ?></h3>
				</header>
					<article id="salas">
					<?php
					include("includes/conexao.php");
					$conexao = new db();
					
					

					if(isset($_GET['acao']) AND isset($_GET['sala']) AND isset($_GET['cod_computador'])) {
						if ($_GET['acao'] == "excluir") {
							if($conexao->deleta("computadores","cod_computador", $_GET['cod_computador'])) {
								header("Location: acoes/atualizaTudo.php?voltar=salas");										
							}
						}
						elseif ($_GET['acao'] == "alterar") {
							$cod_sala = $conexao->listaWhere("salas", "nome", $_GET['sala']);
							$computadores = $conexao->listaWhereOrder("computadores", "cod_sala", $cod_sala[0]['cod_sala'], "hostname");
							
							
							echo "<table class='tabela1'><tr><th colspan='5'>Computadores de " . htmlspecialchars($_GET['sala']) . "</th></tr>";
							echo "<tr><th>Hostname</th><th>MAC</th><th>IP</th><th>Perfil</th><th>Ação</th>";
							
							foreach ($computadores as $key => $value) {
								if($value['cod_computador'] == $_GET['cod_computador']) {
									echo "<form method='POST' action='' class='validaSalaForm'>";
									echo "<input type='hidden' name='sala' class='sala' value='{$_GET['sala']}'>";
									echo "<tr>
									<input type='hidden' name='cod_computador' class='cod_computador' value='{$value['cod_computador']}'>
									<td><input type='text' name='hostname' class='hostname' value='{$value['hostname']}'></td>
									<td><input class='mac' type='text' name='mac' id='mac' value='{$value['mac']}' pattern='^([0-9a-fA-F][0-9a-fA-F]-){5}([0-9a-fA-F][0-9a-fA-F])$'></td>
									<td><input type='text' name='ip' class='ip' value='{$value['ip']}' pattern='^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$'></td>
									<td>
									<select name='perfil'>
										";
									if($value['perfil'] == 'professor') {
										echo "<option value='professor'>professor</option>";
										echo "<option value='aluno'>aluno</option>";
									} else {										
										echo "<option value='aluno'>aluno</option>";
										echo "<option value='professor'>professor</option>";
									}
									
									echo "</select></td>
									<td><button type='submit'>Salvar alteração</button></td></tr>";
									echo "<script src='js/validaMac.js'></script>";
								} else {
									echo "
									<input type='hidden' name='sala' class='sala2' value='{$_GET['sala']}'>
									<input type='hidden' name='cod_computador2' value='{$value['cod_computador']}'>";
									echo "<tr><td>{$value['hostname']}</td><td>{$value['mac']}</td><td>{$value['ip']}</td><td>{$value['perfil']}</td>";					
									echo "<td><a title='alterar' href='valida_salas.php?sala=". htmlspecialchars($_GET['sala']) ."&acao=alterar&cod_computador=" .$value['cod_computador'] ."'><div class='alterar'></div></a>
									</a><a title='excluir' class='excluirHost' href='valida_salas.php?acao=excluir&cod_computador={$value['cod_computador']}'><div class='excluir'></div></a></td></tr>";
								}
								echo "</form>";
								
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
		<script type="text/javascript" >
			window.onload = function(){	
				var acao = document.getElementsByClassName("validaSalaForm");
				for(var i = 0; i < acao.length; i++) {					
					acao[i].onsubmit = function(event){
						event.preventDefault();
						
						var sala = this.elements[0].value;
						var cod_computador = this.elements[1].value;
						var hostname = this.elements[2].value;
						var mac = this.elements[3].value;
						var ip = this.elements[4].value;
						var perfil = this.elements[5].value;					
						

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
									location.reload();							
								}				
							}
						}
					  
					 xhttp.open("POST", "acoes/confirmaValidaHosts.php", true);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhttp.send("hostname=" + hostname + "&mac=" + mac + "&ip=" + ip + "&perfil=" + perfil + "&sala=" + sala + "&cod_computador=" + cod_computador + "&acao=alterar");

					};
				}







				/**************************************************************************************/

				
				
				
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
										location.reload();							
									}					      	
								}
							}
						  
						  xhttp.open("GET", "acoes/confirmaValidaHosts.php?" + parametros[1], true);
						  xhttp.send();

						}

					}
				}










		}

		</script>
		<script type='text/javascript' src='js/reloadSquid.js'></script>

	
		

</body>
</html>

