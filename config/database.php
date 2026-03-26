<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'hotelcore2');
define('DB_USER', 'root');
define('DB_PASS', '');

define('APP_URL', 'http://localhost/hotel');
define('ADMIN_URL', APP_URL . '/admin');
define('API_URL', APP_URL . '/api');

define('UPLOAD_DIR', __DIR__ . '/../public/uploads');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

define('IDIOMAS_DISPONIBLES', ['es', 'en']);
define('IDIOMA_DEFECTO', 'es');
?>
