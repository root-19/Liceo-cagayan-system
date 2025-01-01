<?php
$error = '';
$success = '';
$showNewPasswordForm = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Database connection (replace with your actual database connection)
    require_once './Database/Database.php'; // or include your database connection file
    require_once './Controller/UserModel.php';

    $db = new Database();
    $conn = $db->connect();
    $userModel = new UserModel($conn);

    // Check if email exists in the database
    if ($userModel->checkEmailExists($email)) {
        // Email exists, show the form to reset password
        $showNewPasswordForm = true;
        $emailToUpdate = $email; // Store email to use later for updating the password
    } else {
        $error = "Email not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'], $_POST['confirm_password'], $emailToUpdate)) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords
    if ($new_password === $confirm_password) {
        // Update password in the database
        if ($userModel->updatePassword($emailToUpdate, $new_password)) {
            $success = "Password updated successfully!";
        } else {
            $error = "Error updating password.";
        }
    } else {
        $error = "Passwords do not match.";
    }
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

        <!-- Show success or error message -->
        <?php if ($error): ?>
            <div class="bg-red-200 p-2 text-red-600 text-center rounded mb-4"><?php echo $error; ?></div>
        <?php elseif ($success): ?>
            <div class="bg-green-200 p-2 text-green-600 text-center rounded mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- First Step: Email verification -->
        <?php if (!$showNewPasswordForm): ?>
            <form method="POST" class="space-y-4">
                <div>
                    <input type="email" name="email" placeholder="Enter your email" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="w-full text-white py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" style="background-color:var(--maroon)">Check Account</button>
            </form>
        <?php endif; ?>

        <!-- Second Step: New password form (if email exists) -->
        <?php if ($showNewPasswordForm): ?>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="email" value="<?php echo $emailToUpdate; ?>">

                <div>
                    <input type="password" name="new_password" placeholder="Enter new password" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="password" name="confirm_password" placeholder="Confirm new password" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="w-full text-white py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" style="background-color:var(--maroon)">Update Password</button>
            </form>
        <?php endif; ?>

        <!-- Back to Login Link -->
        <p class="mt-4 text-center text-gray-600"><a href="login.php" style="color: var(--yellow)" class="hover:underline">Back to Login</a></p>
    </div>
</body>
</html>
