<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexao.php';

$error_message = '';
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $error_message = 'Todos os campos são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Por favor, insira um e-mail válido.';
    } elseif (strlen($senha) < 6) {
        $error_message = 'A senha deve conter no mínimo 6 caracteres.';
    } elseif ($senha !== $confirmar_senha) {
        $error_message = 'As senhas informadas não coincidem.';
    } else {
        try {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                $error_message = 'Este endereço de e-mail já está sendo utilizado.';
            } else {
                // Insert user
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
                $stmt->execute([
                    'nome' => $nome,
                    'email' => $email,
                    'senha' => $senha_hash
                ]);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Usuário cadastrado com sucesso!'
                ];
                header("Location: read.php");
                exit;
            }
        } catch (PDOException $e) {
            $error_message = 'Erro ao cadastrar usuário: ' . $e->getMessage();
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
        <h1 class="text-2xl font-bold tracking-tight text-white">Cadastrar Novo Usuário</h1>
        <p class="text-sm text-zinc-400">Adicione uma nova conta administrativa para acessar o painel do sistema.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 md:p-8 shadow-xl">
        <?php if (!empty($error_message)): ?>
            <div class="mb-6 p-4 rounded-lg bg-red-950/40 border border-red-800/40 text-red-400 text-sm flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

        <form action="create.php" method="POST" class="space-y-6">
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div>
                        <label for="senha" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Senha</label>
                        <input type="password" name="senha" id="senha" required
                               placeholder="Mínimo 6 caracteres"
                               class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="confirmar_senha" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Confirmar Senha</label>
                        <input type="password" name="confirmar_senha" id="confirmar_senha" required
                               placeholder="Digite a mesma senha"
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
                    Salvar Usuário
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
