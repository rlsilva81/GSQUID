<?php
	include("includes/valida_sessao.php");
	
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Configurações</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/estilo.css">
	<?php
		if($_SESSION['nivel'] == 2) {
			echo "<link rel='stylesheet' type='text/css' href='css/estilo2.css'>";
		}
	?>


	<?php
	if(isset($_GET['acao'])) {
			if($_GET['acao'] == "senha") {
				echo "<style>
					input[name='senhaAtual'], input[name='senhaNova'], input[name='confirmaSenhaNova'] {
						border: solid 1px #f00;
					}
				</style>";
			}
		}
	?>

	<?php
		if(isset($_GET['acao'])) {
			if($_GET['acao'] == "senha") {
				echo "<script>alert('Altere sua senha!')</script>";
			}
		}
	?>
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
					<h1>Configurações</h1>
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span>"; ?></h3>
				</header>
				

				
				<article id="corpoConfiguracoes">
					<div class="configuracoesForm">
						<form method="POST" action="acoes/alteraSenha.php?acao=alterarSenha" id="alteraSenha">
							<h3>Alterar senha</h3>
							<input type="password" name="senhaAtual" id="senhaAtual" placeholder="Senha atual">
							<br/>
							<input type="password" name="senhaNova" id="senhaNova" placeholder="Nova senha">
							<br/>
							<input type="password" name="confirmaSenhaNova" id="confirmaSenhaNova" placeholder="Confirmar nova senha">
							<br/>
							<input type="hidden" name="acao" value="alteracaoSenha">
							<button type="submit">Alterar</button>
						</form>
					</div>
					<?php
					if($_SESSION['nivel'] == 1) {
					?>
					<div class="configuracoesSquid">
						<h1>Configurações de sistema</h1>
						<?php
							if(isset($_POST['dados'])) {
								$arquivo = fopen("includes/config.php", "w") or die("Não é possível escrever no arquivo");								
								fwrite($arquivo, $_POST['dados']);
								fclose($arquivo);
							}

							include("includes/squid-class.php");							
							$squid = new squid("includes/config.php");
							$resultado = $squid->listaTudo();
							
							$dados = "";
							foreach ($resultado as $key => $value) {
								//if(stripos($value, "[]") !== FALSE) {
									//$dado = explode("\"", $value);
									//$dados = $dado[1] . "\n" . $value;
									$dados = $dados . "\n" . $value;
									//echo "<pre>";
									//print_r($dado[1]);
								//}								
							}
							
							echo "<form method='POST' action=''>";
							echo "<textarea name='dados'>{$dados}</textarea>";
							echo "<br/>";
							echo "<button type='submit'>Salvar</button>";
							echo "</form>";

						?>
					</div>
					<?php
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
			document.getElementById("alteraSenha").onsubmit = function(event){
				event.preventDefault();
				var senhaAtual = document.getElementById("senhaAtual").value;
				var senhaNova = document.getElementById("senhaNova").value;
				var confirmaSenhaNova = document.getElementById("confirmaSenhaNova").value;
				var telaEscura = document.getElementById("telaEscura");
				telaEscura.style.display = "block";
				var log = document.getElementById("aviso");
				log.style.backgroundImage = "url('imagens/loading.gif')";
				var xhttp = new XMLHttpRequest();
				  xhttp.onreadystatechange = function() {
				    if (xhttp.readyState == 4 && xhttp.status == 200) {
				    	log.style.backgroundImage = "";
				    	log.innerHTML = xhttp.responseText;
				    	log.innerHTML = log.innerHTML + "<button id='fecharTelaEscura'>Fechar</button>";
			    		document.getElementById("fecharTelaEscura").focus();
			    		document.getElementById('fecharTelaEscura').onclick = function(){								
							log.innerHTML = "";
							telaEscura.style.display = "none";	
							window.location.href = 'index.php';														
						}
				    }
				  };
				  
				  xhttp.open("POST", "acoes/alteraSenha.php", true);
				  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				  xhttp.send("senhaAtual=" + senhaAtual + "&senhaNova=" + senhaNova + "&confirmaSenhaNova=" + confirmaSenhaNova);

			}


		}

	</script>

	<?php
		if($_SESSION['nivel'] == 1) {
			echo "<script type='text/javascript' src='js/reloadSquid.js'></script>";
		}
	?>

</body>
</html>
