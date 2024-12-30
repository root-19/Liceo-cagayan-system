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
            
            // Fetch documents and adjust file paths if necessary
            $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($documents as &$doc) {
                // Ensure the file_path is relative to the web root
                $doc['file_path'] = '../uploads/' . basename($doc['file_path']);

            }

            return $documents;
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('Error fetching documents: ' . $e->getMessage());
            return [];
        }
    }
}

// Get user ID from query parameter
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    echo json_encode(['error' => 'Invalid user ID']);
    exit;
}

$user_id = intval($_GET['user_id']);

// Create an instance of StudentDocs and fetch the documents
$studentDocs = new StudentDocs();
$documents = $studentDocs->getDocumentsByUserId($user_id);

// Return the documents as JSON
header('Content-Type: application/json');
echo json_encode($documents);
?>
