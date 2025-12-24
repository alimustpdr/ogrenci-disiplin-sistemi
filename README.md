# Öğrenci Disiplin Takip Sistemi

## 📋 Proje Açıklaması

Öğrenci Disiplin Takip Sistemi, eğitim kurumlarının öğrenci davranışlarını takip etmek, disiplin kurallarını yönetmek ve disiplin süreçlerini dijitalleştirmek için geliştirilmiş modern bir web uygulamasıdır.

Bu sistem, okul yöneticilerinin, öğretmenlerin ve öğrencilerin disiplin süreçlerini daha verimli bir şekilde yönetmesine olanak sağlar.

## ✨ Özellikler

### 🔐 Güvenli Kullanıcı Yönetimi
- Şifreler veritabanında **hash'lenerek** saklanır
- Üç farklı kullanıcı rolü: **Yönetici**, **Öğretmen**, **Öğrenci**
- Rol tabanlı erişim kontrolü

### 👨‍💼 Yönetici (Admin) Özellikleri
- Sisteme tam erişim
- Öğretmen ve öğrenci oluşturma, düzenleme, silme
- Tüm disiplin kayıtlarını görüntüleme ve yönetme
- Detaylı raporlama ve istatistikler
- PDF/Yazdırma desteği ile rapor oluşturma

### 👨‍🏫 Öğretmen Özellikleri
- Öğrencilere disiplin kaydı ekleme
- Kendi eklediği kayıtları düzenleme ve silme
- Tüm kayıtları görüntüleme
- Son eklenen kayıtları takip etme

### 👨‍🎓 Öğrenci Özellikleri
- Sadece kendi disiplin kayıtlarını görüntüleme
- Kişisel profil bilgileri
- Salt okunur erişim (düzenleme/silme yok)

### 📊 Raporlama ve İstatistikler
- Tarih aralığına göre filtreleme
- Öğrenciye özel veya toplu raporlar
- Kayıt tipi bazında istatistikler
- Yazdırma dostu görünüm
- PDF olarak kaydetme desteği

### 🔒 Güvenlik Önlemleri
- Prepared statements ile SQL enjeksiyonu koruması
- XSS saldırılarına karşı veri temizleme
- CSRF token koruması
- Güvenli session yönetimi
- .htaccess ile sunucu güvenliği

## 🛠️ Teknoloji Stack

### Backend
- **Dil**: PHP 7.4+
- **Mimari**: OOP (Nesne Yönelimli Programlama)
- **Veritabanı**: MySQL / MariaDB
- **Güvenlik**: PDO Prepared Statements, Password Hashing

### Frontend
- **Framework**: Bootstrap 5
- **İkonlar**: Bootstrap Icons
- **Stil**: Modern, mobil uyumlu, gradient tasarım
- **JavaScript**: Vanilla JS

## 📦 Kurulum

### Gereksinimler
- **PHP**: 7.4 veya üzeri
- **MySQL/MariaDB**: 5.7 veya üzeri
- **Web Server**: Apache 2.4+ (mod_rewrite etkin)
- **Tarayıcı**: Modern web tarayıcı (Chrome, Firefox, Edge, Safari)

### Kurulum Adımları

1. **Depoyu klonlayın veya indirin**
```bash
git clone https://github.com/alimustpdr/ogrenci-disiplin-sistemi.git
cd ogrenci-disiplin-sistemi
```

2. **Veritabanını oluşturun**
- MySQL/MariaDB'ye giriş yapın
- Yeni bir veritabanı oluşturun:
```sql
CREATE DATABASE student_discipline CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. **Veritabanı tablolarını içe aktarın**
- `database.sql` dosyasını içe aktarın:
```bash
mysql -u kullanici_adi -p student_discipline < database.sql
```
veya phpMyAdmin üzerinden `database.sql` dosyasını import edin.

4. **Yapılandırma dosyasını oluşturun**
- `config-sample.php` dosyasını kopyalayın ve `config.php` olarak kaydedin:
```bash
cp config-sample.php config.php
```
- `config.php` dosyasını düzenleyin ve veritabanı bilgilerinizi girin:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'veritabani_kullanici_adi');
define('DB_PASS', 'veritabani_sifresi');
define('DB_NAME', 'student_discipline');
```

5. **Web sunucusunu yapılandırın**
- Projeyi web sunucunuzun root dizinine veya bir alt dizine kopyalayın
- Apache'de `mod_rewrite` modülünün etkin olduğundan emin olun
- `.htaccess` dosyasının çalıştığından emin olun

6. **İzinleri ayarlayın**
```bash
chmod 644 config.php
chmod 755 public
```

## 🚀 Kullanım

### Sisteme Giriş
- Tarayıcınızda projenin URL'ini açın: `http://localhost/ogrenci-disiplin-sistemi/`
- Giriş ekranında kullanıcı türünü seçin (Kullanıcı/Öğrenci)

### Varsayılan Giriş Bilgileri

#### Yönetici
- **Kullanıcı Adı**: `admin`
- **Şifre**: `admin123`

#### Öğretmen (Demo)
- **Kullanıcı Adı**: `ogretmen`
- **Şifre**: `ogretmen123`

#### Öğrenci (Demo)
- **Öğrenci No**: `2024001`
- **Şifre**: `ogrenci123`

> ⚠️ **Önemli**: İlk girişten sonra varsayılan şifreleri mutlaka değiştirin!

### Temel İşlemler

#### Yönetici Olarak
1. **Dashboard**: Sistem geneli istatistikleri görüntüleme
2. **Öğrenci Yönetimi**: Yeni öğrenci ekleme, düzenleme, silme
3. **Öğretmen Yönetimi**: Kullanıcı oluşturma ve yönetme
4. **Disiplin Kayıtları**: Tüm kayıtları görüntüleme ve yönetme
5. **Raporlama**: Detaylı raporlar oluşturma ve PDF'e aktarma

#### Öğretmen Olarak
1. **Dashboard**: Eklediğiniz kayıtları görüntüleme
2. **Yeni Kayıt Ekleme**: Öğrencilere disiplin kaydı ekleme
3. **Kayıtları Düzenleme**: Kendi kayıtlarınızı güncelleme
4. **Profil**: Şifre değiştirme

#### Öğrenci Olarak
1. **Dashboard**: Kişisel bilgiler ve özet istatistikler
2. **Disiplin Kayıtlarım**: Tüm kayıtlarınızı görüntüleme
3. **Profil**: Bilgilerinizi görüntüleme ve şifre değiştirme

## 📁 Proje Yapısı

```
ogrenci-disiplin-sistemi/
├── config/
│   └── config.php              # Veritabanı yapılandırması
├── src/
│   ├── models/                 # Model sınıfları (OOP)
│   │   ├── Database.php        # Veritabanı bağlantısı
│   │   ├── User.php            # Kullanıcı yönetimi
│   │   ├── Student.php         # Öğrenci yönetimi
│   │   └── DisciplineRecord.php # Disiplin kayıtları
│   ├── views/                  # Görünüm dosyaları
│   │   └── layouts/
│   │       └── main.php        # Ana layout şablonu
│   └── helpers.php             # Yardımcı fonksiyonlar
├── public/                     # Sayfa dosyaları
│   ├── login.php               # Giriş sayfası
│   ├── dashboard.php           # Anasayfa
│   ├── profile.php             # Profil sayfası
│   ├── records.php             # Kayıt yönetimi
│   ├── admin/                  # Admin sayfaları
│   │   ├── students.php
│   │   ├── teachers.php
│   │   └── reports.php
│   └── student/                # Öğrenci sayfaları
│       └── records.php
├── assets/                     # Statik dosyalar
│   ├── css/
│   ├── js/
│   └── images/
├── index.php                   # Ana giriş noktası
├── database.sql                # Veritabanı şeması
├── config-sample.php           # Örnek yapılandırma
├── .htaccess                   # Apache yapılandırması
├── .gitignore                  # Git ignore dosyası
└── README.md                   # Bu dosya
```

## 🔒 Güvenlik

### Uygulanan Güvenlik Önlemleri
- ✅ Parolalar bcrypt ile hash'lenerek saklanır
- ✅ Tüm veritabanı sorguları prepared statements kullanır
- ✅ Kullanıcı girdileri XSS'e karşı temizlenir (htmlspecialchars)
- ✅ CSRF token koruması
- ✅ Session güvenliği
- ✅ Rol tabanlı erişim kontrolü
- ✅ .htaccess ile config dosyaları koruması

### Güvenlik Tavsiyeleri
1. İlk kurulumdan sonra varsayılan şifreleri değiştirin
2. Canlı ortamda `config.php` dosyasındaki hata raporlamayı kapatın
3. Düzenli olarak veritabanı yedeği alın
4. SSL/TLS sertifikası kullanın (HTTPS)
5. Güçlü şifreler kullanın (en az 8 karakter, harf, rakam, özel karakter)

## 📚 Veritabanı Şeması

### Tablolar
- **users**: Yönetici ve öğretmen bilgileri
- **students**: Öğrenci bilgileri
- **discipline_records**: Disiplin kayıtları
- **sessions**: Oturum bilgileri (isteğe bağlı)

Detaylı şema için `database.sql` dosyasına bakınız.

## 🤝 Katkıda Bulunma

Katkılarınızı bekliyoruz! Lütfen a��ağıdaki adımları izleyerek katkıda bulunun:

1. Projeyi fork edin
2. Yeni bir branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some AmazingFeature'`)
4. Branch'i push edin (`git push origin feature/AmazingFeature`)
5. Pull Request açın

## 🐛 Hata Raporlama

Hata bulduysanız, lütfen [Issues](https://github.com/alimustpdr/ogrenci-disiplin-sistemi/issues) bölümünde bir issue açın.

## 📝 Lisans

Bu proje [MIT](LICENSE) lisansı altında lisanslanmıştır.

## 🔄 Gelecek Özellikler

- [ ] E-posta bildirimleri
- [ ] SMS entegrasyonu
- [ ] Toplu öğrenci içe aktarma (Excel/CSV)
- [ ] Gelişmiş filtreleme ve grafik raporları
- [ ] Mobil uygulama
- [ ] API desteği
- [ ] Çoklu dil desteği

## 👨‍💻 Yazar

**Ali Mustafa Pdr**
- GitHub: [@alimustpdr](https://github.com/alimustpdr)

## 📞 İletişim

Sorularınız veya önerileriniz için benimle iletişime geçebilirsiniz.

## 🙏 Teşekkürler

Bu projeyi kullandığınız için teşekkür ederiz. İyi kullanımlar!

---

**Son Güncelleme**: 2025-12-24

**Versiyon**: 1.0.0


