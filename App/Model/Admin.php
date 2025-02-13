<?php


class Admin {
    private $db;
    private $notification;

    public function __construct($db) {
        $this->db = $db;
        $this->notification = new Notification($db);
    }

    public function getAllUsers() {
        try {
            $stmt = $this->db->prepare("SELECT id, name, surname FROM users WHERE role = 'user'");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Debugging: Print the result to check if users are fetched
            if (empty($users)) {
                echo "No users found";
            } else {
                var_dump($users);  // Check the users array
            }

            return $users;
        } catch (Exception $e) {
            return [];
        }
    }

    public function sendDocumentRequestToUser($userId, $message) {
        $this->notification->sendRequest($userId, $message);
    }
}

/**
 * Admin class handles user management and notification functionality
 * 
 * This class provides methods to:
 * - Retrieve all users with 'user' role from the database
 * - Send document requests to users via notifications
 * 
 * @property PDO $db Database connection instance
 * @property Notification $notification Notification handler instance
 */
