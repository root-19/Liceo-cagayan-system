<?php

namespace Controller;

class Auth {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    public function registerUser($name ,$middle_initial, $gender, $school_id, $surname, $date_of_birth, $grade, $section, $strand, $phone_number, $email, $password) {
        $role = 'user';
    
        if ($this->userModel->register($name ,$middle_initial, $gender, $school_id, $surname, $date_of_birth, $grade, $section, $strand, $phone_number, $email, $password, $role)) {
            $user = $this->userModel->login($email, $password);
            if ($user) {
             
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];
                
                
              
                header("Location: ../Views/user/index.php");
                exit();
            }
        } else {
            return "Registration failed: email may already be in use.";
        }
    }

    public function loginUser($email, $password) {
        $user = $this->userModel->login($email, $password);

        if ($user) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];

            // Role-based redirection
            if ($user['role'] === 'admin') {
                header("Location: ../Views/admin/index.php");
                exit();
            } else {
                header("Location: ../Views/user/index.php");
                exit();
            }
        }

        return false;
    }
}
?>
