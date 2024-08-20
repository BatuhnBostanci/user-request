<?php
session_start();

// Eğer logout parametresi varsa oturumu sonlandır
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Form verilerini al
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // SQL sorgusu ile kullanıcı adı ve şifreyi kontrol et
    $sql_user = "SELECT * FROM users_list WHERE username = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $input_username);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    $sql_manager = "SELECT * FROM manager_list WHERE username = ?";
    $stmt_manager = $conn->prepare($sql_manager);
    $stmt_manager->bind_param("s", $input_username);
    $stmt_manager->execute();
    $result_manager = $stmt_manager->get_result();

    if ($result_user->num_rows > 0) {
        // Kullanıcı bilgilerini al
        $row_user = $result_user->fetch_assoc();

        // Şifreyi doğrula
        if (password_verify($input_password, $row_user['password'])) {
            // Kullanıcı adını oturuma kaydet
            $_SESSION['username'] = $input_username;
            // users_view.php sayfasına yönlendirme
            header("Location: users_view.php");
            exit();
        }
    } elseif ($result_manager->num_rows > 0) {
        // Yönetici bilgilerini al
        $row_manager = $result_manager->fetch_assoc();

        // Şifreyi doğrula
        if (password_verify($input_password, $row_manager['password'])) {
            // Kullanıcı adını oturuma kaydet
            $_SESSION['username'] = $input_username;
            // manager_view.php sayfasına yönlendirme
            header("Location: manager_view.php");
            exit();
        }
    }

    // Kullanıcı adı veya şifre hatalı uyarısı
    echo "<script>alert('Kullanıcı adı veya şifre hatalıdır.');</script>";

    // Prepared statements kapatılır
    $stmt_user->close();
    $stmt_manager->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            border: none;
        }
        body {
            background: url('https://www.haldizinsaat.com.tr/dcms-sites/haldizinsaat.com.tr/uploads/2015/08/teknopark1.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
        }
        .login__forms {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 100vh;
        }
        .logo {
            margin-bottom: 1rem;
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        .logo img {
            width: 100%;
            height: auto;
            max-width: 300px;
            max-height: 150px;
            display: inline-block;
        }
        .login__box {
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 4px;
            padding: 0.875rem;
            margin-bottom: 1rem;
            width: 450px;
        }
        .login__box input {
            border: none;
            outline: none;
            padding: 0.45rem 0.875rem;
            width: 100%;
        }
        .btn-login {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #3a3a3a;
            box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
            border-radius: 4px;
            color: #fff;
            padding: 0.875rem;
            width: 10rem;
            cursor: pointer;
            margin: 0 auto; /* Butonu ortalamak için margin ekleyin */
        }
        .btn-login span {
            padding-left: 10px;
        }
        .fs-20 {
            font-size: 24px;
        }
    </style>
</head>
<body>
    <div class="login__forms">
        <div class="logo">
            <img src="https://www.tgbd.org.tr/content/upload/companies/teknoparkist-slogansiz-lo-20191101094440.jpg" alt="Logo">
        </div>
        <form method="post" action="">
            <div class="login__box">
                <ion-icon class="fs-20" name="person-outline"></ion-icon>
                <input type="text" name="username" placeholder="Kullanıcı Adı" autocomplete="off" required>
            </div>
            <div class="login__box">
                <ion-icon class="fs-20" name="lock-closed-outline"></ion-icon>
                <input type="password" name="password" placeholder="Şifre" required>
            </div>
            <button type="submit" class="btn-login">
                <ion-icon name="log-in-outline"></ion-icon>
                <span>Giriş Yap</span>
            </button>
        </form>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
