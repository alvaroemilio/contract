<?php
include '../conexao.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT c.*, cl.nome AS cliente_nome, cl.cnpj AS cnpj
    FROM contratos c
    JOIN clientes cl ON cl.id = c.cliente_id
    WHERE c.id = ?
");
$stmt->execute([$id]);
$contrato = $stmt->fetch();

if (!$contrato) {
    echo "Contrato não encontrado!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $situacao = $_POST['situacao'];

    $sql = "UPDATE contratos SET situacao=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$situacao, $id]);

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
    <h2>Editar Contrato</h2>
    <form method="post">

        <div class="mb-3">
            <label>Número Do Contraro:</label>
            <input type="text" name="id" class="form-control" value="<?= $contrato['id'] ?>" disabled> 
        </div>
        
        <div class="mb-3">
            <label>Cliente:</label>
            <input type="text" name="cl.nome" class="form-control" value="<?= $contrato['cliente_nome'] ?>" disabled> 
        </div>

        <div class="mb-3">
            <label>CNPJ:</label>
            <input type="text" name="cnpj" class="form-control" value="<?= $contrato['cnpj'] ?>" disabled>
            
        </div>

        <!-- <div class="mb-3">
            <label>Situação:</label>
           <input type="text" name="situacao" class="form-control" value="<?= $contrato['situacao'] ?>" disabled> 
        </div> -->

        <div class="mb-3">
    <label>Alterar Situação:</label>
    <select name="situacao" class="form-control">
        <option value="Em Aberto" <?= $contrato['situacao'] == 'Em Aberto' ? 'selected' : '' ?>>Em Aberto</option>
        <option value="Fechado" <?= $contrato['situacao'] == 'Fechado' ? 'selected' : '' ?>>Fechado</option>
    </select>
</div>
        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
