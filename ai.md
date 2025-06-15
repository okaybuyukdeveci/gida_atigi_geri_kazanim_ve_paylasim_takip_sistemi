🟩 **Soru 1:**  
Giriş yapan kullanıcıyı rolüne göre (provider ya da recipient) doğru panele yönlendirmek istiyorum. Bunu PHP ile nasıl yapabilirim?

🧠 **ChatGPT Yanıtı:**  
Kullanıcı girişinden sonra `$_SESSION['role']` değerine göre yönlendirme yapabilirsin:

```php
if ($_SESSION['role'] === 'provider') {
    header("Location: provider_dashboard.php");
} elseif ($_SESSION['role'] === 'recipient') {
    header("Location: user_dashboard.php");
} else {
    echo "Geçersiz rol!";
}
exit;


🟩 **Soru 2:**  
Kullanıcının veritabanına kayıt olurken şifresini güvenli şekilde saklamak istiyorum. PHP'de nasıl yapmalıyım?

🧠 **ChatGPT Yanıtı:**  
PHP'de `password_hash()` fonksiyonunu kullanarak güvenli bir şifre saklama işlemi gerçekleştirebilirsin. Örnek kayıt işlemi:

```php
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username, password, email, role, location) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$username, $hashed_password, $email, $role, $location]);

🟩 **Soru 3:**  
Formdan gelen verileri hem boşluklardan temizlemek hem de XSS saldırılarına karşı güvenli şekilde kaydetmek için ne yapmalıyım?

🧠 **ChatGPT Yanıtı:**  
Kullanıcı girişlerini veritabanına kaydetmeden önce `trim()` ile boşlukları temizleyebilir, HTML çıktı verirken de `htmlspecialchars()` ile XSS’e karşı korunabilirsin.

```php
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Geçersiz e-posta!";
    exit;
}

// HTML çıktısı
echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8');


---

```markdown
🟩 **Soru 4:**  
Bir kullanıcının sadece kendi eklediği ürünü silmesini nasıl sağlarız?

🧠 **ChatGPT Yanıtı:**  
Veritabanında silme işlemi yapılırken `user_id` kontrolü de yapılmalıdır. Böylece kullanıcı sadece kendine ait verileri silebilir:

```php
$sql = "DELETE FROM food_items WHERE id = :id AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id' => $food_id,
    ':user_id' => $_SESSION['user_id']
]);


# 🧠 Yapay Zeka Destekli Teknik Danışmanlık (AI.md)

Bu projeyi geliştirirken çeşitli teknik sorunları çözmek ve yapıyı daha güvenli hâle getirmek amacıyla yapay zekadan destek alınmıştır. Aşağıda bazı soru–cevap örnekleri yer almaktadır.

---

🟩 **Soru 5:**  
`request_food.php` dosyasında "Talep Et" butonuna basınca hiçbir şey olmuyor. Hata da gözükmüyor. Nerede yanlış yapıyor olabilirim?

🧠 **ChatGPT Yanıtı:**  
Muhtemelen form gönderimi `GET` parametresi ile yapılmış ama `POST` kontrolü yapılmaya çalışılıyor. Ayrıca `<form>` içinde `food_id` gizli alan olarak gönderilmeli ve `request_food.php` dosyasında bu `POST` değeri kontrol edilmeli.  
Form `method="POST"` olarak ayarlanmalı ve `food_id` kontrolü hem `$_GET` hem `$_POST` ile desteklenebilir.

---

🟩 **Soru 6:**  
Panel butonuna tıklanınca kullanıcı rolüne göre `provider` ya da `recipient` dashboard’a yönlenmesini istiyorum. Ama şu an `pages/pages/index.php` açılıyor.

🧠 **ChatGPT Yanıtı:**  
Navbar’daki `href` yapısında `BASE_URL . 'pages/'` kullanılmış ama `header.php` zaten `pages/` dizininde olduğu için fazladan `pages/` ekleniyor.  
Aşağıdaki gibi kullanılmalı:

```php
<a href="<?php echo BASE_URL; ?><?php echo $_SESSION['role'] === 'provider' ? 'provider_dashboard.php' : 'user_dashboard.php'; ?>">

