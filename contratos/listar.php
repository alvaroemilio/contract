<?php
include '../conexao.php';

// Buscar contratos com nome do cliente
$stmt = $pdo->query("
    SELECT c.id, c.data_criacao, c.situacao, c.pdf_gerado, cl.nome AS cliente_nome
    FROM contratos c
    JOIN clientes cl ON cl.id = c.cliente_id
    ORDER BY c.data_criacao DESC
");
$contratos = $stmt->fetchAll();
?>

<?php
include '../conexao.php';

// Buscar clientes para o filtro
$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome")->fetchAll();

// Obter filtros (se existirem)
$cliente_id = $_GET['cliente_id'] ?? '';
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';

// Montar query base
$sql = "
    SELECT c.id, c.data_criacao ,c.situacao , c.pdf_gerado, cl.nome AS cliente_nome
    FROM contratos c
    JOIN clientes cl ON cl.id = c.cliente_id
    WHERE 1=1
";

$params = [];

// Filtro cliente
if ($cliente_id) {
    $sql .= " AND cl.id = ?";
    $params[] = $cliente_id;
}

// Filtro data
if ($data_inicio) {
    $sql .= " AND DATE(c.data_criacao) >= ?";
    $params[] = $data_inicio;
}

if ($data_fim) {
    $sql .= " AND DATE(c.data_criacao) <= ?";
    $params[] = $data_fim;
}

$sql .= " ORDER BY c.data_criacao DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$contratos = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Contratos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2>Contratos</h2>
    <a href="criar.php" class="btn btn-success mb-3">Novo Contrato</a>
    <a href="../index.php" class="btn btn-secondary mb-3">Voltar</a>

    <!-- FILTROS -->
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <label>Cliente</label>
            <select name="cliente_id" class="form-select">
                <option value="">-- Todos --</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($c['id'] == $cliente_id) ? 'selected' : '' ?>>
                        <?= $c['nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label>Data Início</label>
            <input type="date" name="data_inicio" value="<?= $data_inicio ?>" class="form-control">
        </div>

        <div class="col-md-3">
            <label>Data Fim</label>
            <input type="date" name="data_fim" value="<?= $data_fim ?>" class="form-control">
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Filtrar</button>
            <a href="listar.php" class="btn btn-secondary">Limpar</a>
        </div>
    </form>

    <!-- LISTA -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Finalizado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($contratos) == 0): ?>
                <tr><td colspan="4" class="text-center">Nenhum contrato encontrado.</td></tr>
            <?php else: ?>
                <?php foreach ($contratos as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= $c['cliente_nome'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($c['data_criacao'])) ?></td>
                        <td><?= $c['situacao'] ?></td>
                        <td>
                            <a href="visualizar.php?id=<?= $c['id'] ?>" class="btn btn-primary btn-sm">Visualizar</a>
                            <?php if ($c['pdf_gerado']): ?>
                                <a href="download_pdf.php?id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm">Baixar PDF</a>

                                <a href="editar.php?id=<?= $c['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <?php else: ?>
                                <span class="text-muted">PDF não gerado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
