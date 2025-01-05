<?php
// Admin section (admin_send_request.php)

// Include database connection
require_once '../../Database/Database.php';
require_once '../../Model/Notification.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<p class="text-red-500">Please log in as an admin.</p>';
    exit;
}

try {
    $db = (new Database())->connect();
    $admin = new Admin($db);

    // Handle form submission for sending document request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_POST['user_id']; // Get selected user ID
        $message = $_POST['message'];

        if ($userId) {
            $admin->sendDocumentRequestToUser($userId, $message);
            $response = 'success';
        } else {
            $response = 'error';
        }
    }
} catch (Exception $e) {
    $response = 'error';
    $errorMessage = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Send Document Request</title>
</head>
<body>
    <?php include_once "./layout/sidebar.php";?>

    <div class="p-6 max-w-3xl mx-auto">
        <h1 class="text-3xl font-semibold text-gray-800 mb-6">Send Document Request to User</h1>

        <form method="POST" class="bg-white p-6 rounded-lg shadow-lg">
            <!-- Dropdown for selecting user -->
            <label for="user" class="block text-gray-700 mb-2">Select User</label>
            <select name="user_id" class="w-full border-gray-300 rounded p-2 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="">Select a user</option>
                <?php
                    // Fetch users from the Admin class
                    $users = $admin->getAllUsers();
                    if ($users) {
                        foreach ($users as $user) {
                            echo "<option value='{$user['id']}'>{$user['name']}  {$user['surname']}</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No users found</option>";
                    }
                ?>
            </select>

            <!-- Textarea for message -->
            <label for="message" class="block text-gray-700 mb-2">Message</label>
            <textarea name="message" class="w-full border-gray-300 rounded p-2 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4" placeholder="Enter message for the document request" required></textarea>

            <button type="submit" class="w-full bg-red-800 text-white py-2 rounded-lg hover:bg-red-900 transition duration-300">Send Request</button>
        </form>
    </div>

    <script>
        <?php if (isset($response)): ?>
            <?php if ($response == 'success'): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Request Sent!',
                    text: 'The document request was successfully sent to the user.',
                    confirmButtonText: 'Okay',
                    background: '#f4f7f6'
                });
            <?php elseif ($response == 'error'): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                    confirmButtonText: 'Close',
                    background: '#f4f7f6'
                });
            <?php endif; ?>
        <?php endif; ?>
    </script>
</body>
</html>
