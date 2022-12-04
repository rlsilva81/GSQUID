<?php
	include("includes/valida_sessao.php");
	include("includes/valida_nivel.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Usuários</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/estilo.css">
	<style>
	
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
					<h1><a href='usuarios.php'>Usuários</a></h1>
					<h3>Usuário logado: <?php echo "<span style='color: red'>" . $_SESSION['login'] . "</span> "; ?></h3>
					
				</header>
				<article id="usuariosCorpo">

					
				
				<div id="cadastroUsuario">
					<div id="cabecalhoCadastroUsuario">
						<h1>Cadastro de usuário</h1>
					</div>
					<form method="POST" action="" id="cadastraUsuario">
						<label>Nome</label>
						<br/>
						<input type="text" name="nome" placeholder="Digite o nome" id="nome" required>
						<br/>
						<label>Senha</label>
						<br/>
						<input type="password" name="senha" placeholder="Digite a senha" id="senha" required>
						<br/>
						<label>Login</label>
						<br/>
						<input type="text" name="login" placeholder="Digite o login" id="login" required>
						<br/>
						<label>Email</label>
						<br/>
						<input type="email" name="email" placeholder="Digite o email" id="email" required>
						<br/>
						<label>Nível de permissão</label>
						<br/>
						<div class='radioUsuarios'>
						<input type="radio" name="nivel" value="1" id="nivel1"> Administrador
						<input type="radio" name="nivel" value="2" id="nivel2" checked> Docente
						</div>
						<br/>
						<input type="hidden" name="acao" value="cadastrar">
						<button type="submit">Cadastrar</button>
					</form>
				</div>
				<table class='tabela4'>
					<tr><th colspan='7'>Usuários</th></tr>
					<tr><th>ID</th><th>Nome</th><th>Login</th><th>Email</th><th>Nível</th><th>Senha</th><th>Ação</th></tr>
					<?php
					include("includes/conexao.php");
					$conexao = new db();
					
					$usuarios = $conexao->lista("usuarios");
					if(isset($_GET['acao'])) {
						if($_GET['acao'] == "alterar" AND isset($_GET['id'])) {							
							foreach ($usuarios as $key => $value) {
								if($value['id'] == $_GET['id']){
									echo "<form method='POST' action='' id='alteraUsuario'>
									<tr><input type='hidden' name='id' value='{$value['id']}' id='idAltera'>	
									<input type='hidden' name='acao' value='salvar'>			
									<td>{$value['id']}</td>
									<td><input type='text' name='nome' value='{$value['nome']}' id='nomeAltera'></td>
									<td><input type='text' name='login' value='{$value['login']}' id='loginAltera'></td>
									<td><input type='text' name='email' value='{$value['email']}' id='emailAltera'></td>
									<td><input type='text' name='nivel' value='{$value['nivel']}' id='nivelAltera'></td>
									<td><input type='password' name='senha' value='******' id='senhaAltera'></td>
									<td>
										<button type='submit'>Salvar alterações</button>
									

									</td>
									</tr>
									</form>";
								}  else {
									echo "<tr>
									<td>{$value['id']}</td>
									<td>{$value['nome']}</td>
									<td>{$value['login']}</td>
									<td>{$value['email']}</td>
									<td>{$value['nivel']}</td>
									<td>******</td>
									<td>
									<div class='blocoAcoes2'>
										<a title='alterar' href='?acao=alterar&id={$value['id']}'><div class='alterar'></div></a>
								<a title='excluir' class='excluirUsuario' href='?acao=excluir&id={$value['id']}'><div class='excluir'></div></a>
									</div>

									</td>
									</tr>";
								}
						}
						} 
					} else {
						foreach ($usuarios as $key => $value) {
							echo "<tr><td>{$value['id']}</td><td>{$value['nome']}</td><td>{$value['login']}</td><td>{$value['email']}</td><td>{$value['nivel']}</td><td>******</td>";
							echo "<td>
							<div class='blocoAcoes2'>
								<a title='alterar' href='?acao=alterar&id={$value['id']}'><div class='alterar'></div></a>
								<a title='excluir' class='excluirUsuario' href='?acao=excluir&id={$value['id']}'><div class='excluir'></div></a>
							</div>

							</td>
							</tr>";
						}
					}
					
					?>
				</table>
				</article>

			</section>

			</main>
		</div>
		<div id="rodapeFundo">
			<footer id="rodape"></footer>
		</div>
		
		<script type="text/javascript" src="js/usuarios.js"></script>	
		<script type='text/javascript' src='js/reloadSquid.js'></script>
		
</body>
</html>
