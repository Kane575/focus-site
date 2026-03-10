<?php
require_once 'admin/config.php';
$articles = array_filter(get_articles(), fn($a) => $a['publie'] ?? false);
$articles = array_reverse(array_values($articles));

$cat_filter = $_GET['cat'] ?? '';
if ($cat_filter) {
    $articles = array_filter($articles, fn($a) => ($a['categorie'] ?? '') === $cat_filter);
}

$categories = array_unique(array_filter(array_column(get_articles(), 'categorie')));
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog — FOCUS Audit & Conseil</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
  *{margin:0;padding:0;box-sizing:border-box;}
  body{font-family:'Plus Jakarta Sans',sans-serif;overflow-x:hidden;}
  .scroll-progress{position:fixed;top:0;left:0;width:0%;height:3px;background:linear-gradient(90deg,#10b981,#3b82f6);z-index:9999;}
  .navbar-blur{background:rgba(15,23,42,0.85);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,0.08);}
  .gradient-text{background:linear-gradient(135deg,#10b981,#3b82f6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
  .card-hover{transition:all 0.4s cubic-bezier(0.22,1,0.36,1);}
  .card-hover:hover{transform:translateY(-6px);box-shadow:0 20px 40px -10px rgba(0,0,0,0.2);}
  .mobile-menu{max-height:0;overflow:hidden;transition:max-height 0.4s ease;}
  .mobile-menu.active{max-height:500px;}
  .reveal{opacity:0;transform:translateY(30px);transition:all 0.7s cubic-bezier(0.22,1,0.36,1);}
  .reveal.active{opacity:1;transform:translateY(0);}
  .btn-shine{position:relative;overflow:hidden;}
  .btn-shine::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15),transparent);transition:left 0.5s;}
  .btn-shine:hover::before{left:100%;}
</style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">

<div class="scroll-progress" id="progress-bar"></div>

<!-- Navbar -->
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 py-5">
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
      <a href="index.html#expertise"class="text-xs font-bold text-white/80 hover:text-white tracking-wider">EXPERTISE</a>
      <a href="index.html#services" class="text-xs font-bold text-white/80 hover:text-white tracking-wider">SERVICES</a>
      <a href="blog.php"            class="text-xs font-bold text-white tracking-wider border-b-2 border-emerald-400 pb-0.5">BLOG</a>
      <a href="index.html#contact"  class="text-xs font-bold text-white/80 hover:text-white tracking-wider">CONTACT</a>
      <a href="index.html#contact"  class="px-6 py-2.5 bg-white text-slate-900 rounded-full text-xs font-bold hover:bg-emerald-50 transition-all btn-shine">CONSULTATION</a>
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

<!-- Hero -->
<section class="relative pt-32 pb-16 bg-gradient-to-br from-slate-900 to-slate-800">
  <div class="absolute inset-0 overflow-hidden">
    <div class="absolute top-1/3 left-1/4 w-72 h-72 bg-emerald-500/8 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-blue-500/8 rounded-full blur-3xl"></div>
  </div>
  <div class="max-w-7xl mx-auto px-4 md:px-8 relative z-10 text-center">
    <div class="flex items-center justify-center gap-2 text-sm text-white/50 mb-6">
      <a href="index.html" class="hover:text-white transition-colors">Accueil</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <span class="text-white">Blog</span>
    </div>
    <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4">
      Nos <span class="gradient-text">Articles</span>
    </h1>
    <p class="text-white/70 text-lg max-w-xl mx-auto">
      Actualités, conseils et décryptages de l'expertise comptable et de l'audit.
    </p>
  </div>
</section>

<!-- Filtres catégories -->
<?php if (!empty($categories)): ?>
<div class="bg-white border-b border-slate-100 sticky top-[72px] z-30">
  <div class="max-w-7xl mx-auto px-4 md:px-8 py-4 flex items-center gap-3 overflow-x-auto scrollbar-hide">
    <a href="blog.php" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-all <?= !$cat_filter ? 'bg-gradient-to-r from-emerald-500 to-blue-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
      Tous
    </a>
    <?php foreach ($categories as $cat): ?>
      <a href="?cat=<?= urlencode($cat) ?>" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-all <?= $cat_filter === $cat ? 'bg-gradient-to-r from-emerald-500 to-blue-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
        <?= htmlspecialchars($cat) ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Articles -->
<section class="py-16 md:py-20">
  <div class="max-w-7xl mx-auto px-4 md:px-8">

    <?php if (empty($articles)): ?>
      <div class="text-center py-24">
        <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-6">
          <i data-lucide="file-text" class="w-10 h-10 text-slate-300"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-700 mb-2">Aucun article pour l'instant</h3>
        <p class="text-slate-400">Revenez bientôt, nous publions régulièrement du contenu.</p>
      </div>
    <?php else: ?>

      <!-- Article à la une (premier) -->
      <?php $articles = array_values($articles); $featured = $articles[0] ?? null; ?>
<?php if (!$featured) { echo '<p class="text-center text-slate-400 py-10">Aucun article publié.</p>'; } else { ?>
      <div class="mb-12 reveal">
        <a href="article.php?slug=<?= urlencode($featured['slug']) ?>" class="group grid md:grid-cols-2 gap-0 bg-white rounded-3xl overflow-hidden shadow-xl border border-slate-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-1">
          <div class="h-64 md:h-auto bg-slate-100 overflow-hidden">
            <?php if (!empty($featured['image'])): ?>
              <img src="<?= htmlspecialchars($featured['image']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            <?php else: ?>
              <div class="w-full h-full bg-gradient-to-br from-emerald-500 to-blue-500 flex items-center justify-center">
                <i data-lucide="file-text" class="w-16 h-16 text-white/30"></i>
              </div>
            <?php endif; ?>
          </div>
          <div class="p-8 md:p-12 flex flex-col justify-center">
            <div class="flex items-center gap-3 mb-4">
              <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold">À la une</span>
              <?php if (!empty($featured['categorie'])): ?>
                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-bold"><?= htmlspecialchars($featured['categorie']) ?></span>
              <?php endif; ?>
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-4 group-hover:text-emerald-600 transition-colors leading-tight">
              <?= htmlspecialchars($featured['titre']) ?>
            </h2>
            <p class="text-slate-500 leading-relaxed mb-6"><?= htmlspecialchars($featured['extrait'] ?? '') ?></p>
            <div class="flex items-center justify-between">
              <span class="text-xs text-slate-400 font-medium"><?= htmlspecialchars($featured['date']) ?></span>
              <span class="flex items-center gap-1 text-emerald-600 font-bold text-sm group-hover:gap-2 transition-all">
                Lire <i data-lucide="arrow-right" class="w-4 h-4"></i>
              </span>
            </div>
          </div>
        </a>
      </div>
      <?php } ?>

      <!-- Grille des autres articles -->
      <?php if (count($articles) > 1): ?>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach (array_slice($articles, 1) as $a): ?>
        <div class="reveal">
          <a href="article.php?slug=<?= urlencode($a['slug']) ?>" class="group block bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 card-hover h-full">
            <div class="h-48 bg-slate-100 overflow-hidden">
              <?php if (!empty($a['image'])): ?>
                <img src="<?= htmlspecialchars($a['image']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
              <?php else: ?>
                <div class="w-full h-full bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center">
                  <i data-lucide="file-text" class="w-10 h-10 text-slate-400"></i>
                </div>
              <?php endif; ?>
            </div>
            <div class="p-6">
              <?php if (!empty($a['categorie'])): ?>
                <span class="inline-block px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold mb-3"><?= htmlspecialchars($a['categorie']) ?></span>
              <?php endif; ?>
              <h3 class="font-extrabold text-slate-900 text-lg leading-tight mb-3 group-hover:text-emerald-600 transition-colors">
                <?= htmlspecialchars($a['titre']) ?>
              </h3>
              <p class="text-slate-500 text-sm leading-relaxed line-clamp-2 mb-4"><?= htmlspecialchars($a['extrait'] ?? '') ?></p>
              <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <span class="text-xs text-slate-400"><?= htmlspecialchars($a['date']) ?></span>
                <span class="text-emerald-600 font-bold text-xs flex items-center gap-1 group-hover:gap-2 transition-all">
                  Lire <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </span>
              </div>
            </div>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<!-- CTA -->
<section class="py-16 bg-gradient-to-r from-emerald-600 to-blue-600">
  <div class="max-w-4xl mx-auto px-4 text-center">
    <h2 class="text-3xl font-extrabold text-white mb-4">Besoin d'un expert-comptable ?</h2>
    <p class="text-white/80 mb-8">Prenez rendez-vous gratuitement avec notre équipe.</p>
    <a href="index.html#contact" class="inline-block px-8 py-4 bg-white text-slate-900 rounded-full font-bold text-sm hover:shadow-xl hover:scale-105 transition-all">
      Consultation gratuite
    </a>
  </div>
</section>

<!-- Footer -->
<footer class="bg-slate-900 text-white py-10">
  <div class="max-w-7xl mx-auto px-4 md:px-8 text-center">
    <p class="text-slate-500 text-sm">© 2026 FOCUS Audit & Conseil. Tous droits réservés.</p>
    <div class="flex justify-center gap-6 mt-4 text-xs text-slate-600">
      <a href="index.html" class="hover:text-slate-400">Accueil</a>
      <a href="simulateur.html" class="hover:text-slate-400">Simulateurs</a>
      <a href="index.html#contact" class="hover:text-slate-400">Contact</a>
    </div>
  </div>
</footer>

<script>
lucide.createIcons();
window.addEventListener('scroll',()=>{
  const h=document.documentElement.scrollHeight-document.documentElement.clientHeight;
  document.getElementById('progress-bar').style.width=(document.documentElement.scrollTop/h*100)+'%';
  const nb=document.getElementById('navbar');
  window.scrollY>80?nb.classList.add('navbar-blur','py-3'):nb.classList.remove('navbar-blur','py-3');
});
document.getElementById('mobile-toggle').addEventListener('click',()=>{
  document.getElementById('mobile-menu').classList.toggle('active');
});
new IntersectionObserver((entries)=>{
  entries.forEach(e=>{if(e.isIntersecting)e.target.classList.add('active');});
},{threshold:0.1}).observe(...document.querySelectorAll('.reveal'));
document.querySelectorAll('.reveal').forEach(el=>{
  new IntersectionObserver((e)=>{if(e[0].isIntersecting)el.classList.add('active');},{threshold:0.1}).observe(el);
});
</script>
</body>
</html>