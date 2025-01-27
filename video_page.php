<?php
// Veritabanı bağlantısını dahil et
require_once 'db.php';

// Video ID'sini al
$videoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($videoId === 0) {
    echo "Geçersiz video ID!";
    exit;
}

// Videoyu veritabanından al
$query = "SELECT * FROM videos WHERE id = :id LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $videoId]);
$video = $stmt->fetch();

if (!$video) {
    echo "Video bulunamadı!";
    exit;
}

// Etiketleri almak için sorgu
$query_tags = "SELECT tags.id, tags.name FROM tags 
               INNER JOIN video_tags ON tags.id = video_tags.tag_id
               WHERE video_tags.video_id = :video_id";
$stmt_tags = $pdo->prepare($query_tags);
$stmt_tags->execute(['video_id' => $videoId]);
$tags = $stmt_tags->fetchAll(PDO::FETCH_ASSOC);

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

?>


<!DOCTYPE html>

<html lang="en">
<head>

    <link rel="amphtml" href="http://localhost/amp/video_page.amp.php?id=<?php echo $video['id']; ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($video['title']); ?> - İzle</title>
    <link rel="shortcut icon" href="images/a.ico" type="image/x-icon">
    <link rel="stylesheet" href="info.css">
    <link rel="stylesheet" href="video.css">
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




                            <div style="display:flex; justify-content:space-between; padding-right:190px;">
                                <a href="">
                                <img src="" alt="">
                                </a>
                        
                            </div>

                            
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
            <div class="solunsolu">


           
            <nav style="border-bottom: 1px solid #eee">
                <span class="fa fa-home" aria-hidden="true"></span>
                <a href="index.php">Anasayfa</a>
                <span>/</span>
                <span class="current"><?php echo htmlspecialchars($video['title']); ?></span>
            </nav>

            <div class="bahis">
                    <a href="">
                            <img src="" alt="gif" class="zbahis">
                    </a>
            </div>



            <div class="entry">



            <div class="video-container" style="position: relative;">
    <?php if (!empty($video['video_file'])): ?>
        <!-- Video etiketi -->
        <video id="video-player" width="660" height="370" style="background: black; ">
            <source id="main-video-source" src="<?php echo htmlspecialchars($video['video_file']); ?>" type="video/mp4">
            Tarayıcınız video etiketini desteklemiyor.
        </video>

        <div id="logo-overlay">
            <img src="images/logo.png" alt="Logo" id="video-logo">
        </div>

        <button id="skip-ad-btn" style="display: block; position: absolute; bottom: 130px; right: 1px; z-index: 100000; background-color: #bbc5d1; color: black; padding: 10px; border: none; border-radius: 2px; cursor: pointer; display:none !important;">
            <span id="countdown">5</span> sn sonra Reklamı Geç
        </button>

        <!-- Oynatma İkonu -->
        <div id="play-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 80%;  display: flex; justify-content: center; align-items: center; cursor: pointer; z-index: 1000;">
            <svg width="100" height="100" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 5v14l11-7z"/>
            </svg>
        </div>
    <?php else: ?>
        <!-- Eğer video yoksa mesaj -->
        <p>Video bulunamadı.</p>
    <?php endif; ?>
    <h1 style="margin-top:5px; padding-left:10px; font-size:28px;" class="baslik"><?php echo htmlspecialchars($video['title']); ?></h1>
</div>

            <div class="video-meta" style="padding-left:10px;">
                    <!-- Tarih Dinamik Hesaplama -->
                    <?php
                        // Zaman dilimini doğru ayarladığınızdan emin olun
                        date_default_timezone_set('Europe/Istanbul'); // Zaman dilimini İstanbul olarak ayarlıyoruz
                                                    
                        // Videonun oluşturulma tarihini alıyoruz
                        $createdAt = strtotime($video["created_at"]);  
                                                    
                        // Eğer tarih geçerli değilse, ekrana hata mesajı basabilirsiniz
                        if ($createdAt === false) {
                            echo "Geçersiz tarih formatı!";
                            exit;
                        }
                    
                        $now = time();  // Şu anki zamanı alıyoruz
                        $diff = $now - $createdAt;  // Farkı saniye cinsinden hesaplıyoruz
                    
                        // Farkı zaman birimlerine göre hesapla
                        if ($diff < 3600) {  // 1 saatten küçükse
                            $timeAgo = floor($diff / 60) . " dakika önce";  // Sadece dakika olarak gösteriyoruz
                        } elseif ($diff < 86400) {  // 1 günden küçükse
                            $timeAgo = floor($diff / 3600) . " saat önce";  // Sadece saat olarak gösteriyoruz
                        } elseif ($diff < 2592000) {  // 1 aydan küçükse
                            $timeAgo = floor($diff / 86400) . " gün önce";  // Sadece gün olarak gösteriyoruz
                        } elseif ($diff < 31536000) {  // 1 yıldan küçükse
                            $timeAgo = floor($diff / 2592000) . " ay önce";  // Sadece ay olarak gösteriyoruz
                        } else {
                            $timeAgo = floor($diff / 31536000) . " yıl önce";  // 1 yıldan büyükse, yıl olarak gösteriyoruz
                        }
                    ?>
                    <!-- Dinamik Verileri Göster -->
                    <span class="created-at">
                        <i class="fa fa-calendar" aria-hidden="true"></i>&nbsp; <?php echo $timeAgo; ?>
                    </span>
                    <span class="duration">
                        <i class="fa fa-video-camera"></i>&nbsp; <?php echo htmlspecialchars($video["duration"]); ?>
                    </span>
                    <span>
                        <i class="fa fa-comments"></i>&nbsp; 0
                    </span>
            </div>



            <p style="padding-left:10px;"><?php echo htmlspecialchars($video['hakkinda']); ?></p>


            
                        <br>

            <div class="" style="display: flex; flex-wrap: wrap; padding-left:10px;">
                        <ul class="" style="list-style-type: none; padding: 0; margin: 0; display: flex; flex-wrap: wrap;">
                            <i class='fa-regular fa-folder'></i>&nbsp;
                            <?php
                            // Kategorileri listele, sadece ilk 23 tanesini çek
                            $stmt = $pdo->query("SELECT * FROM categories LIMIT 23");
                            $count = 0; // Initialize counter
                            $total = $stmt->rowCount(); // Get total number of categories
                            while ($category = $stmt->fetch()) {
                                $selected = ($category['id'] == $category_id) ? 'selected' : '';
                                echo "<a href='index.php?category=" . $category['id'] . "'><li $selected>" . htmlspecialchars($category['name']) . "</li></a>";
                            
                                // Add a comma except for the last category
                                $count++;
                                if ($count < $total) {
                                    echo ", "; // Add a comma after each category
                                }
                            }
                            ?>
                        </ul>
            </div>


            <div>
    <ul class="custom-tags">
        <?php if (!empty($tags)): ?>
            <?php foreach ($tags as $tag): ?>
                <li class="custom-tag">
                    <a href="index.php?tag=<?php echo htmlspecialchars($tag['id']); ?>" class="custom-tag-link">
                        <i class="fas fa-tag"></i>  <!-- İkon eklendi -->
                        <?php echo htmlspecialchars($tag['name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="custom-tag"> </li>
        <?php endif; ?>
    </ul>
</div>

    
                            <br><br>

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



            </div><!--enty sonu-->

<div class="recent-video-bar">
    <h3>Son Yüklenen Videolar</h3>
    <div class="recent-video-container">
        <?php
        $stmt = $pdo->query("SELECT * FROM videos ORDER BY created_at DESC LIMIT 3");
        $recentVideos = $stmt->fetchAll();

        if (count($recentVideos) > 0):
            foreach ($recentVideos as $video):
        ?>
                <div class="recent-video-item">
                    <a href="video_page.php?id=<?php echo $video['id']; ?>">
                        <?php if (!empty($video["thumbnail"])): ?>
                            <div class="recent-video-thumbnail-container">
                                <img src="<?php echo htmlspecialchars($video["thumbnail"]); ?>" alt="Video Thumbnail" class="recent-video-thumbnail">
                                <div class="recent-video-logo">
                                    <img src="images/logo.png" alt="Logo">
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="recent-video-thumbnail-container">
                                <img src="images/default-thumbnail.jpg" alt="Video Thumbnail" class="recent-video-thumbnail">
                                <div class="recent-video-logo">
                                    <img src="images/logo.png" alt="Logo">
                                </div>
                            </div>
                        <?php endif; ?>
                        <h4 class="recent-video-title"><?php echo htmlspecialchars($video["title"]); ?></h4>
                    </a>
                    <div class="recent-video-meta">
                        <span class="recent-video-date">
                            <i class="fa fa-calendar" aria-hidden="true"></i>&nbsp; 
                            <?php
                                date_default_timezone_set('Europe/Istanbul');
                                $createdAt = strtotime($video["created_at"]);
                                $now = time();
                                $diff = $now - $createdAt;

                                if ($diff < 3600) {
                                    echo floor($diff / 60) . " dakika önce";
                                } elseif ($diff < 86400) {
                                    echo floor($diff / 3600) . " saat önce";
                                } elseif ($diff < 2592000) {
                                    echo floor($diff / 86400) . " gün önce";
                                } else {
                                    echo date("d.m.Y", $createdAt);
                                }
                            ?>
                        </span>
                        <span class="recent-video-duration">
                            <i class="fa fa-video-camera"></i>&nbsp; <?php echo htmlspecialchars($video["duration"]); ?>
                        </span>
                    </div>
                </div>
        <?php
            endforeach;
        else:
        ?>
            <p>Henüz video eklenmedi.</p>
        <?php endif; ?>
    </div>
</div>








            <br><br>

            <div class="comment-form">
    <h2>Bir Yanıt Yazın</h2>
    <p>E-posta adresiniz yayınlanmayacak. Gerekli alanlar <span>*</span> ile işaretlenmiştir</p>
    <form>
      <label for="comment">Yorum <span>*</span></label>
      <textarea id="comment" name="comment" rows="5" required></textarea>
      
      <label for="name">Ad</label>
      <input type="text" id="name" name="name">
      
      <label for="email">E-posta</label>
      <input type="email" id="email" name="email">
      
      <label for="website">İnternet sitesi</label>
      <input type="url" id="website" name="website">
      
      <button type="submit">Yorum gönder</button>
    </form>
  </div>


  <br><br>

  


    </div><!--solunsolu bitişi-->

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
            <div style="text-align:right;"><i class="fa fa-rss"></i>&nbsp;&nbsp;<a href="" style="color: #ddd; text-align: right;"> test</a></div>            
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
