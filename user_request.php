<?php
session_start();
include 'config.php';

// Oturumdaki kullanıcı adını alın
$username = $_SESSION['username'];

// Statü filtresini alın
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'Tüm Statüler';

// SQL sorgusunu hazırlayın
$sql = "SELECT ID, Status, Department, Subject, Description, Created_At FROM requests WHERE Username = ?";

// Eğer statü filtresi uygulanmışsa sorguya ekleyin
if ($statusFilter !== 'Tüm Statüler') {
    $sql .= " AND Status = ?";
}

$sql .= " ORDER BY Created_At DESC"; // Son eklenen en üstte görünecek şekilde sıralayın

$stmt = $conn->prepare($sql);
if ($statusFilter !== 'Tüm Statüler') {
    $stmt->bind_param("ss", $username, $statusFilter);
} else {
    $stmt->bind_param("s", $username);
}
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taleplerim</title>
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
        .status-devam {
            background-color: green;
            color: white;
            cursor: pointer;
        }
        .status-kapatildi {
            background-color: red;
            color: white;
        }
    </style>
    <script>
        function goToRequestDetail(requestID) {
            window.location.href = 'request_detail_users.php?id=' + requestID;
        }
    </script>
</head>
<body>
    <h1>Taleplerim</h1>

    <form method="GET" action="user_request.php">
        <label for="status">Statüye Göre Filtrele:</label>
        <select name="status" id="status" onchange="this.form.submit()">
            <option value="Tüm Statüler" <?= $statusFilter === 'Tüm Statüler' ? 'selected' : '' ?>>Tüm Statüler</option>
            <option value="Devam Ediyor" <?= $statusFilter === 'Devam Ediyor' ? 'selected' : '' ?>>Devam Ediyor</option>
            <option value="Kapatıldı" <?= $statusFilter === 'Kapatıldı' ? 'selected' : '' ?>>Kapatıldı</option>
        </select>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Statü</th>
                    <th>Departman</th>
                    <th>Konu</th>
                    <th>Tanım</th>
                    <th>Oluşturulma Tarihi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr onclick="goToRequestDetail(<?= htmlspecialchars($row['ID']) ?>)">
                        <td><?= htmlspecialchars($row['ID']) ?></td>
                        <td class="<?= $row['Status'] === 'Devam Ediyor' ? 'status-devam' : 'status-kapatildi' ?>">
                            <?= htmlspecialchars($row['Status']) ?>
                        </td>
                        <td><?= htmlspecialchars($row['Department']) ?></td>
                        <td><?= htmlspecialchars($row['Subject']) ?></td>
                        <td><?= htmlspecialchars($row['Description']) ?></td>
                        <td><?= date("d.m.Y H:i", strtotime($row['Created_At'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Henüz bir talep bulunmamaktadır.</p>
    <?php endif; ?>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
