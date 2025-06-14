<?php
    require_once __DIR__ . '/../database/database.php';
    require_once __DIR__ . '/../../logs/logger.php';
    class User{

        private $db;
        private $minPasswordLength = 6;
        private $logger;

        public function __construct(){
            $this->logger = new Logger();
            $database = new Database();
            $this->db = $database->connectDB();
        }

        public function register($username, $email, $password){

            $inputs = $this->checkInputs($username,$email,$password);
            if($inputs != null){
                return $inputs;
            }

            $emailCheck = $this->checkEmail($email);
            if($emailCheck != null){
                return $emailCheck;
            }

            $hashedPassword = $this->hashPassword($password);
            try{
                $result = $this->insertUser($username, $email, $hashedPassword);
                if ($result){
                    return ['success' => true, 'message' => 'Registration successful'];
                }
                else{
                    $this->logger->logErrorUser("Registration failed in register");
                    return ['success' => false, 'message' => 'Registration failed'];
            }
            } catch(PDOException $e){
                $this->logger->logErrorUser("Database error in register");
                return ['success' => false, 'message' => 'Database error:' . $e->getMessage()];
            }
            
        }
        public function login($email, $password){
            if(empty($email) || empty($password)){
                $this->logger->logErrorUser("Empty fields in login");
                return ['success' => false, 'message' => 'You have empty fields'];
            }

            $result = $this->fetchUser($email, $password);
            if ($result['success']) {
                $token = bin2hex(random_bytes(16));
                $this->updateToken($email, $token);
                $result['token'] = $token;
        }
        return $result;
        }
        private function checkEmail($email){
            try{
                $query = 'SELECT id FROM users WHERE email = :email';
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $this->logger->logErrorUser("Email already registered in checkEmail()");
                    return ['success' => false, 'message' => 'Email already registered'];
                }
                return null;
            } catch (PDOException $e){
                $this->logger->logErrorUser("Database error in checkEmail");
                return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
            }

        }
        private function hashPassword($password){
            $hashPassword = password_hash($password, PASSWORD_BCRYPT);
            return $hashPassword;
        }
        private function insertUser($username, $email, $password){
            try{
                $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->execute();
                return true;
            } catch(PDOException $e){
                $this->logger->logErrorUser("Database error in insertUser");
                return false;
            }
            
        }
        private function fetchUser($email, $password){
            try {
                $query = "SELECT id, email, password FROM users WHERE email = :email";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    return ['success' => true, 'message' => 'Login successful'];
                }
                return ['success' => false, 'message' => 'Invalid email or password'];
            } catch (PDOException $e) {
                $this->logger->logErrorUser("Database error in fetchUser");
                return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
            }
        }
        private function checkInputs($username, $email, $password){
            if(empty($username) || empty($email) || empty($password)){
                $this->logger->logErrorUser("Empty fields in registration");
                return ['success' => false, 'message' => 'You have empty fields'];
            }
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $this->logger->logErrorUser("Incorrect email in registration");
                return ['success' => false, 'message' => 'Incorrect email adress'];
            }
            if(strlen($password) < $this->minPasswordLength){
                $this->logger->logErrorUser("Password is too short in registration");
                return ['success' => false, 'message' => 'Password is too short'];
            }
            return null;
        }
        private function updateToken($email, $token) {
            try {
                $query = "UPDATE users SET token = :token WHERE email = :email";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
            } catch (PDOException $e) {
                $this->logger->logErrorUser("Database error in updateToken: " . $e->getMessage());
            }
        }
    }
?>