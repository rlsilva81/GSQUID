<?php
include("../includes/valida_sessao.php");
include("../includes/squid-class.php");
include("../includes/config.php");

$squid = new squid($arquivo_squid);
$resultado = $squid->listaTudo();
echo "<table><tr><th>Configuração do squid</th></tr><tr><td>";
foreach ($resultado as $key => $value) {
	echo $value . "<br/>";
}
echo "</td></tr></table>";



