<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

$error = '';
$success = '';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

// Generate a random password
function generateRandomPassword() {
    return substr(bin2hex(random_bytes(6)), 0, 12);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Database connection
    require_once './Database/Database.php';
    require_once './Controller/UserModel.php';

    $db = new Database();
    $conn = $db->connect();
    $userModel = new UserModel($conn);

    // Check if email exists in the database
    if ($userModel->checkEmailExists($email)) {
        // Generate a new password
        $new_password = generateRandomPassword();

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        error_log($new_password);

        // Update password in the database
        if ($userModel->updatePassword($email, $hashed_password)) {
            // Send email with the new password
            $mail = new PHPMailer(true);

            try {
                // SMTP server configuration using environment variables
                $mail->isSMTP();
                $mail->Host = $_ENV['SMTP_HOST'];
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['SMTP_USERNAME'];
                $mail->Password = $_ENV['SMTP_PASSWORD'];
                $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
                $mail->Port = $_ENV['SMTP_PORT'];

                // Sender and recipient settings
                $mail->setFrom($_ENV['SMTP_USERNAME'], 'Liceo de Cagayan University');
                $mail->addAddress($email);

                // Email content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset';
                $mail->Body = "Hello,<br><br>Your new password is: <strong>{$new_password}</strong><br><br>Please log in and change it for security purposes.";

                $mail->send();
                $success = "A new password has been sent to your email address.";
            } catch (Exception $e) {
                $error = "Error sending email: {$mail->ErrorInfo}";
            }
        } else {
            $error = "Error updating password in the database.";
        }
    } else {
        $error = "Email not found.";
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

        <!-- Email verification form -->
        <form method="POST" class="space-y-4">
            <div>
                <input type="email" name="email" placeholder="Enter your email" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="w-full text-white py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" style="background-color: var(--maroon)">Send New Password</button>
        </form>

        <!-- Back to Login Link -->
        <p class="mt-4 text-center text-gray-600"><a href="login.php" style="color: var(--yellow)" class="hover:underline">Back to Login</a></p>
    </div>
</body>
</html>
