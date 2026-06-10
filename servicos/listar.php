<?php
include '../conexao.php';
$stmt = $pdo->query("SELECT * FROM servicos");
$servicos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Serviços</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
</head>
<body class="container mt-5">
    <h2>Serviços</h2>
    <a href="adicionar.php" class="btn btn-success mb-3">Adicionar Serviço</a>
    <a href="../index.php" class="btn btn-secondary mb-3">Voltar</a>
    <table id="servicos" class="table table-bordered">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($servicos as $s): ?>
                <tr>
                    <td><?= $s['nome'] ?></td>
                    <td><?= $s['descricao'] ?></td>
                    <td>R$ <?= number_format($s['preco'], 2, ',', '.') ?></td>
                    <td>
                        <a href="editar.php?id=<?= $s['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="deletar.php?id=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Deseja excluir?')">Deletar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#servicos').DataTable();
        });
    </script>
</body>
</html>
