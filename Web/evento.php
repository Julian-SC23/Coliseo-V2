<?php
require_once '../Conexion/conexion.php';
$pagina = '';

$id = (int)($_GET['id'] ?? 0);
$res = consulta('SELECT * FROM eventos WHERE id = ? AND activo = 1', 'i', [$id]);

if (!$res) {
    header('Location: eventos.php');   // si no existe, regresa a la lista
    exit;
}
$ev = $res[0];

$zonas = consulta('SELECT * FROM inventario WHERE evento_id = ? ORDER BY precio DESC', 'i', [$id]);

// Color del puntito de cada zona (los mismos del mapa)
$colores = [
    'VIP'          => '#c2185b',
    'Preferente'   => '#f5b21b',
    'Palcos'       => '#e6d4a0',
    'General baja' => '#9aa0a8',
    'General alta' => '#2fa595',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($ev['nombre']) ?> | Coliseo Centenario</title>
    <link rel="stylesheet" href="../Estilos/estilos.css">
</head>
<body>
    <?php require_once 'encabezado.php'; ?>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="seccion-titulo"><?= e($ev['nombre']) ?></h1>
            <p class="seccion-subtitulo">Detalle del evento</p>

            <div class="compra">
                <section class="compra-caja">
                    <img src="../Recursos/<?= e($ev['imagen']) ?>" alt="Cartel de <?= e($ev['nombre']) ?>" class="detalle-imagen">
                </section>

                <section class="compra-caja">
                    <h2 class="compra-titulo">Información</h2>
                    <p><strong>Fecha:</strong> <?= e(fecha_es($ev['fecha'])) ?></p>
                    <p><strong>Hora:</strong> <?= e($ev['hora'] ?: 'Por confirmar') ?></p>
                    <p><strong>Sede:</strong> <?= e($ev['lugar']) ?></p>
                    <p><strong>Tipo:</strong> <?= e($ev['tipo']) ?></p>
                    <?php if ($ev['descripcion']): ?>
                        <p class="aviso"><?= e($ev['descripcion']) ?></p>
                    <?php endif; ?>

                    <table class="tabla-zonas">
                        <tr><th>Zona</th><th>Precio</th><th>Disponibles</th></tr>
                        <?php foreach ($zonas as $z): ?>
                        <tr>
                            <td><span class="punto" style="background:<?= $colores[$z['zona']] ?? '#888' ?>"></span><?= e($z['zona']) ?></td>
                            <td>$<?= number_format($z['precio']) ?> MXN</td>
                            <td><?= $z['stock'] > 0 ? (int)$z['stock'] : 'Agotado' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>

                    <a href="boletos.php?evento=<?= (int)$ev['id'] ?>" class="boton boton-completo">Comprar boletos</a>
                    <a href="eventos.php" class="boton boton-borde boton-completo">Volver a eventos</a>
                </section>
            </div>
        </div>
    </main>

    <?php require_once 'pie.php'; ?>
</body>
</html>