<?php

class UserModel {
    private $conn;
    private $accounts = "users";

    public function __construct($db) {
        $this->conn = $db;
    }
  
    public function register($name, $middle_initial, $gender,  $school_id, $surname, $date_of_birth, $grade, $section, $strand, $phone_number, $email, $password, $role) {
        
        // Validate if email ends with '@edu.ph'
    if (!str_ends_with($email, '@edu.ph')) {
        return false; // Fail registration if email is invalid
    }

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

   
    public function getUsersByRole($role) {
        // Prepare the SQL query
        $query = "SELECT school_id, name, surname, email FROM " . $this->accounts . " WHERE role = :role";
        $stmt = $this->conn->prepare($query);

        // Bind the role parameter
        $stmt->bindParam(':role', $role);

        // Execute the query
        $stmt->execute();

        // Fetch and return the result
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch users with role and search term (optional)
    public function getUsersByRoleAndSearch($role, $searchTerm = '') {
        $query = "SELECT school_id, name, surname, email FROM " . $this->accounts . " WHERE role = :role";
        
        // Add search filter if a search term is provided
        if (!empty($searchTerm)) {
            $query .= " AND school_id LIKE :searchTerm";
        }

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':role', $role);

        if (!empty($searchTerm)) {
            $searchTerm = "%" . $searchTerm . "%";
            $stmt->bindParam(':searchTerm', $searchTerm);
        }

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Check if email exists in the users table
    public function checkEmailExists($email) {

        if (!preg_match('/@edu\.ph$/', $email)) {
            return false;
        }

    // prepare the query
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->accounts . " WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // update the password sana all INA UPDATE
    public function updatePassword($email, $new_password) {
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE " . $this->accounts . " SET password = :password WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);

        return $stmt->execute();
    }
}
    

?>