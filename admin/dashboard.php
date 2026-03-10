<?php
require_once 'config.php';
require_login();
$articles = get_articles();
$total    = count($articles);
$publies  = count(array_filter($articles, fn($a) => $a['publie'] ?? false));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Admin FOCUS</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
  body{font-family:'Plus Jakarta Sans',sans-serif;}
  .gradient-text{background:linear-gradient(135deg,#10b981,#3b82f6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
  /* Sidebar mobile */
  #sidebar{transform:translateX(-100%);transition:transform 0.3s ease;}
  #sidebar.open{transform:translateX(0);}
  @media (min-width: 768px) {
    #sidebar{transform:translateX(0);}
  }
  #overlay{opacity:0;pointer-events:none;transition:opacity 0.3s ease;}
  #overlay.open{opacity:1;pointer-events:auto;}
</style>
</head>
<body class="bg-slate-950 text-white min-h-screen">

<!-- Overlay mobile -->
<div id="overlay" class="fixed inset-0 bg-black/60 z-30 md:hidden" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 bottom-0 w-72 bg-slate-900 border-r border-white/5 flex flex-col z-40 md:translate-x-0">
  <div class="p-5 border-b border-white/5 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-blue-500 flex items-center justify-center flex-shrink-0">
        <i data-lucide="layout-dashboard" class="w-4 h-4 text-white"></i>
      </div>
      <div>
        <p class="font-extrabold text-white text-sm">FOCUS Admin</p>
        <p class="text-[10px] text-slate-500">Gestion du contenu</p>
      </div>
    </div>
    <button onclick="closeSidebar()" class="md:hidden text-slate-400 hover:text-white p-1">
      <i data-lucide="x" class="w-5 h-5"></i>
    </button>
  </div>

  <nav class="flex-1 p-4 space-y-1">
    <a href="dashboard.php" onclick="closeSidebar()" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/10 text-emerald-400 font-semibold text-sm">
      <i data-lucide="file-text" class="w-4 h-4"></i> Articles
    </a>
    <a href="edit.php" onclick="closeSidebar()" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 font-semibold text-sm transition-all">
      <i data-lucide="plus-circle" class="w-4 h-4"></i> Nouvel article
    </a>
    <a href="../blog.php" target="_blank" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 font-semibold text-sm transition-all">
      <i data-lucide="external-link" class="w-4 h-4"></i> Voir le blog
    </a>
    <a href="../index.html" target="_blank" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 font-semibold text-sm transition-all">
      <i data-lucide="home" class="w-4 h-4"></i> Site principal
    </a>
  </nav>

  <div class="p-4 border-t border-white/5">
    <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 font-semibold text-sm transition-all">
      <i data-lucide="log-out" class="w-4 h-4"></i> Déconnexion
    </a>
  </div>
</aside>

<!-- Main -->
<div class="md:ml-72">

  <!-- Top bar mobile -->
  <div class="sticky top-0 z-20 bg-slate-950/90 backdrop-blur border-b border-white/5 flex items-center justify-between px-4 py-3 md:hidden">
    <button onclick="openSidebar()" class="text-slate-400 hover:text-white p-1">
      <i data-lucide="menu" class="w-6 h-6"></i>
    </button>
    <span class="font-bold text-sm gradient-text">FOCUS Admin</span>
    <a href="edit.php" class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-blue-500 flex items-center justify-center">
      <i data-lucide="plus" class="w-4 h-4 text-white"></i>
    </a>
  </div>

  <main class="p-4 md:p-8">

    <!-- Header desktop -->
    <div class="hidden md:flex items-center justify-between mb-10">
      <div>
        <h1 class="text-3xl font-extrabold text-white">Tableau de bord</h1>
        <p class="text-slate-400 text-sm mt-1">Gérez les articles de votre blog</p>
      </div>
      <a href="edit.php" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-blue-500 text-white rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-emerald-500/20 hover:-translate-y-0.5 transition-all">
        <i data-lucide="plus" class="w-4 h-4"></i> Nouvel article
      </a>
    </div>

    <!-- Header mobile -->
    <div class="md:hidden mb-6">
      <h1 class="text-xl font-extrabold text-white">Tableau de bord</h1>
      <p class="text-slate-400 text-xs mt-0.5">Gérez vos articles</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-3 md:gap-6 mb-6 md:mb-10">
      <div class="bg-slate-900 border border-white/5 rounded-xl md:rounded-2xl p-4 md:p-6">
        <p class="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-wider mb-1 md:mb-2">Total</p>
        <p class="text-2xl md:text-4xl font-extrabold text-white"><?= $total ?></p>
      </div>
      <div class="bg-slate-900 border border-white/5 rounded-xl md:rounded-2xl p-4 md:p-6">
        <p class="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-wider mb-1 md:mb-2">Publiés</p>
        <p class="text-2xl md:text-4xl font-extrabold text-emerald-400"><?= $publies ?></p>
      </div>
      <div class="bg-slate-900 border border-white/5 rounded-xl md:rounded-2xl p-4 md:p-6">
        <p class="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-wider mb-1 md:mb-2">Brouillons</p>
        <p class="text-2xl md:text-4xl font-extrabold text-slate-400"><?= $total - $publies ?></p>
      </div>
    </div>

    <!-- Articles list -->
    <div class="bg-slate-900 border border-white/5 rounded-xl md:rounded-2xl overflow-hidden">
      <div class="p-4 md:p-6 border-b border-white/5 flex items-center justify-between">
        <h2 class="font-bold text-white text-sm md:text-base">Tous les articles</h2>
        <a href="edit.php" class="md:hidden flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 text-emerald-400 rounded-lg text-xs font-bold">
          <i data-lucide="plus" class="w-3 h-3"></i> Nouveau
        </a>
      </div>

      <?php if (empty($articles)): ?>
        <div class="flex flex-col items-center justify-center py-16 text-center px-4">
          <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center mb-4">
            <i data-lucide="file-text" class="w-7 h-7 text-slate-500"></i>
          </div>
          <p class="text-slate-400 font-medium text-sm">Aucun article pour l'instant</p>
          <a href="edit.php" class="mt-4 px-5 py-2.5 bg-emerald-500/10 text-emerald-400 rounded-xl text-sm font-bold hover:bg-emerald-500/20 transition-all">
            Créer le premier article
          </a>
        </div>
      <?php else: ?>
        <div class="divide-y divide-white/5">
          <?php foreach (array_reverse($articles) as $a): ?>

          <!-- Version mobile -->
          <div class="p-4 md:hidden">
            <div class="flex items-start gap-3">
              <!-- Thumbnail mobile -->
              <div class="w-16 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-slate-800">
                <?php if (!empty($a['image'])): ?>
                  <img src="../<?= htmlspecialchars($a['image']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                  <div class="w-full h-full flex items-center justify-center">
                    <i data-lucide="image" class="w-5 h-5 text-slate-600"></i>
                  </div>
                <?php endif; ?>
              </div>

              <div class="flex-1 min-w-0">
                <h3 class="font-bold text-white text-sm leading-tight truncate"><?= htmlspecialchars($a['titre']) ?></h3>
                <div class="flex items-center gap-2 mt-1 flex-wrap">
                  <span class="text-[10px] text-slate-500"><?= htmlspecialchars($a['date']) ?></span>
                  <?php if (!empty($a['categorie'])): ?>
                    <span class="px-1.5 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-medium"><?= htmlspecialchars($a['categorie']) ?></span>
                  <?php endif; ?>
                  <?php if ($a['publie'] ?? false): ?>
                    <span class="px-1.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold">✓ Publié</span>
                  <?php else: ?>
                    <span class="px-1.5 py-0.5 rounded-full bg-slate-700 text-slate-400 text-[10px] font-bold">Brouillon</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- Actions mobile -->
            <div class="flex items-center gap-2 mt-3">
              <a href="edit.php?slug=<?= urlencode($a['slug']) ?>"
                class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-white/5 rounded-lg text-slate-300 hover:text-white hover:bg-emerald-500/10 text-xs font-semibold transition-all">
                <i data-lucide="pencil" class="w-3 h-3"></i> Modifier
              </a>
              <a href="../article.php?slug=<?= urlencode($a['slug']) ?>" target="_blank"
                class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-white/5 rounded-lg text-slate-300 hover:text-white hover:bg-blue-500/10 text-xs font-semibold transition-all">
                <i data-lucide="eye" class="w-3 h-3"></i> Voir
              </a>
              <a href="delete.php?slug=<?= urlencode($a['slug']) ?>"
                onclick="return confirm('Supprimer ?')"
                class="w-9 h-9 flex items-center justify-center bg-white/5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
              </a>
            </div>
          </div>

          <!-- Version desktop -->
          <div class="hidden md:flex items-center gap-6 p-6 hover:bg-white/2 transition-all">
            <div class="w-20 h-14 rounded-xl overflow-hidden flex-shrink-0 bg-slate-800">
              <?php if (!empty($a['image'])): ?>
                <img src="../<?= htmlspecialchars($a['image']) ?>" class="w-full h-full object-cover">
              <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                  <i data-lucide="image" class="w-6 h-6 text-slate-600"></i>
                </div>
              <?php endif; ?>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="font-bold text-white truncate"><?= htmlspecialchars($a['titre']) ?></h3>
              <div class="flex items-center gap-3 mt-1">
                <span class="text-xs text-slate-500"><?= htmlspecialchars($a['date']) ?></span>
                <?php if (!empty($a['categorie'])): ?>
                  <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-xs font-medium"><?= htmlspecialchars($a['categorie']) ?></span>
                <?php endif; ?>
              </div>
            </div>
            <div>
              <?php if ($a['publie'] ?? false): ?>
                <span class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-xs font-bold">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Publié
                </span>
              <?php else: ?>
                <span class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-700 text-slate-400 text-xs font-bold">
                  <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> Brouillon
                </span>
              <?php endif; ?>
            </div>
            <div class="flex items-center gap-2">
              <a href="edit.php?slug=<?= urlencode($a['slug']) ?>"
                class="w-9 h-9 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-white hover:bg-emerald-500/20 transition-all" title="Modifier">
                <i data-lucide="pencil" class="w-4 h-4"></i>
              </a>
              <a href="../article.php?slug=<?= urlencode($a['slug']) ?>" target="_blank"
                class="w-9 h-9 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-white hover:bg-blue-500/20 transition-all" title="Voir">
                <i data-lucide="eye" class="w-4 h-4"></i>
              </a>
              <a href="delete.php?slug=<?= urlencode($a['slug']) ?>"
                onclick="return confirm('Supprimer cet article ?')"
                class="w-9 h-9 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all" title="Supprimer">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
              </a>
            </div>
          </div>

          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>
</div>

<script>
lucide.createIcons();
function openSidebar()  { document.getElementById('sidebar').classList.add('open'); document.getElementById('overlay').classList.add('open'); }
function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('overlay').classList.remove('open'); }
</script>
</body>
</html>