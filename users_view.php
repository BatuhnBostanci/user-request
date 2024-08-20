<?php
session_start();

// Kullanıcı oturumunu kontrol et
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Kullanıcı adı oturumdan alınır
$username = $_SESSION['username'];

// Çıkış yapma
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User View</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('https://static.techinside.com/uploads/2022/06/teknopark-istanbul-min.jpeg');
            background-size: cover;
            background-position: center;
        }
        .container {
            text-align: center;
        }
        .header {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .header a {
            text-decoration: none;
            color: blue;
            margin-left: 10px;
        }
        .buttons {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 60px; /* Butonlar arasındaki boşluk */
            margin-top: 50px;
        }
        .buttons a {
            background-color: #4CAF50; /* Yeşil buton rengi */
            padding: 20px 50px;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 24px;
            border: 3px solid #2596be;
            display: inline-block;
            transition: background-color 0.3s ease, transform 0.3s ease;
            width: 300px; /* Buton genişliği */
            text-align: center;
        }
        .buttons a:hover {
            background-color: #45a049; /* Hover rengi */
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="header">
        <span>Username: <?php echo $username; ?></span>
        <a href="index.php?logout=true">Log Out</a>
    </div>
    <div class="container">
        <div class="buttons">
            <a href="request_form.php" target="_blank">TALEP OLUŞTUR</a>
            <a href="user_request.php" target="_blank">TALEPLERİM</a>
        </div>
    </div>
</body>
</html>
