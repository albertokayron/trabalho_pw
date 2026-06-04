<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexao.php';

try {
    $stmt = $conn->query("SELECT id, nome, valor, funcionalidade, grupo_muscular FROM equipamentos ORDER BY id DESC");
    $equipamentos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar equipamentos: " . $e->getMessage());
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Equipamentos</h1>
            <p class="text-sm text-zinc-400">Gerencie as máquinas e halteres disponíveis na academia Iron Berg.</p>
        </div>
        <div>
            <a href="create.php" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold text-sm rounded-lg transition-all shadow-lg shadow-red-950/20">
                <i class="fa-solid fa-plus text-xs"></i>
                Adicionar Equipamento
            </a>
        </div>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-800 bg-zinc-950/50">
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider w-16">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Equipamento</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Grupo Muscular</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Valor Aquisição</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Funcionalidade / Descrição</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    <?php if (count($equipamentos) > 0): ?>
                        <?php foreach ($equipamentos as $eq): ?>
                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-mono text-zinc-500">#<?php echo $eq['id']; ?></td>
                                <td class="px-6 py-4 text-sm font-bold text-white"><?php echo htmlspecialchars($eq['nome']); ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-zinc-850 text-red-400 border border-zinc-700/50">
                                        <i class="fa-solid fa-child mr-1.5 text-[10px]"></i>
                                        <?php echo htmlspecialchars($eq['grupo_muscular']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-zinc-300">
                                    R$ <?php echo number_format($eq['valor'], 2, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-400 max-w-xs truncate" title="<?php echo htmlspecialchars($eq['funcionalidade']); ?>">
                                    <?php echo htmlspecialchars($eq['funcionalidade']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium">
                                    <div class="flex justify-end gap-2.5">
                                        <a href="edit.php?id=<?php echo $eq['id']; ?>" 
                                           class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white transition-colors"
                                           title="Editar Equipamento">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $eq['id']; ?>" 
                                           onclick="return confirm('Deseja realmente excluir este equipamento?')"
                                           class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-red-950/20 hover:bg-red-600 text-red-400 hover:text-white transition-colors"
                                           title="Excluir Equipamento">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-zinc-500">
                                <i class="fa-solid fa-dumbbell text-4xl text-zinc-700 mb-3 block"></i>
                                Nenhum equipamento cadastrado.
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
