<?php
require_once __DIR__ . '/../Conexion/conexion.php';
$pagina = $pagina ?? '';
function activo($nombre) {
    global $pagina;
    return $pagina === $nombre ? ' class="activo"' : '';
}
?>
<header class="encabezado">
    <div class="contenedor barra">
        <a href="index.php" class="logo">COLISEO<span>CENTENARIO</span></a>

        <input type="checkbox" id="menu-casilla" class="menu-casilla">
        <label for="menu-casilla" class="menu-boton" aria-label="Abrir menú">☰</label>

        <nav class="menu">
            <a href="index.php"<?= activo('inicio') ?>>Inicio</a>
            <a href="cartelera.php"<?= activo('cartelera') ?>>Cartelera</a>
            <a href="eventos.php"<?= activo('eventos') ?>>Eventos</a>
            <a href="MisBoletos.php"<?= activo('boletos') ?>>Mis boletos</a>

            <?php if (!empty($_SESSION['usuario_id'])): ?>
                <?php if ($_SESSION['rol'] === 'administrador'): ?>
                    <a href="../Admin/index.php">Panel admin</a>
                <?php endif; ?>
                <a href="#">Hola, <?= e(explode(' ', $_SESSION['nombre'])[0]) ?></a>
                <a href="logout.php" class="boton-menu">Cerrar sesión</a>
            <?php else: ?>
                <a href="Iniciar.php"<?= activo('login') ?>>Iniciar sesión</a>
                <a href="registro.php" class="boton-menu">Crear cuenta</a>
            <?php endif; ?>
        </nav>
    </div>
</header>