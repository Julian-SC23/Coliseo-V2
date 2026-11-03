<?php
require_once '../Conexion/conexion.php';
requiere_admin();
$pagina = 'usuarios';

$usuarios = consulta(
    'SELECT u.id, u.nombre, u.correo, u.telefono, u.rol, u.creado,
            (SELECT COUNT(*) FROM ventas v WHERE v.usuario_id = u.id) AS compras
     FROM usuarios u
     ORDER BY u.id'
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | Admin Coliseo</title>
    <link rel="stylesheet" href="../Estilos/estilos.css?v=2">
</head>
<body>
    <?php require_once 'encabezado_admin.php'; ?>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="seccion-titulo">Usuarios</h1>
            <p class="seccion-subtitulo">Todas las cuentas registradas en el sistema.</p>

            <div class="tabla-scroll">
                <table class="tabla">
                    <tr>
                        <th>ID</th><th>Nombre</th><th>Correo</th><th>Teléfono</th>
                        <th>Tipo de cuenta</th><th>Compras</th><th>Registro</th>
                    </tr>
                    <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= (int)$u['id'] ?></td>
                        <td><?= e($u['nombre']) ?></td>
                        <td><?= e($u['correo']) ?></td>
                        <td><?= e($u['telefono'] ?: '—') ?></td>
                        <td><?= e(ucfirst($u['rol'])) ?></td>
                        <td><?= (int)$u['compras'] ?></td>
                        <td><?= e(date('d/m/Y', strtotime($u['creado']))) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </main>
</body>
</html>