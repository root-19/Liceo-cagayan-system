<?php
require_once "../../Controller/Middleware.php";
Middleware::auth('admin');
?>

<?php include_once "./layout/sidebar.php";?>

<div class="p-6 max-w-3xl mx-auto text-center">
    <h1 class="text-3xl font-bold mt-40" id="welcome-text">Welcome Licean!  Admin account of Liceo de Cagayan University!</h1>
    <p class="text-gray-700 mb-6">Hello, <strong id="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!</p>
</div>

</body>
</html>
