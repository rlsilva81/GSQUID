<?php
	include("includes/valida_sessao.php");
	include("includes/valida_nivel.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Relatório</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/estilo.css">
	<style>

	#blocoRelatorioPersonalizado {
		text-align: center;
	}
	.blocoRelatorios {
		width: 50%;
		text-align: center;
		float: left;
	}

	.blocoRelatorios:nth-child(2) {
	
	}
	

	.blocoRelatorios table {
		margin: auto;
		margin-top: 20px;
		margin-bottom: 20px;
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
					<h1>Relatorios</h1>
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span>"; ?></h3>
				</header>

				<article id="corpoRelatorios">
				
					<div id="blocoRelatorioPersonalizado">
						<h1>Relatórios de estatísticas personalizadas</h1>	
						<table class='tabela3'>
						<tr><th colspan="5">Pesquisa personalizada</th></tr>
						<tr><th>Endereço IP*</th><th>Data*</th><th>Turno*</th><th>Horario</th><th>Site</th></tr>
						<tr>
							<form method="POST" action="gerarRelatorio.php" target="_blank">
							<input type="hidden" name="relatorio" value="personalizado">
							<td><input type="text" name="ip" placeholder="Ex: 10.10.1.2"></td>
							<td><input type="date" name="data" placeholder="Ex: 01/07/2016"></td>
							<td>
								<select name="turno">
									<option>-- Selecione --</option>
									<option value="madrugada">Madrugada</option>
									<option value="manha">Manhã</option>
									<option value="tarde">Tarde</option>
									<option value="noite">Noite</option>
								</select>
							</td>
							<td><input type="text" name="horario" placeholder="Ex: 18:00:00-19:00:00"></td>
							<td><input type="text" name="site" placeholder="Ex: facebook"></td>
						</tr>
						<tr>
							<td colspan="5"><button type="submit">Gerar relatório</button></td>
							</form>
						</tr>
						</table>

					</div>

					
						
						<?php	
						if(isset($_GET['relatorio'])) {
							$localizacao = explode("/", $_GET['relatorio']);
							$localizacao = end($localizacao);
							header("Location: temp/" . $localizacao);
							exit;
						}
						include("includes/config.php");
						foreach(glob($pastaTemp . "*") as $file) {
							if(stripos($file, "squidlog.") !== FALSE) {
								
								$array = explode(".", $file);
								$data = $array[1];
								$nome = explode("/", $file);
								$nome = end($nome);
								
								if(count($array) == 4) {
									$nome = explode(".", $nome);
									$turno = $nome[2];
									$log[$data][$turno]['nome'] = $nome[0] . "." . $nome[1] . "." .$nome[2];
									$log[$data][$turno]['data'] = $data;
									$log[$data][$turno]['turno'] = $nome[2];
									$log[$data][$turno][$array[3]] = $array[3]; 
								} else {
									$turno = end($array);
									$log[$data][$turno]['nome'] = $nome;
									$log[$data][$turno]['data'] = $data;
									$log[$data][$turno]['turno'] = end($array);
								}

							}
						}


						if(isset($log)) {
							echo "<div class='blocoRelatorios'>";
							echo "<h1>Relatórios de estatísticas do uso de banda</h1>";
							foreach ($log as $key => $value) {
								echo "<table class='tabela3'><tr><th colspan='2'>{$key}</th></tr>";
								echo "<tr><th>Turno</th><th>Arquivo</th></tr>";
								foreach ($value as $turno => $arquivo) {								
									echo "<tr><td>" . $turno . "</td><td><a target='_blank' href='gerarRelatorio.php?relatorio={$arquivo['nome']}'>Gerar relatório</a></td>";
								}
								echo "</table>";
							}
							echo "</div>";
						}
						

						?>			
					
					<div class="blocoRelatorios">
						<h1>Relatórios de ações no sistema</h1>	
						<?php						
						//include("includes/conexao.php");
						//$conexao = new db();
						?>
						<table class='tabela3'>
						<tr><th>Logins realizados</th></tr>
						<tr><td>
						<form method="POST" action="gerarRelatorio.php" target="_blank">							
							<input type="date" name="data">
							<input type="hidden" name="relatorio" value="loginsRealizados">
						
						</td></tr>
						<tr>
						<td><button type="submit" >Gerar relatório</button>	</td>
						</form>
						</tr>
						</table>

						<table class='tabela3'>
						<tr><th>Logins sem sucesso</th></tr>
						<tr><td>
						<form method="POST" action="gerarRelatorio.php" target="_blank">							
							<input type="date" name="data">
							<input type="hidden" name="relatorio" value="loginsFalha">						
						</td></tr>
						<tr>
						<td><button type="submit" >Gerar relatório</button>	</td>
						</form>
						</tr>
						</table>

						<table class='tabela3'>
						<tr><th>Ações realizadas no sistema</th></tr>
						<tr><td>
						<form method="POST" action="gerarRelatorio.php" target="_blank">							
							<input type="date" name="data">
							<input type="hidden" name="relatorio" value="acoesRealizadas">	
						</td></tr>
						<tr>
						<td><button type="submit" >Gerar relatório</button>	</td>
						</form>
						</tr>
						</table>
					</div>
					
				</article>
			</section>

			</main>
		</div>
		<div id="rodapeFundo">
			<footer id="rodape"></footer>
		</div>

		<script type='text/javascript' src='js/reloadSquid.js'></script>

</body>
</html>
