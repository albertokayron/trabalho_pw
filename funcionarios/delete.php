<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexao.php';

$func_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$func_id) {
    header("Location: read.php");
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM funcionarios WHERE id = :id");
    $stmt->execute(['id' => $func_id]);

    $_SESSION['toast'] = [
        'type' => 'success',
        'message' => 'Colaborador removido com sucesso!'
    ];
} catch (PDOException $e) {
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Erro ao excluir colaborador: ' . $e->getMessage()
    ];
}

header("Location: read.php");
exit;
?>
