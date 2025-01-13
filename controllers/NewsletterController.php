<?php
// NewsletterController.php
require_once 'config.php';
require_once 'Database.php';

class NewsletterController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function subscribe($email) {
        try {
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'error' => 'Invalid email address'
                ];
            }

            // Check if email already exists
            $checkQuery = "SELECT id FROM newsletter_subscribers WHERE email = :email";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':email', $email);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                return [
                    'success' => false,
                    'error' => 'Email already subscribed'
                ];
            }

            // Insert new subscription
            $query = "INSERT INTO newsletter_subscribers (email, subscribe_date, status) 
                     VALUES (:email, NOW(), 'active')";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return [
                'success' => true,
                'message' => 'Successfully subscribed to newsletter!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Subscription failed',
                'message' => $e->getMessage()
            ];
        }
    }

    public function unsubscribe($email) {
        try {
            $query = "UPDATE newsletter_subscribers 
                     SET status = 'unsubscribed', unsubscribe_date = NOW() 
                     WHERE email = :email";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Successfully unsubscribed'
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Email not found in subscribers list'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Unsubscribe failed',
                'message' => $e->getMessage()
            ];
        }
    }
}