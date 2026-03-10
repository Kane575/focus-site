<?php
require_once 'config.php';
require_login();

$slug    = $_GET['slug'] ?? '';
$article = $slug ? get_article_by_slug($slug) : null;
$is_edit = $article !== null;
$msg     = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre    = trim($_POST['titre'] ?? '');
    $contenu  = trim($_POST['contenu'] ?? '');
    $extrait  = trim($_POST['extrait'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $publie   = isset($_POST['publie']) ? true : false;

    if (empty($titre) || empty($contenu)) {
        $error = 'Le titre et le contenu sont obligatoires.';
    } else {
        // Image upload
        $image = $article['image'] ?? '';
        if (!empty($_FILES['image']['tmp_name'])) {
            $uploaded = upload_image($_FILES['image']);
            if ($uploaded) $image = $uploaded;
        }

        $articles = get_articles();

        if ($is_edit) {
            // Modifier l'article existant
            foreach ($articles as &$a) {
                if ($a['slug'] === $slug) {
                    $a['titre']     = $titre;
                    $a['contenu']   = $contenu;
                    $a['extrait']   = $extrait ?: mb_substr(strip_tags($contenu), 0, 160) . '…';
                    $a['categorie'] = $categorie;
                    $a['image']     = $image;
                    $a['publie']    = $publie;
                    $a['modifie']   = date('d/m/Y');
                    break;
                }
            }
            save_articles($articles);
            $msg = 'Article modifié avec succès !';
            $article = get_article_by_slug($slug);
        } else {
            // Nouvel article
            $new_slug = slugify($titre);
            // Éviter les doublons de slug
            $existing_slugs = array_column($articles, 'slug');
            $base = $new_slug;
            $i = 2;
            while (in_array($new_slug, $existing_slugs)) {
                $new_slug = $base . '-' . $i++;
            }

            $articles[] = [
                'slug'      => $new_slug,
                'titre'     => $titre,
                'contenu'   => $contenu,
                'extrait'   => $extrait ?: mb_substr(strip_tags($contenu), 0, 160) . '…',
                'categorie' => $categorie,
                'image'     => $image,
                'publie'    => $publie,
                'date'      => date('d/m/Y'),
                'modifie'   => date('d/m/Y'),
            ];
            save_articles($articles);
            header('Location: dashboard.php?created=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $is_edit ? 'Modifier' : 'Nouvel article' ?> — Admin FOCUS</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
  body{font-family:'Plus Jakarta Sans',sans-serif;}
  .gradient-text{background:linear-gradient(135deg,#10b981,#3b82f6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
  /* Éditeur simple */
  #contenu{min-height:320px;outline:none;white-space:pre-wrap;}
  .toolbar-btn{padding:6px 10px;border-radius:8px;font-size:12px;font-weight:600;color:#94a3b8;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);cursor:pointer;transition:all 0.2s;}
  .toolbar-btn:hover{background:rgba(16,185,129,0.15);color:#10b981;border-color:rgba(16,185,129,0.3);}
  /* Sidebar mobile */
  #sidebar{transform:translateX(-100%);transition:transform 0.3s ease;position:fixed;left:0;top:0;bottom:0;width:280px;background:#0f172a;border-right:1px solid rgba(255,255,255,0.05);z-index:50;}
  #sidebar.open{transform:translateX(0);}
  @media (min-width: 1024px) {
    #sidebar{transform:translateX(0);width:280px;}
    #main{padding-left:280px;}
  }
  #overlay{opacity:0;pointer-events:none;transition:opacity 0.3s ease;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:40;}
  #overlay.open{opacity:1;pointer-events:auto;}
</style>
</head>
<body class="bg-slate-950 text-white min-h-screen">

<!-- Overlay mobile -->
<div id="overlay" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar">
  <div class="p-5 border-b border-white/5 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-blue-500 flex items-center justify-center">
        <i data-lucide="layout-dashboard" class="w-4 h-4 text-white"></i>
      </div>
      <div>
        <p class="font-extrabold text-white text-sm">FOCUS Admin</p>
        <p class="text-[10px] text-slate-500">Gestion du contenu</p>
      </div>
    </div>
    <button onclick="closeSidebar()" class="lg:hidden text-slate-400 hover:text-white p-1">
      <i data-lucide="x" class="w-5 h-5"></i>
    </button>
  </div>

  <nav class="flex-1 p-4 space-y-1">
    <a href="dashboard.php" onclick="closeSidebar()" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 font-semibold text-sm transition-all">
      <i data-lucide="file-text" class="w-4 h-4"></i> Articles
    </a>
    <a href="edit.php" onclick="closeSidebar()" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/10 text-emerald-400 font-semibold text-sm">
      <i data-lucide="plus-circle" class="w-4 h-4"></i> Nouvel article
    </a>
    <a href="../blog.php" target="_blank" onclick="closeSidebar()" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 font-semibold text-sm transition-all">
      <i data-lucide="external-link" class="w-4 h-4"></i> Voir le blog
    </a>
    <a href="../index.html" target="_blank" onclick="closeSidebar()" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 font-semibold text-sm transition-all">
      <i data-lucide="home" class="w-4 h-4"></i> Site principal
    </a>
  </nav>

  <div class="p-4 border-t border-white/5">
    <a href="logout.php" onclick="closeSidebar()" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 font-semibold text-sm transition-all">
      <i data-lucide="log-out" class="w-4 h-4"></i> Déconnexion
    </a>
  </div>
</aside>

<!-- Main -->
<div id="main">

  <!-- Top bar mobile -->
  <div class="sticky top-0 z-20 bg-slate-950/90 backdrop-blur border-b border-white/5 flex items-center justify-between px-4 py-3 lg:hidden">
    <button onclick="openSidebar()" class="text-slate-400 hover:text-white p-1">
      <i data-lucide="menu" class="w-6 h-6"></i>
    </button>
    <span class="font-bold text-sm gradient-text"><?= $is_edit ? 'Modifier article' : 'Nouvel article' ?></span>
    <div class="w-8"></div>
  </div>

  <div class="p-4 lg:p-8">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6 lg:mb-8">
      <a href="dashboard.php" class="w-9 h-9 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-white transition-all flex-shrink-0">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
      </a>
      <div>
        <h1 class="text-xl lg:text-2xl font-extrabold"><?= $is_edit ? 'Modifier l\'article' : 'Nouvel article' ?></h1>
        <p class="text-slate-400 text-xs lg:text-sm">Remplissez les champs ci-dessous</p>
      </div>
    </div>

    <?php if ($msg): ?>
      <div class="mb-6 px-5 py-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-400 text-sm flex items-center gap-2">
        <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i> <?= $msg ?>
      </div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="mb-6 px-5 py-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i> <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="flex flex-col lg:grid lg:grid-cols-3 gap-6">

      <!-- Colonne gauche (2/3) - S'affiche en premier sur mobile -->
      <div class="lg:col-span-2 space-y-6 order-1">

        <!-- Titre -->
        <div class="bg-slate-900 border border-white/5 rounded-xl lg:rounded-2xl p-4 lg:p-6">
          <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Titre *</label>
          <input type="text" name="titre" required
            value="<?= htmlspecialchars($article['titre'] ?? '') ?>"
            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-base lg:text-lg font-bold outline-none focus:border-emerald-500 transition-all placeholder:text-slate-600"
            placeholder="Titre de l'article...">
        </div>

        <!-- Éditeur de contenu -->
        <div class="bg-slate-900 border border-white/5 rounded-xl lg:rounded-2xl p-4 lg:p-6">
          <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Contenu *</label>

          <!-- Barre d'outils responsive -->
          <div class="flex flex-wrap gap-1.5 lg:gap-2 mb-3 pb-3 border-b border-white/5">
            <button type="button" class="toolbar-btn px-2 lg:px-3" onclick="fmt('bold')"><b>G</b></button>
            <button type="button" class="toolbar-btn px-2 lg:px-3" onclick="fmt('italic')"><i>I</i></button>
            <button type="button" class="toolbar-btn px-2 lg:px-3" onclick="fmt('underline')"><u>S</u></button>
            <button type="button" class="toolbar-btn px-2 lg:px-3" onclick="insertH2()">H2</button>
            <button type="button" class="toolbar-btn px-2 lg:px-3" onclick="insertH3()">H3</button>
            <button type="button" class="toolbar-btn px-2 lg:px-3" onclick="fmt('insertUnorderedList')">•</button>
            <button type="button" class="toolbar-btn px-2 lg:px-3" onclick="fmt('insertOrderedList')">1.</button>
            <button type="button" class="toolbar-btn px-2 lg:px-3" onclick="insertLink()">🔗</button>
          </div>

          <div id="contenu" contenteditable="true"
            class="w-full min-h-[250px] lg:min-h-80 px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-slate-300 text-sm leading-relaxed focus:border-emerald-500 transition-all overflow-y-auto"
            style="outline:none;"
          ><?= $is_edit ? $article['contenu'] : '' ?></div>
          <input type="hidden" name="contenu" id="contenu_hidden">
        </div>

        <!-- Extrait -->
        <div class="bg-slate-900 border border-white/5 rounded-xl lg:rounded-2xl p-4 lg:p-6">
          <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Extrait <span class="text-slate-600 font-normal normal-case">(optionnel)</span></label>
          <textarea name="extrait" rows="3"
            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-slate-300 text-sm outline-none focus:border-emerald-500 transition-all resize-none placeholder:text-slate-600"
            placeholder="Courte description affichée dans la liste des articles..."><?= htmlspecialchars($article['extrait'] ?? '') ?></textarea>
        </div>
      </div>

      <!-- Colonne droite (1/3) - S'affiche en second sur mobile -->
      <div class="space-y-6 order-2">

        <!-- Publier -->
        <div class="bg-slate-900 border border-white/5 rounded-xl lg:rounded-2xl p-4 lg:p-6">
          <h3 class="font-bold text-white mb-4 text-sm">Publication</h3>
          <label class="flex items-center gap-3 cursor-pointer">
            <div class="relative">
              <input type="checkbox" name="publie" id="publie" class="sr-only"
                <?= ($article['publie'] ?? false) ? 'checked' : '' ?>>
              <div id="toggle-bg" class="w-11 h-6 rounded-full transition-colors <?= ($article['publie'] ?? false) ? 'bg-emerald-500' : 'bg-slate-700' ?>"></div>
              <div id="toggle-dot" class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform <?= ($article['publie'] ?? false) ? 'translate-x-5' : '' ?>"></div>
            </div>
            <span class="text-sm font-medium text-slate-300" id="publie-label">
              <?= ($article['publie'] ?? false) ? 'Publié' : 'Brouillon' ?>
            </span>
          </label>
          <p class="text-xs text-slate-500 mt-3">Les brouillons ne sont pas visibles sur le blog.</p>

          <div class="mt-6 pt-4 border-t border-white/5 flex flex-col gap-3">
            <button type="submit"
              class="w-full py-3 bg-gradient-to-r from-emerald-500 to-blue-500 text-white rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-emerald-500/20 transition-all">
              <?= $is_edit ? '💾 Enregistrer' : '🚀 Publier' ?>
            </button>
            <a href="dashboard.php" class="w-full py-3 bg-white/5 text-slate-400 rounded-xl font-bold text-sm text-center hover:bg-white/10 transition-all block">
              Annuler
            </a>
          </div>
        </div>

        <!-- Catégorie -->
        <div class="bg-slate-900 border border-white/5 rounded-xl lg:rounded-2xl p-4 lg:p-6">
          <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Catégorie</label>
          <select name="categorie"
            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-sm outline-none focus:border-emerald-500 transition-all appearance-none">
            <option value="" class="bg-slate-900">Aucune</option>
            <?php foreach (['Actualités','Comptabilité','Audit','Fiscal','Juridique','RSE','Conseils','Création entreprise'] as $cat): ?>
              <option value="<?= $cat ?>" class="bg-slate-900" <?= ($article['categorie'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Image -->
        <div class="bg-slate-900 border border-white/5 rounded-xl lg:rounded-2xl p-4 lg:p-6">
          <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Image à la une</label>

          <?php if (!empty($article['image'])): ?>
            <div class="mb-3 rounded-xl overflow-hidden">
              <img src="../<?= htmlspecialchars($article['image']) ?>" class="w-full h-32 object-cover">
            </div>
          <?php endif; ?>

          <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-white/10 rounded-xl cursor-pointer hover:border-emerald-500/50 transition-all group">
            <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-500 group-hover:text-emerald-400 transition-colors mb-2"></i>
            <span class="text-xs text-slate-500 group-hover:text-slate-300 transition-colors text-center px-2">Cliquer pour uploader</span>
            <span class="text-[10px] text-slate-600 mt-1">JPG, PNG, WEBP</span>
            <input type="file" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
          </label>
          <div id="img-preview" class="mt-3 hidden">
            <img id="img-preview-src" class="w-full h-32 object-cover rounded-xl">
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
lucide.createIcons();

// Sidebar functions
function openSidebar() { 
  document.getElementById('sidebar').classList.add('open'); 
  document.getElementById('overlay').classList.add('open'); 
}
function closeSidebar() { 
  document.getElementById('sidebar').classList.remove('open'); 
  document.getElementById('overlay').classList.remove('open'); 
}

// Toggle publie
document.getElementById('publie').addEventListener('change', function() {
  document.getElementById('toggle-bg').className = 'w-11 h-6 rounded-full transition-colors ' + (this.checked ? 'bg-emerald-500' : 'bg-slate-700');
  document.getElementById('toggle-dot').className = 'absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform ' + (this.checked ? 'translate-x-5' : '');
  document.getElementById('publie-label').textContent = this.checked ? 'Publié' : 'Brouillon';
});

// Éditeur
function fmt(cmd) { document.execCommand(cmd, false, null); document.getElementById('contenu').focus(); }
function insertH2() { document.execCommand('formatBlock', false, 'h2'); }
function insertH3() { document.execCommand('formatBlock', false, 'h3'); }
function insertLink() {
  const url = prompt('URL du lien :');
  if (url) document.execCommand('createLink', false, url);
}

// Sync éditeur → hidden input avant soumission
document.querySelector('form').addEventListener('submit', function() {
  document.getElementById('contenu_hidden').value = document.getElementById('contenu').innerHTML;
});

// Preview image
function previewImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('img-preview').classList.remove('hidden');
      document.getElementById('img-preview-src').src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// Fermer la sidebar en cliquant sur un lien
document.querySelectorAll('#sidebar a').forEach(link => {
  link.addEventListener('click', closeSidebar);
});
</script>
</body>
</html>