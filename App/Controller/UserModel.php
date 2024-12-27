<?php

class UserModel {
    private $conn;
    private $accounts = "users";

    public function __construct($db) {
        $this->conn = $db;
    }
  
    public function register($name, $middle_initial, $gender,  $school_id, $surname, $date_of_birth, $grade, $section, $strand, $phone_number, $email, $password, $role) {
        // Check if the email already exists
        $query = "SELECT * FROM " . $this->accounts . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // If email exists, return false (indicating a failure)
            return false;
        }
        $query = "INSERT INTO " . $this->accounts . " 
        (name, middle_initial, gender, school_id, surname, date_of_birth, grade, strand, section, phone_number, email, password, role) 
        VALUES (:name, :middle_initial, :gender, :school_id, :surname, :date_of_birth, :grade, :strand, :section, :phone_number, :email, :password, :role)";
        
    $stmt = $this->conn->prepare($query);

       // Hash the password before saving it
       $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':middle_initial', $middle_initial);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':school_id', $school_id);  // Ensure this matches
    $stmt->bindParam(':surname', $surname);
    $stmt->bindParam(':date_of_birth', $date_of_birth);
    $stmt->bindParam(':grade', $grade);
    $stmt->bindParam(':strand', $strand);
    $stmt->bindParam(':section', $section);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':role', $role);
    
    // Execute the query
    if ($stmt->execute()) {
        return true;  
    }
    
    return false;  // Registration failed
    
    }




    public function login($email, $password) {
        // Prepare SQL query to get the user by email
        $stmt = $this->conn->prepare("SELECT id, name, email, role, password FROM " . $this->accounts . " WHERE email = ?");
        
        // Bind the parameter using PDO's bindParam method
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        
        // Execute the statement
        $stmt->execute();
        
        // Fetch the result
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if a user is found
        if ($user) {
            // Verify the password against the stored hash
            if (password_verify($password, $user['password'])) {
                return $user;  // Return user data if password is correct
            }
        }
        return false;  // Return false if no user found or password mismatch
    }
}
?>