<?php

// Configurações de erro para desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define o cabeçalho de resposta como JSON
header('Content-Type: application/json; charset=utf-8');

// Inclui a conexão existente
require_once 'conexao.php';
$con->set_charset("utf8");

// SQL atualizado com JOIN para buscar detalhes do produto
$sql = "SELECT 
            o.idOperacao, 
            p.idProduto,
            p.nmProduto,
            p.nmMarca,
            p.nmCodigo,
            o.dtOperacao, 
            o.qtVenda, 
            o.tpOperacao 
        FROM operacao o
        JOIN produto p ON o.idProduto = p.idProduto";

$result = $con->query($sql);

$response = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // O PHP manterá os nomes das colunas conforme o SQL (ex: nmProduto, nmMarca)
        $response[] = $row;
    }
} else {
    // Retorno padrão caso não haja operações registradas
    // Incluímos os novos campos aqui também para manter a estrutura
    $response[] = [
        "idOperacao" => 0,
        "idProduto" => 0,
        "nmProduto" => "Nenhum dado encontrado",
        "nmMarca" => "",
        "nmCodigo" => "",
        "dtOperacao" => "",
        "qtVenda" => "0.00",
        "tpOperacao" => ""
    ];
}

// Retorna o JSON com codificação UTF-8 preservada
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$con->close();
?>