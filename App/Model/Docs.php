<?php
session_start();
require_once '../Database/Database.php';
require_once '../Model/DocumentUpload.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $userId = $_SESSION['user_id'];
    $documentType = $_POST['document_type'];
    $file = $_FILES['file'];

    try {
        // Create a new instance of the DocumentUpload class
        $db = (new Database())->connect();
        $upload = new DocumentUpload($db, $userId, $documentType, $file);

        // Call the uploadFile method to handle the upload and database insertion
        if ($upload->uploadFile()) {
            $response = ['success' => true, 'message' => 'Document uploaded successfully!'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to upload the document.'];
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid request or no file uploaded.'];
}

echo json_encode($response);
?>
