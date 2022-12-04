<?php
	include("../includes/valida_sessao.php");


/*
You have to setup sudo to make it possible. Apache user should have permissions to exec commands as root using sudo. Also there is no tty in php scripting so you have to disable tty requirement in sudo. And you don't have ability to enter user password to sudo in a php script, so you have to disable authentication too.

To do so put "service tor restart" to text file and make executable. It's much more secure meaning that apache user could only to restart TOR, nothing else. Then in /etc/sudoers comment out line:

Defaults    requiretty
Add lines:

Defaults    !authenticate
apache      ALL=NOPASSWD: /etc/init.d/squid

Now you can do "sudo service tor restart" from your php script.

http://superuser.com/questions/825729/restart-tor-service-using-php-execution

*/
if(isset($_GET['squid'])) {
	sleep(1);
	if($_GET['squid'] == "reload") {
		system("sudo squid -k reconfigure");		
		include("../includes/conexao.php");
		$conexao = new db();
		include("../includes/log_dashboard.php");	
		echo "<h4>Ação aplicada com sucesso!</h4>";	
	}
} 
