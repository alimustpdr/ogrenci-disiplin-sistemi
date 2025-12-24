# 🎓 ODTS - Öğrenci Disiplin Takip Sistemi

## 📋 Proje Açıklaması

ODTS (Öğrenci Disiplin Takip Sistemi), eğitim kurumlarının öğrenci davranışlarını takip etmek, disiplin kurallarını yönetmek ve uyarı işlemlerini digitalize etmek için tasarlanmış modern bir web uygulamasıdır.

InfinityFree ücretsiz hosting üzerinde sorunsuz çalışacak şekilde optimize edilmiş, cookie tabanlı oturum yönetimi kullanan, tamamen PHP ve MySQL ile geliştirilmiş bir sistemdir.

## ✨ Özellikler

- 👥 **Kullanıcı Yönetimi**: Admin, müdür yardımcısı ve öğretmen rollerine göre erişim kontrolü
- 📝 **Öğrenci Yönetimi**: Öğrenci ekleme, düzenleme, silme ve sınıfa atama
- ⚠️ **Uyarı Sistemi**: 1-5 arası seviye ile uyarı kaydı ve takibi
- 🏫 **Sınıf Yönetimi**: Sınıf oluşturma ve danışman öğretmen atama
- 📊 **Raporlama**: Detaylı raporlar ve Excel'e aktarma özelliği
- 🎨 **Modern Tasarım**: Gradient mor-mavi renk teması ile responsive arayüz
- 🔐 **Güvenlik**: Cookie tabanlı güvenli oturum yönetimi
- 🌐 **UTF-8 Desteği**: Tam Türkçe karakter desteği

## 🛠️ Teknoloji Stack

- **Dil**: PHP 7.4+
- **Veritabanı**: MySQL 5.7+
- **Oturum Yönetimi**: Cookie (session_start() kullanılmaz)
- **Karakter Seti**: UTF-8 (utf8mb4)
- **Hosting**: InfinityFree uyumlu

## 📦 Kurulum

### Gereksinimler
- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Web sunucusu (Apache/Nginx)
- InfinityFree veya başka bir web hosting

### Kurulum Adımları

1. **Dosyaları Yükleyin**
   - Tüm dosyaları hosting'inizin public_html veya htdocs klasörüne yükleyin

2. **Kurulum Sihirbazını Çalıştırın**
   - Tarayıcınızda `https://siteniz.com/install/index.php` adresine gidin
   - Veritabanı bilgilerinizi girin:
     - Veritabanı Sunucusu: `localhost`
     - Veritabanı Kullanıcı Adı: (hosting panelinden alın)
     - Veritabanı Şifresi: (hosting panelinden alın)
     - Veritabanı Adı: (hosting panelinden alın veya oluşturun)
     - Site URL: `https://siteniz.com`
   - "Kuruluma Başla" butonuna tıklayın

3. **Admin Hesabı Oluşturun**
   - Kurulum tamamlandıktan sonra admin hesap bilgilerini girin
   - Kullanıcı adı, şifre, ad soyad ve e-posta bilgilerini doldurun
   - "Admin Hesabı Oluştur" butonuna tıklayın

4. **Sisteme Giriş Yapın**
   - Kurulum tamamlandığında otomatik olarak login sayfasına yönlendirileceksiniz
   - Oluşturduğunuz admin bilgileri ile giriş yapın

## 🚀 Kullanım

### İlk Giriş
- **URL**: `https://siteniz.com/login.php`
- **Kullanıcı Adı**: Kurulumda oluşturduğunuz admin kullanıcı adı
- **Şifre**: Kurulumda oluşturduğunuz admin şifresi

### Ana Özellikler

#### 📊 Ana Panel (Dashboard)
- Toplam öğrenci, uyarı, sınıf ve kullanıcı sayılarını görüntüleyin
- Son eklenen uyarıları takip edin
- Sistem geneli istatistikleri görüntüleyin

#### 👥 Öğrenci Yönetimi
- Yeni öğrenci ekleyin (öğrenci no, ad, soyad, sınıf, veli bilgileri)
- Öğrenci bilgilerini düzenleyin
- Öğrencileri arayın ve filtreleyin
- Öğrencileri silin

#### ⚠️ Uyarı Sistemi
- 5 seviyeli uyarı sistemi:
  - Seviye 1: Hafif
  - Seviye 2: Orta
  - Seviye 3: Ciddi
  - Seviye 4: Çok Ciddi
  - Seviye 5: Kritik
- Uyarı kategorileri: Davranış, Devamsızlık, Kıyafet, Ders Düzeni, Diğer
- Uyarı ekleyin, düzenleyin, silin
- Öğrenci, kategori ve seviyeye göre filtreleyin

#### 🏫 Sınıf Yönetimi
- Yeni sınıf oluşturun (9-A, 10-B gibi)
- Danışman öğretmen atayın
- Sınıf bilgilerini düzenleyin
- Sınıflara kayıtlı öğrenci sayısını görün

#### 👤 Kullanıcı Yönetimi (Sadece Admin)
- Yeni kullanıcı ekleyin
- Roller: Admin, Müdür Yardımcısı, Öğretmen
- Kullanıcı bilgilerini düzenleyin
- Kullanıcıları silin

#### 📈 Raporlar
- Öğrenci bazlı raporlar
- Sınıf bazlı raporlar
- Kategori bazlı raporlar
- Tarih aralığı ile filtreleme
- Excel'e aktarma özelliği
- Grafik ve istatistikler

#### ⚙️ Ayarlar (Sadece Admin)
- Okul adı ve iletişim bilgileri
- Tema rengi seçimi
- Sistem bilgileri
- Veritabanı istatistikleri
- Son aktiviteleri görüntüleme

## 📁 Proje Yapısı

```
odts/
├── config/
│   └── config.php          # Veritabanı ve sistem ayarları
├── includes/
│   ├── db.php              # Veritabanı bağlantı fonksiyonları
│   ├── auth.php            # Cookie tabanlı oturum yönetimi
│   ├── functions.php       # Yardımcı fonksiyonlar
│   ├── header.php          # Sayfa üst kısmı şablonu
│   └── footer.php          # Sayfa alt kısmı şablonu
├── install/
│   └── index.php           # Kurulum sihirbazı
├── assets/
│   ├── css/                # CSS dosyaları
│   ├── js/                 # JavaScript dosyaları
│   └── img/                # Resim dosyaları
├── .htaccess               # Apache yapılandırması
├── index.php               # Ana sayfa (yönlendirme)
├── login.php               # Giriş sayfası
├── logout.php              # Çıkış işlemi
├── dashboard.php           # Ana panel
├── students.php            # Öğrenci yönetimi
├── warnings.php            # Uyarı yönetimi
├── classes.php             # Sınıf yönetimi
├── users.php               # Kullanıcı yönetimi
├── reports.php             # Raporlar
├── settings.php            # Sistem ayarları
└── README.md               # Bu dosya
```

## 🗄️ Veritabanı Yapısı

- **users**: Kullanıcı bilgileri ve oturum tokenleri
- **roles**: Rol yetkileri
- **students**: Öğrenci bilgileri
- **classes**: Sınıf bilgileri
- **warnings**: Uyarı kayıtları
- **warning_categories**: Uyarı kategorileri
- **settings**: Sistem ayarları
- **activity_logs**: Kullanıcı aktivite logları

## 🔒 Güvenlik

- ✅ Cookie tabanlı güvenli oturum yönetimi
- ✅ SQL injection koruması
- ✅ XSS koruması
- ✅ Password hashing (bcrypt)
- ✅ Rol tabanlı erişim kontrolü
- ✅ Token tabanlı doğrulama
- ✅ Aktivite logları

## 🌐 InfinityFree Uyumluluk

Bu sistem özellikle InfinityFree ücretsiz hosting için optimize edilmiştir:
- ❌ `session_start()` kullanılmaz (InfinityFree'de sorun yaratır)
- ✅ Cookie tabanlı oturum yönetimi
- ✅ UTF-8 Türkçe karakter desteği
- ✅ MySQL veritabanı uyumlu
- ✅ .htaccess yapılandırması

## 🤝 Katkıda Bulunma

Katkılarınızı bekliyoruz! Lütfen aşağıdaki adımları izleyerek katkıda bulunun:

1. Projeyi fork edin
2. Yeni bir branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some AmazingFeature'`)
4. Branch'i push edin (`git push origin feature/AmazingFeature`)
5. Pull Request açın

## 🐛 Hata Raporlama

Hata bulduysanız, lütfen [Issues](https://github.com/alimustpdr/ogrenci-disiplin-sistemi/issues) bölümünde bir issue açın.

## 📝 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 👨‍💻 Yazar

**Ali Mustafa Pdr**
- GitHub: [@alimustpdr](https://github.com/alimustpdr)

## 📞 İletişim

Sorularınız veya önerileriniz için benimle iletişime geçebilirsiniz.

---

**Versiyon**: 1.0.0  
**Son Güncelleme**: 2025-12-24  
**Domain**: gulayazim.gt.tc
