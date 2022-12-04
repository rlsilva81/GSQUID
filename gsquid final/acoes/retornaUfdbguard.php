<?php

$diretorio_blacklists = "/var/ufdbguard/blacklists/";

if(isset($_POST['acao']) AND isset($_POST['blacklist']) AND isset($_POST['dominio'])) {
	
	// Filtra parâmetros
	$_POST['blacklist'] = strtolower(trim($_POST['blacklist']));
	if(stripos($_POST['blacklist'], $diretorio_blacklists) === FALSE) {
		echo "Diretório inválido";
		exit;
	}
	$_POST['dominio'] = strtolower(trim($_POST['dominio']));

	if($_POST['acao'] == "procurar") {		
		$resultado = shell_exec("grep " . $_POST['dominio'] . " " . $_POST['blacklist']);									
		$sites = explode("\n", $resultado);
		$qtd = (count($sites) - 1);

		if($qtd > 0) {
			echo "<h1>Busca: \"". htmlspecialchars($_POST['dominio']) ."\"</h1>";
			echo "<h1>Blacklist: ". htmlspecialchars($_POST['blacklist']) ."</h1>";
			echo "<h1>" . $qtd . " sites encontrados</h1>";									
			echo "<button id='botaoSites' onclick='exibeSites();'>Mostrar sites</button>";
			echo "<div id='sitesEncontrados' style='display:none;'>";
			if (count($sites) > 1000) {
				echo "<p>A pesquisa ultrapassou 1000 resultados...</p>";
			} else {
				foreach ($sites as $key => $value) {
					echo $value . "<br/>";
				}
			}
			
			echo "</div>";
		} else {
			echo "<h1>Busca: \"". htmlspecialchars($_POST['dominio']) ."\"</h1>";
			echo "<h1>Blacklist: ". htmlspecialchars($_POST['blacklist']) ."</h1>";
			echo "<h1>Nenhum site encontrado!</h1>";
		}
		
		
	} elseif($_POST['acao'] == "inserir") {
		
		$resultado = shell_exec("egrep '^" . $_POST['dominio'] . "\$' " . $_POST['blacklist']);		
		if($resultado) {
			echo "Domínio já registrado!";
		} else {
			$file = fopen($_POST['blacklist'],"a");
			if(fwrite($file,$_POST['dominio'] . "\n")) {
				echo "Domínio \"" . htmlspecialchars($_POST['dominio']) ."\" foi inserido na blacklist \"". htmlspecialchars($_POST['blacklist']) ."\"";
			}
			fclose($file);			
		}
		
		
	} elseif($_POST['acao'] == "excluir"){
		$resultado = shell_exec("egrep '^" . $_POST['dominio'] . "\$' " . $_POST['blacklist']);	
		$resultado = str_replace("\n", "", $resultado);
		if($resultado == $_POST['dominio']) {
			shell_exec("sed -e 's/\<". $_POST['dominio'] ."\>\$//g' ". $_POST['blacklist'] ." | sed '/^$/d' > /tmp/blacklist_exclui_temp.txt");
			shell_exec("cat /tmp/blacklist_exclui_temp.txt | awk '!seen[$0]++' > ". $_POST['blacklist']);
			unlink("/tmp/blacklist_exclui_temp.txt");
			echo "Domínio \"" . htmlspecialchars($_POST['dominio']) ."\" foi excluído da blacklist \"". $_POST['blacklist'] ."\"";
		} else {
			echo "Nenhum domínio encontrado.";
		}	
		
	}  /*elseif ($_POST['acao'] == "atualizarOnline") {
		if(stripos($_POST['blacklist'], "/tor/") !== FALSE) {
			if(isset($_POST['confirma'])) {
				$tornodes = file("/tmp/tornodes");		
				$linhas = preg_grep("/^([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\|)/", $tornodes);
				$file = fopen($_POST['blacklist'],"w");
				foreach ($linhas as $key => $value) {
					$linha = explode("|", $value); 
					fwrite($file,$linha[0] . "\n");	
				}
				fclose($file);
				echo "<p>Atualização realizada com sucesso!</p>";
				exit;
			}
			//system("wget -O /tmp/tornodes https://www.dan.me.uk/tornodes");
			system("wget -O /tmp/tornodes http://127.0.0.1/tornodes");
			$tornodes = file("/tmp/tornodes");		
			$linhas = preg_grep("/^([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\|)/", $tornodes);
			foreach ($linhas as $key => $value) {
				$linha = explode("|", $value); 
				$ips[] = $linha[0];			
			}
			if(!isset($ips)) {
				echo "erro";
				exit;
			}
			$totalIps_site = count($ips);
			$totalIps_blacklist = shell_exec("wc -l " . $_POST['blacklist'] . " | cut -d ' ' -f1");			
			if($totalIps_blacklist > $totalIps_site) {
				echo "<p>A atualização possui {$totalIps_site} endereços e sua blacklist possui {$totalIps_blacklist}.</p>";
				echo "<p>Deseja atualizar a blacklist mesmo assim?</p>";
			} elseif ($totalIps_blacklist < $totalIps_site) {				
				echo "atualizar";
			} else {
				echo "atualizado";
			}
		} elseif (stripos($_POST['blacklist'], "/proxies/") !== FALSE) {
			echo "erro";
		} elseif (stripos($_POST['blacklist'], "/porn/") !== FALSE) {
			echo "erro";
		} else {
			echo "erro";
		}
	} elseif ($_POST['acao'] == 'uploadBlacklist') {
		if (move_uploaded_file($_FILES['blacklistFile']['tmp_name'], "/tmp/blacklist_update_tmp.txt")) {
			echo "Upload realizado com sucesso!";
		} else {
			echo "erro_upload";
		}
	}*/
}

if(isset($_POST['acao'])) {
	if ($_POST['acao'] == "atualizarBlacklist") {
		$resultado = shell_exec("ps -aux | grep 'ufdbGenTable'");
		if(stripos($resultado, "ufdbGenTable -n") !== FALSE) {
			echo "Já existe um processo de atualização em andamento. Por favor, aguarde...";
			exit;
		}
		$convert_blacklist = shell_exec("sudo /usr/sbin/ufdbConvertDB /var/ufdbguard/blacklists");
		$reconfig_ufdb = shell_exec("sudo /etc/init.d/ufdb reconfig");
		echo "<pre>";
		print_r($convert_blacklist);
		print_r($reconfig_ufdb);
		
	}
}




?>