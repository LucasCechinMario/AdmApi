<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Inclui a conexão (certifique-se que $con está definido aqui)
require_once 'conexao.php';
$con->set_charset("utf8");

// Obtém o input JSON
$jsonParam = json_decode(file_get_contents('php://input'), true);

if (!$jsonParam) {
    echo json_encode(['success' => false, 'message' => 'Dados JSON inválidos ou ausentes.']);
    exit;
}

/**
 * Extração e Validação dos Dados da Tabela 'operacao'
 */
$idProduto  = intval($jsonParam['idProduto'] ?? 0);
$qtVenda    = floatval($jsonParam['qtVenda'] ?? 0); // Decimal no SQL vira float/double no PHP
$tpOperacao = trim($jsonParam['tpOperacao'] ?? '');

// Tratamento de Data: Se não enviada, usa a data/hora atual (NOW)
$dtOperacao = !empty($jsonParam['dtOperacao']) 
              ? date('Y-m-d H:i:s', strtotime($jsonParam['dtOperacao'])) 
              : date('Y-m-d H:i:s');

// Validação simples de campos obrigatórios
if ($idProduto <= 0 || empty($tpOperacao)) {
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios faltando (idProduto ou tpOperacao).']);
    exit;
}

// Prepare SQL para a tabela 'operacao'
// O campo idOperacao é AUTO_INCREMENT, então não o incluímos no INSERT
$sql = "INSERT INTO operacao (idProduto, dtOperacao, qtVenda, tpOperacao) VALUES (?, ?, ?, ?)";
$stmt = $con->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $con->error]);
    exit;
}

/**
 * Bind dos parâmetros:
 * i - integer (idProduto)
 * s - string (dtOperacao)
 * d - double (qtVenda)
 * s - string (tpOperacao)
 */
$stmt->bind_param("isds", $idProduto, $dtOperacao, $qtVenda, $tpOperacao);

// Execução
if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Operação registrada com sucesso!',
        'idInserido' => $stmt->insert_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro no registro: ' . $stmt->error]);
}

$stmt->close();
$con->close();

?>