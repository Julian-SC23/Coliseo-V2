<?php require_once '../Conexion/conexion.php'; $pagina = 'eventos'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos</title>
    <link rel="stylesheet" href="../Estilos/estilos.css">
</head>
<body>
    <?php require_once 'encabezado.php'; ?>

    <section class="seccion" id="proximos">
        <div class="contenedor">

            <h2 class="seccion-titulo">Próximos eventos</h2>
            <p class="seccion-subtitulo">Proximos eventos a realizarse (precios sujetos a cambio por parte del organizador)</p>

            <div class="rejilla">
                <?php
                $eventos = consulta(
                    'SELECT e.*, MIN(i.precio) AS desde, COALESCE(SUM(i.stock),0) AS disponibles
                     FROM eventos e
                     LEFT JOIN inventario i ON i.evento_id = e.id
                     WHERE e.activo = 1
                     GROUP BY e.id
                     ORDER BY e.fecha IS NULL, e.fecha'
                );
                foreach ($eventos as $ev) {
                    tarjeta_evento($ev, true);
                }
                ?>
            </div>

        </div>
    </section>

    <?php require_once 'pie.php'; ?>

</body>
</html>