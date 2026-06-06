<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexao.php';

$error_message = '';
$nome = '';
$valor = '';
$funcionalidade = '';
$grupo_muscular = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $valor = trim($_POST['valor']);
    $funcionalidade = trim($_POST['funcionalidade']);
    $grupo_muscular = trim($_POST['grupo_muscular']);

    if (empty($nome) || empty($valor) || empty($funcionalidade) || empty($grupo_muscular)) {
        $error_message = 'Todos os campos são obrigatórios.';
    } elseif (!is_numeric($valor) || floatval($valor) < 0) {
        $error_message = 'O valor de aquisição deve ser um número válido e maior ou igual a zero.';
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO equipamentos (nome, valor, funcionalidade, grupo_muscular) VALUES (:nome, :valor, :funcionalidade, :grupo_muscular)");
            $stmt->execute([
                'nome' => $nome,
                'valor' => floatval($valor),
                'funcionalidade' => $funcionalidade,
                'grupo_muscular' => $grupo_muscular
            ]);

            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Equipamento cadastrado com sucesso!'
            ];
            header("Location: read.php");
            exit;
        } catch (PDOException $e) {
            $error_message = 'Erro ao cadastrar equipamento: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="read.php" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-zinc-500 hover:text-zinc-300 transition-colors mb-3">
            <i class="fa-solid fa-arrow-left"></i> Voltar para a lista
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-white">Adicionar Equipamento</h1>
        <p class="text-sm text-zinc-400">Insira as especificações da nova máquina ou acessório adquirido.</p>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 md:p-8 shadow-xl">
        <?php if (!empty($error_message)): ?>
            <div class="mb-6 p-4 rounded-lg bg-red-950/40 border border-red-800/40 text-red-400 text-sm flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

        <form action="create.php" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label for="nome" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Nome do Equipamento</label>
                    <input type="text" name="nome" id="nome" required value="<?php echo htmlspecialchars($nome); ?>"
                           placeholder="Ex: Cadeira Extensora, Halter Emborrachado 20kg"
                           class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>

                <div>
                    <label for="valor" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Valor de Aquisição (R$)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-500 text-sm font-semibold">R$</span>
                        <input type="number" name="valor" id="valor" required step="0.01" min="0.00" value="<?php echo htmlspecialchars($valor); ?>"
                               placeholder="0.00"
                               class="block w-full pl-10 pr-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    </div>
                </div>

                <div>
                    <label for="grupo_muscular" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Grupo Muscular Principal</label>
                    <select name="grupo_muscular" id="grupo_muscular" required
                            class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                        <option value="" disabled <?php echo empty($grupo_muscular) ? 'selected' : ''; ?>>Selecione o grupo muscular</option>
                        <?php
                        $options = ['Peito', 'Costas', 'Pernas (Quadríceps/Posterior)', 'Glúteos', 'Panturrilhas', 'Ombros', 'Bíceps', 'Tríceps', 'Abdômen', 'Cardio / Aeróbico', 'Treino Funcional'];
                        foreach ($options as $opt) {
                            $selected = ($grupo_muscular === $opt) ? 'selected' : '';
                            echo "<option value=\"$opt\" $selected>$opt</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="funcionalidade" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Funcionalidade / Descrição</label>
                    <textarea name="funcionalidade" id="funcionalidade" required rows="4"
                              placeholder="Descreva a utilidade do equipamento, configurações de ajustes e benefícios musculares..."
                              class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"><?php echo htmlspecialchars($funcionalidade); ?></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-zinc-800">
                <a href="read.php" 
                   class="px-4 py-2.5 bg-zinc-850 hover:bg-zinc-800 active:bg-zinc-750 text-zinc-300 hover:text-white text-sm font-semibold rounded-lg transition-colors border border-zinc-800">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-sm font-semibold rounded-lg transition-colors shadow-lg shadow-red-950/20">
                    Salvar Equipamento
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
