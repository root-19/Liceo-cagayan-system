<?php
class Middleware {
    
    // Check if user is authenticated
    public static function auth($role = null) {
        session_start();

        // If a role is provided, check if the user matches the required role
        if ($role && (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role)) {
            header("Location: ../public/login.php"); // Redirect if role doesn't match
            exit;
        }
    }

    // Redirect authenticated users away from login or register pages
    public static function guest() {
        session_start();

        // Only redirect to login page if the user is not logged in
        if (isset($_SESSION['user_id'])) {
            // Redirect authenticated users to their respective pages
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                header("Location: ../Views/admin/index.php");
            } else {
                header("Location: ../Views/user/index.php");
            }
            exit;
        }
    }

    // Restrict access to admin pages for non-admin users
    public static function restrictAdminPage() {
        session_start();

        // If user is logged in but doesn't have admin rights, restrict access
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'admin') {
            header("Location: ../Views/user/index.php"); // Redirect to user page or another preferred location
            exit;
        }
    }
}
?>
