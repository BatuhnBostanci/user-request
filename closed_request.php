<?php
session_start();

// user_db veritabanına bağlanmak için bağlantı oluşturma
$user_db_conn = new mysqli('127.0.0.1', 'root', '', 'user_db');
if ($user_db_conn->connect_error) {
    die("Bağlantı hatası: " . $user_db_conn->connect_error);
}

// Oturumdaki kullanıcı adını alın
$username = $_SESSION['username'];

// Kullanıcının departmanını almak için SQL sorgusu
$sql = "SELECT Department FROM manager_list WHERE Username = ? LIMIT 1";
$stmt = $user_db_conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$department = $user['Department'];

// user_db bağlantısını kapat
$stmt->close();
$user_db_conn->close();

// request_records veritabanına bağlanmak için config.php dosyasını include et
include 'config.php';

// Kapatılan talepleri listelemek için SQL sorgusu
$sql = "SELECT ID, Username, Department, Subject, Description, Created_At, Priority, Status, Closed_At
        FROM requests 
        WHERE Department = ? AND Status = 'Kapatıldı' 
        ORDER BY Created_At DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kapatılan Talepler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .status-kapatildi {
            background-color: red;
            color: white;
            cursor: pointer;
        }
        .clickable-row {
            cursor: pointer;
        }
        .user-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .popup-close {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            margin-top: 10px;
        }
        .popup-close:hover {
            background-color: darkred;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function goToRequestDetail(requestID) {
            window.location.href = 'request_detail_closed_manager.php?id=' + requestID;
        }

        function showUserDetails(username) {
            $.ajax({
                url: 'get_user_details.php',
                type: 'GET',
                data: { username: username },
                success: function(response) {
                    $('#user-details-content').html(response);
                    $('#user-popup').show();
                },
                error: function() {
                    $('#user-details-content').html('<p>Bir hata oluştu. Lütfen daha sonra tekrar deneyin.</p>');
                }
            });
        }

        function closePopup() {
            $('#user-popup').hide();
        }
    </script>
</head>
<body>
    <h1>Kapatılan Talepler</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Öncelik</th>
                    <th>Kullanıcı Adı</th>
                    <th>Statü</th>
                    <th>Departman</th>
                    <th>Konu</th>
                    <th>Tanım</th>
                    <th>Oluşturulma Tarihi</th>
                    <th>Kapatılma Zamanı</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['ID']) ?></td>
                        <td><?= htmlspecialchars($row['Priority']) ?></td>
                        <td>
                            <a href="javascript:void(0);" onclick="showUserDetails('<?= htmlspecialchars($row['Username']) ?>')">
                                <?= htmlspecialchars($row['Username']) ?>
                            </a>
                        </td>
                        <td class="status-kapatildi" onclick="goToRequestDetail(<?= htmlspecialchars($row['ID']) ?>)">
                            <?= htmlspecialchars($row['Status']) ?>
                        </td>
                        <td><?= htmlspecialchars($row['Department']) ?></td>
                        <td><?= htmlspecialchars($row['Subject']) ?></td>
                        <td><?= htmlspecialchars($row['Description']) ?></td>
                        <td><?= date("d.m.Y", strtotime($row['Created_At'])) ?></td>
                        <td><?= date("d.m.Y H:i", strtotime($row['Closed_At'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Henüz bir kapatılan talep bulunmamaktadır.</p>
    <?php endif; ?>

    <?php
    $stmt->close();
    $conn->close();
    ?>

    <!-- Kullanıcı Detayları Pop-up -->
    <div id="user-popup" class="user-popup">
        <div id="user-details-content">
            <!-- AJAX ile kullanıcı detayları burada gösterilecek -->
        </div>
        <button class="popup-close" onclick="closePopup()">Kapat</button>
    </div>
</body>
</html>
