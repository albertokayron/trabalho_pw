<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$path_prefix = '';
if (file_exists('login.php')) {
    $path_prefix = '';
} elseif (file_exists('../login.php')) {
    $path_prefix = '../';
}

$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

function is_active($page, $dir = '') {
    global $current_page, $current_dir;
    if ($dir !== '') {
        return $current_dir === $dir ? 'bg-red-600 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white';
    }
    return $current_page === $page ? 'bg-red-600 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white';
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-zinc-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iron Berg Gym - Sistema de Gerenciamento</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom scrollbar for premium feel */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #09090b; 
        }
        ::-webkit-scrollbar-thumb {
            background: #27272a; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #3f3f46; 
        }
    </style>
</head>
<body class="h-full font-sans text-zinc-100 flex flex-col md:flex-row overflow-hidden">

    <div class="md:hidden flex items-center justify-between bg-zinc-900 px-4 py-3 border-b border-zinc-800 shrink-0">
        <div class="flex items-center gap-2">
            <span class="p-2 bg-red-600 rounded-lg text-white font-extrabold flex items-center justify-center">
                <i class="fa-solid fa-dumbbell"></i>
            </span>
            <span class="font-extrabold tracking-widest text-lg text-white italic">IRON <span class="text-red-500">BERG</span></span>
        </div>
        <button id="mobile-menu-toggle" class="text-zinc-400 hover:text-white focus:outline-none">
            <i class="fa-solid fa-bars text-2xl"></i>
        </button>
    </div>
    
    <div id="sidebar-container" class="fixed inset-0 z-40 hidden md:relative md:flex md:w-64 md:flex-col shrink-0">
        <div id="mobile-backdrop" class="fixed inset-0 bg-black/60 md:hidden"></div>

        <aside class="relative flex flex-col h-full w-64 bg-zinc-900 border-r border-zinc-800 p-4 transition-all duration-300">
            <div class="flex items-center gap-3 px-2 py-4 mb-6 border-b border-zinc-800">
                <span class="p-2.5 bg-red-600 rounded-xl text-white font-black text-xl shadow-lg shadow-red-900/30 flex items-center justify-center animate-pulse">
                    <i class="fa-solid fa-dumbbell"></i>
                </span>
                <div class="flex flex-col">
                    <span class="font-black tracking-wider text-xl text-white italic leading-tight">IRON <span class="text-red-500">BERG</span></span>
                    <span class="text-[10px] text-zinc-500 font-medium tracking-widest uppercase">Gym Manager</span>
                </div>
            </div>

            <nav class="flex-1 space-y-1.5 px-1 overflow-y-auto">
                <a href="<?php echo $path_prefix; ?>index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all <?php echo is_active('index.php'); ?>">
                    <i class="fa-solid fa-chart-pie text-base w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>

                <div class="pt-4 pb-1">
                    <span class="px-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Cadastros & CRUDS</span>
                </div>

                <a href="<?php echo $path_prefix; ?>alunos/read.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all <?php echo is_active('read.php', 'alunos'); ?>">
                    <i class="fa-solid fa-users text-base w-5 text-center"></i>
                    <span>Alunos</span>
                </a>

                <a href="<?php echo $path_prefix; ?>equipamentos/read.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all <?php echo is_active('read.php', 'equipamentos'); ?>">
                    <i class="fa-solid fa-dumbbell text-base w-5 text-center"></i>
                    <span>Equipamentos</span>
                </a>

                <a href="<?php echo $path_prefix; ?>funcionarios/read.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all <?php echo is_active('read.php', 'funcionarios'); ?>">
                    <i class="fa-solid fa-user-tie text-base w-5 text-center"></i>
                    <span>Funcionários</span>
                </a>

                <a href="<?php echo $path_prefix; ?>usuarios/read.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all <?php echo is_active('read.php', 'usuarios'); ?>">
                    <i class="fa-solid fa-user-gear text-base w-5 text-center"></i>
                    <span>Usuários do Sistema</span>
                </a>
            </nav>

            <div class="mt-auto pt-4 border-t border-zinc-800">
                <div class="flex items-center gap-3 px-2 py-3 mb-2 rounded-lg bg-zinc-950/50 border border-zinc-800/40">
                    <div class="h-9 w-9 rounded-full bg-zinc-800 flex items-center justify-center text-red-500 font-bold border border-zinc-700">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-xs font-semibold text-zinc-200 truncate"><?php echo isset($_SESSION['usuario_nome']) ? htmlspecialchars($_SESSION['usuario_nome']) : 'Admin'; ?></span>
                        <span class="text-[10px] text-zinc-500 truncate"><?php echo isset($_SESSION['usuario_email']) ? htmlspecialchars($_SESSION['usuario_email']) : 'admin@ironberg.com'; ?></span>
                    </div>
                </div>
                <a href="<?php echo $path_prefix; ?>logout.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold text-zinc-400 hover:bg-red-950/30 hover:text-red-400 transition-all">
                    <i class="fa-solid fa-right-from-bracket text-base w-5 text-center"></i>
                    <span>Sair do Sistema</span>
                </a>
            </div>
        </aside>
    </div>

    <main class="flex-1 flex flex-col overflow-hidden bg-zinc-950">
        <div class="flex-1 overflow-y-auto px-4 py-6 md:p-8">
            <?php if (isset($_SESSION['toast'])): ?>
                <div class="mb-6 p-4 rounded-xl border flex items-center justify-between transition-all <?php echo $_SESSION['toast']['type'] === 'success' ? 'bg-emerald-950/40 border-emerald-800/40 text-emerald-400' : 'bg-red-950/40 border-red-800/40 text-red-400'; ?>" id="toast-message">
                    <div class="flex items-center gap-3">
                        <i class="<?php echo $_SESSION['toast']['type'] === 'success' ? 'fa-solid fa-circle-check text-lg' : 'fa-solid fa-circle-xmark text-lg'; ?>"></i>
                        <span class="text-sm font-medium"><?php echo htmlspecialchars($_SESSION['toast']['message']); ?></span>
                    </div>
                    <button onclick="document.getElementById('toast-message').remove()" class="text-zinc-400 hover:text-white transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <?php unset($_SESSION['toast']); ?>
            <?php endif; ?>
