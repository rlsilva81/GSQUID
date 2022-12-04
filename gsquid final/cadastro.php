<?php
	include("includes/valida_sessao.php");
	include("includes/valida_nivel.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Cadastro</title>
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
					<h1>Cadastro</h1>
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span>"; ?></h3>
				</header>
				<article id="corpoCadastro">
					<div id="corpoCadastroBloco1">
						<div class='cadastroForm'>
							<div class='cadastroFormCabecalho'>
							<h3>Cadastro de computadores</h3>
							</div>
							<form method="POST" action="" id="cadastroComputador">
								
									<input type="hidden" name="cadastroComputador">
									<label>Hostname</label>
									<br/>
									<input type="text" id="hostname" name="hostname" placeholder="Ex: LAB20201" required>
									<br/>
									<label>MAC</label>
									<br/>
									<input id="mac" type="text" name="mac" placeholder="Ex: FF-FF-FF-FF-FF-FF" pattern="^([0-9a-fA-F][0-9a-fA-F]-){5}([0-9a-fA-F][0-9a-fA-F])$" required/>
									<br/>
									<label>IP</label>
									<br/>
									<input id="ip" type="text" name="ip" placeholder="Ex: 10.0.0.1" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$" required>
									<br/>
									<label>Sala</label>
									<br/>
									<select name="sala" id="salaComputador" required>
										<option value=""> --- Selecione uma sala --- </option>
										<?php
											include("includes/conexao.php");
											$conexao = new db();
											$salas = $conexao->lista("salas");
											foreach ($salas as $key => $value) {
												echo "<option value=" . $value['nome'] . ">" . $value['nome'] . "</option>";
											}
										?>
									</select>
									<br/>
									<div class="radio">
									<label>Perfil</label>
									<br/>
									<input type="radio" name="perfil" id="perfilAluno" value="aluno" checked> Aluno
									<input type="radio" name="perfil" id="perfilProfessor" value="professor"> Professor
									</div>
									<br/><br/>
									<button type="submit">Cadastrar</button>
							

							</form>
						</div>
					</div>
					<div id="corpoCadastroBloco2">
						<div class='cadastroForm'>
							<div class='cadastroFormCabecalho'>
							<h3>Cadastro de salas</h3>
							</div>
							<form method="POST" action="" id="cadastroSala">
								
									<input type="hidden" name="cadastroSala">
									<label>Sala</label>
									<br/>
									<input type="text" name="sala" id="sala" placeholder="Ex: SALA202" required>							
									<br/>
									<div class="radio">
									<label>Bloqueios</label>	
									<br/>							
									<input type="radio" name="sitesbloqueados" id="sitesbloqueados1" value="1" checked> Habilitar
									<input type="radio" name="sitesbloqueados" id="sitesbloqueados0" value="0"> Desabilitar
									</div>
									<br/><br/>
									<button type="submit">Cadastrar</button>
							

							</form>
						</div>
					</div>
				
				<?php
					

					include("includes/squid-class.php");
					include("includes/config.php");


					$cod_salas = $conexao->pesquisaPersonalizada("SELECT DISTINCT(cod_sala),nome FROM salas");
					echo "<pre>";
					
					foreach ($cod_salas as $key => $value) {
						$computadores = $conexao->pesquisaPersonalizada("SELECT * FROM computadores WHERE cod_sala = {$value['cod_sala']}");		
						
						echo "<table class='tabela3'><tr><th colspan='4'>Computadores de {$value['nome']} </th></tr>";
						echo "<tr><th>Hostname</th><th>MAC</th><th>IP</th><th>Perfil</th>";
						
						foreach ($computadores as $key => $dados) {
							echo "<tr><td>{$dados['hostname']}</td><td>{$dados['mac']}</td><td>{$dados['ip']}</td><td>{$dados['perfil']}</td></tr>";
						}
						echo "</table>";
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
			var mac = document.getElementById("mac");

			function formatMAC(e) {
			    var r = /([a-f0-9]{2})([a-f0-9]{2})/i,
			        str = e.target.value.replace(/[^a-f0-9]/ig, "");

			    while (r.test(str)) {
			        str = str.replace(r, '$1' + '-' + '$2');
			        str = str.toUpperCase();
			    }

			    e.target.value = str.slice(0, 17);
			};
			mac.addEventListener("keyup", formatMAC, false);

		
		}

	</script>

	<script type="text/javascript" src="js/cadastro.js"></script>
	<script type='text/javascript' src='js/reloadSquid.js'></script>

</body>
</html>
