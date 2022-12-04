<?php
	include("includes/valida_sessao.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Dashboard</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/estilo.css">
	<?php
		if($_SESSION['nivel'] == 2) {
			echo "<link rel='stylesheet' type='text/css' href='css/estilo2.css'>";
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
					<h1><a href='index.php'>Dashboard</a></h1>					
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span> "; ?></h3>
				</header>
				<article id="corpoDashboard">					
					<?php						
					include("includes/config.php");
					include("includes/conexao.php");
					$conexao = new db();	
					// Verifica se o usuário logado está com senha padrão					
					if($_SESSION['nivel'] == 2) {
						$senha = $conexao->listaWhere("usuarios","login", $_SESSION['login']);
						if($senha[0]['senha'] == $senha[0]['senha_default']) {
							header("Location: configuracoes.php?acao=senha");
						}			
					}
					?>	
					<div id='corpoDashboardBloco1'>	
					<table class='tabela3'>
						<tr>
							<th colspan='3'>Salas</th>
						</tr>
						<tr>
							<th>Sala</th>
							<th>Status</th>
							<th>Ação</th>
						</tr>						
						<?php
						// Se o usuário não for administrador não irá visualizar a sala210, webspace e biblioteca
						if($_SESSION['nivel'] == 1) {
							$resultado = $conexao->pesquisaPersonalizada("SELECT * FROM salas ORDER BY nome");
						} else {
							$resultado = $conexao->pesquisaPersonalizada("SELECT * FROM salas WHERE nome LIKE '%sala%' AND nome NOT IN('sala210','webspace','biblioteca') ORDER BY nome");;
						}
						
						foreach ($resultado as $key => $value) {
							$sala = $value['nome'];
							// Se o usuário não for administrador não irá visualizar as salas como links
							if($_SESSION['nivel'] > 1) {
								echo "<tr><td><a class='linkSalas' title='Visualizar hosts'>{$sala}</a></td>";
							} else {
								echo "<tr><td><a class='linkSalas' title='Visualizar hosts' href='salas.php?sala={$sala}'>{$sala}</a></td> ";
							}
							// Verificação de status
							if($value['bloqueio_total'] == TRUE){
								echo "<td><span class='bloqueado status'>Sem internet</span></td>";								
							} elseif($value['acl_sites_bloqueados'] == TRUE AND $value['acl_palavras_bloqueadas'] == TRUE) {
								echo "<td><span class='filtrado status'>Filtrado</span></td>";
							} else {
								echo "<td><span class='liberado status'>Liberado</span></td>";
							}

							echo "
								<td>
									<div class='blocosAcoes'>
									<a class='acao' title='Liberar acesso' href='index.php?sala={$value['nome']}&acao=liberar'>
										<div class='liberadoIcon'></div>
									</a>
									<a class='acao' title='Filtrar acesso' href='index.php?sala={$value['nome']}&acao=filtrar'>
										<div class='filtradoIcon'></div>
									</a>
									<a class='acao' title='Bloquear internet' href='index.php?sala={$value['nome']}&acao=bloquear_internet'>
										<div class='bloqueadoIcon'></div>
									</a>
									</div>
								</td>";

							}
							echo "<tr class='reiniciaSquid' ><th colspan='3'><button id='aplicarAcao'>Aplicar ação</button></th></tr></table>";
							echo "</div>";
						
						if($_SESSION['nivel'] == 1) {		
							
							echo "<div id='graficoMemoria'></div>";
							echo "<div id='graficoDiscoRaiz'></div>";
							echo "<div id='graficoDiscoCache'></div>";
							echo "<div id='graficoDiscoBackups'></div>";
							echo "<div id='squidConf'></div>";
						} 


					?>
					
				</article>

			</section>

			</main>
		</div>
		<div id="rodapeFundo">
			<footer id="rodape"></footer>
		</div>

		
		<?php
			if($_SESSION['nivel'] == 1) {
				echo "<script type='text/javascript' src='js/dashboardAdmin.js'></script>";				
				echo "<script type='text/javascript' src='js/reloadSquid.js'></script>";				
			} else {
				echo "<script type='text/javascript' src='js/dashboardDocente.js'></script>";
			}
		?>
</body>
</html>
