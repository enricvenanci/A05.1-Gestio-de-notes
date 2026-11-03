<?php
session_start();
$registerError = $_SESSION['register_error'] ?? '';
unset($_SESSION['register_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="login-page">
        <section class="login-card register-card" aria-label="Crear una cuenta">
            <div class="brand">
                <img src="img/logo_gestion_nota.png" alt="Logo del Colegio Internacional San Miguel Arcángel">
            </div>

            <div class="login-content register-content">
                <p class="eyebrow">GESTIÓN DE NOTAS</p>
                <h1>Crea tu cuenta</h1>
                <p class="welcome-text">Completa tus datos para registrarte.</p>

                <?php if ($registerError !== ''): ?>
                    <p class="form-error" role="alert">
                        <?= htmlspecialchars($registerError, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <form class="login-form register-form" method="post" action="proc/proc_register.php">
                    <div class="form-field">
                        <label for="first-name">Nombre</label>
                        <input type="text" id="first-name" name="nombre" required>
                    </div>

                    <div class="form-field">
                        <label for="last-name">Apellidos</label>
                        <input type="text" id="last-name" name="apellidos" required>
                    </div>

                    <div class="form-field">
                        <label for="school-email">Correo de la escuela</label>
                        <input type="email" id="school-email" name="email" required>
                    </div>

                    <div class="form-field">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="form-field">
                        <label for="confirm-password">Confirma contraseña</label>
                        <input type="password" id="confirm-password" name="confirm_password" required>
                    </div>

                    <div class="form-field">
                        <label for="role">Rol</label>
                        <select id="role" name="role_id" required>
                            <option value="">Cargando roles...</option>
                        </select>
                    </div>

                    <button type="submit">Registrarse <span aria-hidden="true">→</span></button>
                </form>

                <p class="register-text">
                    ¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a>
                </p>
            </div>
        </section>
    </main>
    <script>
        fetch('proc/proc_register.php?roles=1')
            .then(response => {
                if (!response.ok) {
                    throw new Error('No se pudieron cargar los roles.');
                }

                return response.json();
            })
            .then(roles => {
                const roleSelect = document.getElementById('role');
                roleSelect.innerHTML = '<option value="">Selecciona un rol</option>';

                roles.forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.id;
                    option.textContent = role.nombre;
                    roleSelect.appendChild(option);
                });
            })
            .catch(() => {
                document.getElementById('role').innerHTML =
                    '<option value="">No se pudieron cargar los roles</option>';
            });
    </script>
</body>
</html>