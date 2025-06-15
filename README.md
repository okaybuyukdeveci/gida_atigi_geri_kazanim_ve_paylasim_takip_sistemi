# 🥫 Gıda Atığı Geri Kazanım ve Paylaşım Takip Sistemi


🎯 Amaç: Toplumsal dayanışmayı güçlendirmek ve gıda israfını en aza indirmek.

Bu web tabanlı sistem sayesinde;

🏪 Marketler, 🍽️ restoranlar veya 👤 bireyler ellerindeki fazla gıda ürünlerini kolayca sisteme ekleyebilir.

🤝 İhtiyaç sahipleri ise bu ürünleri görüntüleyip, hızlı ve adil bir şekilde talepte bulunabilir.

✅ Rol bazlı giriş sistemi sayesinde her kullanıcı, kendine özel bir panele sahiptir.

✅ Tüm işlemler kayıt altında tutulur ve takip edilebilir.

✅ Gıda israfı azalırken, sosyal yardımlaşma artar!



---

## ▶️ Tanıtım Videosu

📺 Projenin youtube tanıtım linki

https://youtu.be/IAoDbaJ5tcM

## ⚙️ Kullanılan Teknolojiler

- **PHP** 
- **MySQL + PDO**
- **Bootstrap 5**
- **HTML/CSS/JS**
- **Oturum yönetimi:** PHP `$_SESSION`
- **Parola güvenliği:** `password_hash()`

---

## 🧩 Özellikler

### 👥 Kullanıcı Rolleri

- **Provider (Sağlayıcı):** Ürün ekler, talepleri onaylar
- **Recipient (İhtiyaç Sahibi):** Ürün talep eder, iptal edebilir

### 🔐 Güvenlik

- Şifreler hash’lenir
- SQL Injection'a karşı `PDO` kullanımı
- Session güvenliği: `session_regenerate_id(true)`
- `htaccess` kullanılmaz (proje kurallarına uygun)

---

## Giriş Ekranı

![Ekran görüntüsü 2025-06-15 162658](https://github.com/user-attachments/assets/71a1b12a-c70e-4eaa-bcc0-3820aaf53725)

## Kayıt Ekranı

![4](https://github.com/user-attachments/assets/4d357dc4-d724-4d9b-9c2f-b1fcfcc2e148)

## Panel Ekranı

![1](https://github.com/user-attachments/assets/feb5457a-cf81-491d-ac6c-f0fb2340e87c)


## İhtiyaç Sahibi(Kullanıcı) Panel Ekranı

![Ekran görüntüsü 2025-06-15 162425](https://github.com/user-attachments/assets/f82966a3-4e31-4662-babf-f404a297bcd3)


## Sağlayıcı Panel Ekranı

![2](https://github.com/user-attachments/assets/1521fc1c-e777-4e1e-bc7a-58d1bc09b01e)

---

## 👥 Kimler Kullanabilir?

Bu uygulama özellikle aşağıdaki kişiler ve kurumlar için faydalı olabilir:

Gıda bağışında bulunmak isteyen bireyler ve işletmeler (sağlayıcılar)

Gıdaya ihtiyacı olan bireyler veya yardım kuruluşları (alıcılar)

Belediyeler, STK'lar ve sosyal sorumluluk projeleri yürüten organizasyonlar

Gıda israfını azaltmak isteyen çevreci girişimler

---

## ❓ Neden Bu Uygulama?

Gıda israfı, dünya genelinde ciddi bir problemdir. Aynı zamanda birçok insan temel gıda ihtiyacını karşılamakta zorlanmaktadır. Bu sistem, bu iki sorunu bir araya getirerek çözüm üretmeyi hedefler.

Uygulamanın sunduğu avantajlar:

✅ Fazla gıdası olanlar, ellerindeki ürünleri kolayca sisteme ekleyebilir.

✅ İhtiyaç sahipleri, uygun ürünleri görebilir ve kolayca talepte bulunabilir.

✅ Tüm süreç dijital olarak takip edilebilir: Kim, ne zaman, ne talep etti, onaylandı mı gibi bilgiler kayıt altındadır.

✅ Rol bazlı panel yapısı sayesinde hem sağlayıcı hem de alıcılar kendilerine özel arayüzle çalışır.

✅ Gıda israfını azaltırken, sosyal yardımlaşmayı artırır.

---

## Veritabanı Yapısı (Özet)

| Alan         | Tür          | Açıklama                                      |
| ------------ | ------------ | --------------------------------------------- |
| `id`         | INT (PK, AI) | Kullanıcı ID                                  |
| `username`   | VARCHAR(100) | Kullanıcı adı                                 |
| `email`      | VARCHAR(255) | E-posta adresi                                |
| `password`   | VARCHAR(255) | Şifre (bcrypt ile hashlenmiş)                 |
| `role`       | ENUM         | Kullanıcı rolü (`provider`, `recipient`)      |
| `location`   | VARCHAR(255) | Kullanıcının konumu                           |
| `created_at` | TIMESTAMP    | Kayıt tarihi (varsayılan `CURRENT_TIMESTAMP`) |


---


| Alan          | Tür          | Açıklama                                        |
| ------------- | ------------ | ----------------------------------------------- |
| `id`          | INT (PK, AI) | Gıda ürünü ID                                   |
| `user_id`     | INT (FK)     | Ürünü ekleyen kullanıcının ID’si                |
| `name`        | VARCHAR(255) | Ürün adı                                        |
| `quantity`    | INT          | Miktar                                          |
| `expiry_date` | DATE         | Son kullanım tarihi                             |
| `location`    | VARCHAR(255) | Ürün konumu                                     |
| `description` | TEXT         | Açıklama                                        |
| `created_at`  | TIMESTAMP    | Eklenme zamanı (varsayılan `CURRENT_TIMESTAMP`) |


---


| Alan         | Tür          | Açıklama                                                       |
| ------------ | ------------ | -------------------------------------------------------------- |
| `id`         | INT (PK, AI) | Talep ID                                                       |
| `food_id`    | INT (FK)     | Talep edilen ürünün ID’si                                      |
| `user_id`    | INT (FK)     | Talep eden kullanıcının ID’si                                  |
| `status`     | ENUM         | Talep durumu (`pending`, `approved`, `cancelled`, `delivered`) |
| `created_at` | TIMESTAMP    | Talep oluşturulma tarihi (varsayılan `CURRENT_TIMESTAMP`)      |
