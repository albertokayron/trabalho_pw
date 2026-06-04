<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/config/conexao.php';

$error_message = '';
$success_message = '';

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'unauthorized') {
        $error_message = 'Acesso negado. Por favor, faça login para acessar o sistema.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (empty($email) || empty($senha)) {
        $error_message = 'Por favor, preencha todos os campos.';
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($senha, $user['senha'])) {
                // Set session variables
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nome'] = $user['nome'];
                $_SESSION['usuario_email'] = $user['email'];

                header("Location: index.php");
                exit;
            } else {
                $error_message = 'E-mail ou senha incorretos.';
            }
        } catch (PDOException $e) {
            $error_message = 'Erro no servidor: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-zinc-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iron Berg Gym - Login</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="h-full font-sans flex items-center justify-center p-4 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-zinc-900 via-zinc-950 to-black">

    <div class="w-full max-w-md">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <span class="inline-flex p-3 bg-red-600 rounded-2xl text-white font-black text-3xl shadow-xl shadow-red-950/40 mb-3 border border-red-500/20">
                <i class="fa-solid fa-dumbbell"></i>
            </span>
            <h1 class="text-3xl font-extrabold tracking-wider text-white italic">IRON <span class="text-red-500">BERG</span></h1>
            <p class="text-zinc-500 text-xs mt-1 uppercase tracking-widest font-semibold">Pain is weakness leaving the body</p>
        </div>

        <!-- Form Card -->
        <div class="bg-zinc-900/80 backdrop-blur-md rounded-2xl border border-zinc-800 p-8 shadow-2xl shadow-black/50">
            <h2 class="text-xl font-bold text-white mb-6">Acesse sua conta</h2>

            <?php if (!empty($error_message)): ?>
                <div class="mb-5 p-3 rounded-lg bg-red-950/40 border border-red-800/40 text-red-400 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-5">
                <div>
                    <label for="email" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">E-mail</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-500">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" required
                               placeholder="exemplo@ironberg.com"
                               class="block w-full pl-10 pr-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="senha" class="block text-xs font-bold text-zinc-400 uppercase tracking-wider">Senha</label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-500">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="senha" id="senha" required
                               placeholder="Sua senha secreta"
                               class="block w-full pl-10 pr-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold text-sm rounded-lg transition-all shadow-lg shadow-red-950/20 hover:shadow-red-600/10 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-zinc-900 mt-2">
                    Entrar no Sistema
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-zinc-800 text-center">
                <span class="text-zinc-500 text-xs">Acesso administrativo padrão</span>
                <div class="mt-1 text-zinc-400 text-xs font-mono select-all">
                    admin@ironberg.com / admin
                </div>
            </div>
        </div>
    </div>

</body>
</html>
