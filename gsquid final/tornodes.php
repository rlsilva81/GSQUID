<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<link rel="stylesheet" href="">
</head>
<body>
	<button id="retornaIps">Retornar IPs do TOR</button>
	<div id="container">
	<?php
		system("wget -O /tmp/tornodes https://www.dan.me.uk/tornodes");
		$tornodes = file("/tmp/tornodes");		
		$linhas = preg_grep("/^([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\|)/", $tornodes);
		foreach ($linhas as $key => $value) {
			$linha = explode("|", $value); 
			$ips[] = $linha[0];			
		}
		$totalIps = count($ips);

/*
		$arquivo = fopen("/tmp/tornodes.txt", "w") or die("Não é possível escrever no arquivo");
		foreach ($ips as $key => $value) {
			fwrite($arquivo, $value . "\n");
		}
		fclose($arquivo);
*/


		echo "<h1>Total de: " . $totalIps . " IPs</h1>";
		echo "<pre>";
		print_r($ips);
	?>
	</div>
	

	
</body>
</html>