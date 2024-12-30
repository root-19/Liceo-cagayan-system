<?php
// Include database connection
require_once '../Database/Database.php';

class StudentDocs {
    private $db;
    
    // Constructor to initialize the database connection
    public function __construct() {
        $this->db = (new Database())->connect();
    }

    // Method to fetch user documents based on user ID
    public function getDocumentsByUserId($user_id) {
        try {
            $stmt = $this->db->prepare("SELECT document_type, file_path, upload_date FROM user_documents WHERE user_id = ?");
            $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}

// Get user ID from query parameter
if (!isset($_GET['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = intval($_GET['user_id']);

// Create an instance of StudentDocs and fetch the documents
$studentDocs = new StudentDocs();
$documents = $studentDocs->getDocumentsByUserId($user_id);

// Return the documents as JSON
echo json_encode($documents);
?>
