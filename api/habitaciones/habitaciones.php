<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/CSRF.php';
require_once __DIR__ . '/../../includes/Helpers.php';

header('Content-Type: application/json');

try {
    if (!Auth::check()) {
        http_response_code(401);
        die(json_encode(['exito' => false, 'error' => 'No autorizado']));
    }

    $db = Database::getInstance();
    $method = $_SERVER['REQUEST_METHOD'];

    // ---- GET: Listar todas ----
    if ($method === 'GET') {
        $query = "SELECT h.*, t.nombre AS tipo_nombre,
                  (SELECT tht.precio_regular FROM tipo_habitacion_tarifas tht WHERE tht.tipo_habitacion_id = h.tipo_habitacion_id) AS precio_regular,
                  (SELECT tht.tarifa_manual_activa FROM tipo_habitacion_tarifas tht WHERE tht.tipo_habitacion_id = h.tipo_habitacion_id) AS manual_activa,
                  (SELECT tht.precio_manual FROM tipo_habitacion_tarifas tht WHERE tht.tipo_habitacion_id = h.tipo_habitacion_id) AS precio_manual
                  FROM habitaciones h
                  LEFT JOIN tipos_habitacion t ON h.tipo_habitacion_id = t.id
                  ORDER BY h.numero_habitacion ASC";
        $habitaciones = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($habitaciones as &$hab) {
            $s = $db->prepare("SELECT nombre, descripcion FROM habitaciones_idiomas WHERE habitacion_id = ? AND idioma = 'es'");
            $s->execute([$hab['id']]);
            $idioma = $s->fetch(PDO::FETCH_ASSOC);
            $hab['nombre_es'] = $idioma['nombre'] ?? '';
            $hab['descripcion_es'] = $idioma['descripcion'] ?? '';
            $hab['precio_mostrado'] = $hab['manual_activa'] ? $hab['precio_manual'] : $hab['precio_regular'];
            $hab['tarifa_tipo'] = $hab['manual_activa'] ? 'Manual' : 'Regular';
        }

        echo json_encode(['exito' => true, 'habitaciones' => $habitaciones]);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    if (!CSRF::validate($input['csrf_token'] ?? '')) {
        http_response_code(403);
        die(json_encode(['exito' => false, 'error' => 'Token CSRF inválido']));
    }

    // ---- POST: Crear ----
    if ($method === 'POST' && empty($input['_method'])) {
        $numero = (int)($input['numero_habitacion'] ?? 0);
        $tipo_id = (int)($input['tipo_habitacion_id'] ?? 0);
        $nombre_es = trim($input['nombre_es'] ?? '');
        if (!$tipo_id || !$nombre_es) die(json_encode(['exito' => false, 'error' => 'Tipo y nombre español obligatorios']));

        $slug = Helpers::slugify($nombre_es) . ($numero ? '-' . $numero : '');
        $db->beginTransaction();

        // Tabla principal
        $stmt = $db->prepare("INSERT INTO habitaciones (numero_habitacion, slug, tipo_habitacion_id, estado, capacidad_huespedes, num_camas, precio_base, activa) VALUES (?, ?, ?, ?, ?, ?, 0, ?)");
        $stmt->execute([
            $numero, $slug, $tipo_id,
            $input['estado'] ?? 'disponible',
            (int)($input['capacidad_huespedes'] ?? 2),
            (int)($input['num_camas'] ?? 1),
            !empty($input['activa']) ? 1 : 0
        ]);
        $hab_id = $db->lastInsertId();

        // Idiomas
        guardarIdiomas($db, $hab_id, $input, $slug);
        // Extras
        guardarExtras($db, $hab_id, $input);

        $db->commit();
        echo json_encode(['exito' => true, 'id' => $hab_id, 'mensaje' => 'Habitación creada']);
        exit;
    }

    // ---- PUT: Actualizar ----
    if ($method === 'POST' && ($input['_method'] ?? '') === 'PUT') {
        $id = (int)($input['id'] ?? 0);
        if (!$id) die(json_encode(['exito' => false, 'error' => 'ID requerido']));

        $numero = (int)($input['numero_habitacion'] ?? 0);
        $tipo_id = (int)($input['tipo_habitacion_id'] ?? 0);
        $nombre_es = trim($input['nombre_es'] ?? '');
        $slug = Helpers::slugify($nombre_es) . ($numero ? '-' . $numero : '');

        $db->beginTransaction();

        $stmt = $db->prepare("UPDATE habitaciones SET numero_habitacion=?, slug=?, tipo_habitacion_id=?, estado=?, capacidad_huespedes=?, num_camas=?, activa=? WHERE id=?");
        $stmt->execute([
            $numero, $slug, $tipo_id,
            $input['estado'] ?? 'disponible',
            (int)($input['capacidad_huespedes'] ?? 2),
            (int)($input['num_camas'] ?? 1),
            !empty($input['activa']) ? 1 : 0,
            $id
        ]);

        // Idiomas — update or insert
        guardarIdiomasUpdate($db, $id, $input, $slug);
        // Extras — borrar y reinsertar
        guardarExtras($db, $id, $input, true);

        $db->commit();
        echo json_encode(['exito' => true]);
        exit;
    }

    // ---- DELETE ----
    if ($method === 'POST' && ($input['_method'] ?? '') === 'DELETE') {
        $id = (int)($input['id'] ?? 0);
        if (!$id) die(json_encode(['exito' => false, 'error' => 'ID requerido']));
        $db->beginTransaction();
        foreach (['habitacion_faqs','habitacion_imagenes','habitacion_configuracion_camas','habitacion_amenities','habitacion_comodidades','habitaciones_idiomas'] as $tabla) {
            $db->prepare("DELETE FROM $tabla WHERE habitacion_id = ?")->execute([$id]);
        }
        $db->prepare("DELETE FROM seo_geo WHERE tipo='habitacion' AND tipo_id=?")->execute([$id]);
        $db->prepare("DELETE FROM habitaciones WHERE id = ?")->execute([$id]);
        $db->commit();
        echo json_encode(['exito' => true]);
        exit;
    }

    die(json_encode(['exito' => false, 'error' => 'Método no soportado']));

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    http_response_code(500);
    echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
}

// ========== FUNCIONES AUXILIARES ==========

function guardarIdiomas($db, $hab_id, $input, $slug) {
    $nombre_en = trim($input['nombre_en'] ?? '') ?: trim($input['nombre_es'] ?? '');
    $desc_en = trim($input['descripcion_en'] ?? '') ?: trim($input['descripcion_es'] ?? '');
    $slug_en = Helpers::slugify($nombre_en);

    // ES
    $stmt = $db->prepare("INSERT INTO habitaciones_idiomas (habitacion_id, idioma, nombre, descripcion, slug, seo_titulo, seo_descripcion, seo_palabras_clave, schema_json) VALUES (?, 'es', ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $hab_id, trim($input['nombre_es']), trim($input['descripcion_es'] ?? ''), $slug,
        $input['seo_titulo_es'] ?? '', $input['seo_descripcion_es'] ?? '', $input['seo_palabras_clave_es'] ?? '',
        $input['schema_json'] ?? ''
    ]);

    // EN
    $stmt = $db->prepare("INSERT INTO habitaciones_idiomas (habitacion_id, idioma, nombre, descripcion, slug, seo_titulo, seo_descripcion, seo_palabras_clave, schema_json) VALUES (?, 'en', ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $hab_id, $nombre_en, $desc_en, $slug_en,
        $input['seo_titulo_en'] ?? '', $input['seo_descripcion_en'] ?? '', $input['seo_palabras_clave_en'] ?? '',
        $input['schema_json'] ?? ''
    ]);
}

function guardarIdiomasUpdate($db, $hab_id, $input, $slug) {
    $nombre_en = trim($input['nombre_en'] ?? '') ?: trim($input['nombre_es'] ?? '');
    $desc_en = trim($input['descripcion_en'] ?? '') ?: trim($input['descripcion_es'] ?? '');
    $slug_en = Helpers::slugify($nombre_en);

    $stmt = $db->prepare("UPDATE habitaciones_idiomas SET nombre=?, descripcion=?, slug=?, seo_titulo=?, seo_descripcion=?, seo_palabras_clave=?, schema_json=? WHERE habitacion_id=? AND idioma='es'");
    $stmt->execute([
        trim($input['nombre_es']), trim($input['descripcion_es'] ?? ''), $slug,
        $input['seo_titulo_es'] ?? '', $input['seo_descripcion_es'] ?? '', $input['seo_palabras_clave_es'] ?? '',
        $input['schema_json'] ?? '', $hab_id
    ]);

    $stmt = $db->prepare("UPDATE habitaciones_idiomas SET nombre=?, descripcion=?, slug=?, seo_titulo=?, seo_descripcion=?, seo_palabras_clave=?, schema_json=? WHERE habitacion_id=? AND idioma='en'");
    $stmt->execute([
        $nombre_en, $desc_en, $slug_en,
        $input['seo_titulo_en'] ?? '', $input['seo_descripcion_en'] ?? '', $input['seo_palabras_clave_en'] ?? '',
        $input['schema_json'] ?? '', $hab_id
    ]);
}

function guardarExtras($db, $hab_id, $input, $esUpdate = false) {
    // Si es update, limpiar registros previos
    if ($esUpdate) {
        foreach (['habitacion_comodidades','habitacion_amenities','habitacion_configuracion_camas','habitacion_imagenes','habitacion_faqs'] as $t) {
            $db->prepare("DELETE FROM $t WHERE habitacion_id = ?")->execute([$hab_id]);
        }
        $db->prepare("DELETE FROM seo_geo WHERE tipo='habitacion' AND tipo_id=?")->execute([$hab_id]);
    }

    // Comodidades
    $comodidades = $input['comodidades'] ?? [];
    if (!empty($comodidades)) {
        $json = json_encode($comodidades);
        $db->prepare("INSERT INTO habitacion_comodidades (habitacion_id, idioma, comodidades) VALUES (?, 'es', ?)")->execute([$hab_id, $json]);
        $db->prepare("INSERT INTO habitacion_comodidades (habitacion_id, idioma, comodidades) VALUES (?, 'en', ?)")->execute([$hab_id, $json]);
    }

    // Amenities
    $amenities = $input['amenities'] ?? [];
    if (!empty($amenities)) {
        $json = json_encode($amenities);
        $db->prepare("INSERT INTO habitacion_amenities (habitacion_id, idioma, amenities) VALUES (?, 'es', ?)")->execute([$hab_id, $json]);
        $db->prepare("INSERT INTO habitacion_amenities (habitacion_id, idioma, amenities) VALUES (?, 'en', ?)")->execute([$hab_id, $json]);
    }

    // Camas
    $camas = $input['camas'] ?? [];
    if (!empty($camas)) {
        $db->prepare("INSERT INTO habitacion_configuracion_camas (habitacion_id, camas) VALUES (?, ?)")->execute([$hab_id, json_encode($camas)]);
    }

    // Imágenes
    $imagenes = $input['imagenes'] ?? [];
    foreach ($imagenes as $img) {
        $db->prepare("INSERT INTO habitacion_imagenes (habitacion_id, imagen_id, es_principal, orden) VALUES (?, ?, ?, ?)")
            ->execute([$hab_id, (int)$img['imagen_id'], (int)($img['es_principal'] ?? 0), (int)($img['orden'] ?? 0)]);
    }

    // FAQs
    $faqs = $input['faqs'] ?? [];
    foreach ($faqs as $i => $faq) {
        if (!empty($faq['pregunta_es'])) {
            $db->prepare("INSERT INTO habitacion_faqs (habitacion_id, idioma, pregunta, respuesta, orden) VALUES (?, 'es', ?, ?, ?)")
                ->execute([$hab_id, $faq['pregunta_es'], $faq['respuesta_es'] ?? '', $i]);
        }
        if (!empty($faq['pregunta_en'])) {
            $db->prepare("INSERT INTO habitacion_faqs (habitacion_id, idioma, pregunta, respuesta, orden) VALUES (?, 'en', ?, ?, ?)")
                ->execute([$hab_id, $faq['pregunta_en'], $faq['respuesta_en'] ?? '', $i]);
        }
    }

    // GEO
    $geo = $input['geo'] ?? null;
    if ($geo && ($geo['latitud'] || $geo['pais'] || $geo['ciudad'])) {
        $db->prepare("INSERT INTO seo_geo (tipo, tipo_id, idioma, latitud, longitud, pais, region, ciudad, keywords_geo) VALUES ('habitacion', ?, 'es', ?, ?, ?, ?, ?, ?)")
            ->execute([$hab_id, $geo['latitud'] ?: null, $geo['longitud'] ?: null, $geo['pais'] ?? '', $geo['region'] ?? '', $geo['ciudad'] ?? '', $geo['keywords_geo'] ?? '']);
    }
}
