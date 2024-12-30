<?php

require_once '../../Database/Database.php';

if (isset($_SESSION['user_id'])) {
    echo '<p class="text-red-500">Please log in as admin to view student documents.</p>';
    exit;
}

/**
 * Class User
 * Handles operations related to users
 */
class UserModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllStudents() {
        $query = "SELECT id, name, surname, email FROM users WHERE role = 'user'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

/**
 * Class StudentDocuments
 * Handles operations related to student documents
 */
class StudentDocuments {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getDocumentsByUserId($userId) {
        $query = "SELECT id, document_type, file_path, upload_date FROM user_documents WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

try {
    // Instantiate database and models
    $db = (new Database())->connect();
    $userModel = new UserModel($db);

    // Fetch all students
    $students = $userModel->getAllStudents();
} catch (Exception $e) {
    echo '<p class="text-red-500">Error initializing: ' . $e->getMessage() . '</p>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Student Documents</title>
</head>
<body>
    <?php include_once "./layout/sidebar.php"; ?>

    <!-- Main Content -->
    <div class="p-6 ml-60">
        <h1 class="text-3xl font-bold mb-6">Student Documents</h1>
        
        <!-- Student List -->
        <div class="overflow-x-auto bg-white rounded-lg shadow-lg p-6">
            <table class="min-w-full border-collapse border border-gray-200 text-left">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 border border-gray-300">Name</th>
                        <th class="px-4 py-2 border border-gray-300">Surname</th>
                        <th class="px-4 py-2 border border-gray-300">Email</th>
                        <th class="px-4 py-2 border border-gray-300">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)) : ?>
                        <?php foreach ($students as $student) : ?>
                            <tr>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($student['name']); ?></td>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($student['surname']); ?></td>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($student['email']); ?></td>
                                <td class="px-4 py-2 border text-center">
                                    <button 
                                        style="background-color: var(--maroon);"
                                        class="text-white px-4 py-2 rounded" 
                                        onclick="showDocuments(<?php echo $student['id']; ?>, '<?php echo htmlspecialchars($student['name']); ?> <?php echo htmlspecialchars($student['surname']); ?>')">
                                        View Documents
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-center text-gray-500">No students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal to Show Documents -->
        <div id="documentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full p-6">
                <div class="flex justify-between items-center">
                    <h2 id="studentName" class="text-2xl font-bold"></h2>
                    <button class="text-red-500 text-2xl" onclick="closeModal()">×</button>
                </div>
                <!-- Scrollable container for documents -->
                <div id="documentsContainer" class="mt-4 space-y-4 max-h-[400px] overflow-y-auto border-t pt-4">
                    <!-- Documents will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
    function showDocuments(studentId, studentName) {
        // Display the modal
        const modal = document.getElementById('documentModal');
        modal.classList.remove('hidden');

        // Set student name
        document.getElementById('studentName').textContent = `Documents for ${studentName}`;

        // Fetch documents
        fetch(`/Model/StudentDocs.php?user_id=${studentId}`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('documentsContainer');
                container.innerHTML = ''; // Clear previous documents

                if (data.length > 0) {
                    data.forEach(doc => {
                        const docElement = `
                            <div class="border p-4 rounded-lg bg-gray-100">
                                <p class="font-medium">Type: ${doc.document_type}</p>
                                <p class="text-sm text-gray-500">Uploaded on: ${doc.upload_date}</p>
                                <img 
                                    src="/uploads/${doc.file_path}" 
                                    alt="Document Image" 
                                    class="mt-2 max-w-[80px] max-h-[80px] rounded-lg shadow-lg cursor-pointer"
                                    onclick="viewImage('/uploads/${doc.file_path}')">
                                <button 
                                    class="mt-2 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
                                    onclick="approveDocument(${doc.id}, this)">
                                    Approve
                                </button>
                            </div>
                        `;
                        container.innerHTML += docElement;
                    });
                } else {
                    container.innerHTML = '<p class="text-gray-500">No documents uploaded by this student.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching documents:', error);
                document.getElementById('documentsContainer').innerHTML = '<p class="text-red-500">Error loading documents.</p>';
            });
    }

    function closeModal() {
        const modal = document.getElementById('documentModal');
        modal.classList.add('hidden'); // Add the 'hidden' class to hide the modal
    }

    
    
    function approveDocument(documentType, userId, button) {
    console.log("Approving document with Type:", documentType, "for User ID:", userId);

    fetch('/Model/AproveDocument.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `document_type=${encodeURIComponent(documentType)}&user_id=${encodeURIComponent(userId)}`,
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Document approved successfully.');
                button.textContent = 'Approved';
                button.classList.add('bg-gray-400', 'cursor-not-allowed');
                button.disabled = true; // Disable button after approval
            } else {
                alert('Error approving document: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error approving document:', error);
            alert('An unexpected error occurred.');
        });
}

    </script>

</body>
</html>
