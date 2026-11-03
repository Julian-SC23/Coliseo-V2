<?php
require_once '../Conexion/conexion.php';
$pagina = 'login';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo   = $_POST['tipo'] ?? '';
    $correo = trim($_POST['correo'] ?? '');
    $clave  = $_POST['clave'] ?? '';

    $usuarios = consulta('SELECT * FROM usuarios WHERE correo = ?', 's', [$correo]);
    $u = $usuarios[0] ?? null;

    if (!$u || !password_verify($clave, $u['clave'])) {
        $error = 'Correo o contraseña incorrectos.';
    } elseif ($u['rol'] !== $tipo) {
        $error = 'Ese usuario no es de tipo ' . $tipo . '.';
    } else {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $u['id'];
        $_SESSION['nombre']     = $u['nombre'];
        $_SESSION['rol']        = $u['rol'];

        if ($u['rol'] === 'administrador') {
            header('Location: ../Admin/index.php');
        } else {
            header('Location: index.php');
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Coliseo Centenario</title>
    <link rel="stylesheet" href="../Estilos/estilos.css">
</head>
<body>
    <?php require_once 'encabezado.php'; ?>

    <main class="contenedor pagina-centrada">

        <form class="formulario" action="Iniciar.php" method="post">
            <h1 class="formulario-titulo">Iniciar sesión</h1>
            <p class="formulario-subtitulo">Entra a tu cuenta para ver tus boletos o acceder al panel de administrador.</p>

            <?php if ($error !== ''): ?>
                <p class="aviso"><?= e($error) ?></p>
            <?php endif; ?>

            <!-- Tipo de usuario -->
            <div class="campo">
                <label class="campo-etiqueta" for="tipo">Tipo de usuario</label>
                <select class="campo-entrada" id="tipo" name="tipo" required>
                    <option value="">Selecciona una opción</option>
                    <option value="asistente">Asistente (comprador)</option>
                    <option value="administrador">Administrador del Coliseo</option>
                </select>
            </div>

            <!-- Correo -->
            <div class="campo">
                <label class="campo-etiqueta" for="correo">Correo electrónico</label>
                <input class="campo-entrada" type="email" id="correo" name="correo"
                placeholder="tucorreo@ejemplo.com" autocomplete="email" required>
            </div>

            <!-- Contraseña -->
            <div class="campo">
                <label class="campo-etiqueta" for="clave">Contraseña</label>
                <input class="campo-entrada" type="password" id="clave" name="clave"
                placeholder="Ingresa tu contraseña" autocomplete="current-password" required>
            </div>

            <!-- Botón -->
            <button type="submit" class="boton boton-completo">Iniciar sesión</button>

            <p class="formulario-pie">
                <a href="#" class="formulario-enlace">¿Olvidaste tu contraseña?</a>
            </p>
            <p class="formulario-pie">¿No tienes cuenta?
                <a href="registro.php" class="formulario-enlace">Crea una aquí</a>
            </p>
        </form>

    </main>

    <?php require_once 'pie.php'; ?>
</body>
</html>