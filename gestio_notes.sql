DROP DATABASE IF EXISTS gestio_notes;
CREATE DATABASE gestio_notes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestio_notes;

CREATE TABLE roles (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE usuarios (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(50)  NOT NULL,
    apellidos     VARCHAR(100) NOT NULL,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol_id        INT NOT NULL,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE ciclos (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE cursos (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    ciclo_id INT NOT NULL,
    nombre   VARCHAR(50) NOT NULL,
    UNIQUE KEY uq_ciclo_curso (ciclo_id, nombre),
    FOREIGN KEY (ciclo_id) REFERENCES ciclos(id)
) ENGINE=InnoDB;

CREATE TABLE alumnos (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(50)  NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email     VARCHAR(100) NOT NULL UNIQUE,
    curso_id  INT NOT NULL,
    INDEX idx_apellidos (apellidos),
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
) ENGINE=InnoDB;

CREATE TABLE asignaturas (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    curso_id INT NOT NULL,
    nombre   VARCHAR(100) NOT NULL,
    UNIQUE KEY uq_curso_asignatura (curso_id, nombre),
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
) ENGINE=InnoDB;

CREATE TABLE notas (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    alumno_id     INT NOT NULL,
    asignatura_id INT NOT NULL,
    nota          DECIMAL(4,2) NOT NULL CHECK (nota >= 0 AND nota <= 10),
    fecha         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_alumno_asignatura (alumno_id, asignatura_id),
    FOREIGN KEY (alumno_id)     REFERENCES alumnos(id)     ON DELETE CASCADE,
    FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO roles (nombre) VALUES ('admin');

INSERT INTO usuarios (
    nombre,
    apellidos,
    email,
    password_hash,
    rol_id
)
SELECT
    'Usuario',
    'Prueba',
    'usuario.prueba@example.com',
    '$2y$12$FYUWv/gl6sqqh1ugCZXSaueGIiu1GRX6OGP2/4IKih8Dqaf4430Qi',
    id
FROM roles
WHERE nombre = 'admin';
