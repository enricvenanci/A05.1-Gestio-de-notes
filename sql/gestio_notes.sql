DROP DATABASE IF EXISTS gestio_notes;
CREATE DATABASE gestio_notes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestio_notes;

CREATE TABLE usuaris (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NULL,
    rol ENUM('alumno', 'profesor', 'admin', 'educador', 'secretaria') NOT NULL,
    INDEX idx_usuaris_rol_apellidos (rol, apellidos)
) ENGINE=InnoDB;

CREATE TABLE ciclos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE niveles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE promociones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(9) NOT NULL UNIQUE,
    actual TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE asignaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ciclo_id INT NOT NULL,
    nivel_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    UNIQUE KEY uq_asignatura_ciclo_nivel (ciclo_id, nivel_id, nombre),
    CONSTRAINT fk_asignaturas_ciclo
        FOREIGN KEY (ciclo_id) REFERENCES ciclos(id),
    CONSTRAINT fk_asignaturas_nivel
        FOREIGN KEY (nivel_id) REFERENCES niveles(id)
) ENGINE=InnoDB;

CREATE TABLE asignatura_profesor (
    asignatura_id INT NOT NULL,
    profesor_id INT NOT NULL,
    PRIMARY KEY (asignatura_id, profesor_id),
    CONSTRAINT fk_asignatura_profesor_asignatura
        FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id) ,
    CONSTRAINT fk_asignatura_profesor_usuario
        FOREIGN KEY (profesor_id) REFERENCES usuaris(id) 
) ENGINE=InnoDB;

CREATE TABLE matriculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumno_id INT NOT NULL,
    asignatura_id INT NOT NULL,
    promocion_id INT NOT NULL,
    nota DECIMAL(4,2) NULL,
    fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_matricula_alumno_asignatura_promocion
        (alumno_id, asignatura_id, promocion_id),
    INDEX idx_matriculas_promocion_asignatura (promocion_id, asignatura_id),
    CONSTRAINT chk_matriculas_nota CHECK (nota IS NULL OR (nota >= 0 AND nota <= 10)),
    CONSTRAINT fk_matriculas_alumno
        FOREIGN KEY (alumno_id) REFERENCES usuaris(id) ,
    CONSTRAINT fk_matriculas_asignatura
        FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id) ,
    CONSTRAINT fk_matriculas_promocion
        FOREIGN KEY (promocion_id) REFERENCES promociones(id)
) ENGINE=InnoDB;

INSERT INTO usuaris (id, nombre, apellidos, email, password_hash, rol) VALUES
(1, 'Usuario', 'Prueba', 'usuario.prueba@example.com',
 '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'admin'),
(2, 'Carles', 'Vidal Ferrer', 'carles.vidal@example.com',
 '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'profesor'),
(3, 'Montse', 'Casals Prats', 'montse.casals@example.com',
 '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'profesor'),
(4, 'Albert', 'Soler Mir', 'albert.soler@example.com',
 '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'educador'),
(5, 'Marta', 'Pons Riera', 'marta.pons@example.com',
 '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'secretaria'),
(6, 'Lucía', 'Martínez López', 'lucia.martinez@example.com', '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'alumno'),
(7, 'Hugo', 'Sánchez Ruiz', 'hugo.sanchez@example.com', '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'alumno'),
(8, 'Sofía', 'García Pérez', 'sofia.garcia@example.com', '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'alumno'),
(9, 'Mateo', 'Fernández Díaz', 'mateo.fernandez@example.com', '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'alumno'),
(10, 'Valeria', 'López Martín', 'valeria.lopez@example.com', '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'alumno'),
(11, 'Daniel', 'Romero Torres', 'daniel.romero@example.com', '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'alumno'),
(12, 'Emma', 'Navarro Gil', 'emma.navarro@example.com', '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'alumno'),
(13, 'Pablo', 'Vega Morales', 'pablo.vega@example.com', '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'alumno'),
(14, 'Marc', 'Ferrer Soler', 'marc.ferrer@example.com', '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'alumno'),
(15, 'Núria', 'Puig Vidal', 'nuria.puig@example.com', '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'alumno'),
(16, 'Jordi', 'Roca Camps', 'jordi.roca@example.com', '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'alumno'),
(17, 'Laia', 'Serra Mas', 'laia.serra@example.com', '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 'alumno');

INSERT INTO ciclos (id, nombre) VALUES
(1, 'Desarrollo de Aplicaciones Web (DAW)'),
(2, 'Desarrollo de Aplicaciones Multiplataforma (DAM)'),
(3, 'Administración de Sistemas Informáticos en Red (ASIR)');

INSERT INTO niveles (id, nombre) VALUES
(1, '1º curso'),
(2, '2º curso');

INSERT INTO promociones (id, nombre, actual) VALUES
(1, '2025-2026', 0),
(2, '2026-2027', 1);

INSERT INTO asignaturas (id, ciclo_id, nivel_id, nombre) VALUES
(1, 1, 1, 'Programación'),
(2, 1, 1, 'Bases de datos'),
(3, 1, 1, 'Sistemas informáticos'),
(4, 1, 1, 'Lenguajes de marcas'),
(5, 1, 2, 'Desarrollo web en entorno cliente'),
(6, 1, 2, 'Desarrollo web en entorno servidor'),
(7, 1, 2, 'Despliegue de aplicaciones web'),
(8, 1, 2, 'Diseño de interfaces web'),
(9, 2, 1, 'Programación'),
(10, 2, 1, 'Bases de datos'),
(11, 2, 1, 'Sistemas informáticos'),
(12, 2, 1, 'Lenguajes de marcas'),
(13, 2, 2, 'Acceso a datos'),
(14, 2, 2, 'Desarrollo de interfaces'),
(15, 2, 2, 'Programación multimedia y dispositivos móviles'),
(16, 2, 2, 'Programación de servicios y procesos'),
(17, 3, 1, 'Implantación de sistemas operativos'),
(18, 3, 1, 'Planificación y administración de redes'),
(19, 3, 1, 'Fundamentos de hardware'),
(20, 3, 1, 'Gestión de bases de datos'),
(21, 3, 2, 'Administración de sistemas operativos'),
(22, 3, 2, 'Servicios de red e Internet'),
(23, 3, 2, 'Implantación de aplicaciones web'),
(24, 3, 2, 'Seguridad y alta disponibilidad');

INSERT INTO asignatura_profesor (asignatura_id, profesor_id) VALUES
(1, 2), (2, 2), (3, 2), (4, 2), (5, 2), (6, 2), (7, 2), (8, 2),
(9, 3), (10, 3), (11, 3), (12, 3), (13, 3), (14, 3), (15, 3), (16, 3),
(17, 3), (18, 3), (19, 3), (20, 3), (21, 3), (22, 3), (23, 3), (24, 3);

INSERT INTO matriculas (alumno_id, asignatura_id, promocion_id, nota) VALUES
(6, 1, 2, 8.50), (6, 2, 2, 7.75), (6, 3, 2, 9.00), (6, 4, 2, 8.00),
(7, 1, 2, 6.50), (7, 2, 2, 8.00), (7, 3, 2, 7.25), (7, 4, 2, 7.00),
(8, 5, 2, 9.25), (8, 6, 2, 8.50), (8, 7, 2, 7.75), (8, 8, 2, 8.75),
(9, 5, 2, 7.00), (9, 6, 2, 6.75), (9, 7, 2, 8.25), (9, 8, 2, 7.50),
(10, 9, 2, 9.50), (10, 10, 2, 8.25), (10, 11, 2, 8.75), (10, 12, 2, 9.00),
(11, 9, 2, NULL), (11, 10, 2, NULL), (11, 11, 2, NULL), (11, 12, 2, NULL),
(12, 13, 2, 8.75), (12, 14, 2, 9.00), (12, 15, 2, 8.50), (12, 16, 2, 7.75),
(13, 13, 2, 6.75), (13, 14, 2, 7.50), (13, 15, 2, 7.25), (13, 16, 2, 6.50),
(14, 17, 2, 7.50), (14, 18, 2, 8.25), (14, 19, 2, 6.75), (14, 20, 2, 7.00),
(15, 17, 2, 9.00), (15, 18, 2, 8.75), (15, 19, 2, 9.25), (15, 20, 2, 8.50),
(16, 21, 2, 7.25), (16, 22, 2, 8.00), (16, 23, 2, 7.75), (16, 24, 2, 6.50),
(17, 21, 2, NULL), (17, 22, 2, NULL), (17, 23, 2, NULL), (17, 24, 2, NULL);