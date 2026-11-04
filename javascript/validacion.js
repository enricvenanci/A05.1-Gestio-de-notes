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

// Contraseña del LOGIN: solo comprueba que no esté vacía
function comprobarPassword() {
    if (document.getElementById('password').value === '') {
        mostrarError('password', 'La contraseña es obligatoria.');
        return false;
    }
    quitarError('password');
    return true;
}

// Contraseña del REGISTRO: mínimo 2 letras, 1 mayúscula y 1 número
function comprobarPasswordRegistro() {
    var password = document.getElementById('password').value;
    var letras = 0;
    var mayusculas = 0;
    var numeros = 0;

    if (password === '') {
        mostrarError('password', 'La contraseña es obligatoria.');
        return false;
    }

    for (var i = 0; i < password.length; i++) {
        var c = password.charAt(i);

        if (c >= '0' && c <= '9') {
            numeros++;
        } else if (c.toLowerCase() !== c.toUpperCase()) {
            letras++;                      // es una letra
            if (c === c.toUpperCase()) {
                mayusculas++;              // y además es mayúscula
            }
        }
    }

    if (letras < 2) {
        mostrarError('password', 'Debe tener al menos 2 letras.');
        return false;
    }
    if (mayusculas < 1) {
        mostrarError('password', 'Debe tener al menos 1 mayúscula.');
        return false;
    }
    if (numeros < 1) {
        mostrarError('password', 'Debe tener al menos 1 número.');
        return false;
    }

    quitarError('password');

    // Si ya se había escrito la confirmación, se vuelve a comparar
    if (document.getElementById('confirm-password').value !== '') {
        comprobarConfirmar();
    }
    return true;
}

// Devuelve true si el texto contiene algún número
function tieneNumeros(texto) {
    for (var i = 0; i < texto.length; i++) {
        var c = texto.charAt(i);
        if (c >= '0' && c <= '9') {
            return true;
        }
    }
    return false;
}

function comprobarNombre() {
    var nombre = document.getElementById('first-name').value.trim();

    if (nombre === '') {
        mostrarError('first-name', 'El nombre es obligatorio.');
        return false;
    }
    if (nombre.length < 2) {
        mostrarError('first-name', 'El nombre debe tener al menos 2 letras.');
        return false;
    }
    if (tieneNumeros(nombre)) {
        mostrarError('first-name', 'El nombre no puede contener números.');
        return false;
    }
    quitarError('first-name');
    return true;
}

function comprobarApellidos() {
    var apellidos = document.getElementById('last-name').value.trim();

    if (apellidos === '') {
        mostrarError('last-name', 'Los apellidos son obligatorios.');
        return false;
    }
    if (apellidos.length < 2) {
        mostrarError('last-name', 'Los apellidos deben tener al menos 2 letras.');
        return false;
    }
    if (tieneNumeros(apellidos)) {
        mostrarError('last-name', 'Los apellidos no pueden contener números.');
        return false;
    }
    quitarError('last-name');
    return true;
}

function comprobarEmail() {
    var email = document.getElementById('school-email').value.trim();
    // texto + @ + texto + . + al menos 2 letras (sin espacios)
    var formatoEmail = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    if (email === '') {
        mostrarError('school-email', 'El correo es obligatorio.');
        return false;
    }
    if (!formatoEmail.test(email)) {
        mostrarError('school-email', 'El correo electrónico no es válido (ejemplo: nombre@correo.com).');
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
    if (!comprobarPasswordRegistro()) { ok = false; }
    if (!comprobarConfirmar()) { ok = false; }
    if (!comprobarRol()) { ok = false; }

    return ok;
}