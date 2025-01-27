<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');  // Giriş yapmamışsa admin giriş sayfasına yönlendir
    exit;
}

require_once '../db.php';  // Veritabanı bağlantısını dahil et

// Silinecek video ID'sini URL'den alıyoruz
if (isset($_GET['id'])) {
    $video_id = $_GET['id'];

    // Veritabanından videoyu silelim
    $stmt = $pdo->prepare("DELETE FROM videos WHERE id = :id");
    $stmt->execute(['id' => $video_id]);

    // Silme işlemi başarılı olursa dashboard sayfasına yönlendirelim
    header('Location: dashboard.php');
    exit;
} else {
    // Eğer ID yoksa dashboard'a yönlendir
    header('Location: dashboard.php');
    exit;
}
?>
