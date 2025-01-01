<?php

require_once '../Database/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documentId = $_POST['id'] ?? null;
    if (!$documentId) {
        echo json_encode(["success" => false, "message" => "Document ID is missing."]);
        exit;
    }

    $db = (new Database())->connect();
    $stmt = $db->prepare("UPDATE user_documents SET status = 'Approved' WHERE id = :id");
    $stmt->bindParam(':id', $documentId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Document approved successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Document not found or already approved."]);
    }
}

