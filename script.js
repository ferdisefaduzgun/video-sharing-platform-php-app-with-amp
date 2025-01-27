
   function toggleMobileMenu() {
       const menu = document.querySelector('.mobile-menu');
       
       // Menü açıkken kapat, kapalıyken aç
       if (menu.classList.contains('active')) {
           menu.classList.remove('active');
           setTimeout(() => {
               menu.classList.add('inactive'); // Menü sol tarafa kayacak
           }, 300); // 300ms sonra 'inactive' ekle (animasyonun tamamlanması için)
       } else {
           menu.classList.remove('inactive'); // 'inactive' sınıfını kaldır
           menu.classList.add('active'); // 'active' sınıfını ekle
       }
   }
   
   // Menü dışına tıklanınca menüyü kapatma
   document.addEventListener('click', function (event) {
       const menu = document.querySelector('.mobile-menu');
       const menuToggle = document.querySelector('.menu-toggle');
   
       if (!menu.contains(event.target) && !menuToggle.contains(event.target)) {
           menu.classList.remove('active');
           setTimeout(() => {
               menu.classList.add('inactive'); // Menü kapanırken sol tarafa kayacak
           }, 300); // 300ms sonra 'inactive' ekle (animasyonun tamamlanması için)
       }
   });
   
   document.addEventListener('DOMContentLoaded', function () {
    const videoPlayer = document.getElementById('video-player');
    const mainVideoSource = document.getElementById('main-video-source');
    const skipAdButton = document.getElementById('skip-ad-btn');
    const playOverlay = document.getElementById('play-overlay');
    const countdownSpan = document.getElementById('countdown');
    
    // Reklam videosunun URL'si
    const reklamVideo = 'reklam.mp4';

    // Başlangıçta reklamı yükle
    if (videoPlayer) {
        videoPlayer.src = reklamVideo;

        // Kontroller devre dışı
        videoPlayer.controls = false;

        // Reklam başlatma işlemi
        playOverlay.addEventListener('click', function () {
            playOverlay.style.display = 'none'; // İkonu gizle
            videoPlayer.play(); // Reklamı başlat

            // Reklam oynarken 5 saniye sonra "Reklamı Geç" butonunu göster
            let countdown = 5;
            skipAdButton.style.display = 'block'; // Buton görünür hale gelir
            skipAdButton.disabled = true; // Buton tıklanamaz olacak
            countdownSpan.textContent = countdown; // Başlangıçta 5 olarak göster
            const countdownInterval = setInterval(function () {
                countdown--;
                countdownSpan.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    skipAdButton.disabled = false; // Buton tıklanabilir hale gelir
                    countdownSpan.textContent = ''; // Sayıyı temizle
                    skipAdButton.textContent = 'Reklamı Geç'; // Sadece 'Reklamı Geç' yazısını göster
                }
            }, 1000); // Geri sayım her saniyede bir yapılır
        });

        // Reklam bitince veya "Reklamı Geç" butonuna basınca ana videoya geç
        const playMainVideo = () => {
            videoPlayer.src = mainVideoSource.src; // Ana video kaynağına geç
            videoPlayer.controls = true; // Kontrolleri etkinleştir
            videoPlayer.play(); // Ana videoyu başlat
            skipAdButton.style.display = 'none'; // "Reklamı Geç" butonunu gizle
        };

        // Reklam sona erince otomatik olarak ana videoya geç
        videoPlayer.addEventListener('ended', playMainVideo);

        // Buton tıklama olayı
        skipAdButton.addEventListener('click', playMainVideo);
    }
});

    document.getElementById("play-overlay").addEventListener("click", function() {
        // Yeni sekmede açılacak URL
        window.open("https://www.reklam.com", "_blank");
    });





// F12 tuşunu engelleme
document.addEventListener("keydown", function(event) {
    if (event.keyCode === 123) { // 123 F12 tuşunun keyCode'u
        event.preventDefault(); // F12 tuşunun işlevini engelle
    }
});

// Sağ tıklamayı engelleme
document.addEventListener("contextmenu", function(event) {
    event.preventDefault(); // Sağ tıklama menüsünü engelle
});

(function() {
    var devtools = {open: false};

    Object.defineProperty(devtools, 'open', {
        get: function() {
            return true;
        },
        set: function() {
            alert('Geliştirici Araçları Engellendi!');
        }
    });

    // F12 ve sağ tıklama engelleme
    document.addEventListener("keydown", function(event) {
        if (event.keyCode === 123) {
            event.preventDefault();
        }
    });

    document.addEventListener("contextmenu", function(event) {
        event.preventDefault();
    });

    // DevTools ekranını kontrol etmek için interval kullanmak
    setInterval(function() {
        if (window.devtools && window.devtools.open) {
            alert('Geliştirici Araçları Engellendi!');
        }
    }, 1000);
})();































