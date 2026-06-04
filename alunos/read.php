<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexao.php';

// Fetch all students
try {
    $stmt = $conn->query("SELECT id, nome, data_nascimento, dia_vencimento, data_cadastro FROM alunos ORDER BY id DESC");
    $alunos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar alunos: " . $e->getMessage());
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Alunos</h1>
            <p class="text-sm text-zinc-400">Gerencie a matrícula, dados cadastrais e vencimentos dos planos dos alunos da Iron Berg.</p>
        </div>
        <div>
            <a href="create.php" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold text-sm rounded-lg transition-all shadow-lg shadow-red-950/20">
                <i class="fa-solid fa-user-plus text-xs"></i>
                Matricular Aluno
            </a>
        </div>
    </div>

    <!-- Students List -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-800 bg-zinc-950/50">
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider w-16">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Idade</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Nascimento</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Vencimento da Mensalidade</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider">Cadastro</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-wider text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    <?php if (count($alunos) > 0): ?>
                        <?php foreach ($alunos as $aluno): 
                            // Calculate age
                            $birthDate = new DateTime($aluno['data_nascimento']);
                            $today = new DateTime();
                            $age = $today->diff($birthDate)->y;

                            // Dynamic class for payment due warning if day is near or past
                            $currentDay = intval(date('d'));
                            $dueDay = intval($aluno['dia_vencimento']);
                            
                            $badgeClass = 'bg-zinc-850 text-zinc-300 border-zinc-700/50';
                            if ($currentDay > $dueDay) {
                                // Overdue this month or past due
                                $badgeClass = 'bg-red-950/30 text-red-400 border-red-900/30';
                            } elseif ($dueDay - $currentDay <= 3 && $dueDay >= $currentDay) {
                                // Due in 3 days or less
                                $badgeClass = 'bg-amber-950/30 text-amber-400 border-amber-900/30';
                            }
                        ?>
                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-mono text-zinc-500">#<?php echo $aluno['id']; ?></td>
                                <td class="px-6 py-4 text-sm font-bold text-white"><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                <td class="px-6 py-4 text-sm text-zinc-300"><?php echo $age; ?> anos</td>
                                <td class="px-6 py-4 text-sm text-zinc-400 font-mono"><?php echo date('d/m/Y', strtotime($aluno['data_nascimento'])); ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border <?php echo $badgeClass; ?>">
                                        <i class="fa-regular fa-calendar-check mr-1.5 text-[10px]"></i>
                                        Dia <?php echo str_pad($aluno['dia_vencimento'], 2, '0', STR_PAD_LEFT); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-400">
                                    <?php echo date('d/m/Y', strtotime($aluno['data_cadastro'])); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium">
                                    <div class="flex justify-end gap-2.5">
                                        <a href="edit.php?id=<?php echo $aluno['id']; ?>" 
                                           class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white transition-colors"
                                           title="Editar Aluno">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $aluno['id']; ?>" 
                                           onclick="return confirm('Deseja realmente excluir este aluno?')"
                                           class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-red-950/20 hover:bg-red-600 text-red-400 hover:text-white transition-colors"
                                           title="Excluir Aluno">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-zinc-500">
                                <i class="fa-solid fa-users text-4xl text-zinc-700 mb-3 block"></i>
                                Nenhum aluno matriculado.
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
