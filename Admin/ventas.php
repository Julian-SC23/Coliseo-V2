<?php
require_once '../Conexion/conexion.php';
requiere_admin();
$pagina = 'ventas';

$ventas = consulta(
    "SELECT v.id, v.folio, v.total, v.creado, v.estado, v.paypal_order_id,
            u.nombre, u.correo,
            GROUP_CONCAT(CONCAT(d.cantidad, ' x ', e.nombre, ' (', i.zona, ')') SEPARATOR ', ') AS detalle
     FROM ventas v
     JOIN usuarios u ON u.id = v.usuario_id
     JOIN detalle_venta d ON d.venta_id = v.id
     JOIN inventario i ON i.id = d.inventario_id
     JOIN eventos e ON e.id = i.evento_id
     GROUP BY v.id, u.nombre, u.correo
     ORDER BY v.id DESC"
);

$totalGeneral = 0;
foreach ($ventas as $v) {
    $totalGeneral += $v['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas | Admin Coliseo</title>
    <link rel="stylesheet" href="../Estilos/estilos.css?v=2">
</head>
<body>
    <?php require_once 'encabezado_admin.php'; ?>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="seccion-titulo">Ventas</h1>
            <p class="seccion-subtitulo">Todas las compras hechas con PayPal. Total vendido: <strong>$<?= number_format($totalGeneral, 2) ?> MXN</strong></p>

            <div class="tabla-scroll">
                <table class="tabla">
                    <tr>
                        <th>Folio</th><th>Fecha</th><th>Cliente</th><th>Correo</th>
                        <th>Boletos</th><th>Total</th><th>Estado</th><th>ID PayPal</th><th>Recibo</th>
                    </tr>
                    <?php foreach ($ventas as $v): ?>
                    <tr>
                        <td><?= e($v['folio']) ?></td>
                        <td><?= e(date('d/m/Y H:i', strtotime($v['creado']))) ?></td>
                        <td><?= e($v['nombre']) ?></td>
                        <td><?= e($v['correo']) ?></td>
                        <td><?= e($v['detalle']) ?></td>
                        <td>$<?= number_format($v['total'], 2) ?></td>
                        <td><?= e($v['estado']) ?></td>
                        <td><?= e($v['paypal_order_id']) ?></td>
                        <td><a class="formulario-enlace" href="../Web/recibo.php?folio=<?= e($v['folio']) ?>" target="_blank">PDF</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!$ventas): ?>
                    <tr><td colspan="9">Todavía no hay ventas.</td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </main>
</body>
</html>