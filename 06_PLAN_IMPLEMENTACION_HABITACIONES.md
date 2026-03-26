 **PARTE 1: SCHEMA BD \+ CLASES PHP \+ ENDPOINTS API**

### **1\. SCHEMA DE BASE DE DATOS**

sql  
CREATE TABLE IF NOT EXISTS tipos\_habitacion (  
    id INT PRIMARY KEY AUTO\_INCREMENT,  
    nombre VARCHAR(100) NOT NULL,  
    slug VARCHAR(100) NOT NULL UNIQUE,  
    descripcion VARCHAR(500),  
    activo BOOLEAN DEFAULT TRUE,  
    INDEX idx\_slug (slug)  
);

CREATE TABLE IF NOT EXISTS habitaciones (  
    id INT PRIMARY KEY AUTO\_INCREMENT,  
    numero\_habitacion INT NOT NULL,  
    slug VARCHAR(255) NOT NULL UNIQUE,  
    tipo\_habitacion\_id INT NOT NULL,  
    estado ENUM('disponible', 'ocupada', 'mantenimiento') DEFAULT 'disponible',  
    capacidad\_huespedes INT NOT NULL,  
    num\_camas INT NOT NULL,  
    precio\_base DECIMAL(10, 2) NOT NULL,  
    activa BOOLEAN DEFAULT TRUE,  
    rating DECIMAL(3,1) DEFAULT 0,  
    numero\_resenas INT DEFAULT 0,  
    numero\_reservas INT DEFAULT 0,  
    sincronizar\_booking BOOLEAN DEFAULT FALSE,  
    sincronizar\_airbnb BOOLEAN DEFAULT FALSE,  
    id\_externo\_booking VARCHAR(255),  
    id\_externo\_airbnb VARCHAR(255),  
    fecha\_creacion TIMESTAMP DEFAULT CURRENT\_TIMESTAMP,  
    ultima\_actualizacion DATETIME ON UPDATE CURRENT\_TIMESTAMP,  
    FOREIGN KEY (tipo\_habitacion\_id) REFERENCES tipos\_habitacion(id) ON DELETE RESTRICT,  
    INDEX idx\_slug (slug),  
    INDEX idx\_estado (estado),  
    INDEX idx\_tipo (tipo\_habitacion\_id),  
    INDEX idx\_activa (activa)  
);

CREATE TABLE IF NOT EXISTS habitaciones\_idiomas (  
    id INT PRIMARY KEY AUTO\_INCREMENT,  
    habitacion\_id INT NOT NULL,  
    idioma ENUM('es', 'en') NOT NULL,  
    nombre VARCHAR(255) NOT NULL,  
    descripcion LONGTEXT NOT NULL,  
    slug VARCHAR(255) NOT NULL,  
    seo\_titulo VARCHAR(160),  
    seo\_descripcion VARCHAR(160),  
    seo\_palabras\_clave VARCHAR(255),  
    schema\_json LONGTEXT,  
    UNIQUE KEY unique\_idioma (habitacion\_id, idioma),  
    FOREIGN KEY (habitacion\_id) REFERENCES habitaciones(id) ON DELETE CASCADE,  
    INDEX idx\_slug (slug),  
    INDEX idx\_idioma (idioma)  
);

CREATE TABLE IF NOT EXISTS habitacion\_tarifas (  
    id INT PRIMARY KEY AUTO\_INCREMENT,  
    habitacion\_id INT NOT NULL,  
    tipo\_tarifa ENUM('baja', 'alta', 'corporativo') NOT NULL,  
    precio DECIMAL(10, 2) NOT NULL,  
    fecha\_inicio DATE,  
    fecha\_fin DATE,  
    activa BOOLEAN DEFAULT TRUE,  
    descripcion VARCHAR(255),  
    fecha\_creacion TIMESTAMP DEFAULT CURRENT\_TIMESTAMP,  
    FOREIGN KEY (habitacion\_id) REFERENCES habitaciones(id) ON DELETE CASCADE,  
    INDEX idx\_habitacion\_tipo (habitacion\_id, tipo\_tarifa),  
    INDEX idx\_fechas (fecha\_inicio, fecha\_fin)  
);

CREATE TABLE IF NOT EXISTS habitacion\_comodidades (  
    id INT PRIMARY KEY AUTO\_INCREMENT,  
    habitacion\_id INT NOT NULL,  
    idioma ENUM('es', 'en') NOT NULL,  
    comodidades JSON NOT NULL,  
    UNIQUE KEY unique\_comodidad (habitacion\_id, idioma),  
    FOREIGN KEY (habitacion\_id) REFERENCES habitaciones(id) ON DELETE CASCADE  
);

CREATE TABLE IF NOT EXISTS habitacion\_amenities (  
    id INT PRIMARY KEY AUTO\_INCREMENT,  
    habitacion\_id INT NOT NULL,  
    idioma ENUM('es', 'en') NOT NULL,  
    amenities JSON NOT NULL,  
    UNIQUE KEY unique\_amenity (habitacion\_id, idioma),  
    FOREIGN KEY (habitacion\_id) REFERENCES habitaciones(id) ON DELETE CASCADE  
);

CREATE TABLE IF NOT EXISTS habitacion\_configuracion\_camas (  
    id INT PRIMARY KEY AUTO\_INCREMENT,  
    habitacion\_id INT NOT NULL,  
    camas JSON NOT NULL,  
    UNIQUE KEY unique\_camas (habitacion\_id),  
    FOREIGN KEY (habitacion\_id) REFERENCES habitaciones(id) ON DELETE CASCADE  
);

CREATE TABLE IF NOT EXISTS habitacion\_imagenes (  
    id INT PRIMARY KEY AUTO\_INCREMENT,  
    habitacion\_id INT NOT NULL,  
    imagen\_id INT NOT NULL,  
    es\_principal BOOLEAN DEFAULT FALSE,  
    orden INT DEFAULT 0,  
    FOREIGN KEY (habitacion\_id) REFERENCES habitaciones(id) ON DELETE CASCADE,  
    FOREIGN KEY (imagen\_id) REFERENCES imagenes(id) ON DELETE CASCADE,  
    INDEX idx\_habitacion (habitacion\_id),  
    INDEX idx\_principal (es\_principal)  
);

CREATE TABLE IF NOT EXISTS habitacion\_faqs (  
    id INT PRIMARY KEY AUTO\_INCREMENT,  
    habitacion\_id INT NOT NULL,  
    idioma ENUM('es', 'en') NOT NULL,  
    pregunta VARCHAR(255) NOT NULL,  
    respuesta LONGTEXT NOT NULL,  
    orden INT DEFAULT 0,  
    FOREIGN KEY (habitacion\_id) REFERENCES habitaciones(id) ON DELETE CASCADE,  
    INDEX idx\_habitacion (habitacion\_id),  
    INDEX idx\_idioma (idioma)  
);

### **2\. CLASE RoomManager \- includes/RoomManager.php**

php  
**\<?php**

class RoomManager {  
      
    public static function obtenerHabitaciones($soloActivas \= true, $orden \= 'numero\_habitacion ASC') {  
        $db \= new Database();  
        $where \= $soloActivas ? 'WHERE h.activa \= TRUE' : '';  
          
        $query \= "SELECT h.\*, t.nombre as tipo\_nombre  
                  FROM habitaciones h  
                  LEFT JOIN tipos\_habitacion t ON h.tipo\_habitacion\_id \= t.id  
                  $where  
                  ORDER BY $orden";  
          
        return $db\-\>query($query)\-\>fetchAll(PDO::FETCH\_ASSOC);  
    }  
      
    public static function obtenerHabitacionCompleta($habitacion\_id, $idioma \= 'es') {  
        $db \= new Database();  
          
        $habitacion \= $db\-\>findOne('habitaciones', \['id' \=\> $habitacion\_id\]);  
        if (\!$habitacion) return null;  
          
        $idioma\_data \= $db\-\>findOne('habitaciones\_idiomas', \[  
            'habitacion\_id' \=\> $habitacion\_id,  
            'idioma' \=\> $idioma  
        \]);  
          
        $tarifas \= $db\-\>findAll('habitacion\_tarifas', \['habitacion\_id' \=\> $habitacion\_id, 'activa' \=\> true\]);  
          
        $comodidades \= $db\-\>findOne('habitacion\_comodidades', \[  
            'habitacion\_id' \=\> $habitacion\_id,  
            'idioma' \=\> $idioma  
        \]);  
        if ($comodidades) {  
            $comodidades\['comodidades'\] \= json\_decode($comodidades\['comodidades'\], true);  
        }  
          
        $amenities \= $db\-\>findOne('habitacion\_amenities', \[  
            'habitacion\_id' \=\> $habitacion\_id,  
            'idioma' \=\> $idioma  
        \]);  
        if ($amenities) {  
            $amenities\['amenities'\] \= json\_decode($amenities\['amenities'\], true);  
        }  
          
        $camas \= $db\-\>findOne('habitacion\_configuracion\_camas', \['habitacion\_id' \=\> $habitacion\_id\]);  
        if ($camas) {  
            $camas\['camas'\] \= json\_decode($camas\['camas'\], true);  
        }  
          
        $imagenes \= $db\-\>query(  
            "SELECT i.\* FROM imagenes i  
             JOIN habitacion\_imagenes hi ON i.id \= hi.imagen\_id  
             WHERE hi.habitacion\_id \= ?  
             ORDER BY hi.es\_principal DESC, hi.orden ASC",  
            \[$habitacion\_id\]  
        )\-\>fetchAll(PDO::FETCH\_ASSOC);  
          
        $faqs \= $db\-\>findAll('habitacion\_faqs', \[  
            'habitacion\_id' \=\> $habitacion\_id,  
            'idioma' \=\> $idioma  
        \], 'ORDER BY orden ASC');  
          
        return \[  
            'habitacion' \=\> $habitacion,  
            'idioma\_data' \=\> $idioma\_data,  
            'tarifas' \=\> $tarifas,  
            'comodidades' \=\> $comodidades\['comodidades'\] ?? \[\],  
            'amenities' \=\> $amenities\['amenities'\] ?? \[\],  
            'camas' \=\> $camas\['camas'\] ?? \[\],  
            'imagenes' \=\> $imagenes,  
            'faqs' \=\> $faqs  
        \];  
    }  
      
    public static function crearHabitacion($datos) {  
        $db \= new Database();  
          
        try {  
            $db\-\>beginTransaction();  
              
            $db\-\>insert('habitaciones', \[  
                'numero\_habitacion' \=\> (int)$datos\['numero\_habitacion'\],  
                'slug' \=\> slugify($datos\['nombre\_es'\]),  
                'tipo\_habitacion\_id' \=\> (int)$datos\['tipo\_habitacion\_id'\],  
                'estado' \=\> 'disponible',  
                'capacidad\_huespedes' \=\> (int)$datos\['capacidad\_huespedes'\],  
                'num\_camas' \=\> (int)($datos\['num\_camas'\] ?? 0),  
                'precio\_base' \=\> (float)$datos\['precio\_base'\],  
                'activa' \=\> true  
            \]);  
              
            $habitacion\_id \= $db\-\>lastInsertId();  
              
            $db\-\>insert('habitaciones\_idiomas', \[  
                'habitacion\_id' \=\> $habitacion\_id,  
                'idioma' \=\> 'es',  
                'nombre' \=\> $datos\['nombre\_es'\],  
                'descripcion' \=\> sanitizeHTML($datos\['descripcion\_es'\]),  
                'slug' \=\> slugify($datos\['nombre\_es'\]),  
                'seo\_titulo' \=\> $datos\['seo\_titulo\_es'\] ?? '',  
                'seo\_descripcion' \=\> $datos\['seo\_descripcion\_es'\] ?? '',  
                'seo\_palabras\_clave' \=\> $datos\['seo\_palabras\_clave\_es'\] ?? ''  
            \]);  
              
            $db\-\>insert('habitaciones\_idiomas', \[  
                'habitacion\_id' \=\> $habitacion\_id,  
                'idioma' \=\> 'en',  
                'nombre' \=\> $datos\['nombre\_en'\],  
                'descripcion' \=\> sanitizeHTML($datos\['descripcion\_en'\]),  
                'slug' \=\> slugify($datos\['nombre\_en'\]),  
                'seo\_titulo' \=\> $datos\['seo\_titulo\_en'\] ?? '',  
                'seo\_descripcion' \=\> $datos\['seo\_descripcion\_en'\] ?? '',  
                'seo\_palabras\_clave' \=\> $datos\['seo\_palabras\_clave\_en'\] ?? ''  
            \]);  
              
            if (\!empty($datos\['comodidades'\])) {  
                $db\-\>insert('habitacion\_comodidades', \[  
                    'habitacion\_id' \=\> $habitacion\_id,  
                    'idioma' \=\> 'es',  
                    'comodidades' \=\> json\_encode($datos\['comodidades'\])  
                \]);  
                $db\-\>insert('habitacion\_comodidades', \[  
                    'habitacion\_id' \=\> $habitacion\_id,  
                    'idioma' \=\> 'en',  
                    'comodidades' \=\> json\_encode($datos\['comodidades'\])  
                \]);  
            }  
              
            if (\!empty($datos\['amenities'\])) {  
                $db\-\>insert('habitacion\_amenities', \[  
                    'habitacion\_id' \=\> $habitacion\_id,  
                    'idioma' \=\> 'es',  
                    'amenities' \=\> json\_encode($datos\['amenities'\])  
                \]);  
                $db\-\>insert('habitacion\_amenities', \[  
                    'habitacion\_id' \=\> $habitacion\_id,  
                    'idioma' \=\> 'en',  
                    'amenities' \=\> json\_encode($datos\['amenities'\])  
                \]);  
            }  
              
            if (\!empty($datos\['camas'\])) {  
                $db\-\>insert('habitacion\_configuracion\_camas', \[  
                    'habitacion\_id' \=\> $habitacion\_id,  
                    'camas' \=\> json\_encode($datos\['camas'\])  
                \]);  
            }  
              
            $db\-\>commit();  
            return \['exito' \=\> true, 'habitacion\_id' \=\> $habitacion\_id\];  
              
        } catch (Exception $e) {  
            $db\-\>rollback();  
            return \['exito' \=\> false, 'error' \=\> $e\-\>getMessage()\];  
        }  
    }  
      
    public static function actualizarHabitacion($habitacion\_id, $datos) {  
        $db \= new Database();  
          
        try {  
            $db\-\>beginTransaction();  
              
            $db\-\>update('habitaciones', \[  
                'numero\_habitacion' \=\> (int)$datos\['numero\_habitacion'\],  
                'tipo\_habitacion\_id' \=\> (int)$datos\['tipo\_habitacion\_id'\],  
                'estado' \=\> $datos\['estado'\],  
                'capacidad\_huespedes' \=\> (int)$datos\['capacidad\_huespedes'\],  
                'num\_camas' \=\> (int)($datos\['num\_camas'\] ?? 0),  
                'precio\_base' \=\> (float)$datos\['precio\_base'\],  
                'activa' \=\> (bool)$datos\['activa'\]  
            \], \['id' \=\> $habitacion\_id\]);  
              
            $db\-\>update('habitaciones\_idiomas', \[  
                'nombre' \=\> $datos\['nombre\_es'\],  
                'descripcion' \=\> sanitizeHTML($datos\['descripcion\_es'\]),  
                'slug' \=\> slugify($datos\['nombre\_es'\]),  
                'seo\_titulo' \=\> $datos\['seo\_titulo\_es'\] ?? '',  
                'seo\_descripcion' \=\> $datos\['seo\_descripcion\_es'\] ?? '',  
                'seo\_palabras\_clave' \=\> $datos\['seo\_palabras\_clave\_es'\] ?? ''  
            \], \['habitacion\_id' \=\> $habitacion\_id, 'idioma' \=\> 'es'\]);  
              
            $db\-\>update('habitaciones\_idiomas', \[  
                'nombre' \=\> $datos\['nombre\_en'\],  
                'descripcion' \=\> sanitizeHTML($datos\['descripcion\_en'\]),  
                'slug' \=\> slugify($datos\['nombre\_en'\]),  
                'seo\_titulo' \=\> $datos\['seo\_titulo\_en'\] ?? '',  
                'seo\_descripcion' \=\> $datos\['seo\_descripcion\_en'\] ?? '',  
                'seo\_palabras\_clave' \=\> $datos\['seo\_palabras\_clave\_en'\] ?? ''  
            \], \['habitacion\_id' \=\> $habitacion\_id, 'idioma' \=\> 'en'\]);  
              
            if (isset($datos\['comodidades'\])) {  
                $db\-\>update('habitacion\_comodidades', \[  
                    'comodidades' \=\> json\_encode($datos\['comodidades'\])  
                \], \['habitacion\_id' \=\> $habitacion\_id, 'idioma' \=\> 'es'\]);  
                  
                $db\-\>update('habitacion\_comodidades', \[  
                    'comodidades' \=\> json\_encode($datos\['comodidades'\])  
                \], \['habitacion\_id' \=\> $habitacion\_id, 'idioma' \=\> 'en'\]);  
            }  
              
            if (isset($datos\['amenities'\])) {  
                $db\-\>update('habitacion\_amenities', \[  
                    'amenities' \=\> json\_encode($datos\['amenities'\])  
                \], \['habitacion\_id' \=\> $habitacion\_id, 'idioma' \=\> 'es'\]);  
                  
                $db\-\>update('habitacion\_amenities', \[  
                    'amenities' \=\> json\_encode($datos\['amenities'\])  
                \], \['habitacion\_id' \=\> $habitacion\_id, 'idioma' \=\> 'en'\]);  
            }  
              
            if (isset($datos\['camas'\])) {  
                $db\-\>update('habitacion\_configuracion\_camas', \[  
                    'camas' \=\> json\_encode($datos\['camas'\])  
                \], \['habitacion\_id' \=\> $habitacion\_id\]);  
            }  
              
            $db\-\>commit();  
            return \['exito' \=\> true\];  
              
        } catch (Exception $e) {  
            $db\-\>rollback();  
            return \['exito' \=\> false, 'error' \=\> $e\-\>getMessage()\];  
        }  
    }  
      
    public static function eliminarHabitacion($habitacion\_id) {  
        $db \= new Database();  
          
        try {  
            $db\-\>beginTransaction();  
              
            $db\-\>delete('habitacion\_faqs', \['habitacion\_id' \=\> $habitacion\_id\]);  
            $db\-\>delete('habitacion\_imagenes', \['habitacion\_id' \=\> $habitacion\_id\]);  
            $db\-\>delete('habitacion\_configuracion\_camas', \['habitacion\_id' \=\> $habitacion\_id\]);  
            $db\-\>delete('habitacion\_amenities', \['habitacion\_id' \=\> $habitacion\_id\]);  
            $db\-\>delete('habitacion\_comodidades', \['habitacion\_id' \=\> $habitacion\_id\]);  
            $db\-\>delete('habitacion\_tarifas', \['habitacion\_id' \=\> $habitacion\_id\]);  
            $db\-\>delete('habitaciones\_idiomas', \['habitacion\_id' \=\> $habitacion\_id\]);  
            $db\-\>delete('habitaciones', \['id' \=\> $habitacion\_id\]);  
              
            $db\-\>commit();  
            return \['exito' \=\> true\];  
              
        } catch (Exception $e) {  
            $db\-\>rollback();  
            return \['exito' \=\> false, 'error' \=\> $e\-\>getMessage()\];  
        }  
    }  
      
    public static function getPrecioActual($habitacion\_id, $tipo\_tarifa \= 'baja') {  
        $db \= new Database();  
        $hoy \= date('Y-m-d');  
          
        $precio \= $db\-\>query(  
            "SELECT precio FROM habitacion\_tarifas   
             WHERE habitacion\_id \= ?   
             AND tipo\_tarifa \= ?   
             AND fecha\_inicio \<= ?   
             AND fecha\_fin \>= ?   
             AND activa \= TRUE  
             LIMIT 1",  
            \[$habitacion\_id, $tipo\_tarifa, $hoy, $hoy\]  
        )\-\>fetchColumn();  
          
        return $precio ? (float)$precio : null;  
    }  
      
    public static function obtenerRelacionadas($habitacion\_id, $limite \= 3) {  
        $db \= new Database();  
        $hab\_actual \= $db\-\>findOne('habitaciones', \['id' \=\> $habitacion\_id\]);  
          
        if (\!$hab\_actual) return \[\];  
          
        return $db\-\>query(  
            "SELECT h.id, hi.nombre, h.slug  
             FROM habitaciones h  
             JOIN habitaciones\_idiomas hi ON h.id \= hi.habitacion\_id  
             WHERE h.tipo\_habitacion\_id \= ?   
             AND h.id \!= ?   
             AND h.activa \= TRUE  
             AND hi.idioma \= 'es'  
             LIMIT ?",  
            \[$hab\_actual\['tipo\_habitacion\_id'\], $habitacion\_id, $limite\]  
        )\-\>fetchAll(PDO::FETCH\_ASSOC);  
    }  
}

**?\>**

### **3\. ENDPOINTS API**

#### **/api/habitaciones/crear.php**

php  
**\<?php**  
session\_start();  
require\_once \_\_DIR\_\_ . '/../../config/database.php';  
require\_once \_\_DIR\_\_ . '/../../includes/Database.php';  
require\_once \_\_DIR\_\_ . '/../../includes/Auth.php';  
require\_once \_\_DIR\_\_ . '/../../includes/CSRF.php';  
require\_once \_\_DIR\_\_ . '/../../includes/RoomManager.php';  
require\_once \_\_DIR\_\_ . '/../../includes/Validator.php';

http\_response\_code(200);  
header('Content-Type: application/json');

try {  
    if (\!CSRF::validate($\_POST\['csrf\_token'\] ?? '')) {  
        http\_response\_code(403);  
        die(json\_encode(\['exito' \=\> false, 'error' \=\> 'Token CSRF inválido'\]));  
    }  
      
    if (\!Auth::isLoggedIn() || \!Auth::hasPermission('crear\_habitacion')) {  
        http\_response\_code(403);  
        die(json\_encode(\['exito' \=\> false, 'error' \=\> 'No autorizado'\]));  
    }  
      
    $json \= file\_get\_contents('php://input');  
    $datos \= json\_decode($json, true);  
      
    if (\!$datos) {  
        http\_response\_code(400);  
        die(json\_encode(\['exito' \=\> false, 'error' \=\> 'JSON inválido'\]));  
    }  
      
    if (empty($datos\['numero\_habitacion'\]) || empty($datos\['nombre\_es'\])) {  
        http\_response\_code(400);  
        die(json\_encode(\['exito' \=\> false, 'error' \=\> 'Campos requeridos'\]));  
    }  
      
    $resultado \= RoomManager::crearHabitacion($datos);  
      
    if ($resultado\['exito'\]) {  
        http\_response\_code(201);  
        die(json\_encode(\[  
            'exito' \=\> true,  
            'habitacion\_id' \=\> $resultado\['habitacion\_id'\],  
            'mensaje' \=\> 'Habitación creada'  
        \]));  
    } else {  
        http\_response\_code(500);  
        die(json\_encode($resultado));  
    }  
      
} catch (Exception $e) {  
    http\_response\_code(500);  
    die(json\_encode(\['exito' \=\> false, 'error' \=\> $e\-\>getMessage()\]));  
}

**?\>**

#### **/api/habitaciones/actualizar.php**

php  
**\<?php**  
session\_start();  
require\_once \_\_DIR\_\_ . '/../../config/database.php';  
require\_once \_\_DIR\_\_ . '/../../includes/Database.php';  
require\_once \_\_DIR\_\_ . '/../../includes/Auth.php';  
require\_once \_\_DIR\_\_ . '/../../includes/CSRF.php';  
require\_once \_\_DIR\_\_ . '/../../includes/RoomManager.php';

http\_response\_code(200);  
header('Content-Type: application/json');

try {  
    if (\!CSRF::validate($\_POST\['csrf\_token'\] ?? '')) {  
        http\_response\_code(403);  
        die(json\_encode(\['exito' \=\> false, 'error' \=\> 'Token CSRF inválido'\]));  
    }  
      
    if (\!Auth::isLoggedIn() || \!Auth::hasPermission('editar\_habitacion')) {  
        http\_response\_code(403);  
        die(json\_encode(\['exito' \=\> false, 'error' \=\> 'No autorizado'\]));  
    }  
      
    $json \= file\_get\_contents('php://input');  
    $datos \= json\_decode($json, true);  
      
    $habitacion\_id \= (int)($datos\['habitacion\_id'\] ?? 0);  
      
    if (\!$habitacion\_id) {  
        http\_response\_code(400);  
        die(json\_encode(\['exito' \=\> false, 'error' \=\> 'ID requerido'\]));  
    }  
      
    $resultado \= RoomManager::actualizarHabitacion($habitacion\_id, $datos);  
      
    if ($resultado\['exito'\]) {  
        http\_response\_code(200);  
        die(json\_encode(\['exito' \=\> true, 'mensaje' \=\> 'Habitación actualizada'\]));  
    } else {  
        http\_response\_code(500);  
        die(json\_encode($resultado));  
    }  
      
} catch (Exception $e) {  
    http\_response\_code(500);  
    die(json\_encode(\['exito' \=\> false, 'error' \=\> $e\-\>getMessage()\]));  
}

**?\>**

#### **/api/habitaciones/eliminar.php**

php  
**\<?php**  
session\_start();  
require\_once \_\_DIR\_\_ . '/../../config/database.php';  
require\_once \_\_DIR\_\_ . '/../../includes/Database.php';  
require\_once \_\_DIR\_\_ . '/../../includes/Auth.php';  
require\_once \_\_DIR\_\_ . '/../../includes/CSRF.php';  
require\_once \_\_DIR\_\_ . '/../../includes/RoomManager.php';

http\_response\_code(200);  
header('Content-Type: application/json');

try {  
    if (\!CSRF::validate($\_POST\['csrf\_token'\] ?? '')) {  
        http\_response\_code(403);  
        die(json\_encode(\['exito' \=\> false, 'error' \=\> 'Token CSRF inválido'\]));  
    }  
      
    if (\!Auth::isLoggedIn() || \!Auth::hasPermission('eliminar\_habitacion')) {  
        http\_response\_code(403);  
        die(json\_encode(\['exito' \=\> false, 'error' \=\> 'No autorizado'\]));  
    }  
      
    $json \= file\_get\_contents('php://input');  
    $datos \= json\_decode($json, true);  
    $habitacion\_id \= (int)($datos\['habitacion\_id'\] ?? 0);  
      
    if (\!$habitacion\_id) {  
        http\_response\_code(400);  
        die(json\_encode(\['exito' \=\> false, 'error' \=\> 'ID requerido'\]));  
    }  
      
    $resultado \= RoomManager::eliminarHabitacion($habitacion\_id);  
      
    if ($resultado\['exito'\]) {  
        http\_response\_code(200);  
        die(json\_encode(\['exito' \=\> true, 'mensaje' \=\> 'Habitación eliminada'\]));  
    } else {  
        http\_response\_code(500);  
        die(json\_encode($resultado));  
    }  
      
} catch (Exception $e) {  
    http\_response\_code(500);  
    die(json\_encode(\['exito' \=\> false, 'error' \=\> $e\-\>getMessage()\]));  
}

**?\>**

#### **/api/habitaciones/obtener.php**

php  
**\<?php**  
session\_start();  
require\_once \_\_DIR\_\_ . '/../../config/database.php';  
require\_once \_\_DIR\_\_ . '/../../includes/Database.php';  
require\_once \_\_DIR\_\_ . '/../../includes/Auth.php';  
require\_once \_\_DIR\_\_ . '/../../includes/RoomManager.php';

http\_response\_code(200);  
header('Content-Type: application/json');

try {  
    $habitacion\_id \= (int)($\_GET\['id'\] ?? 0);  
    $idioma \= $\_GET\['idioma'\] ?? 'es';  
      
    if (\!$habitacion\_id) {  
        http\_response\_code(400);  
        die(json\_encode(\['exito' \=\> false, 'error' \=\> 'ID requerido'\]));  
    }  
      
    $habitacion \= RoomManager::obtenerHabitacionCompleta($habitacion\_id, $idioma);  
      
    if (\!$habitacion) {  
        http\_response\_code(404);  
        die(json\_encode(\['exito' \=\> false, 'error' \=\> 'No encontrada'\]));  
    }  
      
    http\_response\_code(200);  
    die(json\_encode(\['exito' \=\> true, 'habitacion' \=\> $habitacion\]));  
      
} catch (Exception $e) {  
    http\_response\_code(500);  
    die(json\_encode(\['exito' \=\> false, 'error' \=\> $e\-\>getMessage()\]));  
}

**?\>**

#### **/api/habitaciones/obtener-todas.php**

php  
**\<?php**  
session\_start();  
require\_once \_\_DIR\_\_ . '/../../config/database.php';  
require\_once \_\_DIR\_\_ . '/../../includes/Database.php';  
require\_once \_\_DIR\_\_ . '/../../includes/Auth.php';  
require\_once \_\_DIR\_\_ . '/../../includes/RoomManager.php';

http\_response\_code(200);  
header('Content-Type: application/json');

try {  
    if (\!Auth::isLoggedIn()) {  
        http\_response\_code(401);  
        die(json\_encode(\['exito' \=\> false, 'error' \=\> 'No autorizado'\]));  
    }  
      
    $soloActivas \= $\_GET\['solo\_activas'\] \=== 'true' ? true : false;  
    $habitaciones \= RoomManager::obtenerHabitaciones($soloActivas);  
      
    $db \= new Database();  
    foreach ($habitaciones as &$hab) {  
        $idioma \= $db\-\>findOne('habitaciones\_idiomas', \[  
            'habitacion\_id' \=\> $hab\['id'\],  
            'idioma' \=\> 'es'  
        \]);  
        $hab\['nombre'\] \= $idioma\['nombre'\] ?? '';  
    }  
      
    http\_response\_code(200);  
    die(json\_encode(\['exito' \=\> true, 'total' \=\> count($habitaciones), 'habitaciones' \=\> $habitaciones\]));  
      
} catch (Exception $e) {  
    http\_response\_code(500);  
    die(json\_encode(\['exito' \=\> false, 'error' \=\> $e\-\>getMessage()\]));  
}

**?\>**

**PARTE 2: HTML FORMULARIO \+ CSS \+ JAVASCRIPT LISTADO**

### **1\. HTML FORMULARIO \- /admin/habitaciones/editar.php**

html  
\<\!DOCTYPE html\>  
\<html lang\="es"\>  
\<head\>  
    \<meta charset\="UTF-8"\>  
    \<meta name\="viewport" content\="width=device-width, initial-scale=1"\>  
    \<title\>Editar Habitación\</title\>  
    \<link rel\="stylesheet" href\="/proyecto-hotel/admin/assets/css/admin.css"\>  
    \<link rel\="stylesheet" href\="/proyecto-hotel/admin/assets/css/habitaciones.css"\>  
    \<script src\="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"\>\</script\>  
    \<meta name\="csrf-token" content\="\<?php echo htmlspecialchars(CSRF::generateToken()); ?\>"\>  
\</head\>  
\<body\>  
    \<?php include \_\_DIR\_\_ . '/../includes/header.php'; ?\>  
      
    \<main class\="admin-content"\>  
        \<div class\="page-header"\>  
            \<h1\>Editar Habitación\</h1\>  
            \<a href\="/proyecto-hotel/admin/habitaciones.php" class\="btn btn-secondary"\>← Volver\</a\>  
        \</div\>  
          
        \<form id\="formHabitacion" class\="form-habitacion"\>  
            \<\!-- SECCIÓN 1: DATOS BÁSICOS \--\>  
            \<div class\="form-section"\>  
                \<h2\>Datos Básicos\</h2\>  
                  
                \<div class\="form-group"\>  
                    \<label\>Número de Habitación:\</label\>  
                    \<input type\="number" name\="numero\_habitacion" required\>  
                \</div\>  
                  
                \<div class\="form-group"\>  
                    \<label\>Tipo de Habitación:\</label\>  
                    \<select name\="tipo\_habitacion\_id" required id\="selectTipo"\>  
                        \<option value\=""\>Seleccionar...\</option\>  
                    \</select\>  
                \</div\>  
                  
                \<div class\="form-group"\>  
                    \<label\>Estado:\</label\>  
                    \<select name\="estado" required\>  
                        \<option value\="disponible"\>Disponible\</option\>  
                        \<option value\="ocupada"\>Ocupada\</option\>  
                        \<option value\="mantenimiento"\>Mantenimiento\</option\>  
                    \</select\>  
                \</div\>  
                  
                \<div class\="form-group"\>  
                    \<label\>  
                        \<input type\="checkbox" name\="activa" checked\>  
                        ¿Activa? (Mostrar en web)  
                    \</label\>  
                \</div\>  
            \</div\>  
              
            \<\!-- SECCIÓN 2: CONTENIDO (ES/EN) \--\>  
            \<div class\="form-section"\>  
                \<h2\>Contenido del Sitio\</h2\>  
                  
                \<div class\="idiomas-tabs"\>  
                    \<button type\="button" class\="tab-button active" data-idioma\="es"\>Español\</button\>  
                    \<button type\="button" class\="tab-button" data-idioma\="en"\>Inglés\</button\>  
                \</div\>  
                  
                \<div id\="tab-es" class\="tab-content active"\>  
                    \<div class\="form-group"\>  
                        \<label\>Nombre (ES):\</label\>  
                        \<input type\="text" name\="nombre\_es" required\>  
                    \</div\>  
                      
                    \<div class\="form-group"\>  
                        \<label\>Descripción (ES):\</label\>  
                        \<textarea id\="editor-es" name\="descripcion\_es"\>\</textarea\>  
                    \</div\>  
                \</div\>  
                  
                \<div id\="tab-en" class\="tab-content"\>  
                    \<div class\="form-group"\>  
                        \<label\>Nombre (EN):\</label\>  
                        \<input type\="text" name\="nombre\_en" required\>  
                    \</div\>  
                      
                    \<div class\="form-group"\>  
                        \<label\>Descripción (EN):\</label\>  
                        \<textarea id\="editor-en" name\="descripcion\_en"\>\</textarea\>  
                    \</div\>  
                \</div\>  
            \</div\>  
              
            \<\!-- SECCIÓN 3: CAPACIDAD Y CAMAS \--\>  
            \<div class\="form-section"\>  
                \<h2\>Capacidad y Camas\</h2\>  
                  
                \<div class\="form-group"\>  
                    \<label\>Capacidad de Huéspedes:\</label\>  
                    \<input type\="number" name\="capacidad\_huespedes" required min\="1"\>  
                    \<small\>Se actualiza automáticamente según camas añadidas\</small\>  
                \</div\>  
                  
                \<div class\="camas-repeater"\>  
                    \<h3\>Distribución de Camas\</h3\>  
                    \<div id\="camasContainer"\>\</div\>  
                    \<button type\="button" class\="btn btn-secondary" id\="btnAgregarCama"\>  
                        \+ Agregar Cama  
                    \</button\>  
                \</div\>  
            \</div\>  
              
            \<\!-- SECCIÓN 4: COMODIDADES \--\>  
            \<div class\="form-section"\>  
                \<h2\>Comodidades\</h2\>  
                \<small\>WiFi, Aire Acondicionado, TV, etc.\</small\>  
                  
                \<div id\="comodidadesContainer" class\="repeater"\>\</div\>  
                \<button type\="button" class\="btn btn-secondary" id\="btnAgregarComodidad"\>  
                    \+ Agregar Comodidad  
                \</button\>  
            \</div\>  
              
            \<\!-- SECCIÓN 5: AMENITIES \--\>  
            \<div class\="form-section"\>  
                \<h2\>Amenities\</h2\>  
                \<small\>Desayuno, Parking, Spa, etc.\</small\>  
                  
                \<div id\="amenitiesContainer" class\="repeater"\>\</div\>  
                \<button type\="button" class\="btn btn-secondary" id\="btnAgregarAmenity"\>  
                    \+ Agregar Amenity  
                \</button\>  
            \</div\>  
              
            \<\!-- SECCIÓN 6: IMÁGENES \--\>  
            \<div class\="form-section"\>  
                \<h2\>Imágenes\</h2\>  
                  
                \<h3\>Imagen Principal\</h3\>  
                \<div class\="imagen-principal"\>  
                    \<img id\="previewPrincipal" src\="" alt\="" style\="max-width: 300px; display:none;"\>  
                    \<input type\="hidden" name\="imagen\_principal\_id"\>  
                    \<button type\="button" class\="btn btn-secondary" id\="btnSeleccionarPrincipal"\>  
                        Seleccionar Imagen Principal  
                    \</button\>  
                \</div\>  
                  
                \<h3 style\="margin-top: 1.5rem;"\>Galería Adicional\</h3\>  
                \<div id\="galeriaItems" class\="galeria-sortable"\>\</div\>  
                \<button type\="button" class\="btn btn-secondary" id\="btnAgregarGaleria"\>  
                    \+ Agregar a Galería  
                \</button\>  
            \</div\>  
              
            \<\!-- SECCIÓN 7: TARIFAS \--\>  
            \<div class\="form-section"\>  
                \<h2\>Tarifas por Temporada\</h2\>  
                  
                \<div id\="tarifasContainer" class\="repeater"\>\</div\>  
                \<button type\="button" class\="btn btn-secondary" id\="btnAgregarTarifa"\>  
                    \+ Agregar Tarifa  
                \</button\>  
            \</div\>  
              
            \<\!-- SECCIÓN 8: FAQs \--\>  
            \<div class\="form-section"\>  
                \<h2\>Preguntas Frecuentes\</h2\>  
                  
                \<div class\="idiomas-tabs"\>  
                    \<button type\="button" class\="tab-button active" data-idioma\="es"\>Español\</button\>  
                    \<button type\="button" class\="tab-button" data-idioma\="en"\>Inglés\</button\>  
                \</div\>  
                  
                \<div id\="faqsContainerES" class\="faqsContainer"\>\</div\>  
                \<button type\="button" class\="btn btn-secondary" id\="btnAgregarFAQES" style\="margin-bottom: 1.5rem;"\>  
                    \+ Agregar FAQ (ES)  
                \</button\>  
                  
                \<div id\="faqsContainerEN" class\="faqsContainer" style\="display:none;"\>\</div\>  
                \<button type\="button" class\="btn btn-secondary" id\="btnAgregarFAQEN" style\="display:none;"\>  
                    \+ Agregar FAQ (EN)  
                \</button\>  
            \</div\>  
              
            \<\!-- SECCIÓN 9: SEO \--\>  
            \<div class\="form-section"\>  
                \<h2\>SEO\</h2\>  
                  
                \<div class\="seo-tabs"\>  
                    \<button type\="button" class\="tab-button active" data-tab\="seo-basico"\>SEO Básico\</button\>  
                    \<button type\="button" class\="tab-button" data-tab\="seo-avanzado"\>SEO Avanzado\</button\>  
                \</div\>  
                  
                \<div id\="seo-basico" class\="tab-content active"\>  
                    \<div class\="idiomas-tabs"\>  
                        \<button type\="button" class\="tab-button active" data-idioma\="es"\>Español\</button\>  
                        \<button type\="button" class\="tab-button" data-idioma\="en"\>Inglés\</button\>  
                    \</div\>  
                      
                    \<div id\="seo-es" class\="seo-fields"\>  
                        \<div class\="form-group"\>  
                            \<label\>Título SEO (ES):\</label\>  
                            \<input type\="text" name\="seo\_titulo\_es" maxlength\="60"\>  
                            \<small id\="counter-titulo-es"\>0/60\</small\>  
                        \</div\>  
                          
                        \<div class\="form-group"\>  
                            \<label\>Descripción SEO (ES):\</label\>  
                            \<textarea name\="seo\_descripcion\_es" maxlength\="160"\>\</textarea\>  
                            \<small id\="counter-desc-es"\>0/160\</small\>  
                        \</div\>  
                          
                        \<div class\="form-group"\>  
                            \<label\>Palabras Clave (ES):\</label\>  
                            \<input type\="text" name\="seo\_palabras\_clave\_es" placeholder\="suite, deluxe, lujo"\>  
                        \</div\>  
                    \</div\>  
                      
                    \<div id\="seo-en" class\="seo-fields" style\="display:none;"\>  
                        \<div class\="form-group"\>  
                            \<label\>Título SEO (EN):\</label\>  
                            \<input type\="text" name\="seo\_titulo\_en" maxlength\="60"\>  
                            \<small id\="counter-titulo-en"\>0/60\</small\>  
                        \</div\>  
                          
                        \<div class\="form-group"\>  
                            \<label\>Descripción SEO (EN):\</label\>  
                            \<textarea name\="seo\_descripcion\_en" maxlength\="160"\>\</textarea\>  
                            \<small id\="counter-desc-en"\>0/160\</small\>  
                        \</div\>  
                          
                        \<div class\="form-group"\>  
                            \<label\>Keywords (EN):\</label\>  
                            \<input type\="text" name\="seo\_palabras\_clave\_en" placeholder\="suite, deluxe, luxury"\>  
                        \</div\>  
                    \</div\>  
                \</div\>  
            \</div\>  
              
            \<\!-- BOTONES FINALES \--\>  
            \<div class\="form-actions"\>  
                \<a href\="/proyecto-hotel/admin/habitaciones.php" class\="btn btn-secondary"\>Cancelar\</a\>  
                \<button type\="submit" class\="btn btn-primary"\>Guardar Habitación\</button\>  
            \</div\>  
        \</form\>  
    \</main\>  
      
    \<\!-- MODAL SELECTOR IMAGEN \--\>  
    \<div id\="modalSelectorImagen" class\="modal hidden"\>  
        \<div class\="modal-content"\>  
            \<div class\="modal-header"\>  
                \<h2\>Seleccionar Imagen\</h2\>  
                \<button type\="button" class\="btn-cerrar-modal"\>\&times;\</button\>  
            \</div\>  
            \<div class\="modal-body"\>  
                \<input type\="text" id\="buscarImagen" placeholder\="Buscar..." class\="input-search"\>  
                \<div id\="gridImagenes" class\="grid-imagenes"\>\</div\>  
            \</div\>  
            \<div class\="modal-footer"\>  
                \<button type\="button" class\="btn btn-secondary" id\="btnCancelarImagen"\>Cancelar\</button\>  
                \<button type\="button" class\="btn btn-primary" id\="btnConfirmarImagen" disabled\>Seleccionar\</button\>  
            \</div\>  
        \</div\>  
    \</div\>  
      
    \<input type\="file" id\="inputFile" hidden accept\="image/\*"\>  
      
    \<script src\="/proyecto-hotel/admin/assets/js/habitaciones-formulario.js"\>\</script\>  
\</body\>  
\</html\>

### **2\. HTML LISTADO \- /admin/habitaciones.php**

html  
\<\!DOCTYPE html\>  
\<html lang\="es"\>  
\<head\>  
    \<meta charset\="UTF-8"\>  
    \<meta name\="viewport" content\="width=device-width, initial-scale=1"\>  
    \<title\>Habitaciones\</title\>  
    \<link rel\="stylesheet" href\="/proyecto-hotel/admin/assets/css/admin.css"\>  
    \<link rel\="stylesheet" href\="/proyecto-hotel/admin/assets/css/habitaciones.css"\>  
    \<meta name\="csrf-token" content\="\<?php echo htmlspecialchars(CSRF::generateToken()); ?\>"\>  
\</head\>  
\<body\>  
    \<?php include \_\_DIR\_\_ . '/includes/header.php'; ?\>  
      
    \<main class\="admin-content"\>  
        \<div class\="page-header"\>  
            \<h1\>Habitaciones\</h1\>  
            \<a href\="/proyecto-hotel/admin/habitaciones/editar.php" class\="btn btn-primary"\>  
                \+ NUEVA HABITACIÓN  
            \</a\>  
        \</div\>  
          
        \<div class\="habitaciones-filtros"\>  
            \<input type\="text" id\="filtroBusqueda" placeholder\="Buscar..." class\="input-filter"\>  
            \<select id\="filtroTipo" class\="select-filter"\>  
                \<option value\=""\>Todos los tipos\</option\>  
            \</select\>  
            \<select id\="filtroEstado" class\="select-filter"\>  
                \<option value\=""\>Todos los estados\</option\>  
                \<option value\="disponible"\>Disponible\</option\>  
                \<option value\="ocupada"\>Ocupada\</option\>  
                \<option value\="mantenimiento"\>Mantenimiento\</option\>  
            \</select\>  
        \</div\>  
          
        \<div class\="tabla-responsiva"\>  
            \<table class\="tabla-habitaciones"\>  
                \<thead\>  
                    \<tr\>  
                        \<th\>\#\</th\>  
                        \<th\>Nombre\</th\>  
                        \<th\>Tipo\</th\>  
                        \<th\>Capacidad\</th\>  
                        \<th\>Precio Base\</th\>  
                        \<th\>Estado\</th\>  
                        \<th\>Acciones\</th\>  
                    \</tr\>  
                \</thead\>  
                \<tbody id\="tablaHabitaciones"\>  
                    \<tr\>\<td colspan\="7" class\="loading"\>Cargando...\</td\>\</tr\>  
                \</tbody\>  
            \</table\>  
        \</div\>  
    \</main\>  
      
    \<script src\="/proyecto-hotel/admin/assets/js/habitaciones-listado.js"\>\</script\>  
\</body\>  
\</html\>

### **3\. CSS \- /admin/assets/css/habitaciones.css**

css  
/\* LISTADO \*/  
.tabla-responsiva {  
    overflow-x: auto;  
    background: white;  
    border-radius: 8px;  
    border: 1px solid \#E1E4E3;  
    margin-bottom: 2rem;  
}

.tabla-habitaciones {  
    width: 100%;  
    border-collapse: collapse;  
}

.tabla-habitaciones thead {  
    background: \#E8EDF0;  
}

.tabla-habitaciones th {  
    padding: 1rem;  
    text-align: left;  
    color: \#425363;  
    font-weight: 600;  
    border-bottom: 1px solid \#E1E4E3;  
}

.tabla-habitaciones td {  
    padding: 1rem;  
    border-bottom: 1px solid \#E1E4E3;  
}

.tabla-habitaciones tbody tr:hover {  
    background: \#E8EDF0;  
}

.estado-badge {  
    display: inline-block;  
    padding: 0.25rem 0.75rem;  
    border-radius: 4px;  
    font-size: 0.85rem;  
    font-weight: 600;  
}

.estado-disponible { background: \#d4edda; color: \#155724; }  
.estado-ocupada { background: \#fff3cd; color: \#856404; }  
.estado-mantenimiento { background: \#f8d7da; color: \#721c24; }

.btn-pequeño {  
    display: inline-block;  
    padding: 0.5rem 0.75rem;  
    font-size: 0.85rem;  
    border: none;  
    background: \#425363;  
    color: white;  
    border-radius: 4px;  
    cursor: pointer;  
    margin-right: 0.5rem;  
    text-decoration: none;  
}

.btn-pequeño:hover {  
    background: \#2a3447;  
}

.btn-danger {  
    background: \#A90101;  
}

.btn-danger:hover {  
    background: \#8a0001;  
}

/\* FILTROS \*/  
.habitaciones-filtros {  
    display: flex;  
    gap: 1rem;  
    margin-bottom: 1.5rem;  
    flex-wrap: wrap;  
}

.input-filter, .select-filter {  
    padding: 0.75rem;  
    border: 1px solid \#E1E4E3;  
    border-radius: 6px;  
    font-family: inherit;  
}

.input-filter {  
    flex: 1;  
    min-width: 200px;  
}

/\* FORMULARIO \*/  
.form-section {  
    background: white;  
    border: 1px solid \#E1E4E3;  
    border-radius: 8px;  
    padding: 1.5rem;  
    margin-bottom: 1.5rem;  
}

.form-section h2 {  
    color: \#425363;  
    margin-top: 0;  
    margin-bottom: 1rem;  
    padding-bottom: 0.5rem;  
    border-bottom: 2px solid \#E8EDF0;  
}

.form-section h3 {  
    color: \#425363;  
    font-size: 1.1rem;  
    margin-top: 1rem;  
    margin-bottom: 0.5rem;  
}

.form-section small {  
    color: \#8FA8AE;  
    font-size: 0.85rem;  
}

.form-group {  
    margin-bottom: 1rem;  
}

.form-group label {  
    display: block;  
    margin-bottom: 0.5rem;  
    color: \#425363;  
    font-weight: 600;  
    font-size: 0.95rem;  
}

.form-group input\[type\="text"\],  
.form-group input\[type\="number"\],  
.form-group input\[type\="date"\],  
.form-group input\[type\="email"\],  
.form-group textarea,  
.form-group select {  
    width: 100%;  
    padding: 0.75rem;  
    border: 1px solid \#E1E4E3;  
    border-radius: 6px;  
    font-family: inherit;  
    font-size: 0.95rem;  
    box-sizing: border-box;  
}

.form-group textarea {  
    resize: vertical;  
    min-height: 100px;  
}

.form-group input\[type\="checkbox"\] {  
    margin-right: 0.5rem;  
}

.form-group small {  
    display: block;  
    margin-top: 0.25rem;  
    color: \#8FA8AE;  
}

/\* TABS \*/  
.idiomas-tabs, .seo-tabs {  
    display: flex;  
    gap: 1rem;  
    margin-bottom: 1.5rem;  
    border-bottom: 2px solid \#E1E4E3;  
}

.tab-button {  
    padding: 0.75rem 1rem;  
    background: none;  
    border: none;  
    border-bottom: 3px solid transparent;  
    color: \#8FA8AE;  
    cursor: pointer;  
    font-weight: 600;  
    transition: all 0.3s;  
    font-size: 0.95rem;  
}

.tab-button.active {  
    color: \#425363;  
    border-bottom-color: \#A90101;  
}

.tab-content {  
    display: none;  
}

.tab-content.active {  
    display: block;  
}

.seo-fields {  
    background: \#E8EDF0;  
    padding: 1rem;  
    border-radius: 6px;  
}

/\* REPEATERS \*/  
.repeater {  
    margin-bottom: 1rem;  
}

.repeater-item,  
.cama-item,  
.tarifa-item,  
.faq-item {  
    display: grid;  
    gap: 1rem;  
    padding: 1rem;  
    background: \#E8EDF0;  
    border-radius: 6px;  
    margin-bottom: 0.5rem;  
    align-items: center;  
}

.repeater-item {  
    grid-template-columns: 1fr 1fr auto;  
}

.repeater-item input,  
.repeater-item select,  
.cama-item input,  
.cama-item select,  
.tarifa-item input,  
.tarifa-item select,  
.faq-item input,  
.faq-item textarea {  
    padding: 0.5rem;  
    border: 1px solid \#E1E4E3;  
    border-radius: 4px;  
    font-family: inherit;  
}

.cama-item {  
    grid-template-columns: 150px 80px 200px auto;  
}

.tarifa-item {  
    grid-template-columns: 150px 100px 120px 120px 50px auto;  
}

.faq-item {  
    grid-template-columns: 1fr;  
}

.faq-item .input-pregunta {  
    font-weight: 600;  
}

.faq-item .textarea-respuesta {  
    min-height: 80px;  
    resize: vertical;  
}

.btn-remove {  
    padding: 0.5rem 0.75rem;  
    background: \#A90101;  
    color: white;  
    border: none;  
    border-radius: 4px;  
    cursor: pointer;  
    font-weight: 600;  
    white-space: nowrap;  
}

.btn-remove:hover {  
    background: \#8a0001;  
}

/\* IMÁGENES \*/  
.imagen-principal {  
    background: \#E8EDF0;  
    padding: 1rem;  
    border-radius: 6px;  
    text-align: center;  
}

.imagen-principal img {  
    max-width: 100%;  
    max-height: 300px;  
    margin-bottom: 1rem;  
    border-radius: 6px;  
}

.galeria-sortable {  
    display: grid;  
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));  
    gap: 1rem;  
    margin-bottom: 1rem;  
}

.galeria-item {  
    position: relative;  
    background: \#E8EDF0;  
    border-radius: 6px;  
    overflow: hidden;  
    cursor: grab;  
}

.galeria-item:active {  
    cursor: grabbing;  
}

.galeria-item img {  
    width: 100%;  
    height: 150px;  
    object-fit: cover;  
}

.galeria-item-overlay {  
    position: absolute;  
    top: 0;  
    left: 0;  
    right: 0;  
    bottom: 0;  
    background: rgba(0, 0, 0, 0.5);  
    display: flex;  
    align-items: center;  
    justify-content: center;  
    opacity: 0;  
    transition: opacity 0.3s;  
}

.galeria-item:hover .galeria-item-overlay {  
    opacity: 1;  
}

.galeria-item-overlay button {  
    background: \#A90101;  
    color: white;  
    border: none;  
    padding: 0.5rem 1rem;  
    border-radius: 4px;  
    cursor: pointer;  
}

/\* MODAL \*/  
.modal {  
    display: none;  
    position: fixed;  
    top: 0;  
    left: 0;  
    right: 0;  
    bottom: 0;  
    background: rgba(0, 0, 0, 0.5);  
    z-index: 1000;  
}

.modal.hidden {  
    display: none;  
}

.modal-content {  
    position: fixed;  
    top: 50%;  
    left: 50%;  
    transform: translate(\-50%, \-50%);  
    background: white;  
    border-radius: 12px;  
    width: 90%;  
    max-width: 800px;  
    max-height: 80vh;  
    overflow-y: auto;  
}

.modal-header {  
    padding: 1.5rem;  
    border-bottom: 1px solid \#E1E4E3;  
    display: flex;  
    justify-content: space-between;  
    align-items: center;  
}

.modal-header h2 {  
    margin: 0;  
    color: \#425363;  
}

.btn-cerrar-modal {  
    background: none;  
    border: none;  
    font-size: 1.5rem;  
    cursor: pointer;  
    color: \#8FA8AE;  
}

.modal-body {  
    padding: 1.5rem;  
}

.modal-footer {  
    padding: 1rem 1.5rem;  
    border-top: 1px solid \#E1E4E3;  
    display: flex;  
    justify-content: flex-end;  
    gap: 0.5rem;  
}

.grid-imagenes {  
    display: grid;  
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));  
    gap: 1rem;  
    margin-top: 1rem;  
}

.imagen-selectable {  
    position: relative;  
    border-radius: 6px;  
    overflow: hidden;  
    cursor: pointer;  
    border: 2px solid transparent;  
}

.imagen-selectable img {  
    width: 100%;  
    height: 120px;  
    object-fit: cover;  
}

.imagen-selectable.selected {  
    border-color: \#A90101;  
    box-shadow: 0 0 8px rgba(169, 1, 1, 0.5);  
}

/\* ACCIONES \*/  
.form-actions {  
    display: flex;  
    gap: 1rem;  
    justify-content: flex-end;  
    margin-top: 2rem;  
    padding-top: 1.5rem;  
    border-top: 1px solid \#E1E4E3;  
}

/\* RESPONSIVE \*/  
@media (max-width: 768px) {  
    .repeater-item {  
        grid-template-columns: 1fr;  
    }  
      
    .cama-item {  
        grid-template-columns: 1fr;  
    }  
      
    .tarifa-item {  
        grid-template-columns: 1fr;  
    }  
      
    .tabla-habitaciones {  
        font-size: 0.85rem;  
    }  
      
    .tabla-habitaciones th,  
    .tabla-habitaciones td {  
        padding: 0.5rem;  
    }  
      
    .habitaciones-filtros {  
        flex-direction: column;  
    }  
      
    .input-filter, .select-filter {  
        width: 100%;  
    }  
}

### **4\. JAVASCRIPT LISTADO \- /admin/assets/js/habitaciones-listado.js**

javascript  
const API\_OBTENER\_TODAS \= '/proyecto-hotel/api/habitaciones/obtener-todas.php';  
const API\_ELIMINAR \= '/proyecto-hotel/api/habitaciones/eliminar.php';

async function cargarHabitaciones() {  
    try {  
        const response \= await fetch(API\_OBTENER\_TODAS \+ '?solo\_activas=false');  
        const data \= await response.json();  
          
        if (data.exito) {  
            mostrarHabitaciones(data.habitaciones);  
        }  
    } catch (error) {  
        console.error('Error:', error);  
    }  
}

function mostrarHabitaciones(habitaciones) {  
    const tbody \= document.getElementById('tablaHabitaciones');  
      
    if (habitaciones.length \=== 0) {  
        tbody.innerHTML \= '\<tr\>\<td colspan="7"\>No hay habitaciones\</td\>\</tr\>';  
        return;  
    }  
      
    tbody.innerHTML \= habitaciones.map(hab \=\> \`  
        \<tr data-tipo="${hab.tipo\_nombre}" data-estado="${hab.estado}"\>  
            \<td\>\#${hab.numero\_habitacion}\</td\>  
            \<td\>${hab.nombre}\</td\>  
            \<td\>${hab.tipo\_nombre}\</td\>  
            \<td\>${hab.capacidad\_huespedes} personas\</td\>  
            \<td\>$${parseFloat(hab.precio\_base).toFixed(2)}\</td\>  
            \<td\>  
                \<span class="estado-badge estado-${hab.estado}"\>  
                    ${hab.estado.charAt(0).toUpperCase() \+ hab.estado.slice(1)}  
                \</span\>  
            \</td\>  
            \<td\>  
                \<a href="/proyecto-hotel/admin/habitaciones/editar.php?id=${hab.id}" class="btn-pequeño"\>  
                    ✏️ Editar  
                \</a\>  
                \<button class="btn-pequeño btn-danger" onclick="eliminarHabitacion(${hab.id})"\>  
                    🗑️ Eliminar  
                \</button\>  
            \</td\>  
        \</tr\>  
    \`).join('');  
}

async function eliminarHabitacion(id) {  
    if (\!confirm('¿Estás seguro?')) return;  
      
    try {  
        const response \= await fetch(API\_ELIMINAR, {  
            method: 'POST',  
            headers: {  
                'Content-Type': 'application/json',  
                'X-CSRF-Token': document.querySelector('meta\[name="csrf-token"\]').content  
            },  
            body: JSON.stringify({ habitacion\_id: id, csrf\_token: document.querySelector('meta\[name="csrf-token"\]').content })  
        });  
          
        const resultado \= await response.json();  
          
        if (resultado.exito) {  
            alert('Habitación eliminada');  
            cargarHabitaciones();  
        } else {  
            alert('Error: ' \+ resultado.error);  
        }  
    } catch (error) {  
        alert('Error: ' \+ error.message);  
    }  
}

// Filtros  
document.getElementById('filtroBusqueda').addEventListener('input', filtrar);  
document.getElementById('filtroTipo').addEventListener('change', filtrar);  
document.getElementById('filtroEstado').addEventListener('change', filtrar);

function filtrar() {  
    const busqueda \= document.getElementById('filtroBusqueda').value.toLowerCase();  
    const tipo \= document.getElementById('filtroTipo').value;  
    const estado \= document.getElementById('filtroEstado').value;  
      
    document.querySelectorAll('\#tablaHabitaciones tr').forEach(row \=\> {  
        const coincideBusqueda \= row.textContent.toLowerCase().includes(busqueda);  
        const coincideTipo \= \!tipo || row.dataset.tipo \=== tipo;  
        const coincideEstado \= \!estado || row.dataset.estado \=== estado;  
          
        row.style.display \= (coincideBusqueda && coincideTipo && coincideEstado) ? '' : 'none';  
    });  
}

document.addEventListener('DOMContentLoaded', cargarHabitaciones);

# **PARTE 3: JAVASCRIPT FORMULARIO \+ HELPERS \+ CHECKLIST**

### **1\. JAVASCRIPT FORMULARIO \- /admin/assets/js/habitaciones-formulario.js**

javascript  
// \============================================  
// CONFIGURACIÓN TINYMCE  
// \============================================

tinymce.init({  
    selector: 'textarea\[id^="editor-"\]',  
    plugins: 'link lists image table code',  
    toolbar: 'undo redo | bold italic underline | bullist numlist | link image',  
    height: 400,  
    menubar: false,  
    content\_css: '/proyecto-hotel/admin/assets/css/tinymce.css'  
});

// \============================================  
// CLASE REPEATER GENÉRICA  
// \============================================

class Repeater {  
    constructor(containerId, templateHTML, nombreClase \= 'repeater-item') {  
        this.container \= document.getElementById(containerId);  
        this.template \= templateHTML;  
        this.nombreClase \= nombreClase;  
    }  
      
    agregar() {  
        if (\!this.container) return;  
          
        const item \= document.createElement('div');  
        item.innerHTML \= this.template;  
        item.classList.add(this.nombreClase);  
          
        const btnRemove \= item.querySelector('.btn-remove');  
        if (btnRemove) {  
            btnRemove.addEventListener('click', () \=\> {  
                item.remove();  
                this.actualizar();  
            });  
        }  
          
        this.container.appendChild(item);  
        this.actualizar();  
    }  
      
    obtenerDatos() {  
        const items \= \[\];  
        if (\!this.container) return items;  
          
        this.container.querySelectorAll(\`.${this.nombreClase}\`).forEach(item \=\> {  
            items.push({  
                id: generateUUID(),  
                nombre: item.querySelector('.input-nombre')?.value,  
                icono: item.querySelector('.select-icono')?.value,  
                activa: true  
            });  
        });  
        return items;  
    }  
      
    actualizar() {  
        // Sobrescribir en subclases si es necesario  
    }  
      
    llenarDatos(datos) {  
        if (\!this.container) return;  
          
        this.container.innerHTML \= '';  
          
        if (\!Array.isArray(datos) || datos.length \=== 0) return;  
          
        datos.forEach(item \=\> {  
            const div \= document.createElement('div');  
            div.innerHTML \= this.template;  
            div.classList.add(this.nombreClase);  
            div.dataset.itemId \= item.id;  
              
            const inputNombre \= div.querySelector('.input-nombre');  
            if (inputNombre) inputNombre.value \= item.nombre || '';  
              
            const selectIcono \= div.querySelector('.select-icono');  
            if (selectIcono) selectIcono.value \= item.icono || '';  
              
            const btnRemove \= div.querySelector('.btn-remove');  
            if (btnRemove) {  
                btnRemove.addEventListener('click', () \=\> {  
                    div.remove();  
                    this.actualizar();  
                });  
            }  
              
            this.container.appendChild(div);  
        });  
    }  
}

// \============================================  
// CLASE REPEATER DE CAMAS  
// \============================================

class RepeaterCamas extends Repeater {  
    constructor(containerId, templateHTML) {  
        super(containerId, templateHTML, 'cama-item');  
        this.inputCapacidad \= document.querySelector('\[name="capacidad\_huespedes"\]');  
    }  
      
    obtenerDatos() {  
        const camas \= \[\];  
        let capacidad\_total \= 0;  
          
        if (\!this.container) return { camas: \[\], capacidad\_total\_personas: 0, numero\_camas\_totales: 0 };  
          
        this.container.querySelectorAll('.cama-item').forEach(item \=\> {  
            const tipo \= item.querySelector('.select-tipo-cama')?.value;  
            const cantidad \= parseInt(item.querySelector('.input-cantidad')?.value) || 1;  
            const descripcion \= item.querySelector('.input-descripcion')?.value;  
              
            camas.push({  
                id: item.dataset.camaId || generateUUID(),  
                tipo: tipo,  
                cantidad: cantidad,  
                descripcion: descripcion  
            });  
              
            const personas\_por\_cama \= \['matrimonial', 'queen', 'king'\].includes(tipo) ? 2 : 1;  
            capacidad\_total \+= cantidad \* personas\_por\_cama;  
        });  
          
        return {  
            camas: camas,  
            capacidad\_total\_personas: capacidad\_total,  
            numero\_camas\_totales: camas.reduce((sum, c) \=\> sum \+ c.cantidad, 0)  
        };  
    }  
      
    actualizar() {  
        const datos \= this.obtenerDatos();  
        if (this.inputCapacidad) {  
            this.inputCapacidad.value \= datos.capacidad\_total\_personas;  
        }  
    }  
      
    agregar() {  
        if (\!this.container) return;  
          
        const item \= document.createElement('div');  
        item.innerHTML \= this.template;  
        item.classList.add('cama-item');  
        item.dataset.camaId \= generateUUID();  
          
        const btnRemove \= item.querySelector('.btn-remove');  
        if (btnRemove) {  
            btnRemove.addEventListener('click', () \=\> {  
                item.remove();  
                this.actualizar();  
            });  
        }  
          
        const selectTipo \= item.querySelector('.select-tipo-cama');  
        const inputCantidad \= item.querySelector('.input-cantidad');  
          
        if (selectTipo) selectTipo.addEventListener('change', () \=\> this.actualizar());  
        if (inputCantidad) inputCantidad.addEventListener('input', () \=\> this.actualizar());  
          
        this.container.appendChild(item);  
        this.actualizar();  
    }  
      
    llenarDatos(datos) {  
        if (\!this.container || \!datos || \!datos.camas) return;  
          
        this.container.innerHTML \= '';  
          
        datos.camas.forEach(cama \=\> {  
            const div \= document.createElement('div');  
            div.innerHTML \= this.template;  
            div.classList.add('cama-item');  
            div.dataset.camaId \= cama.id;  
              
            const selectTipo \= div.querySelector('.select-tipo-cama');  
            if (selectTipo) selectTipo.value \= cama.tipo;  
              
            const inputCantidad \= div.querySelector('.input-cantidad');  
            if (inputCantidad) inputCantidad.value \= cama.cantidad;  
              
            const inputDesc \= div.querySelector('.input-descripcion');  
            if (inputDesc) inputDesc.value \= cama.descripcion;  
              
            const btnRemove \= div.querySelector('.btn-remove');  
            if (btnRemove) {  
                btnRemove.addEventListener('click', () \=\> {  
                    div.remove();  
                    this.actualizar();  
                });  
            }  
              
            if (selectTipo) selectTipo.addEventListener('change', () \=\> this.actualizar());  
            if (inputCantidad) inputCantidad.addEventListener('input', () \=\> this.actualizar());  
              
            this.container.appendChild(div);  
        });  
          
        this.actualizar();  
    }  
}

// \============================================  
// CLASE REPEATER DE TARIFAS  
// \============================================

class RepeaterTarifas extends Repeater {  
    constructor(containerId, templateHTML) {  
        super(containerId, templateHTML, 'tarifa-item');  
    }  
      
    obtenerDatos() {  
        const tarifas \= \[\];  
          
        if (\!this.container) return tarifas;  
          
        this.container.querySelectorAll('.tarifa-item').forEach(item \=\> {  
            tarifas.push({  
                id: item.dataset.tarifaId || generateUUID(),  
                tipo\_tarifa: item.querySelector('.select-tipo-tarifa')?.value,  
                precio: parseFloat(item.querySelector('.input-precio')?.value) || 0,  
                fecha\_inicio: item.querySelector('.input-fecha-inicio')?.value,  
                fecha\_fin: item.querySelector('.input-fecha-fin')?.value,  
                activa: item.querySelector('input\[type="checkbox"\]')?.checked ?? true  
            });  
        });  
          
        return tarifas;  
    }  
      
    agregar() {  
        if (\!this.container) return;  
          
        const item \= document.createElement('div');  
        item.innerHTML \= this.template;  
        item.classList.add('tarifa-item');  
        item.dataset.tarifaId \= generateUUID();  
          
        const btnRemove \= item.querySelector('.btn-remove');  
        if (btnRemove) {  
            btnRemove.addEventListener('click', () \=\> {  
                item.remove();  
            });  
        }  
          
        this.container.appendChild(item);  
    }  
      
    llenarDatos(datos) {  
        if (\!this.container) return;  
          
        this.container.innerHTML \= '';  
          
        if (\!Array.isArray(datos) || datos.length \=== 0) return;  
          
        datos.forEach(tarifa \=\> {  
            const div \= document.createElement('div');  
            div.innerHTML \= this.template;  
            div.classList.add('tarifa-item');  
            div.dataset.tarifaId \= tarifa.id;  
              
            const selectTipo \= div.querySelector('.select-tipo-tarifa');  
            if (selectTipo) selectTipo.value \= tarifa.tipo\_tarifa;  
              
            const inputPrecio \= div.querySelector('.input-precio');  
            if (inputPrecio) inputPrecio.value \= tarifa.precio;  
              
            const inputInicio \= div.querySelector('.input-fecha-inicio');  
            if (inputInicio) inputInicio.value \= tarifa.fecha\_inicio || '';  
              
            const inputFin \= div.querySelector('.input-fecha-fin');  
            if (inputFin) inputFin.value \= tarifa.fecha\_fin || '';  
              
            const checkbox \= div.querySelector('input\[type="checkbox"\]');  
            if (checkbox) checkbox.checked \= tarifa.activa;  
              
            const btnRemove \= div.querySelector('.btn-remove');  
            if (btnRemove) {  
                btnRemove.addEventListener('click', () \=\> {  
                    div.remove();  
                });  
            }  
              
            this.container.appendChild(div);  
        });  
    }  
}

// \============================================  
// CLASE REPEATER DE FAQs  
// \============================================

class RepeaterFAQs extends Repeater {  
    constructor(containerId, templateHTML, idioma \= 'es') {  
        super(containerId, templateHTML, 'faq-item');  
        this.idioma \= idioma;  
    }  
      
    obtenerDatos() {  
        const faqs \= \[\];  
          
        if (\!this.container) return faqs;  
          
        this.container.querySelectorAll('.faq-item').forEach((item, index) \=\> {  
            faqs.push({  
                id: item.dataset.faqId || generateUUID(),  
                idioma: this.idioma,  
                pregunta: item.querySelector('.input-pregunta')?.value,  
                respuesta: item.querySelector('.textarea-respuesta')?.value,  
                orden: index  
            });  
        });  
          
        return faqs;  
    }  
      
    agregar() {  
        if (\!this.container) return;  
          
        const item \= document.createElement('div');  
        item.innerHTML \= this.template;  
        item.classList.add('faq-item');  
        item.dataset.faqId \= generateUUID();  
          
        const btnRemove \= item.querySelector('.btn-remove');  
        if (btnRemove) {  
            btnRemove.addEventListener('click', () \=\> {  
                item.remove();  
            });  
        }  
          
        this.container.appendChild(item);  
    }  
      
    llenarDatos(datos) {  
        if (\!this.container) return;  
          
        this.container.innerHTML \= '';  
          
        if (\!Array.isArray(datos) || datos.length \=== 0) return;  
          
        datos.forEach(faq \=\> {  
            const div \= document.createElement('div');  
            div.innerHTML \= this.template;  
            div.classList.add('faq-item');  
            div.dataset.faqId \= faq.id;  
              
            const inputPregunta \= div.querySelector('.input-pregunta');  
            if (inputPregunta) inputPregunta.value \= faq.pregunta;  
              
            const textareaRespuesta \= div.querySelector('.textarea-respuesta');  
            if (textareaRespuesta) textareaRespuesta.value \= faq.respuesta;  
              
            const btnRemove \= div.querySelector('.btn-remove');  
            if (btnRemove) {  
                btnRemove.addEventListener('click', () \=\> {  
                    div.remove();  
                });  
            }  
              
            this.container.appendChild(div);  
        });  
    }  
}

// \============================================  
// INICIALIZAR REPEATERS  
// \============================================

const repeaterComodidades \= new Repeater('comodidadesContainer', \`  
    \<input type="text" placeholder="Nombre" class="input-nombre"\>  
    \<select class="select-icono"\>  
        \<option value="wifi"\>WiFi\</option\>  
        \<option value="tv"\>TV\</option\>  
        \<option value="wind"\>Aire Acondicionado\</option\>  
        \<option value="droplet"\>Baño Privado\</option\>  
        \<option value="coffee"\>Café\</option\>  
    \</select\>  
    \<button type="button" class="btn-remove"\>✕\</button\>  
\`);

const repeaterAmenities \= new Repeater('amenitiesContainer', \`  
    \<input type="text" placeholder="Nombre" class="input-nombre"\>  
    \<select class="select-icono"\>  
        \<option value="coffee"\>Desayuno\</option\>  
        \<option value="map-pin"\>Parking\</option\>  
        \<option value="heart"\>Spa\</option\>  
        \<option value="phone"\>Teléfono\</option\>  
    \</select\>  
    \<button type="button" class="btn-remove"\>✕\</button\>  
\`);

const repeaterCamas \= new RepeaterCamas('camasContainer', \`  
    \<select class="select-tipo-cama"\>  
        \<option value="individual"\>Individual\</option\>  
        \<option value="matrimonial"\>Matrimonial\</option\>  
        \<option value="queen"\>Queen\</option\>  
        \<option value="king"\>King\</option\>  
        \<option value="litera"\>Litera\</option\>  
    \</select\>  
    \<input type="number" placeholder="Cantidad" class="input-cantidad" value="1" min="1"\>  
    \<input type="text" placeholder="Descripción" class="input-descripcion"\>  
    \<button type="button" class="btn-remove"\>✕\</button\>  
\`);

const repeaterTarifas \= new RepeaterTarifas('tarifasContainer', \`  
    \<select class="select-tipo-tarifa"\>  
        \<option value="baja"\>Baja Temporada\</option\>  
        \<option value="alta"\>Alta Temporada\</option\>  
        \<option value="corporativo"\>Corporativo\</option\>  
    \</select\>  
    \<input type="number" placeholder="Precio" class="input-precio" step="0.01"\>  
    \<input type="date" class="input-fecha-inicio"\>  
    \<input type="date" class="input-fecha-fin"\>  
    \<input type="checkbox" title="Activa" checked\>  
    \<button type="button" class="btn-remove"\>✕\</button\>  
\`);

const repeaterFAQsES \= new RepeaterFAQs('faqsContainerES', \`  
    \<input type="text" placeholder="Pregunta" class="input-pregunta"\>  
    \<textarea placeholder="Respuesta" class="textarea-respuesta"\>\</textarea\>  
    \<button type="button" class="btn-remove"\>✕\</button\>  
\`, 'es');

const repeaterFAQsEN \= new RepeaterFAQs('faqsContainerEN', \`  
    \<input type="text" placeholder="Question" class="input-pregunta"\>  
    \<textarea placeholder="Answer" class="textarea-respuesta"\>\</textarea\>  
    \<button type="button" class="btn-remove"\>✕\</button\>  
\`, 'en');

// \============================================  
// EVENT LISTENERS \- BOTONES AGREGAR  
// \============================================

document.getElementById('btnAgregarComodidad')?.addEventListener('click', (e) \=\> {  
    e.preventDefault();  
    repeaterComodidades.agregar();  
});

document.getElementById('btnAgregarAmenity')?.addEventListener('click', (e) \=\> {  
    e.preventDefault();  
    repeaterAmenities.agregar();  
});

document.getElementById('btnAgregarCama')?.addEventListener('click', (e) \=\> {  
    e.preventDefault();  
    repeaterCamas.agregar();  
});

document.getElementById('btnAgregarTarifa')?.addEventListener('click', (e) \=\> {  
    e.preventDefault();  
    repeaterTarifas.agregar();  
});

document.getElementById('btnAgregarFAQES')?.addEventListener('click', (e) \=\> {  
    e.preventDefault();  
    repeaterFAQsES.agregar();  
});

document.getElementById('btnAgregarFAQEN')?.addEventListener('click', (e) \=\> {  
    e.preventDefault();  
    repeaterFAQsEN.agregar();  
});

// \============================================  
// TABS DE IDIOMA  
// \============================================

document.querySelectorAll('.idiomas-tabs, .seo-tabs').forEach(tabsContainer \=\> {  
    tabsContainer.querySelectorAll('.tab-button').forEach(btn \=\> {  
        btn.addEventListener('click', (e) \=\> {  
            e.preventDefault();  
              
            const idioma \= e.target.dataset.idioma;  
            const tab \= e.target.dataset.tab;  
            const selector \= idioma ? \`\[id^="tab-${idioma}"\], \[id^="seo-${idioma}"\]\` : \`\[id^="${tab}"\]\`;  
              
            // Desactivar todos los tabs en este container  
            tabsContainer.parentElement.querySelectorAll('.tab-content').forEach(t \=\> t.classList.remove('active'));  
            tabsContainer.querySelectorAll('.tab-button').forEach(b \=\> b.classList.remove('active'));  
              
            // Activar el seleccionado  
            if (idioma) {  
                document.getElementById(\`tab-${idioma}\`)?.classList.add('active');  
                document.getElementById(\`seo-${idioma}\`)?.classList.add('active');  
            } else {  
                document.getElementById(tab)?.classList.add('active');  
            }  
              
            e.target.classList.add('active');  
        });  
    });  
});

// \============================================  
// CONTADOR DE CARACTERES SEO  
// \============================================

\['titulo', 'descripcion'\].forEach(campo \=\> {  
    \['es', 'en'\].forEach(idioma \=\> {  
        const selector \= \`\[name="seo\_${campo}\_${idioma}"\]\`;  
        const input \= document.querySelector(selector);  
        const counter \= document.getElementById(\`counter-${campo}\-${idioma}\`);  
          
        if (input && counter) {  
            input.addEventListener('input', (e) \=\> {  
                const max \= campo \=== 'titulo' ? 60 : 160;  
                counter.textContent \= \`${e.target.value.length}/${max}\`;  
            });  
        }  
    });  
});

// \============================================  
// MODAL SELECTOR DE IMAGEN  
// \============================================

class SelectorImagen {  
    constructor(modalId \= 'modalSelectorImagen') {  
        this.modalId \= modalId;  
        this.imagenSeleccionada \= null;  
        this.tipo \= 'principal'; // principal o galeria  
    }  
      
    abrir(tipo \= 'principal') {  
        this.tipo \= tipo;  
        this.imagenSeleccionada \= null;  
        const modal \= document.getElementById(this.modalId);  
        if (modal) {  
            modal.classList.remove('hidden');  
            this.cargarImagenes();  
        }  
    }  
      
    async cargarImagenes() {  
        const response \= await fetch('/proyecto-hotel/api/galeria/obtener-imagenes.php?limite=100');  
        const data \= await response.json();  
          
        if (data.exito) {  
            const grid \= document.getElementById('gridImagenes');  
            grid.innerHTML \= data.imagenes.map(img \=\> \`  
                \<div class="imagen-selectable" data-id="${img.id}" data-ruta="${img.ruta\_original}"\>  
                    \<img src="${img.ruta\_tiny}" alt="${img.alt\_text}"\>  
                \</div\>  
            \`).join('');  
              
            grid.querySelectorAll('.imagen-selectable').forEach(el \=\> {  
                el.addEventListener('click', () \=\> this.seleccionar(el));  
            });  
        }  
    }  
      
    seleccionar(elemento) {  
        const id \= elemento.dataset.id;  
        const ruta \= elemento.dataset.ruta;  
          
        this.imagenSeleccionada \= { id, ruta };  
          
        document.querySelectorAll('.imagen-selectable').forEach(el \=\> el.classList.remove('selected'));  
        elemento.classList.add('selected');  
          
        document.getElementById('btnConfirmarImagen').disabled \= false;  
    }  
      
    confirmar() {  
        return this.imagenSeleccionada;  
    }  
      
    cerrar() {  
        const modal \= document.getElementById(this.modalId);  
        if (modal) modal.classList.add('hidden');  
    }  
}

const selectorImagen \= new SelectorImagen();

document.getElementById('btnSeleccionarPrincipal')?.addEventListener('click', () \=\> {  
    selectorImagen.abrir('principal');  
});

document.getElementById('btnAgregarGaleria')?.addEventListener('click', () \=\> {  
    selectorImagen.abrir('galeria');  
});

document.getElementById('btnConfirmarImagen')?.addEventListener('click', () \=\> {  
    const img \= selectorImagen.confirmar();  
    if (\!img) return;  
      
    if (selectorImagen.tipo \=== 'principal') {  
        document.querySelector('\[name="imagen\_principal\_id"\]').value \= img.id;  
        const preview \= document.getElementById('previewPrincipal');  
        if (preview) {  
            preview.src \= img.ruta;  
            preview.style.display \= 'block';  
        }  
    } else {  
        const html \= \`  
            \<div class="galeria-item" data-imagen-id="${img.id}"\>  
                \<img src="${img.ruta}" alt=""\>  
                \<div class="galeria-item-overlay"\>  
                    \<button type="button" class="btn-remove-galeria"\>✕\</button\>  
                \</div\>  
            \</div\>  
        \`;  
        const div \= document.createElement('div');  
        div.innerHTML \= html;  
        const item \= div.firstElementChild;  
          
        item.querySelector('.btn-remove-galeria').addEventListener('click', () \=\> {  
            item.remove();  
        });  
          
        document.getElementById('galeriaItems').appendChild(item);  
    }  
      
    selectorImagen.cerrar();  
});

document.getElementById('btnCancelarImagen')?.addEventListener('click', () \=\> {  
    selectorImagen.cerrar();  
});

// \============================================  
// GUARDAR HABITACIÓN  
// \============================================

document.getElementById('formHabitacion')?.addEventListener('submit', async (e) \=\> {  
    e.preventDefault();  
      
    const csrfToken \= document.querySelector('meta\[name="csrf-token"\]').content;  
    const habitacionId \= new URLSearchParams(window.location.search).get('id');  
      
    const galeriaIds \= Array.from(document.querySelectorAll('\#galeriaItems .galeria-item')).map(el \=\> ({  
        id: parseInt(el.dataset.imagenId),  
        es\_principal: false  
    }));  
      
    const datosPrincial \= document.querySelector('\[name="imagen\_principal\_id"\]').value;  
    if (datosPrincial) {  
        galeriaIds.unshift({ id: parseInt(datosPrincial), es\_principal: true });  
    }  
      
    const datos \= {  
        habitacion\_id: habitacionId ? parseInt(habitacionId) : null,  
        numero\_habitacion: parseInt(document.querySelector('\[name="numero\_habitacion"\]').value),  
        tipo\_habitacion\_id: parseInt(document.querySelector('\[name="tipo\_habitacion\_id"\]').value),  
        estado: document.querySelector('\[name="estado"\]').value,  
        activa: document.querySelector('\[name="activa"\]').checked,  
        capacidad\_huespedes: parseInt(document.querySelector('\[name="capacidad\_huespedes"\]').value),  
          
        nombre\_es: document.querySelector('\[name="nombre\_es"\]').value,  
        descripcion\_es: tinymce.get('editor-es')?.getContent() || '',  
        seo\_titulo\_es: document.querySelector('\[name="seo\_titulo\_es"\]').value,  
        seo\_descripcion\_es: document.querySelector('\[name="seo\_descripcion\_es"\]').value,  
        seo\_palabras\_clave\_es: document.querySelector('\[name="seo\_palabras\_clave\_es"\]').value,  
          
        nombre\_en: document.querySelector('\[name="nombre\_en"\]').value,  
        descripcion\_en: tinymce.get('editor-en')?.getContent() || '',  
        seo\_titulo\_en: document.querySelector('\[name="seo\_titulo\_en"\]').value,  
        seo\_descripcion\_en: document.querySelector('\[name="seo\_descripcion\_en"\]').value,  
        seo\_palabras\_clave\_en: document.querySelector('\[name="seo\_palabras\_clave\_en"\]').value,  
          
        comodidades: repeaterComodidades.obtenerDatos(),  
        amenities: repeaterAmenities.obtenerDatos(),  
        camas: repeaterCamas.obtenerDatos(),  
        tarifas: repeaterTarifas.obtenerDatos(),  
        imagenes: galeriaIds,  
        faqs\_es: repeaterFAQsES.obtenerDatos(),  
        faqs\_en: repeaterFAQsEN.obtenerDatos(),  
          
        csrf\_token: csrfToken  
    };  
      
    const url \= habitacionId   
        ? '/proyecto-hotel/api/habitaciones/actualizar.php'  
        : '/proyecto-hotel/api/habitaciones/crear.php';  
      
    try {  
        const response \= await fetch(url, {  
            method: 'POST',  
            headers: { 'Content-Type': 'application/json' },  
            body: JSON.stringify(datos)  
        });  
          
        const resultado \= await response.json();  
          
        if (resultado.exito) {  
            alert('Habitación guardada correctamente');  
            window.location.href \= '/proyecto-hotel/admin/habitaciones.php';  
        } else {  
            alert('Error: ' \+ resultado.error);  
        }  
    } catch (error) {  
        alert('Error: ' \+ error.message);  
    }  
});

// \============================================  
// CARGAR HABITACIÓN SI ES EDICIÓN  
// \============================================

const habitacionId \= new URLSearchParams(window.location.search).get('id');

if (habitacionId) {  
    cargarHabitacion(parseInt(habitacionId));  
}

async function cargarHabitacion(id) {  
    try {  
        const response \= await fetch(\`/proyecto-hotel/api/habitaciones/obtener.php?id=${id}\&idioma=es\`);  
        const data \= await response.json();  
          
        if (data.exito) {  
            const hab \= data.habitacion;  
              
            document.querySelector('\[name="numero\_habitacion"\]').value \= hab.habitacion.numero\_habitacion;  
            document.querySelector('\[name="tipo\_habitacion\_id"\]').value \= hab.habitacion.tipo\_habitacion\_id;  
            document.querySelector('\[name="estado"\]').value \= hab.habitacion.estado;  
            document.querySelector('\[name="activa"\]').checked \= hab.habitacion.activa;  
            document.querySelector('\[name="capacidad\_huespedes"\]').value \= hab.habitacion.capacidad\_huespedes;  
              
            document.querySelector('\[name="nombre\_es"\]').value \= hab.idioma\_data.nombre;  
            tinymce.get('editor-es').setContent(hab.idioma\_data.descripcion);  
            document.querySelector('\[name="seo\_titulo\_es"\]').value \= hab.idioma\_data.seo\_titulo || '';  
            document.querySelector('\[name="seo\_descripcion\_es"\]').value \= hab.idioma\_data.seo\_descripcion || '';  
            document.querySelector('\[name="seo\_palabras\_clave\_es"\]').value \= hab.idioma\_data.seo\_palabras\_clave || '';  
              
            repeaterComodidades.llenarDatos(hab.comodidades);  
            repeaterAmenities.llenarDatos(hab.amenities);  
            repeaterCamas.llenarDatos(hab.camas);  
            repeaterTarifas.llenarDatos(hab.tarifas);  
            repeaterFAQsES.llenarDatos(hab.faqs);  
              
            // Llenar galería  
            hab.imagenes.forEach(img \=\> {  
                if (img.es\_principal) {  
                    document.querySelector('\[name="imagen\_principal\_id"\]').value \= img.id;  
                    const preview \= document.getElementById('previewPrincipal');  
                    if (preview) {  
                        preview.src \= img.ruta\_original;  
                        preview.style.display \= 'block';  
                    }  
                } else {  
                    const html \= \`  
                        \<div class="galeria-item" data-imagen-id="${img.id}"\>  
                            \<img src="${img.ruta\_tiny}" alt=""\>  
                            \<div class="galeria-item-overlay"\>  
                                \<button type="button" class="btn-remove-galeria"\>✕\</button\>  
                            \</div\>  
                        \</div\>  
                    \`;  
                    const div \= document.createElement('div');  
                    div.innerHTML \= html;  
                    const item \= div.firstElementChild;  
                      
                    item.querySelector('.btn-remove-galeria').addEventListener('click', () \=\> {  
                        item.remove();  
                    });  
                      
                    document.getElementById('galeriaItems').appendChild(item);  
                }  
            });  
        }  
    } catch (error) {  
        console.error('Error cargando habitación:', error);  
    }  
}

// \============================================  
// FUNCIONES AUXILIARES  
// \============================================

function generateUUID() {  
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/\[xy\]/g, function(c) {  
        const r \= Math.random() \* 16 | 0;  
        const v \= c \=== 'x' ? r : (r & 0x3 | 0x8);  
        return v.toString(16);  
    });  
}

### **2\. HELPERS \- includes/Helpers.php**

php  
**\<?php**

function slugify($texto) {  
    $texto \= mb\_strtolower($texto, 'UTF-8');  
    $texto \= iconv('UTF-8', 'ASCII//TRANSLIT', $texto);  
    $texto \= preg\_replace('/\[^a-z0-9\]+/', '-', $texto);  
    return trim($texto, '-');  
}

function sanitizeHTML($html) {  
    $allowed \= '\<p\>\<br\>\<strong\>\<em\>\<u\>\<ol\>\<ul\>\<li\>\<h1\>\<h2\>\<h3\>\<a\>\<img\>';  
    return strip\_tags($html, $allowed);  
}

function getPrecioFormato($precio) {  
    return '$' . number\_format($precio, 2, ',', '.');  
}

function formatearCapacidad($numero) {  
    return $numero . ' ' . ($numero \== 1 ? 'persona' : 'personas');  
}

**?\>**  
---

## **8\. CHECKLIST DE IMPLEMENTACIÓN**

### **Semana 3**

**Día 1-2: Setup Base**

* Crear todas las tablas BD (schema.sql)  
* Crear clase RoomManager.php  
* Crear helpers (slugify, sanitizeHTML, etc)  
* Crear tipos\_habitacion iniciales (Simple, Doble, Suite, Deluxe)

**Día 3-5: APIs**

* POST /api/habitaciones/crear.php  
* POST /api/habitaciones/actualizar.php  
* POST /api/habitaciones/eliminar.php  
* GET /api/habitaciones/obtener.php  
* GET /api/habitaciones/obtener-todas.php  
* Testing de APIs con Postman

**Día 6-10: Admin Listado**

* HTML /admin/habitaciones.php  
* JavaScript habitaciones-listado.js  
* CSS habitaciones.css  
* Testing filtros y búsqueda  
* Testing eliminar con confirmación

### **Semana 4**

**Día 1-5: Formulario Parte 1**

* HTML /admin/habitaciones/editar.php  
* CSS para formulario (secciones, tabs, repeaters)  
* TinyMCE integrado y funcional  
* Tabs ES/EN funcionales  
* Testear navegación entre tabs

**Día 6-10: Formulario Parte 2**

* JavaScript habitaciones-formulario.js  
* Clase Repeater (comodidades, amenities)  
* Clase RepeaterCamas (con cálculo automático)  
* Clase RepeaterTarifas  
* Clase RepeaterFAQs (ES y EN)  
* Modal selector de imagen  
* Guardar habitación (crear)  
* Cargar habitación (editar)  
* Contador de caracteres SEO  
* Testing creación de habitación  
* Testing edición de habitación  
* Testing multiidioma

### **Validaciones Finales**

* Validar que número\_habitacion es único  
* Validar que slug es único  
* Validar capacidad se calcula automáticamente  
* Validar que precio \> 0  
* Validar fechas tarifas (inicio \< fin)  
* Validar descripción HTML (TinyMCE)  
* Validar eliminar habitación con confirmación  
* Testing en navegadores múltiples  
* Testing responsive (mobile)

---

## **9\. ESTRUCTURA FINAL**

**Total de archivos nuevo**:

* 3 APIs (crear, actualizar, eliminar, obtener, obtener-todas)  
* 1 Clase PHP (RoomManager)  
* 2 HTML (listado, formulario)  
* 1 CSS (habitaciones)  
* 2 JS (listado, formulario)  
* 1 Helpers (con funciones reutilizables)

**Tablas BD**: 9 tablas relacionadas

