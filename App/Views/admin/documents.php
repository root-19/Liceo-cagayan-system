<?php

require_once '../../Database/Database.php';

// Ensure that only admin can view this page.
// (Note: This check appears inverted in your code snippet. Adjust as needed.)
if (isset($_SESSION['user_id'])) {
    echo '<p class="text-red-500">Please log in as admin to view student documents.</p>';
    exit;
}

/**
 * Class UserModel
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
        $query = "SELECT id, document_type, file_path, upload_date, status FROM user_documents WHERE user_id = :user_id";
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
        
        <!-- Search Bar -->
        <div class="mb-4">
            <input type="text" id="studentSearch" placeholder="Search by name..." class="w-full p-2 border border-gray-300 rounded">
        </div>
        
        <!-- Student List -->
        <div class="overflow-x-auto bg-white rounded-lg shadow-lg p-6">
            <table id="studentTable" class="min-w-full border-collapse border border-gray-200 text-left">
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
                            <tr class="studentRow">
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($student['name']); ?></td>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($student['surname']); ?></td>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($student['email']); ?></td>
                                <td class="px-4 py-2 border text-center">
                                    <button 
                                        style="background-color: var(--maroon);"
                                        class="text-white px-4 py-2 rounded" 
                                        onclick="showDocuments(<?php echo $student['id']; ?>, '<?php echo htmlspecialchars($student['name'] . ' ' . $student['surname']); ?>')">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
    <script>
        // Filter table rows based on search input
        document.getElementById('studentSearch').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#studentTable tbody .studentRow');

            rows.forEach(row => {
                const nameCell = row.children[0].textContent.toLowerCase();
                if (nameCell.indexOf(searchValue) > -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

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
                            const isApproved = doc.status === 'Approved'; // Check if the document is approved
                            const docElement = `
                                <div class="border border-gray-300 shadow-lg rounded-lg bg-white p-6 flex flex-col items-center space-y-4 hover:shadow-xl transition-all duration-300">
                                    <p class="font-semibold text-lg text-gray-800">Type: ${doc.document_type}</p>
                                    <p class="text-sm text-gray-600">Uploaded on: ${doc.upload_date}</p>

                                    <!-- Image section with hover effect -->
                                    <div class="relative w-full max-w-[150px] max-h-[150px] overflow-hidden rounded-lg shadow-md hover:scale-105 transition-transform">
                                        <img 
                                            src="/uploads/${doc.file_path}" 
                                            alt="Document Image" 
                                            class="w-full h-full object-cover rounded-lg cursor-pointer"
                                            onclick="viewImage('/uploads/${doc.file_path}')">
                                    </div>

                                    <!-- Button section with improved styling -->
                                    <button 
                                        class="mt-2 w-full py-3 rounded-md text-white ${isApproved ? 'bg-gray-400 cursor-not-allowed' : 'bg-amber-900 focus:outline-none focus:ring-2 focus:ring-green-400'}"
                                        onclick="${isApproved ? '' : `approveDocument(${doc.id}, this)`}" 
                                        ${isApproved ? 'disabled' : ''}>
                                        ${isApproved ? 'Approved' : 'Approve'}
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

        function approveDocument(documentId, button) {
            console.log("Document ID:", documentId); // Log the ID to the console for debugging

            fetch('/Model/AproveDocument.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${documentId}`, // Send the `id` to the backend
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Document approved successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            button.textContent = 'Approved';
                            button.classList.add('bg-gray-400', 'cursor-not-allowed');
                            button.classList.remove('bg-green-500', 'hover:bg-green-600');
                            button.disabled = true; // Disable button after approval
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: `Error approving document: ${data.message}`,
                            icon: 'error',
                            confirmButtonText: 'Try Again'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error approving document:', error);
                    Swal.fire({
                        title: 'Unexpected Error',
                        text: 'An unexpected error occurred. Please try again later.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }

        function viewImage(imagePath) {
            Swal.fire({
                imageUrl: imagePath,
                imageAlt: 'Document Image',
                showCloseButton: true,
                showConfirmButton: false
            });
        }

        function closeModal() {
            const modal = document.getElementById('documentModal');
            modal.classList.add('hidden'); // Add the 'hidden' class to hide the modal

            Swal.fire({
                title: 'Closed!',
                text: 'Document modal closed successfully.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }
    </script>

</body>
</html>
