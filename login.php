<?php
// Oturumu başlat
session_start();

// Veritabanı bağlantı bilgileri
$sunucu = "localhost";
$veritabani_adi = "test_db"; // Kendi veritabanı adınızı yazın
$kullanici = "root";       // Kendi veritabanı kullanıcı adınızı yazın
$sifre = "";               // Kendi veritabanı şifrenizi yazın

// Formdan gelen verileri kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Formdan gelen verileri al
    $gelen_kullanici_adi = $_POST['kullanici_adi'];
    $gelen_sifre = $_POST['sifre'];

    try {
        // 1. PDO ile veritabanı bağlantısını kur
        $pdo = new PDO("mysql:host=$sunucu;dbname=$veritabani_adi;charset=utf8", $kullanici, $sifre);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2. SQL SORGUSUNU HAZIRLA (PREPARE)
        // SQL enjeksiyonunu önlemek için '?' placeholder'ları kullanıyoruz.
        // Bu, kullanıcı girdisinin SQL komutu olarak yorumlanmasını engeller.
        $sql = "SELECT * FROM kullanicilar WHERE kullanici_adi = ?";
        $stmt = $pdo->prepare($sql);

        // 3. SORGUCUĞU ÇALIŞTIR (EXECUTE)
        // Placeholder'lara karşılık gelen değerleri bir dizi içinde gönderiyoruz.
        $stmt->execute([$gelen_kullanici_adi]);

        // 4. SONUÇLARI KONTROL ET
        $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kullanıcı bulunduysa ve şifre doğruysa
        // GÜVENLİK UYARISI: Gerçek bir uygulamada şifreler asla düz metin olarak saklanmamalıdır.
        // password_hash() ile şifrelenmeli ve password_verify() ile kontrol edilmelidir.
        if ($kullanici && $gelen_sifre === $kullanici['sifre']) { // Bu satır sadece basit bir örnek içindir
            
            // Başarılı giriş
            $_SESSION['kullanici_adi'] = $kullanici['kullanici_adi'];
            echo "Başarıyla giriş yaptınız! Hoş geldiniz, " . htmlspecialchars($kullanici['kullanici_adi']);
            // header("Location: anasayfa.php"); // Kullanıcıyı ana sayfaya yönlendir
            exit();

        } else {
            // Hatalı kullanıcı adı veya şifre
            echo "Hatalı kullanıcı adı veya şifre.";
        }

    } catch(PDOException $e) {
        // Veritabanı bağlantı hatası
        die("Veritabanı bağlantısı kurulamadı: " . $e->getMessage());
    }

    // Bağlantıyı kapat
    $pdo = null;

} else {
    // Doğrudan login.php'ye erişilirse ana sayfaya yönlendir
    header("Location: index.html");
    exit();
}
?>
