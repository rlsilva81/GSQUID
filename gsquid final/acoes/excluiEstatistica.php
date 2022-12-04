<?php
include("../includes/valida_sessao.php");
include("../includes/valida_nivel.php");
include("../includes/config.php");

$data = $_GET['data'];

// Exclui arquivos de log do dia específico
foreach(glob($pastaTemp . "squidlog." . $data . "*") as $file) {
    unlink($file);        
}
// Exclui os arquivos relatórios temporários do dia específico
foreach(glob($pastaTemp . "relatorios/squidlog." . $data . "*") as $file) {
    unlink($file);        
}