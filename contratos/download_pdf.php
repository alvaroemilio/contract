<?php
include '../conexao.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Contrato inválido.";
    exit();
}

$stmt = $pdo->prepare("SELECT pdf_gerado FROM contratos WHERE id = ?");
$stmt->execute([$id]);
$contrato = $stmt->fetch();

if (!$contrato || !$contrato['pdf_gerado']) {
    echo "PDF não encontrado.";
    exit();
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="contrato_' . $id . '.pdf"');
echo $contrato['pdf_gerado'];
exit();
