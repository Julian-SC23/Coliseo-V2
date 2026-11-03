<?php
require_once '../Conexion/conexion.php';
requiere_admin();                       // solo entra el administrador
$pagina = $pagina ?? '';
function activo($nombre) {
    global $pagina;
    return $pagina === $nombre ? ' class="activo"' : '';
}
?>
<header class="encabezado">
    <div class="contenedor barra">
        <a href="index.php" class="logo">COLISEO<span>ADMIN</span></a>

        <input type="checkbox" id="menu-casilla" class="menu-casilla">
        <label for="menu-casilla" class="menu-boton" aria-label="Abrir menú">☰</label>

        <nav class="menu">
            <a href="index.php"<?= activo('panel') ?>>Panel</a>
            <a href="productos.php"<?= activo('productos') ?>>Productos</a>
            <a href="eventos.php"<?= activo('eventos') ?>>Eventos</a>
            <a href="ventas.php"<?= activo('ventas') ?>>Ventas</a>
            <a href="usuarios.php"<?= activo('usuarios') ?>>Usuarios</a>
            <a href="../Web/index.php">Ver sitio</a>
            <a href="../Web/logout.php" class="boton-menu">Cerrar sesión</a>
        </nav>
    </div>
</header>