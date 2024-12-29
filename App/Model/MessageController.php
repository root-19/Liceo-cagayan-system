<?php
include_once __DIR__ . '/../Database/Database.php';  // Path to Database class
include_once __DIR__ . '/../Controller/UserModel.php';  // Path to UserModel class
include_once 'Message.php';  // Path to Message class

class MessageController {
    private $conn;
    private $message;
    private $user;

    // Constructor initializes database connection and model instances
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();

        // Initialize Message and UserModel objects
        $this->message = new Message($this->conn);
        $this->user = new UserModel($this->conn);
    }

    // Send a message from the user
    public function sendUserMessage($user_id, $message_text) {
        $this->message->user_id = $user_id;
        $this->message->sender = 'user';
        $this->message->message = $message_text;
        $this->message->reply_to = null;

        return $this->message->sendMessage();
    }

    // Send a reply from the admin
    public function sendAdminReply($message_id, $admin_id, $reply_text) {
        // Ensure reply is not empty
        if (empty($reply_text)) {
            return false;
        }

        // Set message properties for the reply
        $this->message->user_id = $admin_id;
        $this->message->sender = 'admin';
        $this->message->message = $reply_text;
        $this->message->reply_to = $message_id;

        // Insert reply into the database
        return $this->message->sendMessage();
    }

    // Fetch all messages for a specific user (including replies)
    public function fetchMessages($user_id) {
        // Retrieve all messages (including replies) for a specific user
        $query = "
            SELECT m.id, m.sender, m.message, m.reply_to
            FROM messages m
            WHERE m.user_id = :user_id OR m.reply_to IN (SELECT id FROM messages WHERE user_id = :user_id)
            ORDER BY m.id ASC";  // Chronological order
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve all users with messages
    public function getUsersWithMessages() {
        $query = "SELECT DISTINCT u.id, u.name, u.surname
                  FROM users u
                  JOIN messages m ON u.id = m.user_id
                  ORDER BY u.name ASC";  // Alphabetical order
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch the original message being replied to
    public function getOriginalMessage($message_id) {
        $query = "SELECT message FROM messages WHERE id = :message_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':message_id', $message_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

