<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexao.php';

$error_message = '';
$nome = '';
$funcao = '';
$data_admissao = '';
$data_nascimento = '';
$salario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $funcao = trim($_POST['funcao']);
    $data_admissao = trim($_POST['data_admissao']);
    $data_nascimento = trim($_POST['data_nascimento']);
    $salario = trim($_POST['salario']);

    if (empty($nome) || empty($funcao) || empty($data_admissao) || empty($data_nascimento) || empty($salario)) {
        $error_message = 'Todos os campos são obrigatórios.';
    } elseif (!is_numeric($salario) || floatval($salario) < 0) {
        $error_message = 'O salário deve ser um valor numérico válido e maior ou igual a zero.';
    } else {
        $birthDate = new DateTime($data_nascimento);
        $today = new DateTime();
        if ($birthDate >= $today) {
            $error_message = 'A data de nascimento deve ser no passado.';
        } else {
            try {
                $stmt = $conn->prepare("INSERT INTO funcionarios (nome, funcao, data_admissao, data_nascimento, salario) VALUES (:nome, :funcao, :data_admissao, :data_nascimento, :salario)");
                $stmt->execute([
                    'nome' => $nome,
                    'funcao' => $funcao,
                    'data_admissao' => $data_admissao,
                    'data_nascimento' => $data_nascimento,
                    'salario' => floatval($salario)
                ]);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Colaborador adicionado com sucesso!'
                ];
                header("Location: read.php");
                exit;
            } catch (PDOException $e) {
                $error_message = 'Erro ao adicionar colaborador: ' . $e->getMessage();
            }
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
        <h1 class="text-2xl font-bold tracking-tight text-white">Adicionar Novo Colaborador</h1>
        <p class="text-sm text-zinc-400">Insira os dados profissionais e pessoais para o registro trabalhista do funcionário.</p>
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
                    <label for="nome" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Nome Completo</label>
                    <input type="text" name="nome" id="nome" required value="<?php echo htmlspecialchars($nome); ?>"
                           placeholder="Ex: Amanda Lima Rodrigues"
                           class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>

                <div>
                    <label for="funcao" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Função / Cargo</label>
                    <input type="text" name="funcao" id="funcao" required value="<?php echo htmlspecialchars($funcao); ?>"
                           placeholder="Ex: Instrutor, Recepcionista"
                           class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>

                <div>
                    <label for="salario" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Salário Base (R$)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-500 text-sm font-semibold">R$</span>
                        <input type="number" name="salario" id="salario" required step="0.01" min="0.00" value="<?php echo htmlspecialchars($salario); ?>"
                               placeholder="0.00"
                               class="block w-full pl-10 pr-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    </div>
                </div>

                <div>
                    <label for="data_admissao" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Data de Admissão</label>
                    <input type="date" name="data_admissao" id="data_admissao" required value="<?php echo htmlspecialchars($data_admissao); ?>"
                           class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>

                <!-- Birth Date -->
                <div>
                    <label for="data_nascimento" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Data de Nascimento</label>
                    <input type="date" name="data_nascimento" id="data_nascimento" required value="<?php echo htmlspecialchars($data_nascimento); ?>"
                           class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
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
                    Salvar Colaborador
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
