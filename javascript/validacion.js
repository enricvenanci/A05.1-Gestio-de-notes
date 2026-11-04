// validacion.js - login y registro (nivel básico, solo onclick y onblur)

// ---------- Mostrar y quitar errores ----------
function mostrarError(id, mensaje) {
    document.getElementById(id).style.borderColor = 'red';

    var span = document.getElementById('error-' + id);
    span.textContent = mensaje;
    span.style.display = 'block';
    span.style.color = 'red';
    span.style.fontSize = '0.85rem';
    span.style.marginTop = '4px';
}

function quitarError(id) {
    document.getElementById(id).style.borderColor = '';
    document.getElementById('error-' + id).textContent = '';
}

// ---------- Comprobaciones de cada campo (devuelven true o false) ----------
function comprobarUsuario() {
    if (document.getElementById('username').value.trim() === '') {
        mostrarError('username', 'Introduce tu nombre.');
        return false;
    }
    quitarError('username');
    return true;
}

function comprobarPassword() {
    if (document.getElementById('password').value === '') {
        mostrarError('password', 'La contraseña es obligatoria.');
        return false;
    }
    quitarError('password');
    return true;
}

function comprobarNombre() {
    if (document.getElementById('first-name').value.trim() === '') {
        mostrarError('first-name', 'El nombre es obligatorio.');
        return false;
    }
    quitarError('first-name');
    return true;
}

function comprobarApellidos() {
    if (document.getElementById('last-name').value.trim() === '') {
        mostrarError('last-name', 'Los apellidos son obligatorios.');
        return false;
    }
    quitarError('last-name');
    return true;
}

function comprobarEmail() {
    var email = document.getElementById('school-email').value.trim();

    if (email === '') {
        mostrarError('school-email', 'El correo es obligatorio.');
        return false;
    }
    if (email.indexOf('@') === -1 || email.indexOf('.') === -1) {
        mostrarError('school-email', 'El correo electrónico no es válido.');
        return false;
    }
    quitarError('school-email');
    return true;
}

function comprobarConfirmar() {
    var password = document.getElementById('password').value;
    var confirmar = document.getElementById('confirm-password').value;

    if (confirmar === '') {
        mostrarError('confirm-password', 'Confirma tu contraseña.');
        return false;
    }
    if (password !== confirmar) {
        mostrarError('confirm-password', 'Las contraseñas no coinciden.');
        return false;
    }
    quitarError('confirm-password');
    return true;
}

function comprobarRol() {
    if (document.getElementById('role').value === '') {
        mostrarError('role', 'Selecciona un rol.');
        return false;
    }
    quitarError('role');
    return true;
}

// ---------- LOGIN (onclick del botón) ----------
function validarLogin() {
    var ok = true;

    if (!comprobarUsuario()) { ok = false; }
    if (!comprobarPassword()) { ok = false; }

    return ok;
}

// ---------- REGISTRO (onclick del botón) ----------
function validarRegistro() {
    var ok = true;

    if (!comprobarNombre()) { ok = false; }
    if (!comprobarApellidos()) { ok = false; }
    if (!comprobarEmail()) { ok = false; }
    if (!comprobarPassword()) { ok = false; }
    if (!comprobarConfirmar()) { ok = false; }
    if (!comprobarRol()) { ok = false; }

    return ok;
}