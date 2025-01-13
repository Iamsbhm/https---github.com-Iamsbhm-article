<?php
// api/articles.php
header('Content-Type: application/json');
require_once '../ArticleController.php';

$controller = new ArticleController();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $category = $_GET['category'] ?? null;
        $limit = $_GET['limit'] ?? 10;
        $offset = $_GET['offset'] ?? 0;
        echo json_encode($controller->getArticles($category, $limit, $offset));
        break;
        
    case 'get':
        $id = $_GET['id'] ?? null;
        if ($id) {
            echo json_encode($controller->getArticleById($id));
        } else {
            echo json_encode(['success' => false, 'error' => 'Article ID required']);
        }
        break;
        
    case 'search':
        $term = $_GET['q'] ?? '';
        if ($term) {
            echo json_encode($controller->searchArticles($term));
        } else {
            echo json_encode(['success' => false, 'error' => 'Search term required']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}