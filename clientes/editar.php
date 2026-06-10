<?php
include '../conexao.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    echo "Cliente não encontrado!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $cnpj = $_POST['cnpj'];

    $sql = "UPDATE clientes SET nome=?, email=?, telefone=?, endereco=?, cnpj=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $email, $telefone, $endereco, $cnpj, $id]);

    header('Location: listar.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Editar Cliente</h2>
    <form method="post">
        <div class="mb-3">
            <label>Nome:</label>
            <input type="text" name="nome" class="form-control" value="<?= $cliente['nome'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" value="<?= $cliente['email'] ?>">
        </div>
        <div class="mb-3">
            <label>Telefone:</label>
            <input type="text" name="telefone" class="form-control" value="<?= $cliente['telefone'] ?>">
        </div>
        <div class="mb-3">
            <label>Endereço:</label>
            <textarea name="endereco" class="form-control"><?= $cliente['endereco'] ?></textarea>
        </div>
        <div class="mb-3">
            <label>CNPJ:</label>
            <input type="text" name="cnpj" class="form-control" value="<?= $cliente['cnpj'] ?>">
        </div>
        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
