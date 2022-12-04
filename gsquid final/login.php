<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Login</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/estilo.css">		
</head>
<body id="loginBody">
	<?php
		include("includes/verifica_navegador.php");
	?>
	<div id="telaEscura">
		<div id="aviso"></div>
	</div>
		<header id="loginCabecalho">
			<img src="imagens/squid.png">
			<img src="imagens/senac.png">			
		</header>	
			<div id="loginCorpo">
				<article id="formularioLogin">
					<div id="formularioCabecalho">
						<h1>Login</h1>
					</div>
					<form method="POST" action="" id="formulario">						
						<input type="text" name="login" placeholder="Login" id="login" required>
						<br/>						
						<br/>
						<input type="password" name="senha" placeholder="Senha" id="senha" required>
						<br/>
						<div id="loginCheckbox">
							<a href="" id="esqueceuSenha">Esqueceu sua senha?</a>
						</div>
						<div id="loginBotao">
						<button type="submit">Entrar</button>						
						</div>
					</form>				
				</article>

			</div>


		
		<script type="text/javascript" src="js/login.js"></script>
</body>
</html>
