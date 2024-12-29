<?php
include_once '../../Model/MessageController.php';
require_once "../../Controller/Middleware.php";
Middleware::auth('admin');  

$messageController = new MessageController();

// Check if the user selected a conversation
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Admin replies to a message
    if (isset($_POST['reply_message']) && !empty($_POST['reply_message']) && isset($_POST['reply_to'])) {
        $messageController->sendAdminReply($_POST['reply_to'], $_SESSION['user_id'], $_POST['reply_message']);
    }
}

// Fetch all users that have messages
$users = $messageController->getUsersWithMessages();

// Fetch messages for the selected user
$messages = $user_id ? $messageController->fetchMessages($user_id) : [];
?>

<?php include_once "./layout/sidebar.php"; ?>

<body class="bg-gray-100 p-6 flex justify-center">
    <div class="flex w-full max-w-screen-xl">
        <!-- Left side: User List -->
        <div class="w-1/4 bg-white shadow-md rounded-lg p-4 mr-6">
            <h2 class="text-lg font-semibold mb-4">Users</h2>
            <ul class="space-y-4">
                <?php foreach ($users as $user): ?>
                    <li>
                        <a href="?user_id=<?= $user['id'] ?>" class="block p-2 rounded-lg <?= $user['id'] == $user_id ? 'bg-orange-500 text-white' : 'bg-gray-200 text-black' ?>">
                            <?= htmlspecialchars($user['name'] . ' ' . $user['surname']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Right side: Messages -->
        <div class="flex-1 bg-white shadow-md rounded-lg p-4 overflow-hidden">
            <div class="mb-4">
                <h1 class="text-xl font-bold">Chat with <?= $user_id ? htmlspecialchars($user['name'] . ' ' . $user['surname']) : 'Select a User' ?></h1>
            </div>

            <div id="messages" class="p-4 h-96 overflow-y-scroll mb-4">
                <?php foreach ($messages as $message): ?>
                    <div class="message p-2 my-2 rounded-lg <?= $message['sender'] === 'admin' ? 'bg-orange-900 text-white' : 'bg-gray-200 text-black' ?>">
                        <p><?= htmlspecialchars($message['message']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($user_id): ?>
                <form method="POST" class="flex mt-4">
                    <!-- Hidden field for reply_to message id -->
                    <input type="hidden" name="reply_to" value="<?= $messages[0]['id'] ?? '' ?>">
                    <input type="text" name="reply_message" id="replyMessage" placeholder="Type a reply..." class="flex-grow border rounded p-2" required>
                    <button type="submit" class="ml-2 bg-blue-500 text-white px-4 py-2 rounded">Reply</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
