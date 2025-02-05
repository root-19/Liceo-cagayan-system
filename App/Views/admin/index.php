<?php
require_once "../../Controller/Middleware.php";
Middleware::auth('admin');

require_once "../../Database/Database.php";
$db = (new Database())->connect();

// Get total users count
$stmtUsers = $db->prepare("SELECT COUNT(*) AS total_users FROM users");
$stmtUsers->execute();
$totalUsers = $stmtUsers->fetch(PDO::FETCH_ASSOC)['total_users'];

// Get total user documents count
$stmtDocs = $db->prepare("SELECT COUNT(*) AS total_documents FROM user_documents");
$stmtDocs->execute();
$totalDocuments = $stmtDocs->fetch(PDO::FETCH_ASSOC)['total_documents'];

// Get total messages count
$stmtMessages = $db->prepare("SELECT COUNT(*) AS total_messages FROM messages");
$stmtMessages->execute();
$totalMessages = $stmtMessages->fetch(PDO::FETCH_ASSOC)['total_messages'];
?>

<?php include_once "./layout/sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Include Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="p-6 max-w-5xl mx-auto text-center mb-30">
    <!-- <h1 class="text-3xl font-bold mt-40" id="welcome-text">
      Welcome Licean! Admin account of Liceo de Cagayan University!
    </h1> -->
    <p class="text-gray-700 mb-6 text-2xl">
      Hello, <strong id="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!
      Welcome Licean! Admin account of Liceo de Cagayan University!
    </p>

    <!-- Statistics Boxes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
      <!-- Total Users Box -->
      <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800">Total Users</h2>
        <p class="mt-4 text-4xl font-bold text-blue-500"><?php echo $totalUsers; ?></p>
      </div>
      <!-- Total Documents Box -->
      <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800">Total User Documents</h2>
        <p class="mt-4 text-4xl font-bold text-green-500"><?php echo $totalDocuments; ?></p>
      </div>
      <!-- Total Messages Box -->
      <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800">Total Messages</h2>
        <p class="mt-4 text-4xl font-bold text-purple-500"><?php echo $totalMessages; ?></p>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Bar Chart -->
      <div class="bg-white shadow-lg rounded-lg p-6">
        <canvas id="barChart"></canvas>
      </div>
      <!-- Pie Chart -->
      <div class="bg-white shadow-lg rounded-lg p-6">
        <canvas id="pieChart"></canvas>
      </div>
    </div>
  </div>

  <script>
    // Bar Chart configuration
    const ctxBar = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: ['Total Users', 'Total Documents', 'Total Messages'],
        datasets: [{
          label: 'Count',
          data: [<?php echo $totalUsers; ?>, <?php echo $totalDocuments; ?>, <?php echo $totalMessages; ?>],
          backgroundColor: [
            'rgba(59, 130, 246, 0.6)',  // Blue for Users
            'rgba(16, 185, 129, 0.6)',  // Green for Documents
            'rgba(139, 92, 246, 0.6)'   // Purple for Messages
          ],
          borderColor: [
            'rgba(59, 130, 246, 1)',
            'rgba(16, 185, 129, 1)',
            'rgba(139, 92, 246, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1 }
          }
        }
      }
    });

    // Pie Chart configuration
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(ctxPie, {
      type: 'pie',
      data: {
        labels: ['Total Users', 'Total Documents', 'Total Messages'],
        datasets: [{
          data: [<?php echo $totalUsers; ?>, <?php echo $totalDocuments; ?>, <?php echo $totalMessages; ?>],
          backgroundColor: [
            'rgba(59, 130, 246, 0.6)',
            'rgba(16, 185, 129, 0.6)',
            'rgba(139, 92, 246, 0.6)'
          ],
          borderColor: [
            'rgba(59, 130, 246, 1)',
            'rgba(16, 185, 129, 1)',
            'rgba(139, 92, 246, 1)'
          ],
          borderWidth: 1
        }]
      }
    });
  </script>
</body>
</html>
