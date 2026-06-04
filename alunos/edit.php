<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexao.php';

$error_message = '';
$aluno_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$aluno_id) {
    header("Location: read.php");
    exit;
}

// Fetch student data
try {
    $stmt = $conn->prepare("SELECT * FROM alunos WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $aluno_id]);
    $aluno = $stmt->fetch();

    if (!$aluno) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Aluno não encontrado.'];
        header("Location: read.php");
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados do aluno: " . $e->getMessage());
}

$nome = $aluno['nome'];
$data_nascimento = $aluno['data_nascimento'];
$dia_vencimento = $aluno['dia_vencimento'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $data_nascimento = trim($_POST['data_nascimento']);
    $dia_vencimento = trim($_POST['dia_vencimento']);

    if (empty($nome) || empty($data_nascimento) || empty($dia_vencimento)) {
        $error_message = 'Todos os campos são obrigatórios.';
    } else {
        $dia_vencimento = intval($dia_vencimento);
        if ($dia_vencimento < 1 || $dia_vencimento > 31) {
            $error_message = 'O dia do vencimento deve ser entre 1 e 31.';
        } else {
            // Validate birth date is in the past
            $birthDate = new DateTime($data_nascimento);
            $today = new DateTime();
            if ($birthDate >= $today) {
                $error_message = 'A data de nascimento deve ser no passado.';
            } else {
                try {
                    $stmt = $conn->prepare("UPDATE alunos SET nome = :nome, data_nascimento = :data_nascimento, dia_vencimento = :dia_vencimento WHERE id = :id");
                    $stmt->execute([
                        'nome' => $nome,
                        'data_nascimento' => $data_nascimento,
                        'dia_vencimento' => $dia_vencimento,
                        'id' => $aluno_id
                    ]);

                    $_SESSION['toast'] = [
                        'type' => 'success',
                        'message' => 'Matrícula do aluno atualizada com sucesso!'
                    ];
                    header("Location: read.php");
                    exit;
                } catch (PDOException $e) {
                    $error_message = 'Erro ao atualizar matrícula do aluno: ' . $e->getMessage();
                }
            }
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
        <h1 class="text-2xl font-bold tracking-tight text-white">Editar Matrícula de Aluno</h1>
        <p class="text-sm text-zinc-400">Modifique as informações cadastrais e do plano do aluno.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 md:p-8 shadow-xl">
        <?php if (!empty($error_message)): ?>
            <div class="mb-6 p-4 rounded-lg bg-red-950/40 border border-red-800/40 text-red-400 text-sm flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

        <form action="edit.php?id=<?php echo $aluno_id; ?>" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label for="nome" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Nome Completo</label>
                    <input type="text" name="nome" id="nome" required value="<?php echo htmlspecialchars($nome); ?>"
                           placeholder="Ex: Carlos Santana de Souza"
                           class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>

                <!-- Birth Date -->
                <div>
                    <label for="data_nascimento" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Data de Nascimento</label>
                    <input type="date" name="data_nascimento" id="data_nascimento" required value="<?php echo htmlspecialchars($data_nascimento); ?>"
                           class="block w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>

                <!-- Plan Due Day -->
                <div>
                    <label for="dia_vencimento" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Dia de Vencimento da Mensalidade</label>
                    <input type="number" name="dia_vencimento" id="dia_vencimento" required min="1" max="31" value="<?php echo htmlspecialchars($dia_vencimento); ?>"
                           placeholder="Ex: 10"
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
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
