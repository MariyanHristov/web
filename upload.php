<?php
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($file['name']);

    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        echo json_encode(['status' => 'success', 'file' => $uploadFile]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>
