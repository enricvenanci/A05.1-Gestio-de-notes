# A05.1 Gestió de notes

Aplicación web desarrollada en PHP y MySQL para gestionar los alumnos y las notas del Colegio Internacional San Miguel Arcángel.

## Funcionalidades

- Registro e inicio de sesión de usuarios, con validación en cliente (JavaScript) y en servidor (PHP)
- Zona privada protegida por sesión, con cierre de sesión
- Listado de alumnos con filtros por nombre, curso y ciclo
- Consulta de las notas de cada alumno, con la media general
- Eliminación de alumnos
- Diseño responsive

## Tecnologías

- PHP (con PDO y consultas preparadas)
- MySQL
- HTML, CSS y JavaScript

## Instalación

1. Copiar el proyecto en la carpeta del servidor local (por ejemplo, `www` en WampServer).
2. Importar el archivo `gestio_notes.sql` desde phpMyAdmin.
3. Revisar los datos de conexión en `conexion/conexion.php`.
4. Abrir el proyecto en el navegador desde `http://localhost/` y la ruta de la carpeta.

## Estructura del proyecto

- `index.php` y `register.php`: inicio de sesión y registro
- `bienvenida.php`: listado de alumnos
- `notas.php`: notas de un alumno
- `logout.php`: cierre de sesión
- `proc/`: procesos del servidor (login, registro y alumnos)
- `filtros/`: consulta y filtrado de alumnos
- `conexion/`: conexión a la base de datos
- `sql/` y `gestio_notes.sql`: base de datos
- `css/`, `javascript/` e `img/`: recursos
- `video-presentacio/`: vídeo de presentación

## Equipo

- Jun Hao, Alexandra, Ivan, Nahuel, Enric