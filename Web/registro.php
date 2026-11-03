<?php
require_once '../Conexion/conexion.php';
$pagina = '';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo     = $_POST['tipo'] ?? 'asistente';
    $nombre   = trim($_POST['nombre'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $clave    = $_POST['clave'] ?? '';
    $clave2   = $_POST['clave2'] ?? '';

    if (!in_array($tipo, ['asistente', 'administrador'])) {
        $tipo = 'asistente';
    }

    if ($nombre === '' || $correo === '') {
        $error = 'Llena todos los campos obligatorios.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo no es válido.';
    } elseif (strlen($clave) < 8) {
        $error = 'La contraseña debe tener mínimo 8 caracteres.';
    } elseif ($clave !== $clave2) {
        $error = 'Las contraseñas no coinciden.';
    } elseif ($tipo === 'administrador' && ($_POST['codigo'] ?? '') !== CODIGO_ADMIN) {
        $error = 'Código de autorización incorrecto.';
    } elseif (consulta('SELECT id FROM usuarios WHERE correo = ?', 's', [$correo])) {
        $error = 'Ese correo ya está registrado.';
    } else {
        // Datos extra según el tipo de cuenta
        if ($tipo === 'asistente') {
            $extra = [
                'institucion' => $_POST['institucion'] ?? '',
                'asistencia'  => $_POST['asistencia'] ?? '',
                'ciudad'      => $_POST['ciudad'] ?? '',
            ];
        } else {
            $extra = [
                'empleado' => $_POST['empleado'] ?? '',
                'area'     => $_POST['area'] ?? '',
            ];
        }

        $hash = password_hash($clave, PASSWORD_DEFAULT);   // nunca guardes la contraseña tal cual

        ejecutar(
            'INSERT INTO usuarios (nombre, correo, telefono, clave, rol, datos_extra) VALUES (?, ?, ?, ?, ?, ?)',
            'ssssss',
            [$nombre, $correo, $telefono, $hash, $tipo, json_encode($extra, JSON_UNESCAPED_UNICODE)]
        );

        // Variables de sesión: ya queda con la sesión iniciada
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $db->insert_id;
        $_SESSION['nombre']     = $nombre;
        $_SESSION['rol']        = $tipo;

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta | Coliseo Centenario</title>
    <link rel="stylesheet" href="../Estilos/estilos.css">
</head>
<body>
    <?php require_once 'encabezado.php'; ?>

    <main class="contenedor pagina-centrada">

        <form class="formulario formulario-ancho" action="registro.php" method="post">
            <h1 class="formulario-titulo">Crear cuenta</h1>
            <p class="formulario-subtitulo">Elige el tipo de cuenta que necesitas y llena tus datos.</p>

            <?php if ($error !== ''): ?>
                <p class="aviso"><?= e($error) ?></p>
            <?php endif; ?>

            <!-- Tipo de cuenta (los radios van primero para poder mostrar/ocultar con CSS) -->
            <input class="tipo-radio" type="radio" id="tipo-asistente" name="tipo" value="asistente" checked>
            <input class="tipo-radio" type="radio" id="tipo-administrador" name="tipo" value="administrador">

            <div class="tipos-cuenta">
                <label class="tipo-tarjeta" for="tipo-asistente">
                    <strong>Asistente</strong>
                    <span>Compra boletos y guárdalos</span>
                </label>
                <label class="tipo-tarjeta" for="tipo-administrador">
                    <strong>Administrador</strong>
                    <span>Personal del Coliseo</span>
                </label>
            </div>

            <!-- Datos comunes -->
            <div class="campo">
                <label class="campo-etiqueta" for="nombre">Nombre completo</label>
                <input class="campo-entrada" type="text" id="nombre" name="nombre"
                placeholder="Nombre como en INE" autocomplete="name" required>
            </div>

            <div class="campo">
                <label class="campo-etiqueta" for="correo">Correo electrónico</label>
                <input class="campo-entrada" type="email" id="correo" name="correo"
                placeholder="tucorreo@ejemplo.com" autocomplete="email" required>
            </div>

            <div class="campo">
                <label class="campo-etiqueta" for="telefono">
                Teléfono <span class="campo-opcional">(opcional)</span>
                </label>
                <input class="campo-entrada" type="tel" id="telefono" name="telefono"
                placeholder="87 00 00 00 00" autocomplete="tel">
            </div>

            <!-- Datos extra según el tipo de cuenta -->
            <div class="extra extra-asistente">
                <h2 class="extra-titulo">Datos de asistente</h2>
                <div class="campo">
                    <label class="campo-etiqueta" for="institucion">Institución o empresa <span class="campo-opcional">(opcional)</span></label>
                    <input class="campo-entrada" type="text" id="institucion" name="institucion" placeholder="Instituto Tecnológico de la Laguna">
                </div>
                <div class="campo">
                    <label class="campo-etiqueta" for="asistencia">Tipo de asistencia</label>
                    <select class="campo-entrada" id="asistencia" name="asistencia">
                        <option value="">Selecciona una opción</option>
                        <option value="Presencial">Presencial</option>
                        <option value="Virtual">Virtual (transmisión en línea)</option>
                    </select>
                </div>
                <div class="campo">
                    <label class="campo-etiqueta" for="ciudad">Ciudad</label>
                    <input class="campo-entrada" type="text" id="ciudad" name="ciudad" placeholder="Torreón, Coahuila">
                </div>
            </div>

            <div class="extra extra-administrador">
                <h2 class="extra-titulo">Datos de administrador</h2>
                <div class="campo">
                    <label class="campo-etiqueta" for="empleado">Número de empleado</label>
                    <input class="campo-entrada" type="text" id="empleado" name="empleado" placeholder="Ej. 00123">
                </div>
                <div class="campo">
                    <label class="campo-etiqueta" for="area-admin">Área</label>
                    <select class="campo-entrada" id="area-admin" name="area">
                        <option value="">Selecciona una opción</option>
                        <option value="Taquillas">Taquillas</option>
                        <option value="Operaciones">Operaciones</option>
                        <option value="Sistemas">Sistemas</option>
                        <option value="Dirección">Dirección</option>
                    </select>
                </div>
                <div class="campo">
                    <label class="campo-etiqueta" for="codigo">Código de autorización</label>
                    <input class="campo-entrada" type="password" id="codigo" name="codigo" placeholder="Código que te dio el Coliseo" autocomplete="off">
                </div>
            </div>

            <!-- Contraseña -->
            <div class="campo">
                <label class="campo-etiqueta" for="clave">Contraseña</label>
                <input class="campo-entrada" type="password" id="clave" name="clave"
                placeholder="Mínimo 8 caracteres" minlength="8" autocomplete="new-password" required>
            </div>

            <div class="campo">
                <label class="campo-etiqueta" for="clave2">Confirmar contraseña</label>
                <input class="campo-entrada" type="password" id="clave2" name="clave2"
                placeholder="Repite tu contraseña" minlength="8" autocomplete="new-password" required>
            </div>

            <label class="formulario-terminos">
                <input class="formulario-casilla" type="checkbox" name="terminos" required>
                <span>Acepto los <a href="#" class="formulario-enlace">términos y condiciones</a> y el aviso de privacidad.</span>
            </label>

            <button type="submit" class="boton boton-completo">Crear cuenta</button>

            <p class="formulario-pie">¿Ya tienes cuenta?
                <a href="Iniciar.php" class="formulario-enlace">Iniciar sesión</a>
            </p>
        </form>

    </main>

    <?php require_once 'pie.php'; ?>
</body>
</html>