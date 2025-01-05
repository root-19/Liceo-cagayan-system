<?php
include "Admin.php";
class Notification {
    private $db;

    public function __construct($db) {
        $this->db = $db; // Corrected the typo
    }

    public function sendRequest($userId, $message) {
        try {
            $stmt = $this->db->prepare("INSERT INTO notifications (user_id, message, status) VALUES (?, ?, ?)");
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->bindValue(2, $message, PDO::PARAM_STR);
            $stmt->bindValue(3, 'Pending', PDO::PARAM_STR); // 'Pending' by default
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Function to fetch all notifications for a user
    public function getRequestsByUser($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Function to update notification status
    public function updateRequestStatus($notificationId, $status) {
        try {
            $stmt = $this->db->prepare("UPDATE notifications SET status = ? WHERE id = ?");
            $stmt->bindValue(1, $status, PDO::PARAM_STR);
            $stmt->bindValue(2, $notificationId, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function getUnreadCountByUser($userId) {
        $query = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchColumn();  // Returns the count of unread notifications
    }
    
}
