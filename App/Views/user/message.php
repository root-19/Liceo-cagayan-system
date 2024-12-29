<?php

include_once '../../Model/MessageController.php';
require_once "../../Controller/Middleware.php";
Middleware::auth('user');

$messageController = new MessageController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // User sends a message
    if (isset($_POST['message']) && !empty($_POST['message'])) {
        $messageController->sendUserMessage($_SESSION['user_id'], $_POST['message']);
    }

    // Admin replies to a message
    if (isset($_POST['reply_message']) && !empty($_POST['reply_message']) && isset($_POST['reply_to'])) {
        $messageController->sendAdminReply($_POST['reply_to'], $_SESSION['user_id'], $_POST['reply_message']);
    }
}

$messages = $messageController->fetchMessages($_SESSION['user_id']);
?>
<?php include_once "./layout/sidebar.php"; ?>
<body class="bg-gray-100 h-screen flex flex-col md:flex-row">

    <!-- Left Sidebar: Admin Contacts -->
    <div class="w-full md:w-1/4 bg-white shadow-md p-4 h-full overflow-y-auto">
        <h2 class="text-lg font-bold">Admin Contacts</h2>
        <ul class="mt-4">
            <!-- Example admin contacts -->
            <li class="p-2 border-b">
                <p class="font-semibold">John Doe</p>
                <p class="text-sm text-gray-600">Phone: +123456789</p>
                <p class="text-sm text-gray-600">Email: john.doe@example.com</p>
            </li>
            <li class="p-2 border-b">
                <p class="font-semibold">Jane Smith</p>
                <p class="text-sm text-gray-600">Phone: +987654321</p>
                <p class="text-sm text-gray-600">Email: jane.smith@example.com</p>
            </li>
            <li class="p-2 border-b">
                <p class="font-semibold">Michael Johnson</p>
                <p class="text-sm text-gray-600">Phone: +1122334455</p>
                <p class="text-sm text-gray-600">Email: michael.johnson@example.com</p>
            </li>
            <li class="p-2 border-b">
                <p class="font-semibold">Emily Davis</p>
                <p class="text-sm text-gray-600">Phone: +5566778899</p>
                <p class="text-sm text-gray-600">Email: emily.davis@example.com</p>
            </li>
            <li class="p-2 border-b">
                <p class="font-semibold">David Brown</p>
                <p class="text-sm text-gray-600">Phone: +9988776655</p>
                <p class="text-sm text-gray-600">Email: david.brown@example.com</p>
            </li>
            <li class="p-2 border-b">
                <p class="font-semibold">Sophia Williams</p>
                <p class="text-sm text-gray-600">Phone: +3344556677</p>
                <p class="text-sm text-gray-600">Email: sophia.williams@example.com</p>
            </li>
            <li class="p-2 border-b">
                <p class="font-semibold">Chris Martinez</p>
                <p class="text-sm text-gray-600">Phone: +4433221100</p>
                <p class="text-sm text-gray-600">Email: chris.martinez@example.com</p>
            </li>
        </ul>
    </div>

    <!-- Right Chat Box (Messages) -->
    <div class="w-full md:w-3/4 bg-white shadow-md rounded-lg flex flex-col">
        <div class="p-4 border-b">
            <h1 class="text-xl font-bold">Chat with Admin</h1>
        </div>

        <!-- Message container (scrollable) -->
        <div id="messages" class="flex-grow p-4 h-96 overflow-y-scroll">
            <?php foreach ($messages as $message): ?>
                <div class="message p-2 my-2 rounded-lg <?= $message['sender'] === 'admin' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-black' ?>">
                    <p><?= htmlspecialchars($message['message']) ?></p>
                    <?php if (isset($message['reply_message']) && $message['reply_message']): ?>
                        <div class="ml-4 text-sm text-gray-600 italic">
                            Reply: <?= htmlspecialchars($message['reply_message']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Message input form -->
        <form method="POST" class="p-4 border-t flex">
            <input type="text" name="message" id="messageInput" placeholder="Type a message..." class="flex-grow border rounded p-2">
            <button type="submit" class="ml-2 bg-blue-500 text-white px-4 py-2 rounded">Send</button>
        </form>
    </div>
</body>
