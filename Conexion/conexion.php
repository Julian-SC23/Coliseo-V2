<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

// Credenciales: viven en config.local.php (no se sube a GitHub)
$config = __DIR__ . '/config.local.php';
if (!file_exists($config)) {
    die('Falta Conexion/config.local.php. Copia config.ejemplo.php, renómbralo a config.local.php y llena tus datos.');
}
require_once $config;

// Conexión
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset('utf8mb4');



// Escapar texto antes de imprimirlo en HTML
function e($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function consulta($sql, $tipos = '', $params = []) {
    global $db;
    $st = $db->prepare($sql);
    if ($tipos !== '') {
        $st->bind_param($tipos, ...$params);
    }
    $st->execute();
    return $st->get_result()->fetch_all(MYSQLI_ASSOC);
}

function ejecutar($sql, $tipos = '', $params = []) {
    global $db;
    $st = $db->prepare($sql);
    if ($tipos !== '') {
        $st->bind_param($tipos, ...$params);
    }
    $st->execute();
    return $st->affected_rows;
}

// Obligar a iniciar sesión (para páginas de compradores)
function requiere_login() {
    if (empty($_SESSION['usuario_id'])) {
        header('Location: Iniciar.php');
        exit;
    }
}

// Obligar a ser administrador (para la carpeta Admin)
function requiere_admin() {
    if (($_SESSION['rol'] ?? '') !== 'administrador') {
        header('Location: ../Web/Iniciar.php');
        exit;
    }
}

// Convierte 2026-10-08 en "8 Oct 2026"
function fecha_es($fecha) {
    if (!$fecha) return 'Fecha por confirmar';
    $meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $t = strtotime($fecha);
    return date('j', $t) . ' ' . $meses[(int)date('n', $t)] . ' ' . date('Y', $t);
}

// Imprime la tarjeta de un evento (la misma que tenías en HTML)
function tarjeta_evento($ev, $conDetalle = false) {
    $agotado = ((int)$ev['disponibles'] <= 0);
    ?>
    <article class="evento">
        <div class="evento-cartel">
            <img src="../Recursos/<?= e($ev['imagen']) ?>" alt="Cartel de <?= e($ev['nombre']) ?>" class="evento-imagen">
        </div>
        <div class="evento-info">
            <h3 class="evento-titulo"><?= e($ev['nombre']) ?></h3>
            <p class="evento-dato"><?= e(fecha_es($ev['fecha'])) ?><?= $ev['hora'] && $ev['hora'] !== 'Por confirmar' ? ' · ' . e($ev['hora']) : '' ?></p>
            <p class="evento-dato"><?= e($ev['lugar']) ?></p>
            <p class="evento-precio">
                <?= $agotado ? 'Agotado' : 'Desde $' . number_format($ev['desde']) . ' MXN' ?>
            </p>
            <?php if (!$agotado): ?>
                <a href="boletos.php?evento=<?= (int)$ev['id'] ?>" class="boton boton-completo">Comprar boletos</a>
            <?php endif; ?>
            <?php if ($conDetalle): ?>
                <a href="evento.php?id=<?= (int)$ev['id'] ?>" class="boton boton-borde boton-completo">Ver detalle</a>
            <?php endif; ?>
        </div>
    </article>
    <?php
}

