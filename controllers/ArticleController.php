<?php
// ArticleController.php
require_once '../config.php';
require_once '../Database.php';

class ArticleController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getArticles($category = null, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT 
                        a.*, 
                        c.name as category_name,
                        u.username as author_name
                    FROM articles a
                    LEFT JOIN categories c ON a.category_id = c.id
                    LEFT JOIN users u ON a.author_id = u.id
                    WHERE 1=1";
            
            $params = [];

            if ($category && $category !== 'latest') {
                $query .= " AND c.slug = :category";
                $params[':category'] = $category;
            }

            $query .= " ORDER BY a.publish_date DESC LIMIT :limit OFFSET :offset";
            $params[':limit'] = (int)$limit;
            $params[':offset'] = (int)$offset;

            $stmt = $this->db->prepare($query);
            foreach ($params as $key => &$value) {
                $stmt->bindParam($key, $value);
            }
            
            $stmt->execute();
            return [
                'success' => true,
                'data' => $stmt->fetchAll()
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getArticleById($id) {
        try {
            $query = "SELECT 
                        a.*, 
                        c.name as category_name,
                        u.username as author_name
                    FROM articles a
                    LEFT JOIN categories c ON a.category_id = c.id
                    LEFT JOIN users u ON a.author_id = u.id
                    WHERE a.id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $article = $stmt->fetch();
            
            return $article ? [
                'success' => true,
                'data' => $article
            ] : [
                'success' => false,
                'error' => 'Article not found'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function searchArticles($term) {
        try {
            $query = "SELECT 
                        a.*, 
                        c.name as category_name,
                        u.username as author_name
                    FROM articles a
                    LEFT JOIN categories c ON a.category_id = c.id
                    LEFT JOIN users u ON a.author_id = u.id
                    WHERE a.title LIKE :term 
                    OR a.content LIKE :term 
                    OR u.username LIKE :term 
                    ORDER BY a.publish_date DESC";
            
            $stmt = $this->db->prepare($query);
            $searchTerm = "%{$term}%";
            $stmt->bindParam(':term', $searchTerm);
            $stmt->execute();
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll()
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Search failed',
                'message' => $e->getMessage()
            ];
        }
    }
}