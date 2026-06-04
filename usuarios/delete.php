<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexao.php';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$user_id) {
    header("Location: read.php");
    exit;
}

// Security check: cannot delete self
if ($user_id === intval($_SESSION['usuario_id'])) {
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Operação negada! Você não pode excluir sua própria conta enquanto estiver logado.'
    ];
    header("Location: read.php");
    exit;
}

try {
    // Delete user from DB
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $user_id]);

    $_SESSION['toast'] = [
        'type' => 'success',
        'message' => 'Usuário excluído com sucesso!'
    ];
} catch (PDOException $e) {
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Erro ao excluir usuário: ' . $e->getMessage()
    ];
}

header("Location: read.php");
exit;
?>
