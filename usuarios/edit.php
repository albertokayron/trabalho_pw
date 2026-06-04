<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexao.php';

$error_message = '';
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$user_id) {
    header("Location: read.php");
    exit;
}

// Fetch user data
try {
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Usuário não encontrado.'];
        header("Location: read.php");
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados do usuário: " . $e->getMessage());
}

$nome = $user['nome'];
$email = $user['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if (empty($nome) || empty($email)) {
        $error_message = 'Os campos Nome e E-mail são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Por favor, insira um e-mail válido.';
    } elseif (!empty($senha) && strlen($senha) < 6) {
        $error_message = 'A nova senha deve conter no mínimo 6 caracteres.';
    } elseif (!empty($senha) && $senha !== $confirmar_senha) {
        $error_message = 'As senhas informadas não coincidem.';
    } else {
        try {
            // Check if email already exists on ANOTHER user
            $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email AND id != :id");
            $stmt->execute(['email' => $email, 'id' => $user_id]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                $error_message = 'Este endereço de e-mail já está sendo utilizado por outro usuário.';
            } else {
                if (!empty($senha)) {
                    // Update user WITH new password
                    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE usuarios SET nome = :nome, email = :email, senha = :senha WHERE id = :id");
                    $stmt->execute([
                        'nome' => $nome,
                        'email' => $email,
                        'senha' => $senha_hash,
                        'id' => $user_id
                    ]);
                } else {
                    // Update user WITHOUT changing password
                    $stmt = $conn->prepare("UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id");
                    $stmt->execute([
                        'nome' => $nome,
                        'email' => $email,
                        'id' => $user_id
                    ]);
                }

                // If editing own user, update session values
                if ($user_id == $_SESSION['usuario_id']) {
                    $_SESSION['usuario_nome'] = $nome;
                    $_SESSION['usuario_email'] = $email;
                }

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Usuário atualizado com sucesso!'
                ];
                header("Location: read.php");
                exit;
            }
        } catch (PDOException $e) {
            $error_message = 'Erro ao atualizar usuário: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-2xl mx-auto space-y-6">
    <!-- Breadcrumb & Title -->
    <div>
        <a href="read.php" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-zinc-500 hover:text-zinc-300 transition-colors mb-3">
            <i class="fa-solid fa-arrow-left"></i> Voltar para a lista
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-white">Editar Usuário</h1>
        <p class="text-sm text-zinc-400">Modifique as informações cadastrais do administrador.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 md:p-8 shadow-xl">
        <?php if (!empty($error_message)): ?>
            <div class="mb-6 p-4 rounded-lg bg-red-950/40 border border-red-800/40 text-red-400 text-sm flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

        <form action="edit.php?id=<?php echo $user_id; ?>" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Name -->
                <div>
                    <label for="nome" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Nome Completo</label>
                    <input type="text" name="nome" id="nome" required value="<?php echo htmlspecialchars($nome); ?>"
                           placeholder="Ex: João Silva"
                           class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">E-mail Corporativo</label>
                    <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($email); ?>"
                           placeholder="Ex: joao@ironberg.com"
                           class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>

                <!-- Password advisory info -->
                <div class="p-4 rounded-lg bg-zinc-950 border border-zinc-800">
                    <h3 class="text-xs font-bold text-zinc-300 uppercase tracking-wider flex items-center gap-2 mb-1.5">
                        <i class="fa-solid fa-circle-info text-red-500"></i> Alterar Senha
                    </h3>
                    <p class="text-xs text-zinc-500">Deixe os campos abaixo em branco caso queira manter a senha atual.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div>
                        <label for="senha" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Nova Senha</label>
                        <input type="password" name="senha" id="senha"
                               placeholder="Mínimo 6 caracteres"
                               class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="confirmar_senha" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Confirmar Nova Senha</label>
                        <input type="password" name="confirmar_senha" id="confirmar_senha"
                               placeholder="Digite a nova senha novamente"
                               class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-zinc-800">
                <a href="read.php" 
                   class="px-4 py-2.5 bg-zinc-850 hover:bg-zinc-800 active:bg-zinc-750 text-zinc-300 hover:text-white text-sm font-semibold rounded-lg transition-colors border border-zinc-800">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-sm font-semibold rounded-lg transition-colors shadow-lg shadow-red-950/20">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
