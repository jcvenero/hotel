-- Script de Seeding para 15 Habitaciones de Ejemplo
-- Generar habitaciones, sus idiomas e imágenes base

DELIMITER //

CREATE PROCEDURE SeedingHabitaciones()
BEGIN
    DECLARE i INT DEFAULT 101;
    DECLARE tipo_id INT;
    DECLARE hab_id INT;
    DECLARE img_id INT;
    DECLARE nombre_hab VARCHAR(100);
    DECLARE nombre_en VARCHAR(100);
    
    WHILE i <= 115 DO
        -- Rotar tipos de habitación (3 al 8)
        SET tipo_id = (i % 6) + 3;
        
        -- Definir nombres según tipo
        CASE tipo_id
            WHEN 3 THEN SET nombre_hab = CONCAT('Habitación Simple Económica #', i), nombre_en = CONCAT('Budget Single Room #', i), img_id = 4;
            WHEN 4 THEN SET nombre_hab = CONCAT('Suite Single Superior #', i), nombre_en = CONCAT('Superior Single Suite #', i), img_id = 2;
            WHEN 5 THEN SET nombre_hab = CONCAT('Doble Clásica Colonial #', i), nombre_en = CONCAT('Classic Colonial Double #', i), img_id = 6;
            WHEN 6 THEN SET nombre_hab = CONCAT('Doble Vista a la Plaza #', i), nombre_en = CONCAT('Double with Plaza View #', i), img_id = 6;
            WHEN 7 THEN SET nombre_hab = CONCAT('Matrimonial Imperial #', i), nombre_en = CONCAT('Imperial Matrimonial #', i), img_id = 4;
            WHEN 8 THEN SET nombre_hab = CONCAT('Matrimonial Presidencial #', i), nombre_en = CONCAT('Presidential Matrimonial #', i), img_id = 2;
        END CASE;

        -- Insertar en habitaciones
        INSERT INTO habitaciones (numero_habitacion, slug, tipo_habitacion_id, estado, capacidad_huespedes, num_camas, precio_base, activa) 
        VALUES (i, CONCAT('habitacion-', i), tipo_id, 'disponible', IF(tipo_id IN (3,4), 1, 2), IF(tipo_id IN (3,4,7,8), 1, 2), 75.00 + (i % 10), 1);
        
        SET hab_id = LAST_INSERT_ID();

        -- Insertar Idiomas ES
        INSERT INTO habitaciones_idiomas (habitacion_id, idioma, nombre, descripcion, slug)
        VALUES (hab_id, 'es', nombre_hab, CONCAT('Disfruta de la comodidad y el encanto de nuestra ', nombre_hab, '. Equipada con todo lo necesario para una estadía inolvidable en el corazón del Cusco.'), CONCAT('habitacion-es-', i));
        
        -- Insertar Idiomas EN
        INSERT INTO habitaciones_idiomas (habitacion_id, idioma, nombre, descripcion, slug)
        VALUES (hab_id, 'en', nombre_en, CONCAT('Enjoy the comfort and charm of our ', nombre_en, '. Equipped with everything you need for an unforgettable stay in the heart of Cusco.'), CONCAT('habitacion-en-', i));

        -- Asociar Imagen Principal
        INSERT INTO habitacion_imagenes (habitacion_id, imagen_id, es_principal, orden)
        VALUES (hab_id, img_id, 1, 0);

        SET i = i + 1;
    END WHILE;
END //

DELIMITER ;

CALL SeedingHabitaciones();
DROP PROCEDURE SeedingHabitaciones;
