<?php
// Veritabanı bağlantısını dahil et
require_once '../db.php';

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


<!doctype html>
<html amp lang="en">
  <head>
    <meta charset="utf-8">
    <title>test</title>
    <link rel="canonical" href="http://localhost/video_page.php?id=<?php echo $video['id']; ?>">
    <link rel="icon" href="img/a.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width">
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
    <script async custom-element="amp-video" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>
    <script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
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


.h2yerine a img {
    height: 45px;
    width: auto;
}

.mobile-navbar {
    display: none;
}

.mobile-menu {
    display: none;
    position: absolute;
    top: 0;
    right: 0;
    background-color: #333;
    width: 100%;
    height: 100%;
    z-index: 9999;
    padding: 20px;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
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
    background-color:black;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    color:white;
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
    flex: 1;
    flex-direction:column;
    gap: 20px;
}

.sol {
    flex: 3;
    margin-top:50px
}


.h3 {
    font-size: 16px;
    color:#bdc1c6;
    padding-left:7.5px;
    padding-top:5px
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
    padding-right:15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
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
    padding: 30px;
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
.h2yerine amp-img {
    width: auto;
    height: auto;
    max-width: 100%;
}

/* Arama ikonu */
.searchlo {
    display: flex;
    justify-content: center;
    align-items: center;
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


.video-container {
        position: relative;
        
    }

    amp-video {
        width: 100%;
        height: auto;
        background-color: black;
    }

    .logo-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
    }

    .logo-overlay img {
        width: 100px;
        height: auto;
    }

    .play-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background: rgba(0, 0, 0, 0.7);
        z-index: 15;
        cursor: pointer;
    }

    .play-overlay svg {
        width: 80px;
        height: 80px;
        fill: white;
    }

    .skip-ad-btn {
        position: absolute;
        bottom: 20px;
        right: 20px;
        z-index: 10;
        background-color: #bbc5d1;
        color: black;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .skip-ad-btn[hidden] {
        display: none;
    }





.beyaz{
    color: white;
}



        /* Wrapper that hosts the video and the overlay */
        .video-player {
      position: relative;
      overflow: hidden;
      width: 100%;
    }

    /* Overlay fills the parent and sits on top of the video */
    .click-to-play-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
    }

    .poster-image {
      position: absolute;
      z-index: 1;
    }

    .poster-image img {
      object-fit: cover;
    }

    .video-title {
      position: absolute;
      z-index: 2;

      /* Align to the top left */
      top: 0;
      left: 0;

      font-size: 1.3em;
      background-color: rgba(0,0,0,0.8);
      color: #fafafa;
      padding: 0.5rem;
      margin: 0px;
    }

    .play-icon {
      position: absolute;
      z-index: 2;

      width: 100px;
      height: 100px;

      background-image: url(https://amp.dev/static/samples/img/play-icon.png);
      background-repeat: no-repeat;
      background-size: 100% 100%;

      /* Align to the middle */
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);

      cursor: pointer;
      opacity: 0.9;
    }

    .play-icon:hover, .play-icon:focus {
      opacity: 1;
    }
    </style>


  </head>
<body>   
<amp-sidebar id="sidebar" layout="nodisplay" side="left">
    
            
<div style="padding-left:10px; padding-right:10px; padding-bottom:10px;">
        <!-- Logo -->
        <a href="test">
            <amp-img src="test" alt="test" width="300" height="100" layout="intrinsic"></amp-img>
        </a>
    </div>




            <div class="den" style="padding-bottom:10px;">
                <h3>test </h3>
                <p>test</p>
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
        <a href="test">
            <amp-img src="test" alt="test" width="300" height="100" layout="intrinsic"></amp-img>
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


                     <div class="sol" style="color: rgb(220, 220, 220);">
                                        
               

                        <div style="color:  rgb(220, 220, 220);;">
                        
                            
                            <div class="bahis">
                                    <a href="test">
                                        <amp-img src="test" alt="gif" class="zbahis" width="390" height="50" layout="responsive"></amp-img>
                                    </a>
                                    <a href="test">
                                    <amp-img src="test" alt="gif" class="zbahis" width="390" height="50" layout="responsive"></amp-img>
                                    </a>
                            </div>
                        </div>

                   
                    <h3 style='margin:10px 0; padding-left:5px;'><?php echo htmlspecialchars($video['title']); ?></h3>
                    

                        

                        <div class="video-container">
<?php if (!empty($video['video_file'])): ?>
  
    <div class="video-player">

<!-- Ana Video -->
<amp-video id="main-video" width="600" height="370" layout="responsive" controls poster="img/simdilik.jpg" >
       <source src="<?php 
       
       if (strpos($video["video_file"], "http://") === 0 || strpos($video["video_file"], "https://") === 0) {
          
           echo htmlspecialchars($video["video_file"]);
       } else {
           // Yerel bir dosya yoluysa ../ ekle
           echo "../" . htmlspecialchars($video["video_file"]);
       }
   ?>" type="video/mp4">
   </amp-video>

<div id="myOverlay" class="click-to-play-overlay">



 <a href="test" target="_blank">
 <div class="play-icon" role="button" tabindex="0" on="tap:myOverlay.hide, myVideo.play"></div>
 </a>

</div>
</div>


   

    <!-- Logo Overlay -->
    <div class="logo-overlay">
        <img src="test" alt="Logo">
    </div>
<?php else: ?>
    <!-- Video bulunamadı mesajı -->
    <p>Video bulunamadı.</p>
<?php endif; ?>
</div>





                        <div class="video-meta" style="padding-top:10px;">
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

                                    <div>
                                    
                                        <div style="border-bottom: 1px solid #3c4043; padding-bottom:10px; display:flex; justify-content:space-between;" >
                                            <div>
                                            <span class="created-at" style="padding-left:10px;">
                                                 <?php echo $timeAgo; ?>
                                             </span>
                                             <span class="duration" style="padding-left:10px;">
                                                 <?php echo htmlspecialchars($video["duration"]); ?>
                                             </span>
                                            </div>
                                                
                                             
                                        </div>

                                   
                                    <p style="padding-left:10px; padding-top:10px;"><?php echo htmlspecialchars($video['hakkinda']); ?></p>
                                    </div>
                                   
                    </div>



                    <div style="padding-left:10px; padding-top:10px;">
                            <a href="https://site.com" style="color:purple; font-weight:bold">test</a>&nbsp;
                            <a href="https://site.com" style="color:purple; font-weight:bold">test</a>&nbsp;
                            <a href="https://site.com" style="color:purple; font-weight:bold">test</a>
                    </div>


                    <div class="amp-container" style="display: flex; flex-wrap: wrap; padding-left: 10px; padding-top:10px;">
    <ul style="list-style-type: none; padding: 0; margin: 0; display: flex; flex-wrap: wrap; justify-content: flex-start;">
        
        <?php
        // Kategorileri listele, sadece ilk 23 tanesini çek
        $stmt = $pdo->query("SELECT * FROM categories LIMIT 23");
        $count = 0; // Initialize counter
        while ($category = $stmt->fetch()) {
            $selected = ($category['id'] == $category_id) ? 'selected' : '';
            echo "<li style='margin-right: 5px; margin-bottom: 5px;'>
                    <a href='index.amp.php?category=" . $category['id'] . "' $selected 
                    style='display: inline-block; height: 25px; padding: 2px 5px; background-color: #303134; border: 1px solid #303134; border-radius: 20px; text-decoration: none; color: white; font-size: 12px; text-align: center; line-height: 20px; font-weight: bold; white-space: nowrap;'>
                    " . htmlspecialchars($category['name']) . "
                    </a>
                  </li>";
        }
        ?>
    </ul>
</div>







<div class="recent-video-bar" style="padding: 10px; text-align: left;">
    <h3 style="font-size: 18px; font-weight: bold; color: rgb(220, 220, 220); padding-bottom:10px;">Benzer Filmler</h3>
    
    <!-- AMP Carousel -->
    <amp-carousel width="auto" height="250" layout="fixed-height" type="carousel" style="max-width: 100%; overflow: hidden;">
        <?php
        // Son 16 videoyu çekiyoruz
        $stmt = $pdo->query("SELECT * FROM videos ORDER BY created_at DESC LIMIT 16");
        $recentVideos = $stmt->fetchAll();

        if (count($recentVideos) > 0):
            foreach ($recentVideos as $video):
        ?>
                <div class="recent-video-item" style="display: flex; justify-content: center; text-align: left; margin-right:10px;  border: 1px solid #303134; padding-bottom:10px; background: #171717;">
                    <a href="video_page.amp.php?id=<?php echo $video['id']; ?>" style="text-decoration: none; color: inherit;">
                        <?php if (!empty($video["thumbnail"])): ?>
                            <!-- Sabit boyutta thumbnail için fixed layout -->
                            <amp-img src="<?php 
                                // Thumbnail'in URL olup olmadığını kontrol et
                                if (strpos($video["thumbnail"], "http://") === 0 || strpos($video["thumbnail"], "https://") === 0) {
                                    // Eğer bir URL ise direkt kullan
                                    echo htmlspecialchars($video["thumbnail"]);
                                } else {
                                    // Yerel bir dosya yoluysa ../ ekle
                                    echo "../" . htmlspecialchars($video["thumbnail"]);
                                }
                            ?>" alt="Video Thumbnail" width="205" height="145" layout="fixed" style="border-radius: 3px; max-width: 100%;"></amp-img>
                        <?php else: ?>
                            <amp-img src="img/default-thumbnail.jpg" alt="Video Thumbnail" width="205" height="145" layout="fixed" style="border-radius: 10px; max-width: 100%;"></amp-img>
                        <?php endif; ?>

                            <div style="padding-left:10px;">
                           
                            <h4 class="recent-video-title" style="font-size: 14px; font-weight: bold; color: rgb(220, 220, 220); margin-top: 8px;">
    <?php 
        $title = htmlspecialchars($video["title"]);
        if (strlen($title) > 25) {
            $title = substr($title, 0, 25) . '...';
        }
        echo $title;
    ?>
</h4>   
                    </a>
                    <div class="recent-video-meta" style="font-size: 12px; color: #777; margin-top: 5px; margin-left:-5px;">
                        <span class="recent-video-date" style="display: block;">
                            <i class="fa fa-calendar" aria-hidden="true" style="font-size: 12px;"></i>&nbsp; 
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
                        <span class="recent-video-duration" style="display: block;">
                            <i class="fa fa-video-camera" style="font-size: 12px;"></i>&nbsp; <?php echo htmlspecialchars($video["duration"]); ?>
                        </span>
                    </div>
                            </div>


                </div>
        <?php
            endforeach;
        else:
        ?>
            <p style="font-size: 14px; color: #303134;">Henüz video eklenmedi.</p>
        <?php endif; ?>
    </amp-carousel>
</div>

                
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
                                                                         test <span class="beyaz">test</span> test <span class="beyaz"> test</span> test
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
