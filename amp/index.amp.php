<?php
// Veritabanı bağlantısını dahil et
require_once "../db.php";

// Arama parametresini al
$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

// Ay seçimini al
$selected_month = isset($_GET["selected_month"]) ? $_GET["selected_month"] : "";

// Kategori seçimini al
$category_id = isset($_GET["category"]) ? (int) $_GET["category"] : 0;

// Sayfa numarasını al (Varsayılan olarak 1)
$page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;
$limit = 30; // Sayfa başına gösterilecek video sayısı
$offset = ($page - 1) * $limit;

$query_months = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, MAX(created_at) AS latest_created_at 
                 FROM videos 
                 WHERE created_at IS NOT NULL 
                 GROUP BY month
                 ORDER BY latest_created_at DESC";

// SQL sorgusunu oluştur
$sql = "SELECT COUNT(*) FROM videos WHERE 1=1";

// Eğer arama varsa
if (!empty($search)) {
    $sql .= " AND (title LIKE :search OR hakkinda LIKE :search)";
}

// Eğer ay seçimi yapılmışsa
if (!empty($selected_month)) {
    $sql .= " AND DATE_FORMAT(created_at, '%Y-%m') = :selected_month";
}

// Eğer kategori seçimi yapılmışsa
if ($category_id > 0) {
    $sql .= " AND id IN (SELECT video_id FROM video_categories WHERE category_id = :category_id)";
}

$stmt = $pdo->prepare($sql);

// Parametreleri bağla
if (!empty($search)) {
    $stmt->bindValue(":search", "%" . $search . "%", PDO::PARAM_STR);
}
if (!empty($selected_month)) {
    $stmt->bindValue(":selected_month", $selected_month, PDO::PARAM_STR);
}
if ($category_id > 0) {
    $stmt->bindValue(":category_id", $category_id, PDO::PARAM_INT);
}
$stmt->execute();
$total_videos = $stmt->fetchColumn();

// Toplam sayfa sayısını hesapla
$total_pages = ceil($total_videos / $limit);

// Videoları veritabanından al
$query = "SELECT * FROM videos WHERE 1=1";

// Eğer arama varsa
if (!empty($search)) {
    $query .= " AND (title LIKE :search OR hakkinda LIKE :search)";
}

// Eğer ay seçimi yapılmışsa
if (!empty($selected_month)) {
    $query .= " AND DATE_FORMAT(created_at, '%Y-%m') = :selected_month";
}

// Eğer kategori seçimi yapılmışsa
if ($category_id > 0) {
    $query .= " AND id IN (SELECT video_id FROM video_categories WHERE category_id = :category_id)";
}

$query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);

// Parametreleri bağla
if (!empty($search)) {
    $stmt->bindValue(":search", "%" . $search . "%", PDO::PARAM_STR);
}
if (!empty($selected_month)) {
    $stmt->bindValue(":selected_month", $selected_month, PDO::PARAM_STR);
}
if ($category_id > 0) {
    $stmt->bindValue(":category_id", $category_id, PDO::PARAM_INT);
}

$stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$videos = $stmt->fetchAll();


// Şu anki oturum kimliğini al
$session_id = session_id();
$current_time = date('Y-m-d H:i:s');

// Tabloya güncel oturumu ekleyin veya var olanı güncelleyin
$query = "INSERT INTO users_online (session_id, last_activity) 
          VALUES (:session_id, :last_activity)
          ON DUPLICATE KEY UPDATE last_activity = :last_activity";
$stmt = $pdo->prepare($query);
$stmt->execute([
    'session_id' => $session_id,
    'last_activity' => $current_time,
]);

// Çevrimdışı kullanıcıları temizleyin (ör. 5 dakikadan uzun süre hareketsiz olanları)
$timeout = date('Y-m-d H:i:s', strtotime('-5 minutes'));
$delete_query = "DELETE FROM users_online WHERE last_activity < :timeout";
$stmt = $pdo->prepare($delete_query);
$stmt->execute(['timeout' => $timeout]);

// Şu an çevrimiçi olan kullanıcıları sayın
$count_query = "SELECT COUNT(*) AS online_users FROM users_online";
$stmt = $pdo->query($count_query);
$online_users = $stmt->fetch(PDO::FETCH_ASSOC)['online_users'];


function limitCharacters($text, $limit = 350) {
    // PHP'nin yerel strlen ve substr fonksiyonları ile uzunluk kontrolü yapıyoruz
    if (strlen($text) > $limit) {
        $text = substr($text, 0, $limit) . '...';  // Metni kes ve '...' ekle
    }
    return $text;
}
?>
<!doctype html>
<html amp lang="en">
  <head>
    <meta charset="utf-8">
    <title>test</title>
    <link rel="canonical" href="http://localhost/">
    <meta name="viewport" content="width=device-width">
    <link rel="icon" href="img/a.ico" type="image/x-icon">
    <script type="application/ld+json">
      {
        "@context": "http://schema.org",
        "@type": "NewsArticle",
        "headline": "Open-source framework for publishing content",
        "datePublished": "2015-10-07T12:02:41Z",
        "image": [
          "logo.jpg"
        ]
      }
    </script>
    <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
    <script async src="https://cdn.ampproject.org/v0.js"></script>

    <script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>
    <script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
    <script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>

    <style amp-custom>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
html{
    scroll-behavior: smooth;
}
body {
    font-family: Arial, sans-serif;
    background-color:#202124;
    color: #333;
    line-height: 1.6;
}
a {
    text-decoration: none;
    color: inherit;
}
/* Ana yapılar */
.all {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}
.search-bar form {
    display: flex;
    margin-bottom: 20px;
}
.search-bar input {
    padding: 8px;
    font-size: 14px;
    width: 80%;
    border: none;
    border-radius: 5px;
    margin-right: 5px;
}

.search-bar button {
    padding: 8px 15px;
    font-size: 14px;
    background-color: #f1c40f;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
/* Menü butonu */
.menu-toggle {
    font-size: 30px;
    cursor: pointer;
    color: white;
    display: block;
    padding: 10px;
    background: none;
    border: none;
    position: relative;
    z-index: 9999;
}
.menu-toggle:focus {
    outline: none;
}
/* İçerik */
.orta {
    display: flex;
    padding: 20px;
    flex: 1;
    flex-direction:column;
    gap: 20px;
}
.sol {
    flex: 3;
    margin-top:50px
}
.video-container {
    display: flex;
    flex-direction:column;
    gap: 20px;
}
.video-item {
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    color:#9aa0a6;
    border-bottom:1px solid #3c4043;
    padding-bottom:10px;
}
.video-thumbnail-container {
    position: relative;
}
.video-thumbnail {
    width: 100%;
    height: auto;
    display: block;
}
.video-logo {
    position: absolute;
    top: 0;
    left: 0;
    width: 100px;
    height: 30px; 
    background-color:rgb(187 198 203 / 80%);
    padding: 5px;
    display: flex; /* Logo resmini ortalamak için */
    align-items: center;
    justify-content: center;
    overflow: hidden; /* Taşan kısmı gizlemek için */
    box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.2); /* Hafif gölge efekti */
}
.video-logo img {
    width: 100%; /* Logo resmi için oran */
    height: auto; /* Oranı korur */
    object-fit: contain;
}
.h3 {
    font-size: 16px;
    color:#bdc1c6;
    padding-left:7.5px;
    padding-top:5px
}
.video-meta {
    align-items:left;
    font-size: 14px;
    display: flex;
    justify-content: left;
    padding-bottom:3px;
    padding-left:10px;
    gap: 10px;
}
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
    font-size: 13px;
    border-top: 4px solid #202124;
}
.pagination a {
    margin-right: 3px;
    padding: 6px 10px 3px;
    text-decoration: none;
    color: #e8eaed;
    border:1px solid #555;
    border-radius:33px;
    font-weight: bold;
}
.pagination a.active {
    background-color: #111;
    color: #FFF;
    pointer-events: none;
}
.pagination .prev,
.pagination .next {
    font-weight: bold;
}
.kategoriler{
    padding: 14px 15px;
    background: #161616;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}
.categories{
  list-style: none;
  margin: 0;
  padding: 0;
  text-align: center;
}
.categories li {
    list-style-type: none;
    display: inline-block;
    margin: 0 8px 5px;
    
}
.categories a {
    color: #9aa0a6;
    padding: 8px;
    margin-bottom: 8px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 300;
    font-size: 14px;
}
/* Footer */
footer {
    background-color: #161616;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    color: #9aa0a6;
    padding: 20px;
    text-align: left;
    font-weight:400;
    font-size:14px;
}
footer p:last-child{
    text-align:center;
}
.den{
    text-align:center;
}
.menu-btn {
    width: 60px;
    color: white;
    background-color:rgb(17, 16, 16);
    border: none;
    cursor: pointer;
    font-size: 30px;
    margin-bottom: 5px;
}
.icon-search {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid #000;
    position: relative;
}
.icon-search::before {
    content: '';
    position: absolute;
    width: 10px;
    height: 2px;
    background-color: #000;
    transform: rotate(45deg);
    top: 10px;
    left: 7px;
}
    /* Navbar düzeni */
.nav {
    padding-top: 5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    background-color: rgb(12, 13, 14);
    padding-right:15px;
    background-color: rgb(12, 13, 14);
    background: url('img/head1.gif') no-repeat center;
    background-size: 100% 100%;
    background-size: cover;
    background-repeat:no-repeat;
    position: fixed;
  z-index: 5000;
}
/* Sidebar menüsü */
amp-sidebar {
    background-color: black;
    color: white;
    width: 550px;
    padding: 16px;
}
amp-sidebar a {
    display: block;
    padding: 16px;
    color: rgb(220, 220, 220);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    
}
/* Menü butonu */
.menu-btn {
    width: 60px;
    color: white;
    background-color: rgb(17, 16, 16);
    border: none;
    cursor: pointer;
    font-size: 30px;
    margin-bottom: 5px;
    background-color: rgb(12, 13, 14);
}
/* Logo alanı */
.h2yerine a img {
    height: 50px;
    width: auto;
}
/* Arama ikonu */
.searchlo {
    display: flex;
    justify-content: center;
    align-items: center;
}

.beyaz{
    color: white;
}

.search-icon svg {
    width: 24px;
    color:white;
    height: 24px;
    fill: none;
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
}
/* Arama linki */
.search-link {
    text-decoration: none;
}
    </style>
  </head>
<body>   
<amp-sidebar id="sidebar" layout="nodisplay" side="left">
    
            
<div style="padding-left:10px; padding-right:10px; padding-bottom:10px;">
        <!-- Logo -->
        <a href="https://test">
            <amp-img src="test" alt="test" width="300" height="100" layout="intrinsic"></amp-img>
        </a>
    </div>
    
    <div class="den" style="padding-bottom:10px;">
                <h3>test </h3>
                <p>test </p>
            </div>

            <ul style="list-style-type:none; padding: 0; margin: 0; border-top: 1px solid #303134ed; color:#bdc1c6; font-size:14px; font-weight:400;">
            <li style="padding-left:5px;"><a href="index.amp.php">Anasayfa</a></li>
                <?php
                // Kategorileri listele
                $stmt = $pdo->query("SELECT * FROM categories");
                while ($category = $stmt->fetch()) {
                    $selectedClass = ($category['id'] == $category_id) ? ' class="selected"' : '';
                    echo "<li$selectedClass><a href='index.amp.php?category=" . $category['id'] . "'>" . htmlspecialchars($category['name']) . "</a></li>";
                }
                ?>
                <li><a href="index.amp.php">Anasayfa</a></li>
                <li><a href="info.amp.php">Hakkımızda</a></li>
                <li><a href="privacy.amp.php">Gizlilik Politikası</a></li>
                <li><a href="legal.amp.php">Hukuksal</a></li>
                <li><a href="copyright.amp.php">Telif Hakkı</a></li>
                <li><a href="contact.amp.php">İletişim</a></li>
            </ul>
</amp-sidebar>

<div class="all">
    
<div class="nav">
    <div>
        <!-- Menü Butonu -->
        <button class="menu-btn" on="tap:sidebar.toggle">☰</button>


    </div>
    
    <div class="h2yerine">
        <!-- Logo -->
        <a href="">
            <amp-img src="" alt="test" width="300" height="100" layout="intrinsic"></amp-img>
        </a>
    </div>

    <div class="searchlo">
                <!-- Arama Linki -->
                <a href="search.amp.php" class="search-link">
                    <div class="search-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                </a>
    </div>
</div><!--nav son-->


                <div class="orta">
                     <div class="sol">
                            
                     <div class="reklamalani" style="padding-bottom:10px;">
                            <p style="color:#bdc1c6;">
                                <a href="test" target="_blank">
                                    <amp-img 
                                        src="test" 
                                        alt="bonus" 
                                        width="390" 
                                        height="96.5" 
                                        layout="responsive">
                                    </amp-img>
                                </a>
                                
                            </p>
                    </div>

                 

                    <div class="video-container">
    <?php if (count($videos) > 0): ?>
        <?php foreach ($videos as $video): ?>
            <div class="video-item">
                <a href="video_page.amp.php?id=<?php echo $video["id"]; ?>">
                    <div class="video-thumbnail-container">
                        <?php if (!empty($video["thumbnail"])): ?>
                            <amp-img 
    src="<?php 
        // Thumbnail'in URL olup olmadığını kontrol et
        if (strpos($video["thumbnail"], "http://") === 0 || strpos($video["thumbnail"], "https://") === 0) {
            // Eğer bir URL ise direkt kullan
            echo htmlspecialchars($video["thumbnail"]);
        } else {
            // Yerel bir dosya yoluysa ../ ekle
            echo "../" . htmlspecialchars($video["thumbnail"]);
        }
    ?>" 
    alt="Video Thumbnail" 
    class="video-thumbnail" 
    width="390" 
    height="220" 
    layout="responsive">
</amp-img>
                        <?php else: ?>
                            <amp-img src="img/default-thumbnail.jpg" alt="Video Thumbnail" class="video-thumbnail" width="390" height="220" layout="responsive"></amp-img>
                        <?php endif; ?>
                        <div class="video-logo">
                            <amp-img src="test" alt="Logo" width="300" height="50"></amp-img>
                        </div>
                    </div>
                    <h3 class="h3"><?php echo htmlspecialchars($video["title"]); ?></h3>
                </a>
                <div class="video-meta">
                    <!-- Tarih Dinamik Hesaplama -->
                    <?php
                        date_default_timezone_set('Europe/Istanbul'); 
                        $createdAt = strtotime($video["created_at"]);  
                        $now = time();  
                        $diff = $now - $createdAt;  

                        if ($diff < 3600) {
                            $timeAgo = floor($diff / 60) . " dakika önce";
                        } elseif ($diff < 86400) {
                            $timeAgo = floor($diff / 3600) . " saat önce";
                        } elseif ($diff < 2592000) {
                            $timeAgo = floor($diff / 86400) . " gün önce";
                        } elseif ($diff < 31536000) {
                            $timeAgo = floor($diff / 2592000) . " ay önce";
                        } else {
                            $timeAgo = floor($diff / 31536000) . " yıl önce";
                        }
                    ?>
                    <span class="created-at">
                        <?php echo $timeAgo; ?>
                    </span>
                    <span class="duration">
                        <?php echo htmlspecialchars($video["duration"]); ?>
                    </span>
                </div>
            


                <p style="padding-left:10px;">
    <?php echo htmlspecialchars(limitCharacters($video['hakkinda'], 350)); ?>
</p>

            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aramanızla eşleşen sonuç bulunamadı.</p>
    <?php endif; ?>
</div>


                             <div class="pagination">
                                   <?php if ($page > 1): ?>
                                       <a href="?page=<?php echo $page - 1; ?>" class="prev">&laquo; Önceki</a>
                                   <?php endif; ?>

                                   <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                       <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? "active" : ""; ?>"> <?php echo $i; ?> </a>
                                   <?php endfor; ?>

                                   <?php if ($page < $total_pages): ?>
                                       <a href="?page=<?php echo $page + 1; ?>" class="next">Sonraki &raquo;</a>
                                   <?php endif; ?>
                             </div> <!--paginition sonu-->        
                    </div><!--solun sonu--> 
                </div><!--orta son-->

                                    <div class="kategoriler">
                                            <ul class="categories">
                                                <?php
                                                // Kategorileri listele
                                                $stmt = $pdo->query("SELECT * FROM categories");
                                                while ($category = $stmt->fetch()) {
                                                    $selectedClass = ($category['id'] == $category_id) ? ' class="selected"' : '';
                                                    echo "<li$selectedClass><a href='index.amp.php?category=" . $category['id'] . "'>" . htmlspecialchars($category['name']) . "</a></li>";
                                                }
                                                ?>
                                            </ul>
                                    </div>


                                    <footer>
                                                                         <h3 class="den">test</h3>
                                                                         <p>
                                                                         test <span class="beyaz">test</span> test 
                                                                         test
                                                                         test <span class="beyaz">test</span> test <span class="beyaz">test test</span> test
                                     </p>
                                     <p>
                                     test <span class="beyaz">test test</span> test <span class="beyaz">test</span> test
                                         test <span class="beyaz">test</span> test
                                         <span class="beyaz">test</span>test
                                         test
                                         <span class="beyaz">test</span> test
                                         test
                                         test 
                                         test
                                         test
                                         testtest
                                         test
                                         test
                                         test <strong>test</strong> test
                                         test
                                         test
                                         test
                                         test <strong>test</strong> test
                                         <span class="beyaz">test</span> test 
                                         test <span class="beyaz">test</span> test
                                     </p>
                                        <p class="den">
                                            <a href="">Test</a>
                                        </p>
                                        <p class="den">© 2024 - Tüm Hakları Saklıdır | test</p>
                                </footer>
  </div><!--all bitiş-->

</body>
</html>
