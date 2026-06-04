<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexao.php';

$eq_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$eq_id) {
    header("Location: read.php");
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM equipamentos WHERE id = :id");
    $stmt->execute(['id' => $eq_id]);

    $_SESSION['toast'] = [
        'type' => 'success',
        'message' => 'Equipamento excluído com sucesso!'
    ];
} catch (PDOException $e) {
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Erro ao excluir equipamento: ' . $e->getMessage()
    ];
}

header("Location: read.php");
exit;
?>
