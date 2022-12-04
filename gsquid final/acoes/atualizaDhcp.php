<?php
include("../includes/valida_sessao.php");
include("../includes/config.php");

$dhcp[] = "option domain-name \"{$dominio}\";";
$dhcp[] = "option domain-name-servers {$dns1}, {$dns2};";

$dhcp[] = "default-lease-time {$default_lease_time};";
$dhcp[] = "max-lease-time {$max_lease_time};";

$dhcp[] = "authoritative;";

$dhcp[] = "log-facility local7;";


$dhcp[] = "subnet {$id} netmask {$netmask} {";
$dhcp[] = "range {$range};";
$dhcp[] = "option routers {$option_router};";
$dhcp[] = "option broadcast-address {$broadcast};";


include("../includes/conexao.php");

$conexao = new db();

$computadores = $conexao->pesquisaPersonalizada("SELECT c.hostname, c.mac, c.ip, s.nome FROM computadores c JOIN salas s ON c.cod_sala = s.cod_sala");
$dhcp[] = "";
$dhcp[] = "############# HOSTS ###############";
foreach ($computadores as $key => $value) {
	$dhcp[] = "host {$value['hostname']} {";	
	$dhcp[] = "option host-name \"{$value['hostname']}\";";
	$value['mac'] = str_replace("-", ":", $value['mac']);
	$dhcp[] = "hardware ethernet {$value['mac']};";
	$dhcp[] = "fixed-address {$value['ip']};";
	$dhcp[] = "}";
	$dhcp[] = "";
}

$arquivo = fopen("/etc/dhcp/dhcpd.conf", "w") or die("Não é possível escrever no arquivo");

foreach($dhcp as $key => $value) {
	//echo $value . "<br/>";
	fwrite($arquivo, $value . "\n");

}
fwrite($arquivo, "} \n");
fclose($arquivo);

echo "<pre>";
system("sudo /etc/init.d/dhcpd restart");

//header("Location: ../temp/dhcpd.conf");
