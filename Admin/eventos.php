<?php
require_once '../Conexion/conexion.php';
requiere_admin();
$pagina = 'eventos';

// Mostrar u ocultar un evento en el sitio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        ejecutar('UPDATE eventos SET activo = 1 - activo WHERE id = ?', 'i', [$id]);
        $_SESSION['msg_admin'] = 'Evento actualizado.';
    }
    header('Location: eventos.php');
    exit;
}

$mensaje = $_SESSION['msg_admin'] ?? '';
unset($_SESSION['msg_admin']);

$eventos = consulta(
    'SELECT e.*, COALESCE(SUM(i.stock), 0) AS disponibles, COALESCE(SUM(i.vendidos), 0) AS vendidos
     FROM eventos e
     LEFT JOIN inventario i ON i.evento_id = e.id
     GROUP BY e.id
     ORDER BY e.fecha IS NULL, e.fecha, e.id'
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos | Admin Coliseo</title>
    <link rel="stylesheet" href="../Estilos/estilos.css?v=2">
</head>
<body>
    <?php require_once 'encabezado_admin.php'; ?>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="seccion-titulo">Eventos</h1>
            <p class="seccion-subtitulo">Crea, edita u oculta los eventos que se ven en el sitio.</p>

            <?php if ($mensaje !== ''): ?>
                <p class="aviso"><?= e($mensaje) ?></p>
            <?php endif; ?>

            <p><a href="evento_form.php" class="boton">+ Nuevo evento</a></p>
            <br>

            <div class="tabla-scroll">
                <table class="tabla">
                    <tr>
                        <th>ID</th><th>Evento</th><th>Fecha</th><th>Hora</th><th>Tipo</th>
                        <th>Disponibles</th><th>Vendidos</th><th>Estado</th><th></th>
                    </tr>
                    <?php foreach ($eventos as $ev): ?>
                    <tr>
                        <td><?= (int)$ev['id'] ?></td>
                        <td><?= e($ev['nombre']) ?></td>
                        <td><?= e(fecha_es($ev['fecha'])) ?></td>
                        <td><?= e($ev['hora']) ?></td>
                        <td><?= e($ev['tipo']) ?></td>
                        <td><?= (int)$ev['disponibles'] ?></td>
                        <td><?= (int)$ev['vendidos'] ?></td>
                        <td><?= $ev['activo'] ? 'Visible' : 'Oculto' ?></td>
                        <td>
                            <a href="evento_form.php?id=<?= (int)$ev['id'] ?>" class="boton">Editar</a>
                            <form method="post" action="eventos.php" style="display:inline">
                                <input type="hidden" name="id" value="<?= (int)$ev['id'] ?>">
                                <button type="submit" class="boton boton-borde"><?= $ev['activo'] ? 'Ocultar' : 'Mostrar' ?></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </main>
</body>
</html>