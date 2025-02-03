<?php 

// Include database connection
require_once '../../Database/Database.php';

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<p class="text-red-500">Please log in to view your documents.</p>';
    exit;
}

// Fetch user documents from the database
try {
    $db = (new Database())->connect();
    $stmt = $db->prepare("SELECT id, document_type, file_path, upload_date, status FROM user_documents WHERE user_id = ?");
    $stmt->bindValue(1, $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo '<p class="text-red-500">Error fetching documents: ' . $e->getMessage() . '</p>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Upload Documents</title>
</head>
<!-- <body class="bg-gray-100 min-h-screen flex flex-col"> -->

    <!-- Sidebar -->
    <?php include_once "./layout/sidebar.php";?>

    <!-- Main Content -->
    <div class="flex flex-col md:flex-row flex-grow">
        
        <!-- Document Upload Form -->
        <div class="bg-white rounded-lg shadow-lg p-6 md:w-1/3 mx-4 my-4">
            <h1 class="text-2xl font-bold mb-4 text-center">Upload Your Documents</h1>
            
            <form id="uploadForm" class="space-y-4">
                <div>
                    <label for="document_type" class="block text-gray-700 font-medium mb-2">Document Type</label>
                    <select name="document_type" id="document_type" class="block w-full border-gray-300 rounded p-2">
                        <option value="Form 137">Form 137</option>
                        <option value="Form 138">Form 138</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Good Moral Character">Good Moral Character</option>
                        <option value="2x2 Picture">2x2 Picture</option>
                        <option value="PSA Birth Certificate">PSA Birth Certificate</option>
                    </select>
                </div>
                <div>
                    <label for="file" class="block text-gray-700 font-medium mb-2">Upload File</label>
                    <input type="file" name="file" id="file" class="block w-full border-gray-300 rounded p-2">
                </div>
                <button type="submit" style="background-color: var(--maroon);" class="w-full text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                    Upload Document
                </button>
            </form>

            <div id="responseMessage" class="mt-4"></div>
            <div id="loadingSpinner" class="hidden mt-4 text-center">
                <div class="w-8 h-8 border-4 border-t-blue-500 border-gray-200 rounded-full animate-spin mx-auto"></div>
                <p class="text-gray-500 mt-2">Uploading...</p>
            </div>
        </div>

        <!-- Uploaded Documents List -->
        <div class="bg-white rounded-lg shadow-lg p-6 md:w-2/3 mx-4 my-4 flex-grow">
            <h2 class="text-2xl font-bold mb-4 text-center">Your Uploaded Documents</h2>

            <ul class="bg-white shadow rounded-lg divide-y divide-gray-200">
    <?php if (!empty($documents)) : ?>
        <?php foreach ($documents as $doc) : ?>
            <li class="py-4 px-6 flex justify-between items-center">
                <div>
                    <p class="text-gray-700 font-medium">Document Type: <?php echo htmlspecialchars($doc['document_type']); ?></p>
                    <p class="text-sm text-gray-500">Uploaded on: <?php echo htmlspecialchars($doc['upload_date']); ?></p>
                    <p class="text-sm <?php echo ($doc['status'] == 'Approved') ? 'font-bold text-green-500' : 'font-bold text-yellow-500'; ?>">
                        Status: <?php echo htmlspecialchars($doc['status']); ?>
                    </p>
                </div>
                <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank" class="text-blue-500 hover:underline">Download</a>
            </li>
        <?php endforeach; ?>
    <?php else : ?>
        <li class="py-4 px-6 text-gray-500">No documents uploaded yet.</li>
    <?php endif; ?>
</ul>

        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            event.preventDefault();  // Prevent form submission

            document.getElementById('loadingSpinner').classList.remove('hidden');
            document.getElementById('responseMessage').innerHTML = '';

            const formData = new FormData();
            formData.append('document_type', document.getElementById('document_type').value);
            formData.append('file', document.getElementById('file').files[0]);

            fetch('/Model/Docs.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('responseMessage');
                if (data.success) {
                    messageDiv.innerHTML = '<p class="text-green-500">Document uploaded successfully!</p>';
                    setTimeout(() => {
                        location.reload();
                    }, 5000);
                } else {
                    messageDiv.innerHTML = `<p class="text-red-500">Error: ${data.message}</p>`;
                }
                document.getElementById('loadingSpinner').classList.add('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('responseMessage').innerHTML = '<p class="text-red-500">An error occurred while uploading the document.</p>';
                document.getElementById('loadingSpinner').classList.add('hidden');
            });
        });
    </script>

</body>
</html>
