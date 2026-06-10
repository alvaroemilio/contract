<?php
include '../conexao.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Contrato não encontrado.";
    exit();
}

// Buscar contrato e cliente
$stmt = $pdo->prepare("
    SELECT c.*, cl.nome AS cliente_nome, cl.email, cl.telefone, cl.endereco, cl.cnpj
    FROM contratos c
    JOIN clientes cl ON cl.id = c.cliente_id
    WHERE c.id = ?
");

$stmt->execute([$id]);
$contrato = $stmt->fetch();

if (!$contrato) {
    echo "Contrato inválido.";
    exit();
}

// Buscar itens do contrato
// $stmt = $pdo->prepare("
//     SELECT ic.*, s.nome AS servico_nome
//     FROM itens_contrato ic
//     JOIN servicos s ON s.id = ic.servico_id
//     WHERE ic.contrato_id = ?
// ");

$stmt = $pdo->prepare("
    SELECT ic.*, s.nome AS servico_nome, s.descricao AS servico_descricao
    FROM itens_contrato ic
    JOIN servicos s ON s.id = ic.servico_id
    WHERE ic.contrato_id = ?
");


$stmt->execute([$id]);
$itens = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Visualizar Contrato</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <a href="listar.php" class="btn btn-secondary mb-3">Voltar</a>
    <h2>Contrato #<?= $contrato['id'] ?></h2>
    <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($contrato['data_criacao'])) ?></p>

    <h4>Cliente</h4>
    <ul>
        <li><strong>Nome:</strong> <?= $contrato['cliente_nome'] ?></li>
        <li><strong>Cnpj:</strong> <?= $contrato['cnpj'] ?></li>
        <li><strong>Email:</strong> <?= $contrato['email'] ?></li>
        <li><strong>Telefone:</strong> <?= $contrato['telefone'] ?></li>
        <li><strong>Endereço:</strong> <?= $contrato['endereco'] ?></li>
    </ul>

    <h4>Serviços</h4>
    <table class="table table-bordered">
        <!-- <thead>
            <tr>
                <th>Serviço</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_geral = 0;
            foreach ($itens as $item): 
                $total = $item['quantidade'] * $item['preco_unitario'];
                $total_geral += $total;
            ?>
                <tr>
                    <td><?= $item['servico_nome'] ?></td>
                    <td><?= $item['quantidade'] ?></td>
                    <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($total, 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody> -->

        <thead>
    <tr>
        <th>Serviço</th>
        <th>Descrição</th> <!-- NOVA COLUNA -->
        <th>Quantidade</th>
        <th>Preço Unitário</th>
        <th>Total</th>
    </tr>
</thead>
<tbody>
    <?php 
    $total_geral = 0;
    foreach ($itens as $item): 
        $total = $item['quantidade'] * $item['preco_unitario'];
        $total_geral += $total;
    ?>
        <tr>
            <td><?= $item['servico_nome'] ?></td>
            <td><?= nl2br(htmlspecialchars($item['servico_descricao'])) ?></td> <!-- NOVA COLUNA -->
            <td><?= $item['quantidade'] ?></td>
            <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
            <td>R$ <?= number_format($total, 2, ',', '.') ?></td>
        </tr>
    <?php endforeach; ?>
</tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total Geral</th>
                <th>R$ <?= number_format($total_geral, 2, ',', '.') ?></th>
            </tr>
        </tfoot>
    </table>
    <p>Gerar PDF</p>
    <p>Prazo DETERMINADO e INDETERMINADO</p>
    <a href="gerar_pdf.php?id=<?= $contrato['id'] ?>" class="btn btn-primary">Gerar PDF Tempo Determinado</a>
    <a href="gerar_pdf2.php?id=<?= $contrato['id'] ?>" class="btn btn-primary">Gerar PDF Tempo Indeterminado</a>
    <a href="criar.php" class="btn btn-secondary">Novo Contrato</a>
</body>
</html>
