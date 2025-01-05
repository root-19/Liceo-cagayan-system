<?php
// User section (user_notifications.php)

require_once '../../Database/Database.php';
require_once '../../Model/Notification.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo '<p class="text-red-500">Please log in to view your document requests.</p>';
    exit;
}

try {
    $db = (new Database())->connect();
    $notification = new Notification($db);
    
    // Retrieve the user info (including username)
    $userId = $_SESSION['user_id'];
    $userQuery = $db->prepare("SELECT name FROM users WHERE id = :id");
    $userQuery->bindParam(':id', $userId);
    $userQuery->execute();
    $user = $userQuery->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('User not found.');
    }

    // Modify the query to fetch requests ordered by created_at in descending order
    $requests = $notification->getRequestsByUser($userId);

} catch (Exception $e) {
    echo '<p class="text-red-500">Error: ' . $e->getMessage() . '</p>';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Your Document Requests</title>
    <style>
        /* Add scrollable content with a maximum height */
        .scrollable-content {
            max-height: 90vh; /* Adjust height based on your design */
            overflow-y: auto;
        }

        /* For smoother scrolling */
        .scrollable-content::-webkit-scrollbar {
            width: 8px;
        }

        .scrollable-content::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }
    </style>
</head>

<?php include_once "./layout/sidebar.php"; ?>

<!-- Main content area -->
<div class="w-full max-w-6xl bg-white shadow-md rounded-lg p-6 flex flex-col">
    <h1 class="text-3xl font-semibold text-gray-800">Your Document Requests</h1>

    <!-- Scrollable container for requests -->
    <div class="scrollable-content mt-6 space-y-4">
        <?php if (!empty($requests)) : ?>
            <?php foreach ($requests as $request) : ?>
                <div class="p-4 bg-gray-100 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-gray-700 text-lg font-medium mt-4">Hi, <?php echo htmlspecialchars($user['name']); ?>! You have new request(s).</p>
                        <button 
                            class="text-blue-500 hover:text-blue-700 focus:outline-none"
                            onclick="toggleVisibility(<?php echo $request['id']; ?>)">
                            View
                        </button>
                    </div>

                    <!-- Display the created_at timestamp below the request -->
                    <p class="text-sm text-gray-500 mt-2">Requested on: <?php echo date('F j, Y, g:i a', strtotime($request['created_at'])); ?></p> 

                    <div id="details-<?php echo $request['id']; ?>" class="mt-4 text-gray-700 hidden">
                        <p class="text-sm"><?php echo htmlspecialchars($request['message']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="text-gray-500">No requests found.</div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Function to toggle visibility of request details
    function toggleVisibility(requestId) {
        const details = document.getElementById(`details-${requestId}`);
        if (details.classList.contains('hidden')) {
            details.classList.remove('hidden');
        } else {
            details.classList.add('hidden');
        }
    }
</script>
</body>
</html>
