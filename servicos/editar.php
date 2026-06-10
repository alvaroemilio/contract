<?php
include '../conexao.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->execute([$id]);
$servico = $stmt->fetch();

if (!$servico) {
    echo "Serviço não encontrado!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = str_replace(',', '.', $_POST['preco']);

    $sql = "UPDATE servicos SET nome=?, descricao=?, preco=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $descricao, $preco, $id]);

    header('Location: listar.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Editar Serviço</h2>
    <form method="post">
        <div class="mb-3">
            <label>Nome:</label>
            <input type="text" name="nome" class="form-control" value="<?= $servico['nome'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Descrição:</label>
            <textarea name="descricao" class="form-control"><?= $servico['descricao'] ?></textarea>
        </div>
        <div class="mb-3">
            <label>Preço:</label>
            <input type="text" name="preco" class="form-control" value="<?= $servico['preco'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
