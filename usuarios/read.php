<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexao.php';

// Fetch all users
try {
    $stmt = $conn->query("SELECT id, nome, email, data_cadastro FROM usuarios ORDER BY id DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar usuários: " . $e->getMessage());
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Usuários do Sistema</h1>
            <p class="text-sm text-zinc-400">Gerencie as contas de administradores que têm acesso a este sistema.</p>
        </div>
        <div>
            <a href="create.php" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold text-sm rounded-lg transition-all shadow-lg shadow-red-950/20">
                <i class="fa-solid fa-user-plus text-xs"></i>
                Cadastrar Usuário
            </a>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-800 bg-zinc-950/50">
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">E-mail</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Data de Cadastro</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-mono text-zinc-500">#<?php echo $user['id']; ?></td>
                                <td class="px-6 py-4 text-sm font-semibold text-zinc-200">
                                    <div class="flex items-center gap-2">
                                        <?php echo htmlspecialchars($user['nome']); ?>
                                        <?php if ($user['id'] == $_SESSION['usuario_id']): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-950 text-red-400 border border-red-800/30">Você</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-400 font-mono"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="px-6 py-4 text-sm text-zinc-400">
                                    <?php echo date('d/m/Y H:i', strtotime($user['data_cadastro'])); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium">
                                    <div class="flex justify-end gap-2.5">
                                        <a href="edit.php?id=<?php echo $user['id']; ?>" 
                                           class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white transition-colors"
                                           title="Editar Usuário">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        
                                        <?php if ($user['id'] != $_SESSION['usuario_id']): ?>
                                            <a href="delete.php?id=<?php echo $user['id']; ?>" 
                                               onclick="return confirm('Deseja realmente excluir este usuário? Esta ação não pode ser desfeita.')"
                                               class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-red-950/20 hover:bg-red-600 text-red-400 hover:text-white transition-colors"
                                               title="Excluir Usuário">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-zinc-800/40 text-zinc-600 cursor-not-allowed"
                                                  title="Você não pode excluir sua própria conta">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-zinc-500">
                                <i class="fa-solid fa-users text-4xl text-zinc-700 mb-3 block"></i>
                                Nenhum usuário cadastrado no sistema.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
