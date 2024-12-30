<?php
class DocumentUpload {
    private $db;
    private $userId;
    private $documentType;
    private $file;
    private $uploadDir;

    public function __construct($db, $userId, $documentType, $file) {
        $this->db = $db;
        $this->userId = $userId;
        $this->documentType = $documentType;
        $this->file = $file;

        // Update the upload directory path to point to the correct location
        $this->uploadDir = realpath(__DIR__ . '/../uploads/') . DIRECTORY_SEPARATOR;
    }

    public function uploadFile() {
        // Validate file upload
        if ($this->file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload error: ' . $this->file['error']);
        }

        $fileName = basename($this->file['name']);
        $filePath = $this->uploadDir . $fileName;

        // Validate file extension
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            throw new Exception('Invalid file type. Only PDF, JPG, JPEG, and PNG files are allowed.');
        }

        // Sanitize the file name to avoid directory traversal
        $fileName = preg_replace('/[^a-zA-Z0-9\._-]/', '_', $fileName);

        // Ensure the directory exists and is writable
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                throw new Exception('Failed to create upload directory.');
            }
        }

        if (!is_writable($this->uploadDir)) {
            throw new Exception('Upload directory is not writable.');
        }

        // Check if the file already exists
        if (file_exists($filePath)) {
            throw new Exception('File already exists.');
        }

        // Attempt to move the uploaded file to the upload directory
        if (!move_uploaded_file($this->file['tmp_name'], $filePath)) {
            throw new Exception('Failed to move uploaded file. File path: ' . $filePath);
        }

        // Insert the document information into the database
        $stmt = $this->db->prepare("INSERT INTO user_documents (user_id, document_type, file_path) VALUES (?, ?, ?)");
        $stmt->bindValue(1, $this->userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $this->documentType, PDO::PARAM_STR);
        $stmt->bindValue(3, $filePath, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception('Failed to insert document info into the database: ' . implode(", ", $stmt->errorInfo()));
        }
    }
}
