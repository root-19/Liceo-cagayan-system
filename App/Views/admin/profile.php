<?php
session_start();

require_once '../../Database/Database.php';
require_once '../../Model/UpdateUser.php';

$db = (new Database())->connect();
$userModel = new UpdateUser($db);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /path/to/login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$userProfile = $userModel->getUserById($userId);

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $data = [
        'name' => $_POST['name'],
        'middle_initial' => $_POST['middle_initial'],
        'gender' => $_POST['gender'],
        'school_id' => $_POST['school_id'],
        'surname' => $_POST['surname'],
        'date_of_birth' => $_POST['date_of_birth'],
        'grade' => $_POST['grade'],
        'section' => $_POST['section'],
        'strand' => $_POST['strand'],
        'phone_number' => $_POST['phone_number'],
        'email' => $_POST['email'],
    ];

    if ($userModel->updateUser($userId, $data)) {
        $success = "Profile updated successfully!";
        $userProfile = $userModel->getUserById($userId); // Refresh the profile data
    } else {
        $error = "Failed to update profile. Please try again.";
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<?php include_once "./layout/sidebar.php";?>
<!-- <body class="bg-gray-100 min-h-screen flex justify-center items-center p-4"> -->
    <div class="bg-white ml-44 mt-5 rounded-lg shadow-lg p-6 w-full h-15 max-w-2xl">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Your Profile</h1>

        <!-- Success and Error Messages -->
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Profile View -->
        <div id="profileView" class="space-y-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-amber-500 text-black rounded-full flex items-center justify-center font-bold">
                    <?= strtoupper(substr($userProfile['name'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($userProfile['name'] ?? 'N/A') ?></h2>
                    <p class="text-sm text-gray-600"><?= htmlspecialchars($userProfile['email'] ?? 'N/A') ?></p>
                </div>
            </div>
            <hr class="border-gray-200 my-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <p><strong>Middle Initial:</strong> <?= htmlspecialchars($userProfile['middle_initial'] ?? 'N/A') ?></p>
                <p><strong>Gender:</strong> <?= htmlspecialchars($userProfile['gender'] ?? 'N/A') ?></p>
                <p><strong>School ID:</strong> <?= htmlspecialchars($userProfile['school_id'] ?? 'N/A') ?></p>
                <p><strong>Surname:</strong> <?= htmlspecialchars($userProfile['surname'] ?? 'N/A') ?></p>
                <p><strong>Date of Birth:</strong> <?= htmlspecialchars($userProfile['date_of_birth'] ?? 'N/A') ?></p>
                <p><strong>Grade:</strong> <?= htmlspecialchars($userProfile['grade'] ?? 'N/A') ?></p>
                <p><strong>Section:</strong> <?= htmlspecialchars($userProfile['section'] ?? 'N/A') ?></p>
                <p><strong>Strand:</strong> <?= htmlspecialchars($userProfile['strand'] ?? 'N/A') ?></p>
                <p><strong>Phone Number:</strong> <?= htmlspecialchars($userProfile['phone_number'] ?? 'N/A') ?></p>
            </div>
            <button 
                onclick="showEditForm()" 
                style="background-color:var(--maroon)"
                class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                update
            </button>
        </div>

        <!-- Edit Profile Form -->
        <form id="editProfileForm" method="POST" class="space-y-4 hidden">
            <input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($userProfile['name'] ?? '') ?>" class="block w-full border-gray-300 rounded p-2">
            <input type="text" name="middle_initial" placeholder="Middle Initial" value="<?= htmlspecialchars($userProfile['middle_initial'] ?? '') ?>" class="block w-full border-gray-300 rounded p-2">
            <input type="text" name="gender" placeholder="Gender" value="<?= htmlspecialchars($userProfile['gender'] ?? '') ?>" class="block w-full border-gray-300 rounded p-2">
            <input type="text" name="school_id" placeholder="School ID" value="<?= htmlspecialchars($userProfile['school_id'] ?? '') ?>" class="block w-full border-gray-300 rounded p-2">
            <input type="text" name="surname" placeholder="Surname" value="<?= htmlspecialchars($userProfile['surname'] ?? '') ?>" class="block w-full border-gray-300 rounded p-2">
            <input type="date" name="date_of_birth" value="<?= htmlspecialchars($userProfile['date_of_birth'] ?? '') ?>" class="block w-full border-gray-300 rounded p-2">
            <input type="text" name="grade" placeholder="Grade" value="<?= htmlspecialchars($userProfile['grade'] ?? '') ?>" class="block w-full border-gray-300 rounded p-2">
            <input type="text" name="section" placeholder="Section" value="<?= htmlspecialchars($userProfile['section'] ?? '') ?>" class="block w-full border-gray-300 rounded p-2">
            <input type="text" name="strand" placeholder="Strand" value="<?= htmlspecialchars($userProfile['strand'] ?? '') ?>" class="block w-full border-gray-300 rounded p-2">
            <input type="text" name="phone_number" placeholder="Phone Number" value="<?= htmlspecialchars($userProfile['phone_number'] ?? '') ?>" class="block w-full border-gray-300 rounded p-2">
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($userProfile['email'] ?? '') ?>" class="block w-full border-gray-300 rounded p-2">
            <button 
                type="submit" 
                name="update_profile" 
                class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
                Save Changes
            </button>
            <button 
                type="button" 
                onclick="hideEditForm()" 
                class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                Cancel
            </button>
        </form>
    </div>

    <script>
        function showEditForm() {
            document.getElementById('profileView').style.display = 'none';
            document.getElementById('editProfileForm').style.display = 'block';
        }

        function hideEditForm() {
            document.getElementById('profileView').style.display = 'block';
            document.getElementById('editProfileForm').style.display = 'none';
        }
    </script>
</body>
</html>
