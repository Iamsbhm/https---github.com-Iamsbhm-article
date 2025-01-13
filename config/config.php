<?php
// config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'news_portal');
define('DB_USER', 'user');
define('DB_PASS', 'password');
define('SITE_URL', 'http://localhost/news-portal');

// Error reporting for development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Time zone setting
date_default_timezone_set('UTC');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

// Maximum file upload size
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');