<?php
require_once '../Database/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = (new Database())->connect();

        // Get the `document_type` and `user_id` from POST request
        $documentType = $_POST['document_type'];
        $userId = $_POST['user_id'];

        // Log received data for debugging
        error_log("Document Type: " . $documentType);
        error_log("User ID: " . $userId);

        // Update the status to 'Approved' for the given document_type and user_id
        $query = "UPDATE user_documents 
                  SET status = 'Approved' 
                  WHERE document_type = :document_type AND user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':document_type', $documentType, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => "Document approved successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "No document found to update."]);
        }
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
