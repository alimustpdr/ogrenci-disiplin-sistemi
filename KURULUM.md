# Hızlı Kurulum Kılavuzu

## 1. Gereksinimleri Kontrol Edin

- ✅ PHP 7.4 veya üzeri
- ✅ MySQL/MariaDB 5.7 veya üzeri
- ✅ Apache web sunucusu (mod_rewrite etkin)
- ✅ Web tarayıcısı

## 2. Dosyaları Web Sunucusuna Yükleyin

Tüm dosyaları web sunucunuzun root dizinine (örn: `htdocs`, `public_html`, `www`) kopyalayın.

## 3. Veritabanını Oluşturun

### MySQL/MariaDB Komut Satırı:
```bash
mysql -u root -p
```

```sql
CREATE DATABASE student_discipline CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

### Tabloları İçe Aktarın:
```bash
mysql -u kullanici_adi -p student_discipline < database.sql
```

veya phpMyAdmin'den:
1. `student_discipline` veritabanını seçin
2. "Import" (İçe Aktar) sekmesine gidin
3. `database.sql` dosyasını seçin
4. "Go" (Git) butonuna tıklayın

## 4. Yapılandırma Dosyasını Oluşturun

```bash
cp config-sample.php config.php
```

`config.php` dosyasını düzenleyin:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'veritabani_kullanici_adi');  // Değiştirin
define('DB_PASS', 'veritabani_sifresi');         // Değiştirin
define('DB_NAME', 'student_discipline');
```

## 5. İzinleri Ayarlayın (Linux/Mac)

```bash
chmod 644 config.php
chmod 755 public
```

## 6. Sisteme Giriş Yapın

Tarayıcınızda projenin URL'ini açın:
```
http://localhost/ogrenci-disiplin-sistemi/
```

### Varsayılan Giriş Bilgileri:

**Yönetici:**
- Kullanıcı Adı: `admin`
- Şifre: `admin123`

**Öğretmen:**
- Kullanıcı Adı: `ogretmen`
- Şifre: `ogretmen123`

**Öğrenci:**
- Öğrenci No: `2024001`
- Şifre: `ogrenci123`

## 7. Şifreleri Değiştirin! ⚠️

İlk girişten sonra mutlaka varsayılan şifreleri değiştirin:
1. Profil sayfasına gidin
2. "Şifre Değiştir" bölümünü kullanın
3. Güçlü bir şifre belirleyin

## Sorun Giderme

### "Veritabanı bağlantı hatası" alıyorsanız:
- `config.php` dosyasındaki veritabanı bilgilerini kontrol edin
- MySQL/MariaDB servisinin çalıştığından emin olun
- Veritabanı kullanıcısının yeterli izinlere sahip olduğunu kontrol edin

### "500 Internal Server Error" alıyorsanız:
- Apache error loglarını kontrol edin
- `.htaccess` dosyasının düzgün yüklendiğinden emin olun
- `mod_rewrite` modülünün etkin olduğunu kontrol edin

### Sayfalar düzgün görünmüyorsa:
- Tarayıcı konsolunu kontrol edin (F12)
- Bootstrap CDN'lerine erişilebildiğinden emin olun
- İnternet bağlantınızı kontrol edin

## Destek

Sorunlarınız için:
- GitHub Issues: https://github.com/alimustpdr/ogrenci-disiplin-sistemi/issues
- README.md dosyasını okuyun
- Detaylı dokümantasyonu inceleyin

İyi kullanımlar! 🎉
