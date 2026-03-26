<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageHandler {
    
    const MAX_WEIGHT = 5 * 1024 * 1024;  // 5MB
    const MIN_WIDTH = 32;   // Bajado a 32px para permitir favicons e iconos
    const MIN_HEIGHT = 32;  // Bajado a 32px para permitir favicons e iconos
    const MAX_WIDTH = 4000;
    const MAX_HEIGHT = 4000;
    
    const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/svg+xml',
        'image/x-icon',
        'image/vnd.microsoft.icon'
    ];
    
    /**
     * Procesar imagen subida (multi-tamaño + WebP)
     * 
     * @param string $archivo_tmp Ruta temporal del archivo
     * @param string $nombre_original Nombre original del archivo
     * @param string $tipo Tipo de imagen (habitacion, hotel, etc)
     * @param string $alt_text Texto alternativo
     * @param string $etiquetas Etiquetas separadas por coma
     * @return array ['exito' => true/false, 'datos' => [...], 'error' => '...']
     */
    public static function procesarImagen(
        $archivo_tmp,
        $nombre_original,
        $tipo = 'general',
        $alt_text = '',
        $etiquetas = ''
    ) {
        try {
            // DIRECTORIO BASE DE UPLOADS
            $upload_dir = realpath(__DIR__ . '/../public/uploads');

            // 1. VALIDAR ARCHIVO
            $validacion = self::validarArchivo($archivo_tmp, $nombre_original);
            if (!$validacion['valido']) {
                return [
                    'exito' => false,
                    'error' => $validacion['error']
                ];
            }
            
            // 2. OBTENER PESO ORIGINAL ANTES DE PROCESAR
            $peso_original = filesize($archivo_tmp);
            
            // 3. GENERAR NOMBRE ÚNICO
            $nombre_archivo = self::generarNombreUnico($nombre_original);
            
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $archivo_tmp);
            finfo_close($finfo);

            $datos_archivos = [];

            // SI ES UN SVG O ICO, SIMPLEMENTE LO COPIAMOS (No se puede procesar con Intervention y perdería calidad/animación)
            if ($mime === 'image/svg+xml' || str_contains($mime, 'icon')) {
                // Conservar extensión original para svg y favicons
                $ext = pathinfo($nombre_original, PATHINFO_EXTENSION);
                $nombre_archivo = pathinfo($nombre_archivo, PATHINFO_FILENAME) . '.' . $ext;
                
                $ruta_webp = $upload_dir . '/webp/' . $nombre_archivo; // Lo guardamos en webp pero en su formato original
                copy($archivo_tmp, $ruta_webp);
                
                // Las otras carpetas tendrán el mismo archivo físico copiándose (o symlinks) para no romper endpoints webp
                $ruta_thumbnail = $upload_dir . '/thumbnails/' . $nombre_archivo;
                $ruta_tiny = $upload_dir . '/tiny/' . $nombre_archivo;
                copy($archivo_tmp, $ruta_thumbnail);
                copy($archivo_tmp, $ruta_tiny);

                $peso_optimizado = filesize($ruta_webp);
                $datos_archivos['webp'] = ['ruta' => $ruta_webp, 'peso' => $peso_optimizado];
                $datos_archivos['thumbnail'] = ['ruta' => $ruta_thumbnail, 'peso' => $peso_optimizado];
                $datos_archivos['tiny'] = ['ruta' => $ruta_tiny, 'peso' => $peso_optimizado];

            } else {
                // 4. CREAR INSTANCIA DE INTERVENTION IMAGE v3
                $manager = new ImageManager(new Driver());
                
                // 5. PROCESAR A 3 TAMAÑOS (Solo imagenes raster JPEG, PNG, WEBP)
                
                // 5a. Imagen Original (máximo 1920x1080)
                $imagen = $manager->read($archivo_tmp);
                
                // Redimensionar si es mayor
                if ($imagen->width() > 1920 || $imagen->height() > 1080) {
                    $imagen->scaleDown(width: 1920, height: 1080);
                }
                
                $nombre_archivo = pathinfo($nombre_archivo, PATHINFO_FILENAME) . '.webp'; // Forzar extensión WEBP
                $ruta_webp = $upload_dir . '/webp/' . $nombre_archivo;
                file_put_contents($ruta_webp, $imagen->toWebp(80));
                
                $datos_archivos['webp'] = [
                    'ruta' => $ruta_webp,
                    'peso' => filesize($ruta_webp)
                ];
                
                // 5b. Thumbnail (300x200) - Conservamos ratio usando contain o cover segun necesidad
                // Cover corta la imagen rellenando el marco
                $thumbnail = $manager->read($archivo_tmp);
                $thumbnail->cover(width: 300, height: 200);
                
                $ruta_thumbnail = $upload_dir . '/thumbnails/' . $nombre_archivo;
                file_put_contents($ruta_thumbnail, $thumbnail->toWebp(75));
                
                $datos_archivos['thumbnail'] = [
                    'ruta' => $ruta_thumbnail,
                    'peso' => filesize($ruta_thumbnail)
                ];
                
                // 5c. Tiny (150x100)
                $tiny = $manager->read($archivo_tmp);
                $tiny->cover(width: 150, height: 100);
                
                $ruta_tiny = $upload_dir . '/tiny/' . $nombre_archivo;
                file_put_contents($ruta_tiny, $tiny->toWebp(70));
                
                $datos_archivos['tiny'] = [
                    'ruta' => $ruta_tiny,
                    'peso' => filesize($ruta_tiny)
                ];
            }
            
            // 6. CALCULAR AHORRO
            $peso_total_optimizado = array_sum(array_column($datos_archivos, 'peso'));
            $porcentaje_ahorro = $peso_original > 0 ? round((1 - ($peso_total_optimizado / $peso_original)) * 100) : 0;
            if($porcentaje_ahorro < 0) $porcentaje_ahorro = 0; // Si el optimizado pesara mas
            
            // Retornar rutas relativas seguras para BD
            $rel_webp = '/hotel/public/uploads/webp/' . basename($datos_archivos['webp']['ruta']);
            $rel_thumb = '/hotel/public/uploads/thumbnails/' . basename($datos_archivos['thumbnail']['ruta']);
            $rel_tiny = '/hotel/public/uploads/tiny/' . basename($datos_archivos['tiny']['ruta']);

            // 7. RETORNAR DATOS PARA INSERCIÓN EN BD
            return [
                'exito' => true,
                'datos' => [
                    'nombre_original' => $nombre_original,
                    'nombre_archivo' => $nombre_archivo,
                    'slug' => self::slugify($nombre_original),
                    'peso_original' => $peso_original,
                    'peso_webp' => $datos_archivos['webp']['peso'],
                    'ruta_original' => $rel_webp, // Apuntamos original al WEBP más grande para consumo real
                    'ruta_webp' => $rel_webp,
                    'ruta_thumbnail' => $rel_thumb,
                    'ruta_tiny' => $rel_tiny,
                    'alt_text' => $alt_text,
                    'etiquetas' => $etiquetas,
                    'tipo' => $tipo,
                    'porcentaje_ahorro' => $porcentaje_ahorro
                ]
            ];
            
        } catch (Exception $e) {
            // Limpiar archivos si algo falla
            if (isset($ruta_webp) && file_exists($ruta_webp)) @unlink($ruta_webp);
            if (isset($ruta_thumbnail) && file_exists($ruta_thumbnail)) @unlink($ruta_thumbnail);
            if (isset($ruta_tiny) && file_exists($ruta_tiny)) @unlink($ruta_tiny);
            
            return [
                'exito' => false,
                'error' => 'Error procesando imagen: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Validar archivo subido (exhaustivo)
     * 
     * @param string $archivo_tmp
     * @param string $nombre_original
     * @return array ['valido' => true/false, 'error' => '...']
     */
    private static function validarArchivo($archivo_tmp, $nombre_original) {
        // 1. Verificar que archivo existe
        if (!file_exists($archivo_tmp) || !is_uploaded_file($archivo_tmp)) {
            return ['valido' => false, 'error' => 'Archivo no encontrado o no subido por HTTP'];
        }
        
        // 2. Validar peso
        $peso = filesize($archivo_tmp);
        if ($peso > self::MAX_WEIGHT) {
            $mb_actual = round($peso / 1024 / 1024, 1);
            $mb_maximo = round(self::MAX_WEIGHT / 1024 / 1024, 1);
            return [
                'valido' => false,
                'error' => "Archivo muy grande ({$mb_actual}MB). Máximo: {$mb_maximo}MB"
            ];
        }
        
        if ($peso < 100) {  // Mínimo 100 Bytes (Para soportar SVG pequeños)
            return ['valido' => false, 'error' => 'Archivo corrupto o muy pequeño'];
        }
        
        // 3. Validar MIME type (doble validación)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $archivo_tmp);
        finfo_close($finfo);
        
        if (!in_array($mime, self::ALLOWED_MIMES)) {
            return [
                'valido' => false,
                'error' => "Tipo de archivo no permitido. MIME: {$mime}"
            ];
        }
        
        // 4. Validar que es imagen real (getimagesize) para Raster Images
        if ($mime !== 'image/svg+xml') {
            $info = @getimagesize($archivo_tmp);
            if ($info === false) {
                return ['valido' => false, 'error' => 'No es una imagen válida o está corrupta'];
            }
            
            // 5. Validar dimensiones
            $width = $info[0];
            $height = $info[1];
            
            if ($width < self::MIN_WIDTH || $height < self::MIN_HEIGHT) {
                return [
                    'valido' => false,
                    'error' => "Resolución muy baja ({$width}x{$height}). Mínimo: " .
                              self::MIN_WIDTH . "x" . self::MIN_HEIGHT . "px"
                ];
            }
            
            if ($width > self::MAX_WIDTH || $height > self::MAX_HEIGHT) {
                return [
                    'valido' => false,
                    'error' => "Resolución muy alta ({$width}x{$height}). Máximo: " .
                              self::MAX_WIDTH . "x" . self::MAX_HEIGHT . "px"
                ];
            }
        }
        
        // 6. Validar nombre original (sanitizar)
        if (strlen($nombre_original) > 255) {
            return ['valido' => false, 'error' => 'Nombre de archivo muy largo. Mámixo 255 caracteres.'];
        }
        
        if (preg_match('/\x00|\.\.\//', $nombre_original)) {
            return ['valido' => false, 'error' => 'Nombre de archivo inválido por razones de seguridad.'];
        }
        
        return ['valido' => true];
    }
    
    /**
     * Generar nombre único para archivo
     * 
     * @param string $nombre_original
     * @return string Nombre único (ej: "suite-deluxe-xyw9z.webp")
     */
    private static function generarNombreUnico($nombre_original) {
        $nombre_sin_ext = pathinfo($nombre_original, PATHINFO_FILENAME);
        $slug = self::slugify($nombre_sin_ext);
        $random = substr(bin2hex(random_bytes(3)), 0, 5);
        return $slug . '-' . $random . '.webp';
    }
    
    /**
     * Convertir texto a slug URL-friendly
     * 
     * @param string $texto
     * @return string
     */
    private static function slugify($texto) {
        $texto = mb_strtolower($texto, 'UTF-8');
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto);
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
        return trim($texto, '-');
    }
    
    /**
     * Eliminar imagen (archivos físicos + BD)
     * 
     * @param int $imagen_id
     * @return array ['exito' => true/false, 'error' => '...']
     */
    public static function eliminarImagen($imagen_id) {
        try {
            $db = Database::getInstance();
            
            // Obtener rutas de imagen
            $stmt = $db->prepare("SELECT * FROM imagenes WHERE id = ? LIMIT 1");
            $stmt->execute([$imagen_id]);
            $imagen = $stmt->fetch();
            
            if (!$imagen) {
                return ['exito' => false, 'error' => 'Imagen no encontrada en la base de datos'];
            }
            
            // Transformar las URL a Paths fisicos seguros
            $basePath = realpath(__DIR__ . '/../../');
            
            // Eliminar archivos físicos
            $rutas_relativas = [
                $imagen['ruta_webp'],
                $imagen['ruta_thumbnail'],
                $imagen['ruta_tiny'],
                $imagen['ruta_original']
            ];
            
            foreach ($rutas_relativas as $ruta_rel) {
                if ($ruta_rel) {
                    // /hotel/public/uploads/xxx -> /public/uploads/xxx
                    $cleanedPath = str_replace('/hotel/', '/', $ruta_rel);
                    $fullPath = $basePath . $cleanedPath;
                    if (file_exists($fullPath) && is_file($fullPath)) {
                        @unlink($fullPath);
                    }
                }
            }
            
            // Eliminar registro de BD
            $delStmt = $db->prepare("DELETE FROM imagenes WHERE id = ?");
            $delStmt->execute([$imagen_id]);
            
            return ['exito' => true];
            
        } catch (Exception $e) {
            return [
                'exito' => false,
                'error' => 'Error eliminando imagen: ' . $e->getMessage()
            ];
        }
    }
}
