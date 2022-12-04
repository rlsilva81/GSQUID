<?php
include("../includes/config.php");

$linhas = shell_exec("wc -l {$arquivo_log}");
$linhas = explode(" ", $linhas);
$logs['logAtual']['linhas'] = $linhas[0];

$linhas = shell_exec("du -sh {$arquivo_log}");
$linhas = explode("/", $linhas);
$logs['logAtual']['tamanho'] = trim($linhas[0]);


$linhas = shell_exec("wc -l {$arquivo_log}.original");
$linhas = explode(" ", $linhas);
$logs['logOriginal']['linhas'] = $linhas[0];

$linhas = shell_exec("du -sh {$arquivo_log}.original");
$linhas = explode("/", $linhas);
$logs['logOriginal']['tamanho'] = trim($linhas[0]);


echo json_encode($logs);
