<?php
require_once "../../Controller/Middleware.php";
Middleware::auth('admin');



// Check if the logout button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Destroy the session to log out the user
    session_destroy();
    // Redirect to the login page
    header('Location: ../../index.php');
    exit();
}

?>
<?php include_once "./layout/sidebar.php";?>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md text-center">
        <h1 class="text-2xl font-bold mb-4">Welcome to the admin Dashboard!</h1>
        <p class="text-gray-700 mb-6">Hello, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!</p>
        
        <!-- Logout Button -->
        <form method="POST">
            <button type="submit" name="logout" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                Logout
            </button>
        </form>
    </div>

