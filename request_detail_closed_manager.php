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
        WHERE ID = ? AND Status = 'Kapatıldı'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $requestID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Talep bulunamadı veya talep kapatılmamış.');
}

$request = $result->fetch_assoc();

$stmt->close();
$conn->close();

// Dosya türünü kontrol etmek için bir yardımcı fonksiyon
function isImage($filePath) {
    $imageFileTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    return in_array($fileExtension, $imageFileTypes);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talep Detayları - Kapatıldı</title>
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
            <span><?= nl2br(htmlspecialchars($request['Description'])) ?></span>
        </div>
        <div class="detail-item">
            <label>Oluşturulma Tarihi:</label>
            <span><?= date("d.m.Y", strtotime($request['Created_At'])) ?></span>
        </div>
        <div class="detail-item">
            <label>Öncelik:</label>
            <span><?= htmlspecialchars($request['Priority']) ?></span>
        </div>
        <div class="detail-item">
            <label>Statü:</label>
            <span><?= htmlspecialchars($request['Status']) ?></span>
        </div>
        <div class="detail-item">
            <label>Kapatılma Zamanı:</label>
            <span><?= date("d.m.Y H:i", strtotime($request['Closed_At'])) ?></span>
        </div>
        <div class="detail-item">
            <label>Çözüm Açıklaması:</label>
            <span><?= nl2br(htmlspecialchars($request['Solution_Description'])) ?></span>
        </div>
        <?php if (!empty($request['File_Path'])): ?>
            <div class="detail-item">
                <label>Ekli Dosya:</label>
                <?php if (isImage($request['File_Path'])): ?>
                    <img src="<?= htmlspecialchars($request['File_Path']) ?>" alt="Ekli Görsel">
                <?php else: ?>
                    <span><a href="<?= htmlspecialchars($request['File_Path']) ?>" download>Dosyayı İndir</a></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
