<?php
// Veritabanı bağlantı bilgileri
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db"; // Kullanıcı bilgilerini çekmek için 'user_db' veritabanını kullanıyoruz

// Bağlantı oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı başarısız: " . $conn->connect_error);
}

// Kullanıcı adını al
$username = isset($_GET['username']) ? $_GET['username'] : '';

if (empty($username)) {
    echo "Geçersiz kullanıcı adı.";
    exit;
}

// Tablonun var olup olmadığını kontrol edin
if (!$conn->query("DESCRIBE users_list")) {
    echo "Tablo mevcut değil veya ad hatalı.";
    exit;
}

// Kullanıcı bilgilerini almak için SQL sorgusu
$sql = "SELECT FirstName, LastName, Department, PhoneNumber, Email FROM users_list WHERE Username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<p><strong>Adı Soyadı:</strong> " . htmlspecialchars($user['FirstName']) . " " . htmlspecialchars($user['LastName']) . "</p>";
    echo "<p><strong>Birimi:</strong> " . htmlspecialchars($user['Department']) . "</p>";
    echo "<p><strong>Telefon Numarası:</strong> " . htmlspecialchars($user['PhoneNumber']) . "</p>";
    echo "<p><strong>E-posta:</strong> " . htmlspecialchars($user['Email']) . "</p>";
} else {
    echo "Kullanıcı bulunamadı.";
}

// Bağlantıyı kapat
$stmt->close();
$conn->close();
?>
