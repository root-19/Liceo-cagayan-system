<?php
// Include the necessary files
require_once '../../Database/Database.php'; // Include the Database class
require_once '../../Controller/UserModel.php'; // Include the UserModel class

// Create an instance of the Database class
$database = new Database();
$db = $database->connect(); // Establish the connection and return the PDO object

// Create an instance of the UserModel class, passing the $db connection
$userModel = new UserModel($db);

// Get the role from the GET parameters (default is 'user')
$role = isset($_GET['role']) ? $_GET['role'] : 'user';

// Get the search term from the GET parameters (if available)
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch users with the specified role and search term
$users = $userModel->getUsersByRoleAndSearch($role, $searchTerm);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students List</title>
    <!-- Tailwind CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <?php include_once "./layout/sidebar.php"; ?>
    <div class="container mx-auto mt-10 ml-10">
        <h1 class="text-3xl font-semibold mb-6 text-gray-800">Students Data</h1>

        <!-- Search Bar -->
        <div class="mb-4 flex justify-between items-center">
            <form method="GET" action="" class="w-full max-w-lg flex space-x-2">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" 
                       class="px-4 py-2 w-full border border-gray-300 rounded-lg" 
                       placeholder="Search by Student ID...">
                <button type="submit" style="background-color: var(--maroon);" class="px-4 py-2 text-white rounded-lg hover:bg-blue-600">
                    Search
                </button>
            </form>
        </div>

        <?php if (count($users) > 0): ?>
            <!-- Table displaying users -->
            <table class="min-w-full table-auto border-collapse border border-gray-300 rounded-lg">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Student ID</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Name</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Surname</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Email</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Document Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <?php
                        // For each user, count the number of distinct document types in the users_document table.
                        // Assuming that the user's primary key is 'id' and it is referenced as 'user_id' in users_document.
                        $stmt = $db->prepare("SELECT COUNT(DISTINCT document_type) AS doc_count FROM user_documents WHERE user_id = :user_id");
                        $stmt->bindParam(':user_id', $user['id']);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        $docCount = $result ? (int)$result['doc_count'] : 0;

                        if ($docCount >= 6) {
                            $status = '<span class="text-green-600 font-bold">Complete</span>';
                        } else {
                            $status = '<span class="text-orange-500 font-bold">Incomplete</span>';
                        }
                        ?>
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo htmlspecialchars($user['school_id']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo htmlspecialchars($user['name']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo htmlspecialchars($user['surname']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo $status; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="mt-4 text-lg text-gray-600">No users found with the role "<?php echo htmlspecialchars($role); ?>"</p>
        <?php endif; ?>
    </div>
</body>
</html>
