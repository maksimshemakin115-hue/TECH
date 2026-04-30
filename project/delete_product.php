<?php
require_once 'config.php';
requireAdmin();

$product_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if ($product) {
    if ($product['image_path'] && file_exists($product['image_path'])) {
        unlink($product['image_path']);
    }
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
}

header('Location: index.php');
exit;