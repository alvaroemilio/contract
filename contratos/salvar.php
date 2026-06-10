<?php
include '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $servico_ids = $_POST['servico_id'];
    $quantidades = $_POST['quantidade'];
    $precos = $_POST['preco'];

    // Inserir o contrato
    $stmt = $pdo->prepare("INSERT INTO contratos (cliente_id) VALUES (?)");
    $stmt->execute([$cliente_id]);
    $contrato_id = $pdo->lastInsertId();

    // Inserir os itens do contrato
    for ($i = 0; $i < count($servico_ids); $i++) {
        if (empty($servico_ids[$i])) continue;

        $servico_id = $servico_ids[$i];
        $quantidade = $quantidades[$i];
        $preco = str_replace(',', '.', $precos[$i]);

        $stmt = $pdo->prepare("INSERT INTO itens_contrato (contrato_id, servico_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
        $stmt->execute([$contrato_id, $servico_id, $quantidade, $preco]);
    }

    // Redirecionar (poderá ir para visualização ou lista depois)
    header("Location: visualizar.php?id=$contrato_id");
    exit();
}
?>
