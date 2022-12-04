<?php

include("plugins/tcpdf/tcpdf.php");
include("includes/config.php");

ini_set('memory_limit', '1024M');

//create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Senac informatica');
$pdf->SetTitle('Relatório Squid ' . date("d/m/Y H:i:s"));
$pdf->SetSubject('Relatório diário');
$pdf->SetKeywords('TCPDF, PDF, squid, senac, relatório');


// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language dependent data:
$lg = Array();
$lg['a_meta_charset'] = 'UTF-8';
$lg['a_meta_dir'] = 'ltr';
$lg['a_meta_language'] = 'fa';
$lg['w_page'] = 'page';


// add a page
$pdf->AddPage();







// print newline
//$pdf->Ln();

// ---------------------------------------------------------
include("includes/conexao.php");
$contador = 0;
/* Relatório de logins efetuados */
if(isset($_POST['relatorio'])) {
	if($_POST['relatorio'] == 'loginsRealizados' AND isset($_POST['data'])) {
		$pdf->SetFont('helvetica', 'B', 14);
		$pdf->Write(0, "Logins realizados no sistema em " . htmlspecialchars($_POST['data']), '', 0, 'L', true, 0, false, false, 0);
		$pdf->SetFont('helvetica', '', 9);
			$html = <<<EOD
			<br><br>
			<table cellspacing="1" cellpadding="1" border="1" align="center">
				<tr nobr="true">
					<th style="font-weight: bold;">Login</th>
					<th style="font-weight: bold;">Data</th>
					<th style="font-weight: bold;">Endereço IP</th>
				</tr>
EOD;
		$conexao = new db();
		$resultado = $conexao->pesquisaPersonalizada("SELECT l.logou, l.ip, u.login FROM log_logins l JOIN usuarios u ON l.id_usuario = u.id ORDER BY l.logou DESC");
		foreach ($resultado as $key => $value) {
			$data = date("Y-m-d", $value['logou']);
			if($_POST['data'] == $data) {
				$contador++;
				$html .= "<tr><td>{$value['login']}</td><td>" . date("Y/m/d H:i:s", $value['logou']) . "</td><td>{$value['ip']}</td></tr>";				
			}
		}
		$html .= "</table><br/><br/>";		

		
	} elseif ($_POST['relatorio'] == 'loginsFalha' AND isset($_POST['data'])) {		
		$pdf->SetFont('helvetica', 'B', 14);
		$pdf->Write(0, "Logins sem sucesso em " . htmlspecialchars($_POST['data']), '', 0, 'L', true, 0, false, false, 0);
		$pdf->SetFont('helvetica', '', 9);
				$html = <<<EOD
			<br><br>
			<table cellspacing="1" cellpadding="1" border="1" align="center">
				<tr nobr="true">
					<th style="font-weight: bold;">Login</th>
					<th style="font-weight: bold;">Data</th>
					<th style="font-weight: bold;">Endereço IP</th>
				</tr>
EOD;
		$conexao = new db();
		$resultado = $conexao->lista("log_tentativas");
		foreach ($resultado as $key => $value) {
			$data = date("Y-m-d", $value['hora']);
			if($_POST['data'] == $data) {
				$contador++;
				$html .= "<tr><td>{$value['login']}</td><td>" . date("Y/m/d H:i:s", $value['hora']) . "</td><td>{$value['ip']}</td></tr>";				
			}
		}
		$html .= "</table><br/><br/>";	
	} elseif ($_POST['relatorio'] == 'acoesRealizadas' AND isset($_POST['data'])) {
		
		$pdf->SetFont('helvetica', 'B', 14);
		$pdf->Write(0, "Ações realizadas no sistema em " . htmlspecialchars($_POST['data']), '', 0, 'L', true, 0, false, false, 0);
		$pdf->SetFont('helvetica', '', 9);
				$html = <<<EOD
			<br><br>
			<table cellspacing="1" cellpadding="1" border="1" align="center">
				<tr nobr="true">
					<th style="font-weight: bold;">Usuário</th>
					<th style="font-weight: bold;">Ação</th>
					<th style="font-weight: bold;">Sala</th>
					<th style="font-weight: bold;">Hora</th>
					<th style="font-weight: bold;">Endereço IP</th>
				</tr>
EOD;
		$conexao = new db();
		$resultado = $conexao->pesquisaPersonalizada("SELECT u.login, l.acao, l.sala, l.hora, l.ip FROM usuarios u JOIN log_dashboard l ON u.id = l.id_usuario ORDER BY l.hora DESC");
		foreach ($resultado as $key => $value) {
			$data = date("Y-m-d", $value['hora']);
			if($_POST['data'] == $data) {
				$contador++;
				if($value['acao'] == 'bloquear_internet') 
					$value['acao'] = "<span class='bloqueado'>Bloqueou</span>";
				if($value['acao'] == 'filtrar') 
					$value['acao'] = "<span class='filtrado'>Filtrou</span>";
				if($value['acao'] == 'liberar') 
					$value['acao'] = "<span class='liberado'>Liberou</span>";
				if($value['acao'] == 'squid reload') {
					$value['acao'] = "Aplicou ação";
					$html .= "<tr style='background-color: #f50;'><td>{$value['login']}</td><td>{$value['acao']}</td><td>{$value['sala']}</td><td>" . date("Y/m/d H:i:s", $value['hora']) . "</td><td>{$value['ip']}</td></tr>";
				} else {
					$html .= "<tr><td>{$value['login']}</td><td>{$value['acao']}</td><td>{$value['sala']}</td><td>" . date("Y/m/d H:i:s", $value['hora']) . "</td><td>{$value['ip']}</td></tr>";
				}
			}
			
		}

		$html .= "</table>";
	} elseif ($_POST['relatorio'] == 'personalizado' AND isset($_POST['ip']) AND isset($_POST['data']) AND isset($_POST['turno'])) {
		

		$ip = $_POST['ip'];
		
		$html = "";
		$arquivo_log = $pastaTemp . "squidlog." . $_POST['data'] . "." . $_POST['turno'];

		if(file_exists($arquivo_log)) {			
			$arquivo = explode(".", $arquivo_log);
			include("includes/squid-class.php");
			$logs = new squid($arquivo_log);
			$resultado = $logs->listaTudo();	
			$estatisticas = new estatisticas("/{$ip}/", $resultado);
			$acessos = $estatisticas->capturaInfo();
			if($acessos) {
				$contador++;
				$data = $arquivo[1];
				$turno = $arquivo[2];
				//$html .= "<h3>O endereço {$ip} realizou " . count($acessos[$ip]) . " acessos em {$data} pela {$turno}</h3><br/>";
			}
			$pdf->SetFont('helvetica', 'B', 14);

			if(!empty($_POST['site']) AND !empty($_POST['horario'])) {
				$pdf->Write(0, "Pesquisa por sites contendo '{$_POST['site']}' acessados pelo endereço {$ip} no dia {$data} pelo turno da {$turno} entre os horarios " . $_POST['horario'], '', 0, 'L', true, 0, false, false, 0);

			} elseif(!empty($_POST['site'])) {
				$pdf->Write(0, "Pesquisa por sites contendo '{$_POST['site']}' acessados pelo endereço {$ip} no dia {$data} pelo turno da {$turno}", '', 0, 'L', true, 0, false, false, 0);
			} elseif (!empty($_POST['horario'])) {
				$pdf->Write(0, "Pesquisa por sites acessados pelo endereço {$ip} no dia {$data} pelo turno da {$turno} entre os horarios " . $_POST['horario'], '', 0, 'L', true, 0, false, false, 0);
			} else {
				$pdf->Write(0, "O endereço {$ip} realizou " . count($acessos[$ip]) . " acessos no dia {$data} pelo turno da {$turno}", '', 0, 'L', true, 0, false, false, 0);
				if(count($acessos[$ip]) > 1400) {
					$pdf->Write(0, "Obs: Exibindo somente os ultimos 1400 acessos", '', 0, 'L', true, 0, false, false, 0);
				}
			}

			
			
			$pdf->SetFont('helvetica', '', 9);
			$html = <<<EOD
			<br><br>
			<table cellspacing="1" cellpadding="1" border="1" align="center">
				<tr nobr="true">
					<th style="font-weight: bold;">Site</th>
					<th style="font-weight: bold;">Data</th>
					<th style="font-weight: bold;">Bytes</th>
				</tr>
EOD;

			
			$acessos[$ip] = array_reverse($acessos[$ip]);
			$contador = 0;
			foreach ($acessos as $key => $value) {
				foreach ($value as $key2 => $value2) {
					if($contador > 1400) {
						break;
					} else {
						$contador++;
					}
					
					$infos = explode("|", $value2);
					if(!empty($_POST['site']) AND !empty($_POST['horario'])) {
						$intervalo = explode("-", $_POST['horario']);
						$horario_acesso = explode(" ", $infos[1]);
						if(stripos($infos[0], $_POST['site']) !== FALSE AND $horario_acesso[1] >= $intervalo[0] AND $horario_acesso[1] <= $intervalo[1]) {
							$html .= "<tr><td>" . $infos[0] . "</td><td>". $infos[1] ."</td><td>" . $estatisticas->calculaBytes($infos[2]) . "</td></tr>";
						}
					} elseif(!empty($_POST['site'])) {
						if(stripos($infos[0], $_POST['site']) !== FALSE) {
							$html .= "<tr><td>" . $infos[0] . "</td><td>". $infos[1] ."</td><td>" . $estatisticas->calculaBytes($infos[2]) . "</td></tr>";
						}
					} elseif(!empty($_POST['horario'])){						
						$intervalo = explode("-", $_POST['horario']);
						$horario_acesso = explode(" ", $infos[1]);
						if($horario_acesso[1] >= $intervalo[0] AND $horario_acesso[1] <= $intervalo[1]) {
							$html .= "<tr><td>" . $infos[0] . "</td><td>". $infos[1] ."</td><td>" . $estatisticas->calculaBytes($infos[2]) . "</td></tr>";
						}

					} else {
						$html .= "<tr><td>" . $infos[0] . "</td><td>". $infos[1] ."</td><td>" . $estatisticas->calculaBytes($infos[2]) . "</td></tr>";
					}
					
				}
			}
			$html .= "</table>";
			
			
			
		}

	

		
	}



}  elseif (isset($_GET['relatorio'])) {
	$contador = 1;
	$relatorio = explode(".", $_GET['relatorio']);
	//$html = "<h3>Estatistica de consumo de banda na " . htmlspecialchars($relatorio[2]) . " do dia " . htmlspecialchars($relatorio[1]). "</h3>";
			
	include("includes/squid-class.php");
	$logs = new squid($pastaTemp . $_GET['relatorio']);
	$resultado = $logs->listaTudo();
	$estatisticas = new estatisticas($range_ips_regex, $resultado);	
	$acessos = $estatisticas->capturaInfo();	
	$conexao = new db();
	$host = $estatisticas->calculaBanda($acessos);	
	array_multisort($host, SORT_DESC);
	//$html .= "<div id='estatisticaHosts'>";
	//$html .= "<table class='tabela1 ordenadoBanda'><tr><th>Endereço IP</th><th>Banda</th><th>Sala</th></tr>";
	$pdf->SetFont('helvetica', 'B', 14);
		$pdf->Write(0, "Estatistica de consumo de banda na " . htmlspecialchars($relatorio[2]) . " do dia " . htmlspecialchars($relatorio[1]), '', 0, 'L', true, 0, false, false, 0);
		$pdf->Write(0, "Total usado: " . $estatisticas->calculaBytes($host['banda_total']), '', 0, 'L', true, 0, false, false, 0);

		$pdf->SetFont('helvetica', '', 9);
				$html = <<<EOD
			<br><br>
			<table cellspacing="1" cellpadding="1" border="1" align="center">
				<tr nobr="true">					
					<th style="font-weight: bold;">Endereço IP</th>
					<th style="font-weight: bold;">Banda</th>
					<th style="font-weight: bold;">Sala</th>
				</tr>
EOD;
	foreach ($host as $ip => $banda_consumida) {	
		if($ip != "banda_total") {			
			$computador = $conexao->listaWhere("computadores","ip",$ip);	
			if(isset($computador[0]['cod_sala'])) {
				$sala = $conexao->listaWhere("salas","cod_sala", $computador[0]['cod_sala']);
				$link = "<a target='_blank' href='salas.php?destaque={$ip}&sala=" . $sala[0]['nome'] . "'>{$ip}</a>";
				$sala = "<span class='liberado'>{$sala[0]['nome']}</span>";
			} else {
				$link = $ip;
				$sala = "<span class='bloqueado'>Não cadastrado</span>";
			}			
				$html .= "<tr>";			
			
			$html .= "<td>{$link}</td>
					<td>" . $estatisticas->calculaBytes($banda_consumida) . "</td>
					<td>" . $sala ."</td>";
			$html .= "</tr>";								
		}						
	}	
	$html .= "</table>";

	
		
}






if($contador == 0) {
	$html = "<h1>Nenhuma informação retornada</h1>";
}


/*
$html = "";
$html .= "<table><tr><th>Usuário</th><th>Horário de login</th><th>IP</th></tr>";
$conexao = new db();
$resultado = $conexao->pesquisaPersonalizada("SELECT l.logou, l.ip, u.login FROM log_logins l JOIN usuarios u ON l.id_usuario = u.id ORDER BY l.logou DESC");
foreach ($resultado as $key => $value) {
	$html .= "<tr><td>{$value['login']}</td><td>" . date("Y/m/d H:i:s", $value['logou']) . "</td><td>{$value['ip']}</td></tr>";
}
$html .= "</table><br/><br/>";
$html .= "<table><tr><th>Usuário</th><th>Ação</th><th>Sala</th><th>Hora</th><th>IP</th></tr>";
						
$resultado = $conexao->pesquisaPersonalizada("SELECT u.login, l.acao, l.sala, l.hora, l.ip FROM usuarios u JOIN log_dashboard l ON u.id = l.id_usuario ORDER BY l.hora DESC");
foreach ($resultado as $key => $value) {
	if($value['acao'] == 'bloquear_internet') 
		$value['acao'] = "<span class='bloqueado'>Bloqueou</span>";
	if($value['acao'] == 'filtrar') 
		$value['acao'] = "<span class='filtrado'>Filtrou</span>";
	if($value['acao'] == 'liberar') 
		$value['acao'] = "<span class='liberado'>Liberou</span>";
	if($value['acao'] == 'squid reload') {
		$html .= "<tr style='background-color: #f50;'><td>{$value['login']}</td><td>{$value['acao']}</td><td>{$value['sala']}</td><td>" . date("Y/m/d H:i:s", $value['hora']) . "</td><td>{$value['ip']}</td></tr>";
	} else {
		$html .= "<tr><td>{$value['login']}</td><td>{$value['acao']}</td><td>{$value['sala']}</td><td>" . date("Y/m/d H:i:s", $value['hora']) . "</td><td>{$value['ip']}</td></tr>";
	}
							
}

$html .= "</table>";
*/
					



	// Print text using writeHTMLCell()
	//$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
	$pdf->writeHTML($html, true, false, false, false, '');
	
	





	//Close and output PDF document
	$pdf->Output('Relatório-'. time() .'.pdf', 'I');



//============================================================+
// END OF FILE
//============================================================+
