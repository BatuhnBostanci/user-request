<?php
session_start();
include 'config.php';

// Talep ID'sini URL'den alın
$requestID = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($requestID <= 0) {
    die('Geçersiz talep ID.');
}

// Talep verilerini almak için SQL sorgusu
$sql = "SELECT ID, Username, Department, Subject, Description, Created_At, Priority, Status, File_Path, Closed_At, Solution_Description 
        FROM requests 
        WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $requestID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Talep bulunamadı.');
}

$request = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talep Detayları</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .detail-box {
            border: 1px solid #ddd;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .detail-box h2 {
            margin-top: 0;
        }
        .detail-item {
            margin-bottom: 15px;
        }
        .detail-item label {
            font-weight: bold;
        }
        .detail-item span, .detail-item img {
            display: block;
            margin-top: 5px;
        }
        .detail-item img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="detail-box">
        <h2>Talep Detayları</h2>
        <div class="detail-item">
            <label>ID:</label>
            <span><?= htmlspecialchars($request['ID']) ?></span>
        </div>
        <div class="detail-item">
            <label>Kullanıcı Adı:</label>
            <span><?= htmlspecialchars($request['Username']) ?></span>
        </div>
        <div class="detail-item">
            <label>Departman:</label>
            <span><?= htmlspecialchars($request['Department']) ?></span>
        </div>
        <div class="detail-item">
            <label>Konu:</label>
            <span><?= htmlspecialchars($request['Subject']) ?></span>
        </div>
        <div class="detail-item">
            <label>Tanım:</label>
            <span><?= htmlspecialchars($request['Description']) ?></span>
        </div>
        <div class="detail-item">
            <label>Oluşturulma Tarihi:</label>
            <span><?= date("d.m.Y H:i", strtotime($request['Created_At'])) ?></span>
        </div>
        <div class="detail-item">
            <label>Öncelik:</label>
            <span><?= htmlspecialchars($request['Priority']) ?></span>
        </div>
        <div class="detail-item">
            <label>Statü:</label>
            <span><?= htmlspecialchars($request['Status']) ?></span>
        </div>

        <?php if ($request['Status'] === 'Kapatıldı'): ?>
            <div class="detail-item">
                <label>Kapatılma Tarihi:</label>
                <span><?= date("d.m.Y H:i", strtotime($request['Closed_At'])) ?></span>
            </div>
            <div class="detail-item">
                <label>Kapatılma Açıklaması:</label>
                <span><?= htmlspecialchars($request['Solution_Description']) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($request['File_Path'])): ?>
        <div class="detail-item">
            <label>Görsel:</label>
            <img src="<?= htmlspecialchars($request['File_Path']) ?>" alt="Talep Görseli">
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
