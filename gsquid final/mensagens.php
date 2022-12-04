<?php
	include("includes/valida_sessao.php");
	
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Mensagens</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/estilo.css">
	<?php
		if($_SESSION['nivel'] > 1) {
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
					<h1>Mensagens</h1>
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span>"; ?></h3>
				</header>
				<?php
					include("includes/conexao.php");
					$conexao = new db();
					if(isset($_POST['acao'])) {
						if ($_POST['acao'] == "mensagem") {								
							$id_remetente = $conexao->listaWhere("usuarios", "login", $_SESSION['login']);
							if($id_remetente[0]['nivel'] == '2') {
								$id_destinatario = $conexao->listaWhere("usuarios", "nivel", 1);							
							} elseif(isset($_POST['destinatario'])) {								
								$id_destinatario = $conexao->listaWhere("usuarios", "login", $_POST['destinatario']);								
							} else {
								$id_destinatario = $conexao->listaWhere("usuarios", "nivel", 2);
							}
							
							foreach ($id_destinatario as $key => $value) {
								$dados = array(
									'mensagem' => $_POST['mensagem'],
									'id_destinatario' => $value['id'],
									'id_remetente' => $id_remetente[0]['id'],
									'hora' => time()
								);
								if($conexao->insere("mensagens", $dados)) {
									echo "<h1>Mensagem enviada!</h1>";
								} else {
									echo "<h1>Erro ao enviar a mensagem!</h1>";
								}
							}
						}
					}


				?>
				
				<article id="corpoMensagens">
					<div class="mensagensForm">
						<form method="POST" action="">
							<?php
								if(isset($_GET['acao'])) {
									if($_GET['acao'] == "responder") {
										$cod_mensagem = $_GET['id'];
										$destinatario = $_GET['destinatario'];
										echo "<h3>Enviar mensagem para " . htmlspecialchars($destinatario) . "</h3>";
										?>
										<textarea name="mensagem" cols="50" rows="6"></textarea>
										<br/>
										<input type="hidden" name="acao" value="mensagem">
										<input type="hidden" name="destinatario" <?php echo "value='{$destinatario}'"; ?>>
										<button type="submit">Enviar</button>

										<?php
									} 
								} else {
										$usuario = $conexao->listaWhere("usuarios", "login", $_SESSION['login']);
										if($usuario[0]['nivel'] > 1) {
										?>
										<h3>Enviar mensagem para o administrador</h3>
										
										<textarea name="mensagem" cols="50" rows="6"></textarea>
										<br/>
										<input type="hidden" name="acao" value="mensagem">
										<button type="submit">Enviar</button>

										<?php
										} else {
											?>
												<h3>Enviar mensagem para usuários</h3>
										
												<textarea name="mensagem" cols="50" rows="6"></textarea>
												<br/>
												<input type="hidden" name="acao" value="mensagem">
												<button type="submit">Enviar</button>

											<?php
										}
								}
							?>
							
							
						</form>
					</div>
					<table class="tabela3">
						<tr><th colspan="5">Mensagens recebidas</th></tr>
						<tr><th>Remetente</th><th>Destinatário</th><th>Mensagem</th><th>Horário</th><th>Ação</th></tr>
						<?php
					
							/* PROCEDURES UTILIZADA

								DELIMITER $$
								CREATE PROCEDURE busca_mensagens_destinada(var_login varchar(45)) 
								BEGIN  
								SELECT u.login,u.nome, m.cod_mensagem, m.mensagem, m.hora, (SELECT nome FROM usuarios WHERE id = m.id_remetente) AS remetente_nome, (SELECT login FROM usuarios WHERE id = m.id_remetente) AS remetente_login FROM usuarios u 
								JOIN mensagens m ON u.id = m.id_destinatario WHERE u.login = var_login;
								END$$
								DELIMITER ;


								DELIMITER $$
								CREATE PROCEDURE busca_mensagens_originada(var_login varchar(45)) 
								BEGIN  
								SELECT u.login,u.nome, m.cod_mensagem, m.mensagem, m.hora, (SELECT nome FROM usuarios WHERE id = m.id_remetente) AS remetente_nome, (SELECT login FROM usuarios WHERE id = m.id_remetente) AS remetente_login FROM usuarios u 
								JOIN mensagens m ON u.id = m.id_remetente WHERE u.login = var_login;
								END$$

								DELIMITER ;

							*/
							
							$resultado = $conexao->listaWhere("usuarios", "login", $_SESSION['login']);
							$id = $resultado[0]['id'];
							$resultado = $conexao->pesquisaPersonalizada("select cod_mensagem, mensagem, hora, (select login from usuarios where id = id_destinatario) as login_destinatario, (select login from usuarios where id = id_remetente) as login_remetente from mensagens WHERE id_destinatario = {$id};");

							foreach ($resultado as $key => $value) {
								echo "<tr style='background-color: orange;'> 
									<td>{$value['login_remetente']}</td>
									<td>{$value['login_destinatario']}</td>
									<td>{$value['mensagem']}</td> 
									<td>" . date("H:m:s d/m/Y",$value['hora']) ."</td>
									<td><a href='?acao=responder&id={$value['cod_mensagem']}&destinatario={$value['login_remetente']}'><button>Responder</button></a></td>
									</tr>
								";
							}

							?> </table>

							<table class="tabela3">
							<tr><th colspan="5">Mensagens enviadas</th></tr>
							<tr><th>Remetente</th><th>Destinatário</th><th>Mensagem</th><th>Horário</th><th>Ação</th></tr>
							<?php


							$resultado = $conexao->pesquisaPersonalizada("select mensagem, hora, (select login from usuarios where id = id_destinatario) as login_destinatario, (select login from usuarios where id = id_remetente) as login_remetente from mensagens WHERE id_remetente = {$id};");

							foreach ($resultado as $key => $value) {
								echo "<tr> 
									<td>{$value['login_remetente']}</td>
									<td>{$value['login_destinatario']}</td>
									<td>{$value['mensagem']}</td> 
									<td>" . date("H:m:s d/m/Y",$value['hora']) ."</td>
									<td><a href=''>Excluir</a></td>
									</tr>
								";
							}


						?>
						</table>
					
					
				</article>
			</section>

			</main>
		</div>
		<div id="rodapeFundo">
			<footer  id="rodape"></footer>
		</div>

		<script type='text/javascript' src='js/reloadSquid.js'></script>

</body>
</html>
