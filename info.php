<?php
// Veritabanı bağlantısını dahil et
require_once "db.php";

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="amphtml" href="http://localhost/amp/info.amp.php">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hakkımızda | </title>
    <link rel="shortcut icon" href="images/a.ico" type="image/x-icon">
    <link rel="stylesheet" href="info.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
</head>

<body>
<div class="background">
    <a href="" class="background-cover" target="_blank" rel="nofollow noopener"></a>

    <div class="hepsi">


        <div class="anav">
            <div class="üstü">

                <div class="container">
                    <span class="today-date">Pazartesi , Aralık 16 2024    |</span>
                    <div class="ikinci">
                    <ul class="" style="list-style-type: none; padding: 10px; margin: 0 0 0 10px;  display: flex; flex-wrap: wrap;">
                             <?php
                             // İstediğin kategorilerin ID'lerini belirt
                             $desired_ids = [26, 29, 48, 20, 62, 38, 41, 43, 59, 65];
                             $placeholders = implode(',', array_fill(0, count($desired_ids), '?'));

                             // Sadece istediğin ID'lere göre kategorileri çek
                             $stmt = $pdo->prepare("SELECT * FROM categories WHERE id IN ($placeholders)");
                             $stmt->execute($desired_ids);

                             // Kategorileri listele
                             while ($category = $stmt->fetch()) {
                                 $selected = ($category['id'] == $category_id) ? 'selected' : '';
                                 echo "<a href='index.php?category=" . $category['id'] . "'><li style='color:#333;' $selected>" . htmlspecialchars($category['name']) . "</li></a>";
                             }
                             ?>
                        </ul>

                    </div>
                </div>



            </div><!--üstü son-->


            <div class="alti">
                    <div class="logo">



                    <div>
                             <div>
                                

                                <div class="mobile-navbar" style="display:none;">
   
   
                                
       <div class="menu-toggle" onclick="toggleMobileMenu()">☰</div> <!-- Küçük kare menü -->
   
                                
   
       <div class="mobile-menu">
       <div class="search-bar" style="border:none;">
                           <form method="GET" action="index.php">
                               <input type="text" name="search" placeholder="Ara... " style="font-size:10px !important;">
                               <button type="submit">Ara</button>
                           </form>
                       </div>
           <a href="index.php" style="font-size:15px !important;">Anasayfa</a>
           <a href="info.php" style="font-size:15px !important;">Hakkımızda</a>
           <a href="privacy.php" style="font-size:15px !important;">Gizlilik Politikası</a>
           <a href="legal.php" style="font-size:15px !important;">Hukuksal</a>
           <a href="copyright.php" style="font-size:15px !important;">Telif Hakkı</a>
           <a href="contact.php" style="font-size:15px !important;">İletişim</a>
       </div>
   </div>

                                </div>
   
                             </div>


                            <h2>
                                <a href="">
                                <img src="" alt=" ">
                                </a>
                            </h2>
                    </div>
            </div><!--alti son-->
        </div><!--anav son-->

        <div class="navbar">
            <a href="index.php" class="home-icon">

                <img src="images/ev.png" alt="logo">

            </a>
            <a href="info.php">Hakkımızda</a>
            <a href="privacy.php">Gizlilik Politikası</a>
            <a href="legal.php">Hukuksal</a>
            <a href="copyright.php">Telif Hakkı</a>
            <a href="contact.php">İletişim</a>
        </div> <!--navbar sonu--> 

        <div class="orta">
            <div class="sol">
            <div class="hak">
    <div class="icerik">

            <nav style="border-bottom: 1px solid #eee">
                <span class="fa fa-home" aria-hidden="true"></span>
                <a href="index.php">Anasayfa</a>
                <span>/</span>
                <span class="current">Hakkımızda</span>
            </nav>

    <h2>HAKKIMIZDA</h2>
        <p>site adıyla yayın yapan sitemiz en iyi, en yeni ve güncel film videolarını kullanıcılarıyla buluşturuyor. 
            Türkiye’nin en iyi ve en güncel film sitelerinden birisi olan sitemiz kullanıcı deneyimine büyük önem vermektedir.</p>
            
        <p>Bize destek olmak için bol bol sitemizde film izleyip vakit geçirebilirsiniz 🙂</p>
        
        <p>Önemli Not!:  TEST</p>

    </div>
    <br>
    
<div class="paylasimlo">
<div class="paylas-text">PAYLAŞ</div>
    <div class="share-container">
        <a href="#" class="share-button facebook">Facebook</a>
        <a href="#" class="share-button twitter">Twitter</a>
        <a href="#" class="share-button stumbleupon">Stumbleupon</a>
        <a href="#" class="share-button linkedin">LinkedIn</a>
        <a href="#" class="share-button pinterest">Pinterest</a>
    </div>
</div>
    


</div> <!--hak bitiş-->
            </div><!--solun sonu--> 

        <div class="sag">
                <div>
                <h4>VİDEO ARA</h4>
                <div class="search-bar">
                        <form method="GET" action="index.php">
                            <input type="text" name="search" placeholder="Ara...">
                            <button type="submit">Ara</button>
                        </form>
                    </div>
                </div>
                    
        <div class="mecbursidebar">
                <h4>VİDEO KATEGORİLERİ</h4>
                <div class="sidebar">
                    <div class="kategoriler">
                        <ul class="categories">
                            <?php
                            // Kategorileri listele
                            $stmt = $pdo->query("SELECT * FROM categories");
                            while ($category = $stmt->fetch()) {
                                $selected = ($category['id'] == $category_id) ? 'selected' : '';
                                echo "<a href='index.php?category=" . $category['id'] . "'><li $selected><i class='fa-regular fa-folder'></i>&nbsp;" . htmlspecialchars($category['name']) . "</li></a>";
                            }
                            ?>
                        </ul>
                    </div>
                </div><!--sidebar son-->
        </div> <!--mecbursidebar son-->


                <form method="GET" action="index.php">
                <h4 for="video-select">VİDEO ARŞİVLERİ</h4>
                    <div class="video-archives">
                        <select id="video-select" name="selected_month" onchange="this.form.submit()">
                            <option value="">Ay seçin</option>
                            <?php
                            // Ayları listele
                            foreach ($months as $month) {
                                $month_value = $month['month'];
                                $month_label = date("F Y", strtotime($month_value)); // Ay ismi ve yıl
                                echo "<option value=\"$month_value\" " . ($month_value === $selected_month ? 'selected' : '') . ">$month_label</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>

                   <div>
                            <div class="reklamalani" >
                                    <p>
                                            <a href="" target="_blank">
                                                    <img src="" alt="" alt="" width="290" height="96.50" sizes="auto, (max-width: 618px) 100vw, 618px">
                                                    
                                            </a>

                                    </p>

                            </div>

                   </div>         


        </div> <!-- sagın sonu -->
    </div> <!-- ortanın sonu -->





    <footer>
            <div style="text-align:right;"><i class="fa fa-rss"></i>&nbsp;&nbsp;<a href="" style="color: #ddd; text-align: right;">Test</a></div>            
            <p>© 2024 - Tüm Hakları Saklıdır</p>
            <h3 style="margin: 20px 0; font-size: 24px; font-weight: bold;">test</h3>
            <p>
            test <span class="beyaz">test</span> test
            test
            test <span class="beyaz">test</span> test <span class="beyaz">test</span> test
    </p>
    <p>
    test <span class="beyaz">test</span> test<span class="beyaz">test</span> izleyin.
    test <span class="beyaz">test test</span> izleyin. 
        <span class="beyaz">test</span> 
        test
        <span class="beyaz">test</span>test
        test
        test 
        test
        test
        test
        test
        test
        test <strong>test</strong>test
        test
        test
        test
        test <strong>test</strong>test
        <span class="beyaz">test</span>test
        test <span class="beyaz">test</span> test
    </p>

    <a href="#">test</a>
        </footer><!--footer bitiş-->

    </div> <!--hepsinin-->

</div>
<script src="script.js"></script>
</body>
</html>
