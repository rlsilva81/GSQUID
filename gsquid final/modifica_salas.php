<?php
	include("includes/valida_sessao.php");
	include("includes/valida_nivel.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Mensagens</title>
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
							echo "<h1><a href='salas.php'>Salas</a> >> <a href='salas.php?sala={$_GET['sala']}'>". htmlspecialchars($_GET['sala']) ."</a> >> modificar sala </h1>";
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
					if(isset($_GET['sala'])) {
						if(isset($_GET['acao'])) {
							if($_GET['acao'] == 'alterar') {
								$resultado = $conexao->listaWhere("salas", "nome", $_GET['sala']);
								echo "<table class='tabela1'><tr><th>Nome da sala</th><th>Ação</th></tr>";
								foreach ($resultado as $key => $value) {
									echo "<tr><td>
									<form method='GET' action='' id='alteraSala'>
									<input type='text' name='sala' value='{$value['nome']}'>
									<input type='hidden' name='salaOld' value='{$value['nome']}'>
									<input type='hidden' name='acao' value='salvar'>
									</td>
									<td>
									<a href=''>
									<button>Salvar alteração</button>
									</a>
									</form>
									</td>
									</tr>";
								}
								echo "</table>";
							} elseif ($_GET['acao'] == 'excluir') {
								$resultado = $conexao->listaWhere("salas", "nome", $_GET['sala']);
								$cod_sala = $resultado[0]['cod_sala'];
								if($conexao->listaWhere("computadores", "cod_sala", $cod_sala)) {
									echo "<h1>Não é possível excluir esta sala pois existem computadores registrados nela</h1>";
								} else {
									if($conexao->deleta("salas", "nome", $resultado[0]['nome'])){
										header("Location: acoes/atualizaTudo.php?voltar=salas");
									}
								}	
							} elseif ($_GET['acao'] == 'salvar' AND isset($_GET['salaOld'])) {
								$dados = array(
									'nome' => $_GET['sala']
								);
								if($conexao->altera("salas", $dados, "nome", $_GET['salaOld'])){
									header("Location: acoes/atualizaTudo.php?voltar=salas");
								} else {
									echo "<h1>Erro</h1>";
								}
							}
						} else {
							$resultado = $conexao->listaWhere("salas", "nome", $_GET['sala']);
							echo "<table class='tabela1'><tr><th>Nome da sala</th><th>Ação</th></tr>";
							foreach ($resultado as $key => $value) {
								echo "<tr><td>" . htmlspecialchars($value['nome']) . "</td>									
									<td>
									<div class='blocoAcoes2'>
										<a title='alterar' href='modifica_salas.php?sala=". htmlspecialchars($value['nome']) ."&acao=alterar'><div class='alterar'></div></a></a><a id='excluirSala' title='excluir' href='modifica_salas.php?sala=". htmlspecialchars($value['nome']) ."&acao=excluir'><div class='excluir'></div></a></div></td></tr>";
							}
							echo "</table>";
							echo "<script src='js/excluiSala.js'></script>";
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
						


			document.getElementById("alteraSala").onsubmit = function(event){
					event.preventDefault();
					
						
						var sala = this.elements[0].value;
						var salaOld = this.elements[1].value;
						

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
					  

					 xhttp.open("GET", "acoes/atualizaSalas.php?" + "sala=" + sala + "&salaOld=" + salaOld + "&acao=salvar", true);
					 
					  xhttp.send();

				
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
