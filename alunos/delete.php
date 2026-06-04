<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexao.php';

$aluno_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$aluno_id) {
    header("Location: read.php");
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM alunos WHERE id = :id");
    $stmt->execute(['id' => $aluno_id]);

    $_SESSION['toast'] = [
        'type' => 'success',
        'message' => 'Matrícula do aluno excluída com sucesso!'
    ];
} catch (PDOException $e) {
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Erro ao excluir matrícula do aluno: ' . $e->getMessage()
    ];
}

header("Location: read.php");
exit;
?>
