<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexao.php';

try {
    $stmt = $conn->query("SELECT id, nome, funcao, data_admissao, data_nascimento, salario FROM funcionarios ORDER BY id DESC");
    $funcionarios = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar funcionários: " . $e->getMessage());
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Funcionários</h1>
            <p class="text-sm text-zinc-400">Gerencie a equipe de colaboradores da academia Iron Berg.</p>
        </div>
        <div>
            <a href="create.php" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold text-sm rounded-lg transition-all shadow-lg shadow-red-950/20">
                <i class="fa-solid fa-user-plus text-xs"></i>
                Adicionar Colaborador
            </a>
        </div>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-800 bg-zinc-950/50">
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider w-16">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Função / Cargo</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Salário</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Admissão</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Nascimento</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    <?php if (count($funcionarios) > 0): ?>
                        <?php foreach ($funcionarios as $func): ?>
                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-mono text-zinc-500">#<?php echo $func['id']; ?></td>
                                <td class="px-6 py-4 text-sm font-bold text-white"><?php echo htmlspecialchars($func['nome']); ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-zinc-800 text-zinc-200 border border-zinc-700/60">
                                        <i class="fa-solid fa-briefcase mr-1.5 text-[10px] text-red-500"></i>
                                        <?php echo htmlspecialchars($func['funcao']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-zinc-300">
                                    R$ <?php echo number_format($func['salario'], 2, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-400 font-mono">
                                    <?php echo date('d/m/Y', strtotime($func['data_admissao'])); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-400 font-mono">
                                    <?php echo date('d/m/Y', strtotime($func['data_nascimento'])); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium">
                                    <div class="flex justify-end gap-2.5">
                                        <a href="edit.php?id=<?php echo $func['id']; ?>" 
                                           class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white transition-colors"
                                           title="Editar Funcionário">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $func['id']; ?>" 
                                           onclick="return confirm('Deseja realmente excluir este funcionário?')"
                                           class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-red-950/20 hover:bg-red-600 text-red-400 hover:text-white transition-colors"
                                           title="Excluir Funcionário">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-zinc-500">
                                <i class="fa-solid fa-user-tie text-4xl text-zinc-700 mb-3 block"></i>
                                Nenhum funcionário cadastrado.
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
