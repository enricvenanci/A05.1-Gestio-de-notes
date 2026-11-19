# Gestión de notas

Aplicación PHP y MySQL para consultar alumnos, matrículas y calificaciones.

## Base de datos

Importa `gestio_notes.sql` para recrear la base de datos `gestio_notes` y cargar los datos
de demostración. El script elimina la base de datos anterior, así que guarda
una copia antes de importarlo si contiene información que quieras conservar.
La promoción `2026-2027` queda marcada como actual. Los alumnos de ejemplo no
tienen contraseña asignada; las cuentas de alumno nuevas se crean desde el
formulario de registro.

La conexión de la aplicación está configurada en `conexion/conexion.php`.

## Funcionalidades

- Registro de alumnos e inicio de sesión.
- Listado de alumnos con filtros por nombre, ciclo, curso y promoción.
- Consulta de las matrículas y notas de cada alumno.
- Acceso de profesores a sus asignaturas para introducir o modificar notas.
- Gestión de usuarios y borrado de alumnos disponible para administración.
