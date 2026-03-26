<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Validator.php';

http_response_code(200);
header('Content-Type: application/json');

try {
    // Verificar login
    if (!Auth::check()) {
        http_response_code(401);
        die(json_encode(['exito' => false, 'error' => 'No autorizado']));
    }
    
    $db = Database::getInstance();
    
    // Parámetros
    $q = Validator::sanitizeString($_GET['q'] ?? '');
    $tipo = Validator::sanitizeString($_GET['tipo'] ?? '');
    $ordenar = $_GET['ordenar'] ?? 'reciente';
    $pagina = (int)($_GET['pagina'] ?? 1);
    $limite = min((int)($_GET['limite'] ?? 30), 100);  // Máximo 100
    
    // Validar ordenamiento
    $orden_valido = [
        'reciente' => 'fecha_subida DESC',
        'antiguo' => 'fecha_subida ASC',
        'nombre_az' => 'nombre_original ASC',
        'nombre_za' => 'nombre_original DESC',
        'tamaño_mayor' => 'peso_original DESC',
        'tamaño_menor' => 'peso_original ASC'
    ];
    
    $order_by = $orden_valido[$ordenar] ?? 'fecha_subida DESC';
    
    // Construir query
    $where = [];
    $params = [];
    
    if ($q) {
        $where[] = "(nombre_original LIKE ? OR nombre_archivo LIKE ? OR etiquetas LIKE ? OR alt_text LIKE ?)";
        $busca = "%$q%";
        $params = array_merge($params, [$busca, $busca, $busca, $busca]);
    }
    
    if ($tipo && in_array($tipo, ['habitacion', 'hotel', 'amenidades', 'vista', 'general', 'logo'])) {
        $where[] = "tipo = ?";
        $params[] = $tipo;
    }
    
    $where_str = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
    
    // Contar total
    $count_query = "SELECT COUNT(*) FROM imagenes $where_str";
    $stmtTotal = $db->prepare($count_query);
    $stmtTotal->execute($params);
    $total = $stmtTotal->fetchColumn();
    
    // Paginar
    $offset = ($pagina - 1) * $limite;
    
    // Obtener imágenes
    $query = "SELECT 
        i.*,
        u.nombre_completo as subida_por_nombre
        FROM imagenes i
        LEFT JOIN usuarios u ON i.subida_por = u.id
        $where_str
        ORDER BY $order_by
        LIMIT $limite OFFSET $offset";
    
    $stmtImg = $db->prepare($query);
    $stmtImg->execute($params);
    $imagenes = $stmtImg->fetchAll(PDO::FETCH_ASSOC);
    
    // Procesar campos extra
    foreach ($imagenes as &$img) {
        $img['ahorro_mb'] = isset($img['peso_original']) && (int) $img['peso_original'] > 0 
                            ? round(((int)$img['peso_original'] - (int)$img['peso_webp']) / 1024 / 1024, 2) 
                            : 0;
        $img['peso_mb'] = round((int)$img['peso_webp'] / 1024 / 1024, 2);
    }
    
    echo json_encode([
        'exito' => true,
        'total' => $total,
        'pagina' => $pagina,
        'paginas' => ceil($total / $limite),
        'limite' => $limite,
        'imagenes' => $imagenes
    ]);

} catch (Exception $e) {
    http_response_code(500);
    die(json_encode([
        'exito' => false,
        'error' => 'Error: ' . $e->getMessage()
    ]));
}
