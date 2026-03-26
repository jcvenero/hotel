<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Database.php';

try {
    $db = Database::getInstance();
    
    $email = 'admin@hotel.com';
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Check if user exists
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "El usuario admin@hotel.com ya existe.\n";
        exit;
    }
    
    $stmt = $db->prepare("INSERT INTO usuarios (email, password_hash, nombre_completo, rol, activo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $email,
        $hash,
        'Super Administrador',
        'super_admin',
        1
    ]);
    
    echo "¡Super Administrador creado exitosamente!\nEmail: admin@hotel.com\nPassword: admin123\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
