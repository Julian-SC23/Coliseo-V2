<?php
require_once '../Conexion/conexion.php';
$pagina = 'boletos';
requiere_login();

$boletos = consulta(
    'SELECT v.id AS venta_id, v.folio, v.creado, d.cantidad, d.precio_unitario,
            i.zona, e.nombre, e.fecha, e.lugar, e.imagen
     FROM ventas v
     JOIN detalle_venta d ON d.venta_id = v.id
     JOIN inventario i ON i.id = d.inventario_id
     JOIN eventos e ON e.id = i.evento_id
     WHERE v.usuario_id = ?
     ORDER BY v.id DESC', 'i', [$_SESSION['usuario_id']]
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis boletos | Coliseo Centenario</title>
    <link rel="stylesheet" href="../Estilos/estilos.css">
</head>
<body>
    <?php require_once 'encabezado.php'; ?>

    <main class="seccion">
        <div class="contenedor">

            <h1 class="seccion-titulo">Mis boletos</h1>
            <p class="seccion-subtitulo">Aquí aparecen todos los boletos que has comprado.</p>

            <?php if (!$boletos): ?>
                <p class="aviso">Todavía no has comprado boletos.</p>
                <a href="eventos.php" class="boton">Ver eventos</a>
            <?php endif; ?>

            <?php
            $ventaActual = 0;
            $numero = 0;
            foreach ($boletos as $b):
                if ($b['venta_id'] != $ventaActual) {   // empieza otra compra: reinicia el número
                    $ventaActual = $b['venta_id'];
                    $numero = 0;
                }
                // Un renglón por cada boleto comprado
                for ($k = 0; $k < $b['cantidad']; $k++):
                    $numero++;
            ?>
            <article class="boleto">
                <img src="../Recursos/<?= e($b['imagen']) ?>" alt="Cartel de <?= e($b['nombre']) ?>" class="boleto-imagen">

                <div class="boleto-info">
                    <h2 class="boleto-titulo"><?= e($b['nombre']) ?></h2>
                    <p><?= e(fecha_es($b['fecha'])) ?> · <?= e($b['lugar']) ?></p>
                    <p>Zona <?= e($b['zona']) ?> · Acceso general</p>
                    <p>$<?= number_format($b['precio_unitario']) ?> MXN</p>
                </div>

                <div class="boleto-lateral">
                    <p class="boleto-id">ID: <?= e($b['folio']) ?>-<?= str_pad($numero, 2, '0', STR_PAD_LEFT) ?></p>
                    <a href="recibo.php?folio=<?= e($b['folio']) ?>" class="boton" target="_blank">Recibo PDF</a>
                </div>
            </article>
            <?php
                endfor;
            endforeach;
            ?>

        </div>
    </main>

    <?php require_once 'pie.php'; ?>
</body>
</html>