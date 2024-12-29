<?php
class Message {
    private $conn;
    public $id;
    public $user_id;
    public $sender;
    public $message;
    public $reply_to;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Insert message into database
    public function sendMessage() {
        $query = "INSERT INTO messages (user_id, sender, message, reply_to) 
                  VALUES (:user_id, :sender, :message, :reply_to)";
        $stmt = $this->conn->prepare($query);

        // Bind values to query
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':sender', $this->sender);
        $stmt->bindParam(':message', $this->message);
        $stmt->bindParam(':reply_to', $this->reply_to);

        // Execute and return success or failure
        return $stmt->execute();
    }

    // Get messages and their replies for a user
    public function getMessages($user_id) {
        // Query to get messages along with replies
        $query = "
            SELECT m.id, m.sender, m.message, m.reply_to
            FROM messages m
            WHERE m.user_id = :user_id
            ORDER BY m.id ASC";  // Order messages in chronological order

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
