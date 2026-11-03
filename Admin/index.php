<?php
require_once '../Conexion/conexion.php';
requiere_admin();
$pagina = 'panel';

$resumen = consulta(
    'SELECT
        (SELECT COUNT(*) FROM eventos)                           AS eventos,
        (SELECT COALESCE(SUM(vendidos), 0) FROM inventario)      AS vendidos,
        (SELECT COALESCE(SUM(stock), 0) FROM inventario)         AS disponibles,
        (SELECT COUNT(*) FROM ventas)                            AS ventas,
        (SELECT COALESCE(SUM(total), 0) FROM ventas)             AS ingresos,
        (SELECT COUNT(*) FROM usuarios)                          AS usuarios'
)[0];

$ultimas = consulta(
    'SELECT v.folio, v.total, v.creado, u.nombre
     FROM ventas v JOIN usuarios u ON u.id = v.usuario_id
     ORDER BY v.id DESC LIMIT 5'
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de administrador | Coliseo Centenario</title>
    <link rel="stylesheet" href="../Estilos/estilos.css?v=2">
</head>
<body>
    <?php require_once 'encabezado_admin.php'; ?>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="seccion-titulo">Panel de administrador</h1>
            <p class="seccion-subtitulo">Bienvenido, <?= e($_SESSION['nombre']) ?>. Aquí ves el resumen de todo el sistema.</p>

            <div class="tarjetas-datos">
                <div class="dato"><p class="dato-numero"><?= (int)$resumen['eventos'] ?></p><p class="dato-texto">Eventos</p></div>
                <div class="dato"><p class="dato-numero"><?= (int)$resumen['vendidos'] ?></p><p class="dato-texto">Boletos vendidos</p></div>
                <div class="dato"><p class="dato-numero"><?= (int)$resumen['disponibles'] ?></p><p class="dato-texto">Boletos disponibles</p></div>
                <div class="dato"><p class="dato-numero"><?= (int)$resumen['ventas'] ?></p><p class="dato-texto">Ventas</p></div>
                <div class="dato"><p class="dato-numero">$<?= number_format($resumen['ingresos'], 2) ?></p><p class="dato-texto">Ingresos (MXN)</p></div>
                <div class="dato"><p class="dato-numero"><?= (int)$resumen['usuarios'] ?></p><p class="dato-texto">Usuarios</p></div>
            </div>

            <h2 class="compra-titulo">Últimas ventas</h2>
            <div class="tabla-scroll">
                <table class="tabla">
                    <tr><th>Folio</th><th>Cliente</th><th>Total</th><th>Fecha</th><th>Recibo</th></tr>
                    <?php foreach ($ultimas as $v): ?>
                    <tr>
                        <td><?= e($v['folio']) ?></td>
                        <td><?= e($v['nombre']) ?></td>
                        <td>$<?= number_format($v['total'], 2) ?></td>
                        <td><?= e(date('d/m/Y H:i', strtotime($v['creado']))) ?></td>
                        <td><a class="formulario-enlace" href="../Web/recibo.php?folio=<?= e($v['folio']) ?>" target="_blank">PDF</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!$ultimas): ?>
                    <tr><td colspan="5">Todavía no hay ventas.</td></tr>
                    <?php endif; ?>
                </table>
            </div>

            <p><a href="productos.php" class="boton">Ver tabla de productos</a></p>
        </div>
    </main>
</body>
</html>