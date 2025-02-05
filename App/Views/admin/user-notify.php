<?php
// notification.php

// Include the database connection file
require_once '../../Database/Database.php';

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<p class="text-red-500 text-center mt-6">Please log in to view notifications.</p>';
    exit;
}

try {
    $db = (new Database())->connect();

    // Fetch notifications for all users who have sent a document.
    // If you want to restrict this to the logged in user, add: WHERE ud.user_id = ?
    $stmt = $db->prepare("
       SELECT ud.document_type, ud.upload_date, u.name 
       FROM user_documents ud 
       INNER JOIN users u ON ud.user_id = u.id 
       ORDER BY ud.upload_date DESC
    ");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo '<p class="text-red-500 text-center mt-6">Error: ' . $e->getMessage() . '</p>';
    exit;
}
?>

<?php include_once "./layout/sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Notifications</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Document Upload Notifications</h1>
    
    <?php if ($notifications && count($notifications) > 0): ?>
      <div class="space-y-4">
        <?php foreach($notifications as $notif): ?>
          <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
            <!-- Icon -->
            <div class="flex-shrink-0">
              <svg class="h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 1010 10A10 10 0 0012 2z" />
              </svg>
            </div>
            <div>
              <p class="text-lg font-semibold text-gray-800">Notification</p>
              <p class="text-gray-600">
                Hello, <span class="font-bold"><?php echo htmlspecialchars($notif['name']); ?></span>! You have successfully sent your 
                <span class="font-bold"><?php echo htmlspecialchars($notif['document_type']); ?></span> document on 
                <span class="font-medium"><?php echo date("F j, Y", strtotime($notif['upload_date'])); ?></span>.
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="bg-white p-6 rounded-lg shadow-md text-center">
        <p class="text-gray-600">No notifications available.</p>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
