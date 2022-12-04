<?php
	include("includes/valida_sessao.php");
	include("includes/valida_nivel.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Bloqueios</title>
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
					<h1><a href='salas.php'>Bloqueios</a> </h1>
											
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span>"; ?></h3>
				</header>
					<article id="bloqueios">						
						<div class='bloqueiosBloco'>
						<div class="bloqueiosFormulario">
							<form method="POST" action="" id="cadastraSitesBloqueados">
								<label>Site para bloqueio</label>
								<br/>
								<input type="text" name="site" placeholder="Ex: .facebook.com">
								<br/>
								<input type="hidden" name="acao" value="inserir">
								<input type="hidden" name="sites_bloqueados" value="1">
								<button type="submit">Cadastrar</button>
							</form>
						</div>
						<table class='tabela3'>
						<tr><th colspan='2'>Sites bloqueados</th></tr>
						<tr><th>Site</th><th>Ação</th></tr>
					
						<?php
					include("includes/conexao.php");
					$conexao = new db();
					$sites_bloqueados = $conexao->lista("sites_bloqueados");
					
					if(isset($_GET['acao']) AND isset($_GET['sites_bloqueados'])) {
						if($_GET['acao'] == "alterar" AND isset($_GET['id'])) {							
							foreach ($sites_bloqueados as $key => $value) {
								if($value['id'] == $_GET['id']){
									echo "<form method='POST' action='' id='alteraSitesBloqueados'>
									<tr>
									<input type='hidden' name='id' value='{$value['id']}'>	
									<input type='hidden' name='acao' value='salvar'>		
									<input type='hidden' name='sites_bloqueados' value='1'>
									<td><input type='text' name='site' value='{$value['site']}'></td>
									<td>
										<button type='submit'>Salvar alterações</button>
									</td>
									</tr>
									</form>";
								}  else {
									echo "<tr><td>{$value['site']}</td>";
									echo "<td>
									<div class='blocoAcoes2'>
										<a title='alterar' href='?acao=alterar&id={$value['id']}&sites_bloqueados=1'><div class='alterar'></div></a>
										<a title='excluir' class='excluirSiteBloqueado' href='?acao=excluir&id={$value['id']}&sites_bloqueados=1'><div class='excluir'></div></a>
									</div>

									</td>
									</tr>";
								}
							}
						} 
					} else {
						foreach ($sites_bloqueados as $key => $value) {
							echo "<tr><td>{$value['site']}</td>";
							echo "<td>
							<div class='blocoAcoes2'>
								<a title='alterar' href='?acao=alterar&id={$value['id']}&sites_bloqueados=1'><div class='alterar'></div></a>
								<a title='excluir' class='excluirSiteBloqueado' href='?acao=excluir&id={$value['id']}&sites_bloqueados=1'><div class='excluir'></div></a>
							</div>

							</td>
							</tr>";
						}
					}
					
					?>
					</table>
				</div>

				<!--************************* CÓDIGO REPETIDO ***************************************-->
				<div class='bloqueiosBloco'>
						<div class="bloqueiosFormulario">
							<form method="POST" action="" id='cadastraPalavrasBloqueadas'>
								<label>Palavras para bloqueio</label>
								<br/>
								<input type="text" name="palavra" placeholder="Ex: sexo">
								<br/>
								<input type="hidden" name="acao" value="inserir">
								<input type="hidden" name="palavras_bloqueadas" value="1">
								<button type="submit">Cadastrar</button>
							</form>
						</div>
						<table class='tabela3'>
						<tr><th colspan='2'>Palavras bloqueadas</th></tr>
						<tr><th>Site</th><th>Ação</th></tr>
					
						<?php
					
					
					$palavras_bloqueadas = $conexao->lista("palavras_bloqueadas");
					
					if(isset($_GET['acao']) AND isset($_GET['palavras_bloqueadas'])) {
						if($_GET['acao'] == "alterar" AND isset($_GET['id'])) {							
							foreach ($palavras_bloqueadas as $key => $value) {
								if($value['id'] == $_GET['id']){
									echo "<form method='POST' action='' id='alteraPalavrasBloqueadas'>
									<tr>
									<input type='hidden' name='id' value='{$value['id']}'>	
									<input type='hidden' name='acao' value='salvar'>		
									<input type='hidden' name='palavras_bloqueadas' value='1'>
									<td><input type='text' name='palavra' value='{$value['palavra']}'></td>
									<td>
										<button type='submit'>Salvar alterações</button>
									</td>
									</tr>
									</form>";
								}  else {
									echo "<tr><td>{$value['palavra']}</td>";
							echo "<td>
							<div class='blocoAcoes2'>
								<a title='alterar' href='?acao=alterar&id={$value['id']}&palavras_bloqueadas=1'><div class='alterar'></div></a>
								<a title='excluir' class='excluirPalavraBloqueada' href='?acao=excluir&id={$value['id']}&palavras_bloqueadas=1'><div class='excluir'></div></a>
							</div>

							</td>
							</tr>";
								}
						}
					} 
					} else {
						foreach ($palavras_bloqueadas as $key => $value) {
							echo "<tr><td>{$value['palavra']}</td>";
							echo "<td>
							<div class='blocoAcoes2'>
								<a title='alterar' href='?acao=alterar&id={$value['id']}&palavras_bloqueadas=1'><div class='alterar'></div></a>
								<a title='excluir' class='excluirPalavraBloqueada' href='?acao=excluir&id={$value['id']}&palavras_bloqueadas=1'><div class='excluir'></div></a>
							</div>

							</td>
							</tr>";
						}
					}
					
					?>
					</table>
				</div>

				<!--************************* CÓDIGO REPETIDO ***************************************-->
				<div class='bloqueiosBloco'>
						<div class="bloqueiosFormulario">
							<form method="POST" action="" id='cadastraSiteLiberado'>
								<label>Sites Liberados</label>
								<br/>
								<input type="text" name="site" placeholder="Ex: .senacrs.com.br">
								<br/>
								<input type="hidden" name="acao" value="inserir">
								<input type="hidden" name="sites_liberados" value="1">
								<button type="submit">Cadastrar</button>
							</form>
						</div>
						<table class='tabela3'>
						<tr><th colspan='2'>Sites Liberados</th></tr>
						<tr><th>Site</th><th>Ação</th></tr>
					
						<?php
					
					
					$sites_liberados = $conexao->lista("sites_liberados");
					
					if(isset($_GET['acao']) AND isset($_GET['sites_liberados'])) {
						if($_GET['acao'] == "alterar" AND isset($_GET['id'])) {							
							foreach ($sites_liberados as $key => $value) {
								if($value['id'] == $_GET['id']){
									echo "<form method='POST' action='' id='alteraSitesLiberados'>
									<tr>
									<input type='hidden' name='id' value='{$value['id']}'>	
									<input type='hidden' name='acao' value='salvar'>		
									<input type='hidden' name='sites_liberados' value='1'>
									<td><input type='text' name='site' value='{$value['site']}'></td>
									<td>
										<button type='submit'>Salvar alterações</button>
									</td>
									</tr>
									</form>";
								}  else {
									echo "<tr><td>{$value['site']}</td>";
							echo "<td>
							<div class='blocoAcoes2'>
								<a title='alterar' href='?acao=alterar&id={$value['id']}&sites_liberados=1'><div class='alterar'></div></a>
								<a title='excluir' class='excluirSiteLiberado' href='?acao=excluir&id={$value['id']}&sites_liberados=1'><div class='excluir'></div></a>
							</div>

							</td>
							</tr>";
								}
						}
					} 
					} else {
						foreach ($sites_liberados as $key => $value) {
							echo "<tr><td>{$value['site']}</td>";
							echo "<td>
							<div class='blocoAcoes2'>
								<a title='alterar' href='?acao=alterar&id={$value['id']}&sites_liberados=1'><div class='alterar'></div></a>
								<a title='excluir' class='excluirSiteLiberado' href='?acao=excluir&id={$value['id']}&sites_liberados=1'><div class='excluir'></div></a>
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

		<script type="text/javascript" src="js/bloqueios.js"></script>
		<script type='text/javascript' src='js/reloadSquid.js'></script>

</body>
</html>
