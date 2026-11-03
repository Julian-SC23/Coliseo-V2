<?php
require_once '../Conexion/conexion.php';
$pagina = 'cartelera';

// Filtros (vienen por la URL: cartelera.php?dia=2026-10-08&tipo=Concierto)
$dia  = $_GET['dia'] ?? '';
$tipo = $_GET['tipo'] ?? '';

// Opciones de los selects, sacadas de los eventos que sí existen
$dias  = consulta('SELECT DISTINCT fecha FROM eventos WHERE activo = 1 AND fecha IS NOT NULL ORDER BY fecha');
$tipos = consulta('SELECT DISTINCT tipo FROM eventos WHERE activo = 1 ORDER BY tipo');

// Armar la consulta según los filtros elegidos
$sql    = 'SELECT id, nombre, fecha, hora, tipo FROM eventos WHERE activo = 1';
$types  = '';
$params = [];

if ($dia !== '') {
    $sql    .= ' AND fecha = ?';
    $types  .= 's';
    $params[] = $dia;
}
if ($tipo !== '') {
    $sql    .= ' AND tipo = ?';
    $types  .= 's';
    $params[] = $tipo;
}
$sql .= ' ORDER BY fecha IS NULL, fecha, id';

$eventos = consulta($sql, $types, $params);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartelera | Coliseo Centenario</title>
    <link rel="stylesheet" href="../Estilos/estilos.css">
</head>
<body>
    <?php require_once 'encabezado.php'; ?>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="seccion-titulo">Cartelera</h1>
            <p class="seccion-subtitulo">Agenda de eventos por día y hora. Filtra por fecha o por tipo de evento.</p>

            <form class="filtros" action="cartelera.php" method="get">
                <div class="campo">
                    <label class="campo-etiqueta" for="dia">Día</label>
                    <select class="campo-entrada" id="dia" name="dia">
                        <option value="">Todos</option>
                        <?php foreach ($dias as $d): ?>
                            <option value="<?= e($d['fecha']) ?>" <?= $dia === $d['fecha'] ? 'selected' : '' ?>>
                                <?= e(fecha_es($d['fecha'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label class="campo-etiqueta" for="tipo">Tipo</label>
                    <select class="campo-entrada" id="tipo" name="tipo">
                        <option value="">Todos</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= e($t['tipo']) ?>" <?= $tipo === $t['tipo'] ? 'selected' : '' ?>>
                                <?= e($t['tipo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="boton">Filtrar</button>
                <a href="cartelera.php" class="boton boton-borde">Limpiar</a>
            </form>

            <div class="tabla-scroll">
                <table class="tabla-zonas">
                    <tr><th>Día</th><th>Hora</th><th>Evento</th><th>Tipo</th></tr>
                    <?php foreach ($eventos as $ev): ?>
                    <tr>
                        <td><?= e(fecha_es($ev['fecha'])) ?></td>
                        <td><?= e($ev['hora'] ?: 'Por confirmar') ?></td>
                        <td><a class="formulario-enlace" href="evento.php?id=<?= (int)$ev['id'] ?>"><?= e($ev['nombre']) ?></a></td>
                        <td><?= e($ev['tipo']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!$eventos): ?>
                    <tr><td colspan="4">No hay eventos con esos filtros.</td></tr>
                    <?php endif; ?>
                </table>
            </div>
            <p class="aviso">Los horarios se actualizan cuando el organizador los confirme.</p>
        </div>
    </main>

    <?php require_once 'pie.php'; ?>
</body>
</html>