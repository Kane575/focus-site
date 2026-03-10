<?php
require_once 'config.php';
require_login();

$slug = $_GET['slug'] ?? '';
if ($slug) {
    $articles = get_articles();
    $articles = array_filter($articles, fn($a) => $a['slug'] !== $slug);
    save_articles($articles);
}
header('Location: dashboard.php');
exit;