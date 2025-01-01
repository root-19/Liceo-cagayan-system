<?php
require_once '../Database/Database.php';

class StudentDocs {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function getDocumentsByUserId($user_id) {
        $stmt = $this->db->prepare("SELECT id, document_type, file_path, upload_date, status FROM user_documents WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($documents as &$doc) {
            // Ensure the file_path is relative to the uploads directory
            $doc['file_path'] = '../uploads/' . basename($doc['file_path']);
        }

        return $documents;
    }
}

if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    echo json_encode(['error' => 'Invalid user ID']);
    exit;
}

$studentDocs = new StudentDocs();
$documents = $studentDocs->getDocumentsByUserId((int)$_GET['user_id']);
header('Content-Type: application/json');
echo json_encode($documents);
