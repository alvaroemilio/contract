<?php
include '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $cnpj = $_POST['cnpj'];

    $sql = "INSERT INTO clientes (nome, email, telefone, endereco, cnpj) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $email, $telefone, $endereco, $cnpj]);

    header('Location: listar.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Adicionar Cliente</h2>
    <form method="post">
        <div class="mb-3">
            <label>Nome:</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
            <label>Telefone:</label>
            <input type="text" name="telefone" class="form-control">
        </div>
        <div class="mb-3">
            <label>Endereço:</label>
            <textarea name="endereco" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>CNPJ:</label>
            <input type="text" name="cnpj" class="form-control"></input>
        </div>
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
