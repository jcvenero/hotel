# **📘 PARTE 1: SISTEMA DE RESERVAS (CORE DEL CMS)**

## **🎯 OBJETIVO**

Convertir tu CMS en un sistema capaz de:

* gestionar habitaciones reales  
* registrar reservas  
* calcular disponibilidad por fechas  
* evitar sobreventas

---

# **🧱 1\. MODELO DE DATOS (BASE DE DATOS)**

## **🔴 PROBLEMA ACTUAL**

La tabla `habitaciones` tiene:

estado ENUM('disponible','ocupada','mantenimiento')

👉 Esto es incorrecto para hoteles.

---

## **✅ SOLUCIÓN**

### **✔️ Modificar tabla `habitaciones`**

estado ENUM('activa','mantenimiento')

👉 La ocupación ya NO se guarda aquí.

---

## **🆕 NUEVA TABLA: `reservas`**

CREATE TABLE reservas (  
   id INT AUTO\_INCREMENT PRIMARY KEY,  
    
   habitacion\_id INT NOT NULL,  
    
   fecha\_inicio DATE NOT NULL,  
   fecha\_fin DATE NOT NULL,  
    
   estado ENUM('pendiente','confirmada','cancelada') DEFAULT 'pendiente',  
    
   nombre\_cliente VARCHAR(255),  
   email VARCHAR(255),  
   telefono VARCHAR(50),  
    
   total DECIMAL(10,2),  
   adelanto DECIMAL(10,2),  
    
   metodo\_pago VARCHAR(100),  
   referencia\_pago VARCHAR(255),  
    
   origen ENUM('web','booking','airbnb','manual') DEFAULT 'web',  
    
   fecha\_creacion TIMESTAMP DEFAULT CURRENT\_TIMESTAMP,  
    
   FOREIGN KEY (habitacion\_id) REFERENCES habitaciones(id)  
);  
---

## **🧠 IMPORTANTE**

* `fecha_inicio` \= check-in  
* `fecha_fin` \= check-out  
* La habitación queda ocupada entre esas fechas

---

# **⚙️ 2\. LÓGICA DE DISPONIBILIDAD**

## **🎯 OBJETIVO**

Saber qué habitaciones están disponibles en un rango de fechas.

---

## **🔍 QUERY CLAVE**

SELECT \* FROM habitaciones h  
WHERE h.estado \= 'activa'  
AND h.id NOT IN (  
   SELECT r.habitacion\_id  
   FROM reservas r  
   WHERE r.estado IN ('pendiente','confirmada')  
   AND (  
       r.fecha\_inicio \< '2026-04-10'  
       AND r.fecha\_fin \> '2026-04-05'  
   )  
);  
---

## **🧠 EXPLICACIÓN**

Una habitación NO está disponible si:

fecha\_inicio\_reserva \< fecha\_fin\_busqueda  
Y  
fecha\_fin\_reserva \> fecha\_inicio\_busqueda

👉 Esto evita solapamientos.

---

# **🖥️ 3\. BACKEND (LÓGICA DEL SISTEMA)**

## **🔹 A. CREAR RESERVA**

Flujo:

1. Usuario selecciona fechas  
2. Backend valida disponibilidad  
3. Si hay habitación:  
   * asigna automáticamente  
4. guarda reserva en estado `pendiente`

---

## **🔹 B. CONFIRMAR RESERVA**

Cuando se paga (o manual):

UPDATE reservas  
SET estado \= 'confirmada'  
WHERE id \= ?  
---

## **🔹 C. CANCELAR RESERVA**

UPDATE reservas  
SET estado \= 'cancelada'  
WHERE id \= ?  
---

## **🔹 D. ASIGNACIÓN AUTOMÁTICA**

Cuando el usuario selecciona:

👉 tipo de habitación (ej: Doble)

Backend debe:

SELECT id FROM habitaciones  
WHERE tipo\_habitacion\_id \= ?  
AND estado \= 'activa'  
AND id NOT IN (subquery disponibilidad)  
LIMIT 1;

👉 asignas la primera disponible

---

# **🎨 4\. FRONTEND (EXPERIENCIA DEL USUARIO)**

## **🔹 FLUJO**

### **1\. Página de habitación**

* fotos  
* descripción  
* precio  
* selector de fechas

---

### **2\. Usuario selecciona fechas**

→ AJAX o submit

---

### **3\. Backend responde:**

* disponible ❌ / ✔️  
* precio calculado

---

### **4\. Formulario**

* nombre  
* email  
* teléfono

---

### **5\. Reserva creada**

Estado:

👉 `pendiente`

---

### **6\. Pago (opcional)**

* redirección o link

---

### **7\. Confirmación**

👉 cambia a `confirmada`

---

# **🧠 5\. PANEL ADMIN (BACKOFFICE)**

Debe permitir:

### **✔️ Ver reservas**

* calendario  
* lista

### **✔️ Crear manualmente**

### **✔️ Editar estado**

### **✔️ Bloquear habitaciones manualmente**

---

# **📊 6\. CALENDARIO (ADMIN)**

Vista tipo:

Habitación 101 | ███░░░░██  
Habitación 102 | ░░███░░░░  
---

👉 basado en tabla `reservas`

---

# **🚀 PARTE 2: IMPLEMENTACIÓN DE ICAL**

---

# **🧠 1\. ¿QUÉ ES iCal?**

Es un formato estándar (`.ics`) que contiene eventos:

BEGIN:VEVENT  
DTSTART:20260401  
DTEND:20260405  
END:VEVENT

👉 representa reservas

---

# **🔗 2\. QUÉ NECESITAS**

Cada OTA te da una URL tipo:

https://...calendar.ics  
---

# **⚙️ 3\. NUEVA TABLA: `ical_sources`**

CREATE TABLE ical\_sources (  
   id INT AUTO\_INCREMENT PRIMARY KEY,  
    
   habitacion\_id INT,  
   url TEXT NOT NULL,  
    
   origen ENUM('booking','airbnb'),  
    
   ultima\_sync DATETIME,  
    
   activo TINYINT(1) DEFAULT 1  
);  
---

# **🔄 4\. PROCESO DE SINCRONIZACIÓN**

## **🧠 Flujo:**

1\. Leer URL iCal  
2\. Descargar archivo  
3\. Parsear eventos  
4\. Insertar/actualizar reservas  
---

# **🧩 5\. PARSEO EN PHP (concepto)**

$ical \= file\_get\_contents($url);

preg\_match\_all('/BEGIN:VEVENT(.\*?)END:VEVENT/s', $ical, $events);

foreach ($events\[1\] as $event) {  
   preg\_match('/DTSTART:(\\d+)/', $event, $start);  
   preg\_match('/DTEND:(\\d+)/', $event, $end);

   $fecha\_inicio \= date('Y-m-d', strtotime($start\[1\]));  
   $fecha\_fin \= date('Y-m-d', strtotime($end\[1\]));

   // insertar en reservas  
}  
---

# **⚠️ 6\. REGLAS IMPORTANTES**

## **🔴 NO duplicar reservas**

Antes de insertar:

* verificar si ya existe

---

## **🔴 marcar origen**

origen \= 'booking'  
---

## **🔴 no sobrescribir reservas web**

---

# **⏱️ 7\. FRECUENCIA**

* cada 5–15 minutos (ideal)  
* o cron job

---

# **🧠 8\. RELACIÓN CON TU SISTEMA**

👉 reservas iCal \= bloquean habitaciones

👉 se guardan en la misma tabla `reservas`

---

# **💥 9\. LIMITACIONES**

* no es 100% tiempo real  
* puede haber delay  
* no permite precios

---

# **🚀 CONCLUSIÓN FINAL**

## **FASE 1 (OBLIGATORIA)**

* tabla reservas  
* lógica de disponibilidad  
* flujo completo

---

## **FASE 2**

* integración pagos

---

## **FASE 3**

* iCal automático

---

# **🔥 ÚLTIMO CONSEJO (MUY IMPORTANTE)**

No intentes hacer todo perfecto desde el inicio.

👉 primero haz:

**“reserva \+ bloqueo de habitaciones correcto”**

