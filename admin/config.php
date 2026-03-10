<?php
define('ADMIN_EMAIL',    'contact@focus-ac.fr');
define('ADMIN_PASSWORD', 'Focus2024!');
define('SITE_NAME',      'FOCUS Audit & Conseil');
define('DATA_FILE',      __DIR__ . '/data/articles.json');
define('UPLOAD_DIR',     __DIR__ . '/uploads/');
define('UPLOAD_URL',     'admin/uploads/');

session_start();

function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
function require_login() {
    if (!is_logged_in()) { header('Location: index.php'); exit; }
}
function get_articles() {
    if (!file_exists(DATA_FILE)) return [];
    return json_decode(file_get_contents(DATA_FILE), true) ?? [];
}
function save_articles($articles) {
    file_put_contents(DATA_FILE, json_encode(array_values($articles), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
function get_article_by_slug($slug) {
    foreach (get_articles() as $a) { if ($a['slug'] === $slug) return $a; }
    return null;
}
function slugify($text) {
    $text = strtolower(trim($text));
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-') ?: uniqid('article-');
}
function upload_image($file) {
    if (empty($file['tmp_name'])) return '';
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp','gif'])) return '';
    $name = uniqid('img_') . '.' . $ext;
    move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $name);
    return UPLOAD_URL . $name;
}