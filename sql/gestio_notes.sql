DROP DATABASE IF EXISTS gestio_notes;
CREATE DATABASE gestio_notes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestio_notes;

-- Roles de usuario (hoy solo admin; escalable a profesor, tutor...)
CREATE TABLE roles (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Usuarios que entran a la aplicación (login)
CREATE TABLE usuarios (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(50)  NOT NULL,
    apellidos     VARCHAR(100) NOT NULL,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,   -- se guarda con password_hash() de PHP
    rol_id        INT NOT NULL,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- Ciclos formativos (DAW, DAM, ASIR...)
CREATE TABLE ciclos (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Cursos de cada ciclo (1º curso, 2º curso)
CREATE TABLE cursos (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    ciclo_id INT NOT NULL,
    nombre   VARCHAR(50) NOT NULL,
    UNIQUE KEY uq_ciclo_curso (ciclo_id, nombre),
    FOREIGN KEY (ciclo_id) REFERENCES ciclos(id)
) ENGINE=InnoDB;

-- Alumnos (cada alumno pertenece a un curso)
CREATE TABLE alumnos (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(50)  NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email     VARCHAR(100) NOT NULL UNIQUE,
    curso_id  INT NOT NULL,
    INDEX idx_apellidos (apellidos),
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
) ENGINE=InnoDB;

-- Asignaturas (cada asignatura pertenece a un curso)
CREATE TABLE asignaturas (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    curso_id INT NOT NULL,
    nombre   VARCHAR(100) NOT NULL,
    UNIQUE KEY uq_curso_asignatura (curso_id, nombre),
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
) ENGINE=InnoDB;

-- Notas (una nota por alumno y asignatura)
CREATE TABLE notas (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    alumno_id     INT NOT NULL,
    asignatura_id INT NOT NULL,
    nota          DECIMAL(4,2) NOT NULL,
    fecha         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CHECK (nota >= 0 AND nota <= 10),
    UNIQUE KEY uq_alumno_asignatura (alumno_id, asignatura_id),
    FOREIGN KEY (alumno_id)     REFERENCES alumnos(id)     ON DELETE CASCADE,
    FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- SELECT para la tabla de los alumnos

CREATE VIEW vista_alumnos AS
SELECT
    a.id,
    a.nombre,
    a.apellidos,
    a.email,
    a.curso_id,
    c.nombre  AS curso,
    c.ciclo_id,
    ci.nombre AS ciclo
FROM alumnos a
JOIN cursos c  ON c.id  = a.curso_id
JOIN ciclos ci ON ci.id = c.ciclo_id;


-- Cargos de los gestores de notas (se muestran junto al nombre tras el login)
INSERT INTO roles (id, nombre) VALUES
(1, 'admin'),
(2, 'profesor'),
(3, 'educador'),
(4, 'secretaria');

-- Gestores de notas (profesores, educadores, secretaría...)
-- Todos comparten el mismo hash que el usuario de prueba (misma contraseña)
INSERT INTO usuarios (nombre, apellidos, email, password_hash, rol_id) VALUES
('Usuario', 'Prueba',  'usuario.prueba@example.com',
 '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 1),
('Carles',  'Vidal Ferrer', 'carles.vidal@example.com',
 '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 2),
('Montse',  'Casals Prats', 'montse.casals@example.com',
 '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 2),
('Albert',  'Soler Mir',    'albert.soler@example.com',
 '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 3),
('Marta',   'Pons Riera',   'marta.pons@example.com',
 '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi', 4);

INSERT INTO ciclos (id, nombre) VALUES
(1, 'Desarrollo de Aplicaciones Web (DAW)'),
(2, 'Desarrollo de Aplicaciones Multiplataforma (DAM)'),
(3, 'Administración de Sistemas Informáticos en Red (ASIR)');

INSERT INTO cursos (id, ciclo_id, nombre) VALUES
(1, 1, '1º curso'), (2, 1, '2º curso'),
(3, 2, '1º curso'), (4, 2, '2º curso'),
(5, 3, '1º curso'), (6, 3, '2º curso');

INSERT INTO asignaturas (id, curso_id, nombre) VALUES
-- DAW 1º
(1, 1, 'Programación'),
(2, 1, 'Bases de datos'),
(3, 1, 'Sistemas informáticos'),
(4, 1, 'Lenguajes de marcas'),
-- DAW 2º
(5, 2, 'Desarrollo web en entorno cliente'),
(6, 2, 'Desarrollo web en entorno servidor'),
(7, 2, 'Despliegue de aplicaciones web'),
(8, 2, 'Diseño de interfaces web'),
-- DAM 1º
(9,  3, 'Programación'),
(10, 3, 'Bases de datos'),
(11, 3, 'Sistemas informáticos'),
(12, 3, 'Lenguajes de marcas'),
-- DAM 2º
(13, 4, 'Acceso a datos'),
(14, 4, 'Desarrollo de interfaces'),
(15, 4, 'Programación multimedia y dispositivos móviles'),
(16, 4, 'Programación de servicios y procesos'),
-- ASIR 1º
(17, 5, 'Implantación de sistemas operativos'),
(18, 5, 'Planificación y administración de redes'),
(19, 5, 'Fundamentos de hardware'),
(20, 5, 'Gestión de bases de datos'),
-- ASIR 2º
(21, 6, 'Administración de sistemas operativos'),
(22, 6, 'Servicios de red e Internet'),
(23, 6, 'Implantación de aplicaciones web'),
(24, 6, 'Seguridad y alta disponibilidad');

INSERT INTO alumnos (id, nombre, apellidos, email, curso_id) VALUES
(1,  'Lucía',  'Martínez López',  'lucia.martinez@example.com',   1),
(2,  'Hugo',   'Sánchez Ruiz',    'hugo.sanchez@example.com',     1),
(3,  'Sofía',  'García Pérez',    'sofia.garcia@example.com',     2),
(4,  'Mateo',  'Fernández Díaz',  'mateo.fernandez@example.com',  2),
(5,  'Valeria','López Martín',    'valeria.lopez@example.com',    3),
(6,  'Daniel', 'Romero Torres',   'daniel.romero@example.com',    3),
(7,  'Emma',   'Navarro Gil',     'emma.navarro@example.com',     4),
(8,  'Pablo',  'Vega Morales',    'pablo.vega@example.com',       4),
(9,  'Marc',   'Ferrer Soler',    'marc.ferrer@example.com',      5),
(10, 'Núria',  'Puig Vidal',      'nuria.puig@example.com',       5),
(11, 'Jordi',  'Roca Camps',      'jordi.roca@example.com',       6),
(12, 'Laia',   'Serra Mas',       'laia.serra@example.com',       6);

-- Notas de ejemplo (los alumnos 6 y 12 se dejan sin notas para probar la introducción)
INSERT INTO notas (alumno_id, asignatura_id, nota) VALUES
(1, 1, 8.50), (1, 2, 7.75), (1, 3, 9.00), (1, 4, 8.00),
(2, 1, 6.50), (2, 2, 8.00), (2, 3, 7.25), (2, 4, 7.00),
(3, 5, 9.25), (3, 6, 8.50), (3, 7, 7.75), (3, 8, 8.75),
(4, 5, 7.00), (4, 6, 6.75), (4, 7, 8.25), (4, 8, 7.50),
(5, 9, 9.50), (5, 10, 8.25), (5, 11, 8.75), (5, 12, 9.00),
(7, 13, 8.75), (7, 14, 9.00), (7, 15, 8.50), (7, 16, 7.75),
(8, 13, 6.75), (8, 14, 7.50), (8, 15, 7.25), (8, 16, 6.50),
(9, 17, 7.50), (9, 18, 8.25), (9, 19, 6.75), (9, 20, 7.00),
(10, 17, 9.00), (10, 18, 8.75), (10, 19, 9.25), (10, 20, 8.50),
(11, 21, 7.25), (11, 22, 8.00), (11, 23, 7.75), (11, 24, 6.50);