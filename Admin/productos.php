<?php
require_once '../Conexion/conexion.php';
requiere_admin();
$pagina = 'productos';

// Guardar cambios de precio / stock de un producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['id'] ?? 0);
    $precio = (float)($_POST['precio'] ?? -1);
    $stock  = (int)($_POST['stock'] ?? -1);

    if ($id > 0 && $precio >= 0 && $stock >= 0) {
        ejecutar('UPDATE inventario SET precio = ?, stock = ? WHERE id = ?', 'dii', [$precio, $stock, $id]);
        $_SESSION['msg_admin'] = 'Producto actualizado.';
    } else {
        $_SESSION['msg_admin'] = 'Datos no válidos: el precio y el stock no pueden ser negativos.';
    }
    header('Location: productos.php');
    exit;
}

$mensaje = $_SESSION['msg_admin'] ?? '';
unset($_SESSION['msg_admin']);

$productos = consulta(
    'SELECT i.id, e.nombre, e.fecha, e.activo, i.zona, i.precio, i.stock, i.vendidos,
            COALESCE((SELECT SUM(d.cantidad * d.precio_unitario)
                      FROM detalle_venta d WHERE d.inventario_id = i.id), 0) AS ingresos
     FROM inventario i
     JOIN eventos e ON e.id = i.evento_id
     ORDER BY e.fecha IS NULL, e.fecha, e.id, i.precio DESC'
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | Admin Coliseo</title>
    <link rel="stylesheet" href="../Estilos/estilos.css?v=2">
</head>
<body>
    <?php require_once 'encabezado_admin.php'; ?>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="seccion-titulo">Productos (boletos)</h1>
            <p class="seccion-subtitulo">Cada renglón es una zona de un evento, con su precio, boletos disponibles y ventas. Puedes editar el precio y el stock.</p>

            <?php if ($mensaje !== ''): ?>
                <p class="aviso"><?= e($mensaje) ?></p>
            <?php endif; ?>

            <div class="tabla-scroll">
                <table class="tabla">
                    <tr>
                        <th>ID</th><th>Evento</th><th>Fecha</th><th>Zona</th>
                        <th>Precio (MXN)</th><th>Disponibles</th><th>Vendidos</th><th>Ingresos</th><th></th>
                    </tr>
                    <?php foreach ($productos as $p): ?>
                    <tr>
                        <td>
                            <?= (int)$p['id'] ?>
                            <form id="f<?= (int)$p['id'] ?>" method="post" action="productos.php">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            </form>
                        </td>
                        <td><?= e($p['nombre']) ?><?= $p['activo'] ? '' : ' (oculto)' ?></td>
                        <td><?= e(fecha_es($p['fecha'])) ?></td>
                        <td><?= e($p['zona']) ?></td>
                        <td><input form="f<?= (int)$p['id'] ?>" class="campo-entrada campo-corto" type="number" name="precio" min="0" step="0.01" value="<?= e($p['precio']) ?>"></td>
                        <td><input form="f<?= (int)$p['id'] ?>" class="campo-entrada campo-corto" type="number" name="stock" min="0" step="1" value="<?= (int)$p['stock'] ?>"></td>
                        <td><?= (int)$p['vendidos'] ?></td>
                        <td>$<?= number_format($p['ingresos'], 2) ?></td>
                        <td><button form="f<?= (int)$p['id'] ?>" type="submit" class="boton">Guardar</button></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </main>
</body>
</html>