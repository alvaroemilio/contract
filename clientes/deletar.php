<?php
include '../conexao.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
$stmt->execute([$id]);

header('Location: listar.php');
exit();
?>
