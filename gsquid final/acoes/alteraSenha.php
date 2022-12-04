
<?php
	include("../includes/valida_sessao.php");

	include("../includes/conexao.php");
	$conexao = new db();
								
		$senhaAtual = $conexao->listaWhere("usuarios","login", $_SESSION['login']);
		if($senhaAtual[0]['senha'] == sha1($_POST['senhaAtual'])) {
			if($_POST['senhaNova'] == $_POST['confirmaSenhaNova']) {
				$dados = array(
				'senha' => sha1($_POST['senhaNova'])
				);
				if($conexao->altera("usuarios",$dados,"login",$_SESSION['login'])){
					echo "<h3>Senha alterada com sucesso!</h3>";
				} else {
					echo "<h3>Sua senha não foi alterada!</h3>";
				}
			} else {
				echo "<h3>Senhas não conferem!</h3>";
			}								
		} else {
			echo "<h3>Senha atual incorreta!</h3>";
		}
	
