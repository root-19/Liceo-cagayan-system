<?php
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Handle email validation and sending password reset link
    // Example: send an email with a reset link (you'll need to implement this)
    $error = "Password reset link sent to your email."; // Placeholder for successful reset
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="./public/style.css" />
</head>
<body class="flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Reset Your Password</h2>
        
        <!-- Show error message if reset fails -->
        <?php if ($error): ?>
            <div class="bg-red-200 p-2 text-red-600 text-center rounded mb-4"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Forgot Password Form -->
        <form method="POST" class="space-y-4">
            <div>
                <input type="email" name="email" placeholder="Enter your email" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="w-full text-white py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" style="background-color:var(--maroon)">Send Reset Link</button>
        </form>

        <!-- Back to Login Link -->
        <p class="mt-4 text-center text-gray-600"><a href="login.php" style="color: var(--yellow)" class="hover:underline">Back to Login</a></p>
    </div>
</body>
</html>
