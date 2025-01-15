<?php
require_once "./Controller/Middleware.php";
Middleware::guest();

require_once "./Database/Database.php";
require_once "./Controller/UserModel.php";
require_once "./Controller/Auth.php";
require_once "./vendor/autoload.php";
use Controller\Auth;

$db = new Database();
$conn = $db->connect();
$userModel = new UserModel($conn);
$auth = new Auth($userModel);

$message = "";  // Default message variable

// Handle registration request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $middle_initial = $_POST['middle_initial'];  // Collect the middle initial
    $school_id = $_POST['school_id'];  // Collect the school ID
    $gender = $_POST['gender'];  // Collect the gender
    $surname = $_POST['surname'];  // Collect surname
    $date_of_birth = $_POST['date_of_birth'];  // Collect date of birth
    $grade = $_POST['grade'];  // Collect grade
    $section = $_POST['section'];  // Collect section
    $strand = $_POST['strand'];  // Collect strand
    $phone_number = $_POST['phone_number'];  // Collect phone number
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Call the registerUser method to handle user registration
    $message = $auth->registerUser($name, $middle_initial, $gender, $school_id, $surname, $date_of_birth, $grade, $section, $strand, $phone_number, $email, $password);

    if ($message === true) {
        $message = "Registration successful";
    } else {
        $message = $message;  // Display error message returned from registerUser
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="./public/style.css" />
</head>
<style>
        /* Add background image */
        body {
            background-image: url('./Storage/image/house.jpg');
     
            background-position: center;  /* Center the image */
            background-repeat: no-repeat;  /* Prevent the image from repeating */
            background-attachment: fixed;  /* Make the background image fixed */
        }
    </style>
<body class="bg-gray-100 flex justify-center items-center h-screen">

    <!-- Notification -->
    <div id="notification" class="hidden fixed top-10 left-1/2 transform -translate-x-1/2 p-4 rounded-md text-white">
        <span id="notification-message"></span>
    </div>

    <!-- Modal -->
    <div class="bg-gray-700 bg-opacity-50 fixed inset-0 flex items-center justify-center z-50">
        <div class="bg-white  bg-opacity-80  p-8 rounded-lg shadow-lg w-full sm:w-96 md:w-1/2 lg:w-2/3 xl:w-1/2"> <!-- Adjusting width for responsiveness -->
            <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>
            <?php if ($message): ?>
                <div class="bg-green-200 p-2 text-red-600 text-center rounded mb-4"><?php echo $message; ?></div>
            <?php endif; ?>
            <!-- Registration Form -->
            <form method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <input type="text" name="name" placeholder="Name" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="text" name="middle_initial" placeholder="Middle Initial" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="text" name="surname" placeholder="Surname" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="date" name="date_of_birth" placeholder="Date of Birth" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="text" name="grade" placeholder="Grade" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="text" name="section" placeholder="Section" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="text" name="strand" placeholder="Strand" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="text" name="phone_number" placeholder="Phone Number" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="email"
                    pattern="[a-zA-z0-9/._$+-]+@liceo.edu..\.ph"
                     name="email"
                      placeholder="Email" 
                    required
                     class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="password" name="password" placeholder="Password" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- School ID Field -->
                <div>
                    <input type="text" name="school_id" placeholder="School ID" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Gender Dropdown -->
                <div>
                    <!-- <label class="block text-gray-700 font-semibold mb-2">Gender</label> -->
                    <select name="gender" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <button type="submit" name="register"  style="background-color:var(--maroon)" class="w-full text-white py-3 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Register</button>
                </div>
            </form>

            <!-- Login Link -->
            <p class="mt-4 text-center text-gray-600">Already have an account? <a href="./login.php"  style="color:var(--yellow)" class="hover:underline">Login here</a></p>
        </div>
    </div>

    <script>
        // If the PHP message is set, show the notification
        <?php if ($message) : ?>
            showNotification('<?php echo $message; ?>', '<?php echo ($message == "Registration successful") ? "success" : "error"; ?>');
        <?php endif; ?>

        // Notification function to show success/error message
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notification-message');
            notificationMessage.textContent = message;
            notification.classList.remove('hidden', 'bg-red-500', 'bg-green-500');
            notification.classList.add(type === 'success' ? 'bg-green-500' : 'bg-red-500');
            setTimeout(() => notification.classList.add('hidden'), 3000);
        }
    </script>

</body>
</html>
