header('Content-Type: application/json');
require_once '../NewsletterController.php';

$controller = new NewsletterController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $email = $_POST['email'] ?? '';

    if (!$email) {
        echo json_encode(['success' => false, 'error' => 'Email required']);
        exit;
    }

    switch ($action) {
        case 'subscribe':
            echo json_encode($controller->subscribe($email));
            break;
            
        case 'unsubscribe':
            echo json_encode($controller->unsubscribe($email));
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}