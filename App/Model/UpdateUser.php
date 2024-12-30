<?php
class UpdateUser {
    private $conn;
    private $accounts = 'users'; // Replace 'users' with your table name

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->accounts . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $data) {
        $query = "UPDATE " . $this->accounts . " 
                  SET 
                      name = :name,
                      middle_initial = :middle_initial,
                      gender = :gender,
                      school_id = :school_id,
                      surname = :surname,
                      date_of_birth = :date_of_birth,
                      grade = :grade,
                      section = :section,
                      strand = :strand,
                      phone_number = :phone_number,
                      email = :email
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':middle_initial', $data['middle_initial'], PDO::PARAM_STR);
        $stmt->bindParam(':gender', $data['gender'], PDO::PARAM_STR);
        $stmt->bindParam(':school_id', $data['school_id'], PDO::PARAM_STR);
        $stmt->bindParam(':surname', $data['surname'], PDO::PARAM_STR);
        $stmt->bindParam(':date_of_birth', $data['date_of_birth'], PDO::PARAM_STR);
        $stmt->bindParam(':grade', $data['grade'], PDO::PARAM_STR);
        $stmt->bindParam(':section', $data['section'], PDO::PARAM_STR);
        $stmt->bindParam(':strand', $data['strand'], PDO::PARAM_STR);
        $stmt->bindParam(':phone_number', $data['phone_number'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);

        return $stmt->execute();
    }
}
