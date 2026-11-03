<?php
require_once '../Conexion/conexion.php';
$pagina = '';
requiere_login();

$folio = $_GET['folio'] ?? '';
$v = consulta('SELECT * FROM ventas WHERE folio = ? AND usuario_id = ?', 'si', [$folio, $_SESSION['usuario_id']]);
if (!$v) {
    header('Location: MisBoletos.php');
    exit;
}
$venta = $v[0];

$detalle = consulta(
    'SELECT d.cantidad, d.precio_unitario, i.zona, e.nombre, e.fecha
     FROM detalle_venta d
     JOIN inventario i ON i.id = d.inventario_id
     JOIN eventos e ON e.id = i.evento_id
     WHERE d.venta_id = ?', 'i', [$venta['id']]
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago recibido | Coliseo Centenario</title>
    <link rel="stylesheet" href="../Estilos/estilos.css">
</head>
<body>
    <?php require_once 'encabezado.php'; ?>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="seccion-titulo">¡Pago recibido!</h1>
            <p class="seccion-subtitulo">Gracias por tu compra. Ya quedaron apartados tus boletos.</p>

            <div class="compra-caja">
                <p class="aviso">Folio: <?= e($venta['folio']) ?> · Total: $<?= number_format($venta['total'], 2) ?> MXN</p>
                <?php foreach ($detalle as $d): ?>
                    <p><?= (int)$d['cantidad'] ?> × <?= e($d['nombre']) ?> · <?= e(fecha_es($d['fecha'])) ?> · Zona <?= e($d['zona']) ?></p>
                <?php endforeach; ?>

                <a href="recibo.php?folio=<?= e($venta['folio']) ?>" class="boton">Descargar recibo (PDF)</a>
                <a href="MisBoletos.php" class="boton boton-borde">Ver mis boletos</a>
            </div>
        </div>
    </main>

    <?php require_once 'pie.php'; ?>
</body>
</html>