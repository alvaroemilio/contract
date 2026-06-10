<?php
include '../conexao.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM servicos WHERE id = ?");
$stmt->execute([$id]);

header('Location: listar.php');
exit();
?>
