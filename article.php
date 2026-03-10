<?php
require_once 'admin/config.php';

$slug    = $_GET['slug'] ?? '';
$article = $slug ? get_article_by_slug($slug) : null;

if (!$article || !($article['publie'] ?? false)) {
    header('Location: blog.php');
    exit;
}

// Article suivant / précédent
$all = array_values(array_filter(get_articles(), fn($a) => $a['publie'] ?? false));
$idx = array_search($slug, array_column($all, 'slug'));
$prev = $idx > 0 ? $all[$idx - 1] : null;
$next = $idx < count($all) - 1 ? $all[$idx + 1] : null;
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($article['titre']) ?> — FOCUS Audit & Conseil</title>
<meta name="description" content="<?= htmlspecialchars($article['extrait'] ?? '') ?>">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
  *{margin:0;padding:0;box-sizing:border-box;}
  body{font-family:'Plus Jakarta Sans',sans-serif;overflow-x:hidden;}
  .scroll-progress{position:fixed;top:0;left:0;width:0%;height:3px;background:linear-gradient(90deg,#10b981,#3b82f6);z-index:9999;}
  .navbar-blur{background:rgba(15,23,42,0.85);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,0.08);}
  .gradient-text{background:linear-gradient(135deg,#10b981,#3b82f6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
  .mobile-menu{max-height:0;overflow:hidden;transition:max-height 0.4s ease;}
  .mobile-menu.active{max-height:500px;}
  /* Contenu article */
  .article-content{font-family:'Lora',serif;font-size:1.125rem;line-height:1.85;color:#334155;}
  .article-content h2{font-family:'Plus Jakarta Sans',sans-serif;font-size:1.6rem;font-weight:800;color:#0f172a;margin:2.5rem 0 1rem;padding-bottom:0.5rem;border-bottom:2px solid #f1f5f9;}
  .article-content h3{font-family:'Plus Jakarta Sans',sans-serif;font-size:1.25rem;font-weight:700;color:#1e293b;margin:2rem 0 0.75rem;}
  .article-content p{margin-bottom:1.5rem;}
  .article-content ul,.article-content ol{margin:1.25rem 0 1.5rem 1.5rem;}
  .article-content li{margin-bottom:0.5rem;}
  .article-content strong{color:#0f172a;font-weight:700;}
  .article-content em{font-style:italic;color:#64748b;}
  .article-content a{color:#10b981;text-decoration:underline;text-underline-offset:3px;}
  .article-content a:hover{color:#059669;}
  .article-content blockquote{border-left:4px solid #10b981;padding:1rem 1.5rem;background:#f0fdf4;border-radius:0 12px 12px 0;margin:2rem 0;font-style:italic;color:#475569;}
</style>
</head>
<body class="bg-white text-slate-900 antialiased">

<div class="scroll-progress" id="progress-bar"></div>

<!-- Navbar -->
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 py-5 navbar-blur">
  <div class="max-w-7xl mx-auto px-4 md:px-8 flex items-center justify-between">
    <a href="index.html" class="flex items-center gap-2.5">
      <img class="w-24" src="img/logo.png" alt="Logo FOCUS">
      <div class="flex flex-col">
        <span class="text-xl font-extrabold text-white tracking-tight">FOCUS</span>
        <span class="text-[9px] uppercase tracking-[0.2em] text-emerald-400 font-bold -mt-0.5">Audit & Conseil</span>
      </div>
    </a>
    <div class="hidden md:flex items-center gap-8">
      <a href="index.html#about"    class="text-xs font-bold text-white/80 hover:text-white tracking-wider">À PROPOS</a>
      <a href="index.html#services" class="text-xs font-bold text-white/80 hover:text-white tracking-wider">SERVICES</a>
      <a href="blog.php"            class="text-xs font-bold text-emerald-400 tracking-wider">BLOG</a>
      <a href="index.html#contact"  class="text-xs font-bold text-white/80 hover:text-white tracking-wider">CONTACT</a>
      <a href="index.html#contact"  class="px-6 py-2.5 bg-white text-slate-900 rounded-full text-xs font-bold hover:bg-emerald-50 transition-all">CONSULTATION</a>
    </div>
    <button id="mobile-toggle" class="md:hidden text-white p-2"><i data-lucide="menu" class="w-6 h-6"></i></button>
  </div>
    <div id="mobile-menu" class="mobile-menu md:hidden bg-slate-900/95 backdrop-blur-xl border-b border-white/10">
    <div class="px-4 py-6 flex flex-col gap-4">
      <a href="index.html#about"    class="text-sm font-bold text-white/80 hover:text-white py-2">À PROPOS</a>
      <a href="index.html#services" class="text-sm font-bold text-white/80 hover:text-white py-2">SERVICES</a>
      <a href="blog.php"            class="text-sm font-bold text-white py-2">BLOG</a>
      <a href="index.html#contact"  class="text-sm font-bold text-white/80 hover:text-white py-2">CONTACT</a>
    </div>
  </div>
</nav>

<!-- Hero article -->
<section class="relative pt-24 pb-0 min-h-[50vh] flex items-end bg-slate-900 overflow-hidden">
  <?php if (!empty($article['image'])): ?>
    <div class="absolute inset-0">
      <img src="<?= htmlspecialchars($article['image']) ?>" class="w-full h-full object-cover opacity-30">
      <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-slate-900/20"></div>
    </div>
  <?php else: ?>
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 to-slate-800">
      <div class="absolute top-1/3 left-1/4 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
    </div>
  <?php endif; ?>

  <div class="relative z-10 max-w-4xl mx-auto px-4 md:px-8 pb-14 pt-10">
    <!-- Fil d'ariane -->
    <div class="flex items-center gap-2 text-sm text-white/50 mb-6">
      <a href="index.html" class="hover:text-white transition-colors">Accueil</a>
      <i data-lucide="chevron-right" class="w-3 h-3"></i>
      <a href="blog.php" class="hover:text-white transition-colors">Blog</a>
      <i data-lucide="chevron-right" class="w-3 h-3"></i>
      <span class="text-white/80 truncate"><?= htmlspecialchars($article['titre']) ?></span>
    </div>

    <?php if (!empty($article['categorie'])): ?>
      <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold mb-4 border border-emerald-500/20">
        <?= htmlspecialchars($article['categorie']) ?>
      </span>
    <?php endif; ?>

    <h1 class="text-3xl md:text-5xl font-extrabold text-white leading-tight mb-5">
      <?= htmlspecialchars($article['titre']) ?>
    </h1>

    <div class="flex items-center gap-4 text-white/50 text-sm">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-blue-500 flex items-center justify-center">
          <span class="text-white font-bold text-xs">F</span>
        </div>
        <span class="font-medium text-white/70">FOCUS Audit & Conseil</span>
      </div>
      <span>·</span>
      <span><?= htmlspecialchars($article['date']) ?></span>
    </div>
  </div>
</section>

<!-- Contenu -->
<div class="max-w-4xl mx-auto px-4 md:px-8 py-14">
  <div class="grid lg:grid-cols-4 gap-12">

    <!-- Article -->
    <article class="lg:col-span-3">
      <div class="article-content">
        <?= $article['contenu'] ?>
      </div>

      <!-- Navigation prev/next -->
      <div class="mt-16 pt-8 border-t border-slate-100 grid sm:grid-cols-2 gap-4">
        <?php if ($prev): ?>
          <a href="article.php?slug=<?= urlencode($prev['slug']) ?>"
             class="group flex items-center gap-3 p-4 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-200 transition-all">
            <i data-lucide="arrow-left" class="w-5 h-5 text-slate-400 group-hover:text-emerald-500 flex-shrink-0"></i>
            <div class="min-w-0">
              <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Précédent</p>
              <p class="text-sm font-bold text-slate-700 truncate group-hover:text-emerald-700"><?= htmlspecialchars($prev['titre']) ?></p>
            </div>
          </a>
        <?php else: ?><div></div><?php endif; ?>

        <?php if ($next): ?>
          <a href="article.php?slug=<?= urlencode($next['slug']) ?>"
             class="group flex items-center justify-end gap-3 p-4 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-200 transition-all text-right">
            <div class="min-w-0">
              <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Suivant</p>
              <p class="text-sm font-bold text-slate-700 truncate group-hover:text-emerald-700"><?= htmlspecialchars($next['titre']) ?></p>
            </div>
            <i data-lucide="arrow-right" class="w-5 h-5 text-slate-400 group-hover:text-emerald-500 flex-shrink-0"></i>
          </a>
        <?php endif; ?>
      </div>
    </article>

    <!-- Sidebar -->
    <aside class="hidden lg:block">
      <div class="sticky top-28 space-y-6">

        <!-- À propos -->
        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-blue-500 flex items-center justify-center mb-4">
            <span class="text-white font-extrabold">F</span>
          </div>
          <h4 class="font-bold text-slate-900 mb-2">FOCUS Audit & Conseil</h4>
          <p class="text-slate-500 text-sm leading-relaxed">Cabinet d'expertise comptable et d'audit à Paris. Société à mission.</p>
          <a href="index.html#contact" class="mt-4 block w-full py-2.5 bg-gradient-to-r from-emerald-500 to-blue-500 text-white rounded-xl font-bold text-sm text-center hover:shadow-lg transition-all">
            Nous contacter
          </a>
        </div>

        <!-- Partager -->
        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
          <h4 class="font-bold text-slate-900 mb-4 text-sm">Partager</h4>
          <div class="flex gap-2">
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode('https://focus-ac.fr/article.php?slug=' . $slug) ?>"
               target="_blank"
               class="flex-1 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold text-center hover:bg-blue-700 transition-all">
              LinkedIn
            </a>
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode('https://focus-ac.fr/article.php?slug=' . $slug) ?>&text=<?= urlencode($article['titre']) ?>"
               target="_blank"
               class="flex-1 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold text-center hover:bg-black transition-all">
              X / Twitter
            </a>
          </div>
        </div>

        <a href="blog.php" class="flex items-center gap-2 text-sm font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
          <i data-lucide="arrow-left" class="w-4 h-4"></i> Tous les articles
        </a>
      </div>
    </aside>
  </div>
</div>

<!-- CTA -->
<section class="py-16 bg-gradient-to-r from-emerald-600 to-blue-600">
  <div class="max-w-4xl mx-auto px-4 text-center">
    <h2 class="text-2xl md:text-3xl font-extrabold text-white mb-3">Une question sur cet article ?</h2>
    <p class="text-white/80 mb-8 text-sm">Nos experts sont disponibles pour vous accompagner.</p>
    <a href="index.html#contact" class="inline-block px-8 py-4 bg-white text-slate-900 rounded-full font-bold text-sm hover:shadow-xl hover:scale-105 transition-all">
      Consultation gratuite
    </a>
  </div>
</section>

<!-- Footer -->
<footer class="bg-slate-900 text-white py-10">
  <div class="max-w-7xl mx-auto px-4 text-center">
    <p class="text-slate-500 text-sm">© 2026 FOCUS Audit & Conseil. Tous droits réservés.</p>
  </div>
</footer>

<script>
lucide.createIcons();
window.addEventListener('scroll',()=>{
  const h=document.documentElement.scrollHeight-document.documentElement.clientHeight;
  document.getElementById('progress-bar').style.width=(document.documentElement.scrollTop/h*100)+'%';
  const nb=document.getElementById('navbar');
  window.scrollY>20?nb.classList.add('navbar-blur','py-3'):nb.classList.remove('py-3');
});
document.getElementById('mobile-toggle').addEventListener('click',()=>{
  document.getElementById('mobile-menu')?.classList.toggle('active');
});
</script>
</body>
</html>