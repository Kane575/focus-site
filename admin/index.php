<?php
require_once 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['email'] === ADMIN_EMAIL && $_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php'); exit;
    }
    $error = 'Email ou mot de passe incorrect.';
}
if (is_logged_in()) { header('Location: dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — FOCUS Audit & Conseil</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  body{font-family:'Plus Jakarta Sans',sans-serif;}
  .gradient-text{background:linear-gradient(135deg,#10b981,#3b82f6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
  @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
  .float{animation:float 4s ease-in-out infinite;}
</style>
</head>
<body class="min-h-screen bg-slate-900 flex items-center justify-center px-4 py-10">

  <div class="fixed top-1/4 left-1/4 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
  <div class="fixed bottom-1/4 right-1/4 w-52 h-52 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

  <div class="w-full max-w-sm relative z-10">
    <div class="text-center mb-8 float">
      <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-blue-500 mb-4 shadow-xl shadow-emerald-500/20">
        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
      </div>
      <h1 class="text-xl font-extrabold text-white">Espace Admin</h1>
      <p class="text-slate-400 text-sm mt-1"><span class="gradient-text font-bold">FOCUS</span> Audit & Conseil</p>
    </div>

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl">
      <?php if ($error): ?>
        <div class="mb-4 px-4 py-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm flex items-center gap-2">
          <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
      <form method="POST" class="space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Email</label>
          <input type="email" name="email" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white text-base outline-none focus:border-emerald-500 transition-all placeholder:text-slate-600"
            placeholder="votre@email.fr">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Mot de passe</label>
          <div class="relative">
            <input type="password" name="password" id="pwd" required
              class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white text-base outline-none focus:border-emerald-500 transition-all placeholder:text-slate-600 pr-12"
              placeholder="••••••••">
            <button type="button" onclick="document.getElementById('pwd').type=document.getElementById('pwd').type==='password'?'text':'password'"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 p-1">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
          </div>
        </div>
        <button type="submit" class="w-full py-4 bg-gradient-to-r from-emerald-500 to-blue-500 text-white rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-emerald-500/20 active:scale-95 transition-all duration-200">
          Se connecter
        </button>
      </form>
    </div>
    <p class="text-center text-slate-600 text-xs mt-5">
      <a href="../index.html" class="hover:text-slate-400 transition-colors inline-flex items-center gap-1">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour au site
      </a>
    </p>
  </div>
</body>
</html>