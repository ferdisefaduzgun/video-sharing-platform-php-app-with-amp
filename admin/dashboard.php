<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');  // Giriş yapmamışsa admin giriş sayfasına yönlendir
    exit;
}

require_once '../db.php';

// Dizinlerin varlığını kontrol et ve oluştur
$thumbnail_dir = "../uploads/thumbnails/";
$video_dir = "../uploads/videos/";

if (!file_exists($thumbnail_dir)) {
    mkdir($thumbnail_dir, 0777, true);
}

if (!file_exists($video_dir)) {
    mkdir($video_dir, 0777, true);
}

// Kategorileri veritabanından al
$stmt_categories = $pdo->query("SELECT * FROM categories");
$categories = $stmt_categories->fetchAll();

// Etiketleri veritabanından al
$stmt_tags = $pdo->query("SELECT * FROM tags");
$tags = $stmt_tags->fetchAll();

// Form verilerini gönderme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $video_url = $_POST['video_url'];
    $hakkinda = $_POST['hakkinda']; // Video açıklaması
    $thumbnail_url = $_POST['thumbnail_url'];
    $duration = $_POST['duration']; // Video süresi (saniye olarak)

    $thumbnail = $_FILES['thumbnail']['name'] ?? '';
    $video_file = $_FILES['video_file']['name'] ?? '';

    // Geçici dosya yolları
    $thumbnail_tmp = $_FILES['thumbnail']['tmp_name'] ?? '';
    $video_tmp = $_FILES['video_file']['tmp_name'] ?? '';

    // Thumbnail yükleme işlemi
    $thumbnail_path = '';
    if (!empty($thumbnail_url)) {
        $thumbnail_path = $thumbnail_url;
    } elseif (!empty($thumbnail_tmp) && is_uploaded_file($thumbnail_tmp)) {
        $thumbnail_target = $thumbnail_dir . basename($thumbnail);
        if (move_uploaded_file($thumbnail_tmp, $thumbnail_target)) {
            $thumbnail_path = 'uploads/thumbnails/' . $thumbnail;
        } else {
            echo "Thumbnail yüklenemedi.";
            exit;
        }
    } else {
        $thumbnail_path = 'images/default-thumbnail.jpg';
    }

    // Video dosyası yükleme
    $video_path = '';
    if (!empty($video_tmp) && is_uploaded_file($video_tmp)) {
        $video_target = $video_dir . basename($video_file);
        if (move_uploaded_file($video_tmp, $video_target)) {
            $video_path = 'uploads/videos/' . $video_file;
        } else {
            echo "Video dosyası yüklenemedi.";
            exit;
        }
    } elseif (!empty($video_url)) {
        $video_path = $video_url;
    } else {
        echo "Video URL veya dosyası sağlanmalı.";
        exit;
    }

    // Veritabanına video ekleme (duration alanı artık TEXT olarak kaydedilecek)
    $stmt = $pdo->prepare("INSERT INTO videos (title, video_url, thumbnail, hakkinda, video_file, thumbnail_url, duration) 
    VALUES (:title, :video_url, :thumbnail, :hakkinda, :video_file, :thumbnail_url, :duration)");
    $stmt->execute([
        'title' => $title,
        'video_url' => $video_path,
        'thumbnail' => $thumbnail_path,
        'hakkinda' => $hakkinda,
        'video_file' => $video_path,
        'thumbnail_url' => $thumbnail_url,
        'duration' => $duration
    ]);

    // Video ID'sini al
    $video_id = $pdo->lastInsertId();

    // Kategorileri video ile ilişkilendir
    if (!empty($_POST['categories'])) {
        foreach ($_POST['categories'] as $category_id) {
            $stmt = $pdo->prepare("INSERT INTO video_categories (video_id, category_id) VALUES (:video_id, :category_id)");
            $stmt->execute([
                'video_id' => $video_id,
                'category_id' => $category_id
            ]);
        }
    }

    // Etiketleri video ile ilişkilendir
    if (!empty($_POST['tags'])) {
        foreach ($_POST['tags'] as $tag_id) {
            $stmt = $pdo->prepare("INSERT INTO video_tags (video_id, tag_id) VALUES (:video_id, :tag_id)");
            $stmt->execute([
                'video_id' => $video_id,
                'tag_id' => $tag_id
            ]);
        }
    }

    // Başarıyla gönderildikten sonra yönlendirme
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Video Ekleme</title>
    <link rel="stylesheet" href="admin.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

    <div class="container">
        <h1 class="page-title">Video Ekleme</h1>

        <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="form-container">
            <div class="form-group">
                <label for="title">Video Başlığı:</label>
                <input type="text" name="title" id="title" required>
            </div>

            <div class="form-group">
                <label for="video_url">Video URL:</label>
                <input type="text" name="video_url" id="video_url">
            </div>

            <div class="form-group">
                <label for="video_file">Video dosyası:</label>
                <input type="file" name="video_file" id="video_file" accept="video/*">
            </div>

            <div class="form-group">
                <label for="thumbnail">Video Thumbnail (Resim):</label>
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*">
            </div>

            <div class="form-group">
                <label for="thumbnail_url">Video Thumbnail (URL):</label>
                <input type="text" name="thumbnail_url" id="thumbnail_url">
            </div>

            <div class="form-group">
                <label for="hakkinda">Video Hakkında kısmı:</label>
                <textarea name="hakkinda" id="hakkinda" required placeholder="Videonun hakkında yazınız..."></textarea>
            </div>

<!-- Kategori Seçimi -->
<div class="form-group">
    <label for="categories">Kategoriler:</label>
    <div id="categories">
        <?php foreach ($categories as $category): ?>
            <label>
                <input type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>" /> 
                <?php echo htmlspecialchars($category['name']); ?>
            </label><br />
        <?php endforeach; ?>
    </div>
</div>

<!-- Etiket Seçimi -->
<div class="form-group">
    <label for="tags">Etiketler:</label>
    <div id="tags">
        <?php foreach ($tags as $tag): ?>
            <label>
                <input type="checkbox" name="tags[]" value="<?php echo $tag['id']; ?>" /> 
                <?php echo htmlspecialchars($tag['name']); ?>
            </label><br />
        <?php endforeach; ?>
    </div>
</div>

            <div class="form-group">
                <label for="duration">Video Süresi (saniye):</label>
                <input type="text" name="duration" id="duration" required>
            </div>

            <button type="submit" class="submit-btn">Video Ekle</button>
        </form>

        <h2 class="section-title">Mevcut Videolar</h2>

        <table class="video-table">
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>Etiketler</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM videos");
                while ($row = $stmt->fetch()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";

                    $stmt_video_tags = $pdo->prepare("
                        SELECT tags.name 
                        FROM tags
                        INNER JOIN video_tags ON tags.id = video_tags.tag_id
                        WHERE video_tags.video_id = :video_id
                    ");
                    $stmt_video_tags->execute(['video_id' => $row['id']]);
                    $video_tags = $stmt_video_tags->fetchAll(PDO::FETCH_COLUMN);

                    echo "<td>";
                    if (!empty($video_tags)) {
                        foreach ($video_tags as $tag) {
                            echo "<span class='tag'>" . htmlspecialchars($tag) . "</span> ";
                        }
                    }
                    echo "</td>";

                    echo "<td>
                            <a href='delete_video.php?id=" . $row['id'] . "' class='delete-btn'><i class='fas fa-trash'></i> Sil</a>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>
