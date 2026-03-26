-- Migración para el Módulo de Formularios
-- Añadir campos de contexto operativo a formulario_respuestas

ALTER TABLE formulario_respuestas
ADD COLUMN pagina_origen VARCHAR(255) AFTER formulario_id,
ADD COLUMN tipo_entidad_origen VARCHAR(50) AFTER pagina_origen,
ADD COLUMN entidad_origen_id INT AFTER tipo_entidad_origen,
ADD COLUMN idioma_origen VARCHAR(10) AFTER entidad_origen_id;

-- Seeding Inicial: Formulario de Contacto
INSERT INTO formularios (nombre, slug, tipo, activo, mensaje_exito) 
VALUES ('Formulario de Contacto', 'contacto', 'contacto', 1, '¡Gracias por contactarnos! Te responderemos lo antes posible.');

SET @contacto_id = LAST_INSERT_ID();

INSERT INTO formulario_campos (formulario_id, nombre_campo, tipo_campo, label, label_en, requerido, orden) VALUES
(@contacto_id, 'nombre', 'texto', 'Nombre Completo', 'Full Name', 1, 1),
(@contacto_id, 'email', 'email', 'Correo Electrónico', 'Email Address', 1, 2),
(@contacto_id, 'telefono', 'telefono', 'Teléfono / WhatsApp', 'Phone Number', 0, 3),
(@contacto_id, 'asunto', 'texto', 'Asunto', 'Subject', 1, 4),
(@contacto_id, 'mensaje', 'textarea', 'Mensaje', 'Message', 1, 5);

-- Seeding Inicial: Formulario de Reserva
INSERT INTO formularios (nombre, slug, tipo, activo, mensaje_exito) 
VALUES ('Formulario de Reserva', 'reserva', 'reserva', 1, 'Tu solicitud de reserva ha sido enviada. Nos pondremos en contacto para confirmar disponibilidad.');

SET @reserva_id = LAST_INSERT_ID();

INSERT INTO formulario_campos (formulario_id, nombre_campo, tipo_campo, label, label_en, requerido, orden) VALUES
(@reserva_id, 'nombre', 'texto', 'Nombre completo', 'Full Name', 1, 1),
(@reserva_id, 'email', 'email', 'Correo Electrónico', 'Email Address', 1, 2),
(@reserva_id, 'telefono', 'telefono', 'Teléfono de contacto', 'Phone Number', 1, 3),
(@reserva_id, 'fecha_llegada', 'fecha', 'Fecha de Llegada', 'Check-in Date', 1, 4),
(@reserva_id, 'fecha_salida', 'fecha', 'Fecha de Salida', 'Check-out Date', 1, 5),
(@reserva_id, 'huespedes', 'texto', 'Número de Huéspedes', 'Number of Guests', 1, 6),
(@reserva_id, 'comentarios', 'textarea', 'Comentarios adicionales', 'Additional Comments', 0, 7);
