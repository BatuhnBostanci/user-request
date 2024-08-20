<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $department = $_POST['department'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $priority = $_POST['priority']; // Yeni eklenen önem derecesi alanı
    $status = 'Devam Ediyor';
    $file_path = null;

    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_name = basename($_FILES['file']['name']);
        $target_directory = "uploads/";
        $target_file = $target_directory . uniqid() . "-" . $file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $file_path = $target_file;
        } else {
            echo json_encode(['success' => false, 'message' => 'Dosya yüklenirken bir hata oluştu.']);
            exit();
        }
    }

    // SQL sorgusunu önem derecesi alanını içerecek şekilde güncelleyin
    $sql = "INSERT INTO requests (Username, Department, Subject, Description, File_Path, Status, Priority) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $username, $department, $subject, $description, $file_path, $status, $priority);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Talep kaydedilirken bir hata oluştu.']);
    }

    $stmt->close();
    $conn->close();
}
?>
