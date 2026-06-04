<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/conexao.php';

// Fetch stats
try {
    // Counts
    $count_alunos = $conn->query("SELECT COUNT(*) FROM alunos")->fetchColumn();
    $count_equipamentos = $conn->query("SELECT COUNT(*) FROM equipamentos")->fetchColumn();
    $count_funcionarios = $conn->query("SELECT COUNT(*) FROM funcionarios")->fetchColumn();
    $count_usuarios = $conn->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

    // Financial calculations
    $total_inventario = $conn->query("SELECT SUM(valor) FROM equipamentos")->fetchColumn() ?: 0;
    $folha_pagamento = $conn->query("SELECT SUM(salario) FROM funcionarios")->fetchColumn() ?: 0;

    // Recent items lists
    $recent_alunos = $conn->query("SELECT id, nome, data_cadastro FROM alunos ORDER BY id DESC LIMIT 5")->fetchAll();
    $recent_equipamentos = $conn->query("SELECT id, nome, grupo_muscular FROM equipamentos ORDER BY id DESC LIMIT 5")->fetchAll();

} catch (PDOException $e) {
    die("Erro ao carregar dados do dashboard: " . $e->getMessage());
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-gradient-to-r from-zinc-900 via-zinc-900 to-red-950/20 border border-zinc-800 rounded-2xl shadow-lg">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white italic">OLÁ, <span class="text-red-500 uppercase"><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>!</h1>
            <p class="text-sm text-zinc-400 mt-1">Bem-vindo de volta ao painel administrativo da <strong>Iron Berg Gym</strong>.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-zinc-500 font-medium">Status do Sistema:</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-950 text-emerald-400 border border-emerald-900/50">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                Conectado
            </span>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Stat Alunos -->
        <div class="bg-zinc-900 border border-zinc-800/80 rounded-xl p-5 shadow-md flex items-center justify-between hover:border-red-900/40 transition-all group">
            <div class="space-y-1">
                <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Total Alunos</span>
                <h3 class="text-3xl font-black text-white"><?php echo $count_alunos; ?></h3>
                <span class="text-[10px] text-emerald-400 font-semibold"><i class="fa-solid fa-arrow-trend-up mr-0.5"></i> Alunos ativos</span>
            </div>
            <div class="h-12 w-12 rounded-xl bg-zinc-950 flex items-center justify-center text-red-500 text-xl border border-zinc-800 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- Stat Equipamentos -->
        <div class="bg-zinc-900 border border-zinc-800/80 rounded-xl p-5 shadow-md flex items-center justify-between hover:border-red-900/40 transition-all group">
            <div class="space-y-1">
                <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Equipamentos</span>
                <h3 class="text-3xl font-black text-white"><?php echo $count_equipamentos; ?></h3>
                <span class="text-[10px] text-zinc-400 font-mono">Inv: R$ <?php echo number_format($total_inventario, 0, ',', '.'); ?></span>
            </div>
            <div class="h-12 w-12 rounded-xl bg-zinc-950 flex items-center justify-center text-red-500 text-xl border border-zinc-800 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-dumbbell"></i>
            </div>
        </div>

        <!-- Stat Funcionários -->
        <div class="bg-zinc-900 border border-zinc-800/80 rounded-xl p-5 shadow-md flex items-center justify-between hover:border-red-900/40 transition-all group">
            <div class="space-y-1">
                <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Colaboradores</span>
                <h3 class="text-3xl font-black text-white"><?php echo $count_funcionarios; ?></h3>
                <span class="text-[10px] text-zinc-400 font-mono">Folha: R$ <?php echo number_format($folha_pagamento, 0, ',', '.'); ?></span>
            </div>
            <div class="h-12 w-12 rounded-xl bg-zinc-950 flex items-center justify-center text-red-500 text-xl border border-zinc-800 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-user-tie"></i>
            </div>
        </div>

        <!-- Stat Admins -->
        <div class="bg-zinc-900 border border-zinc-800/80 rounded-xl p-5 shadow-md flex items-center justify-between hover:border-red-900/40 transition-all group">
            <div class="space-y-1">
                <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Operadores</span>
                <h3 class="text-3xl font-black text-white"><?php echo $count_usuarios; ?></h3>
                <span class="text-[10px] text-zinc-500">Acesso administrativo</span>
            </div>
            <div class="h-12 w-12 rounded-xl bg-zinc-950 flex items-center justify-center text-red-500 text-xl border border-zinc-800 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Split Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2 Cols: Recent activity tables -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Recent Students -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-lg text-white flex items-center gap-2">
                        <i class="fa-solid fa-user-clock text-red-500"></i>
                        Matrículas Recentes
                    </h3>
                    <a href="alunos/read.php" class="text-xs text-red-500 hover:text-red-400 font-semibold uppercase tracking-wider flex items-center gap-1 transition-colors">
                        Ver Todos <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-zinc-800 text-zinc-500 text-xs uppercase tracking-wider">
                                <th class="py-2.5 font-bold">Aluno</th>
                                <th class="py-2.5 font-bold">Data Matrícula</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-850">
                            <?php if (count($recent_alunos) > 0): ?>
                                <?php foreach ($recent_alunos as $al): ?>
                                    <tr class="text-sm">
                                        <td class="py-3 font-semibold text-zinc-200"><?php echo htmlspecialchars($al['nome']); ?></td>
                                        <td class="py-3 font-mono text-zinc-400"><?php echo date('d/m/Y H:i', strtotime($al['data_cadastro'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="py-4 text-center text-zinc-500 text-xs">Nenhuma matrícula registrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Equipment -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-lg text-white flex items-center gap-2">
                        <i class="fa-solid fa-plus-minus text-red-500"></i>
                        Novas Aquisições (Equipamentos)
                    </h3>
                    <a href="equipamentos/read.php" class="text-xs text-red-500 hover:text-red-400 font-semibold uppercase tracking-wider flex items-center gap-1 transition-colors">
                        Ver Todos <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-zinc-800 text-zinc-500 text-xs uppercase tracking-wider">
                                <th class="py-2.5 font-bold">Equipamento</th>
                                <th class="py-2.5 font-bold">Grupo Muscular</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-850">
                            <?php if (count($recent_equipamentos) > 0): ?>
                                <?php foreach ($recent_equipamentos as $eq): ?>
                                    <tr class="text-sm">
                                        <td class="py-3 font-semibold text-zinc-200"><?php echo htmlspecialchars($eq['nome']); ?></td>
                                        <td class="py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-800 text-red-400 border border-zinc-700/50">
                                                <?php echo htmlspecialchars($eq['grupo_muscular']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="py-4 text-center text-zinc-500 text-xs">Nenhum equipamento cadastrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Quick Links Panel -->
        <div class="space-y-6">
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 shadow-xl space-y-4">
                <h3 class="font-bold text-lg text-white flex items-center gap-2">
                    <i class="fa-solid fa-fire text-red-500"></i>
                    Ações Rápidas
                </h3>
                <div class="grid grid-cols-1 gap-3">
                    <a href="alunos/create.php" class="flex items-center gap-3 p-3 bg-zinc-950 border border-zinc-850 hover:border-red-900/40 rounded-xl text-sm transition-all group">
                        <span class="h-8 w-8 rounded-lg bg-red-950/40 border border-red-800/30 flex items-center justify-center text-red-400 group-hover:bg-red-600 group-hover:text-white transition-all">
                            <i class="fa-solid fa-user-plus text-xs"></i>
                        </span>
                        <div>
                            <p class="font-semibold text-zinc-200">Matricular Aluno</p>
                            <p class="text-[10px] text-zinc-500">Registrar novo membro</p>
                        </div>
                    </a>

                    <a href="equipamentos/create.php" class="flex items-center gap-3 p-3 bg-zinc-950 border border-zinc-850 hover:border-red-900/40 rounded-xl text-sm transition-all group">
                        <span class="h-8 w-8 rounded-lg bg-red-950/40 border border-red-800/30 flex items-center justify-center text-red-400 group-hover:bg-red-600 group-hover:text-white transition-all">
                            <i class="fa-solid fa-dumbbell text-xs"></i>
                        </span>
                        <div>
                            <p class="font-semibold text-zinc-200">Cadastrar Máquina</p>
                            <p class="text-[10px] text-zinc-500">Registrar novo equipamento</p>
                        </div>
                    </a>

                    <a href="funcionarios/create.php" class="flex items-center gap-3 p-3 bg-zinc-950 border border-zinc-850 hover:border-red-900/40 rounded-xl text-sm transition-all group">
                        <span class="h-8 w-8 rounded-lg bg-red-950/40 border border-red-800/30 flex items-center justify-center text-red-400 group-hover:bg-red-600 group-hover:text-white transition-all">
                            <i class="fa-solid fa-user-tie text-xs"></i>
                        </span>
                        <div>
                            <p class="font-semibold text-zinc-200">Contratar Funcionário</p>
                            <p class="text-[10px] text-zinc-500">Adicionar à folha de pagamento</p>
                        </div>
                    </a>

                    <a href="usuarios/create.php" class="flex items-center gap-3 p-3 bg-zinc-950 border border-zinc-850 hover:border-red-900/40 rounded-xl text-sm transition-all group">
                        <span class="h-8 w-8 rounded-lg bg-red-950/40 border border-red-800/30 flex items-center justify-center text-red-400 group-hover:bg-red-600 group-hover:text-white transition-all">
                            <i class="fa-solid fa-user-shield text-xs"></i>
                        </span>
                        <div>
                            <p class="font-semibold text-zinc-200">Adicionar Operador</p>
                            <p class="text-[10px] text-zinc-500">Cadastrar conta de acesso</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Gym Quotes card for extra premium feel -->
            <div class="bg-gradient-to-br from-zinc-900 to-zinc-950 border border-zinc-800 rounded-xl p-5 shadow-lg flex flex-col justify-between aspect-video relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 text-zinc-800/30 text-8xl font-black select-none pointer-events-none transform -rotate-12">
                    IRON
                </div>
                <div class="text-red-500 text-2xl font-bold">
                    <i class="fa-solid fa-quote-left"></i>
                </div>
                <p class="text-sm italic text-zinc-300 font-medium relative z-10">
                    "O único treino ruim é aquele que não aconteceu. Sem desculpas, apenas resultados."
                </p>
                <div class="text-xs text-zinc-500 font-bold uppercase tracking-wider mt-4">
                    - Iron Berg Gym Team
                </div>
            </div>
        </div>

    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
