<?php require_once '../Conexion/conexion.php'; $pagina = 'inicio'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ColiseoCentenario</title>
    <link rel="stylesheet" href="../Estilos/estilos.css">
</head>
<body>
    <?php require_once 'encabezado.php'; ?>

    <!-- =============== banner coliseo ============================ -->
    <section class="banner">
        <img src="../Recursos/coliseo.jpg" alt="Vista del coliseo" class="banner-imagen">

        <div class="contenedor banner-texto">
            <h1 class="banner-titulo">Vive los mejores eventos en vivo</h1>
            <p class="banner-descripcion">Conciertos, lucha libre, conferencias y espectáculos.
                Aparta tus lugares y paga seguro con PayPal.</p>
            <a href="eventos.php" class="boton">Ver todos los eventos</a>
            <a href="#proximos" class="boton boton-borde">Próximos eventos</a>
        </div>
    </section>

    <!-- =============== próximos ============================ -->
    <section class="seccion" id="proximos">
        <div class="contenedor">

            <h2 class="seccion-titulo">Próximos eventos</h2>
            <p class="seccion-subtitulo">Lo que viene en el Coliseo Centenario. Aparta tu lugar antes de que se agoten.</p>

            <div class="rejilla">
                <?php
                $eventos = consulta(
                    'SELECT e.*, MIN(i.precio) AS desde, COALESCE(SUM(i.stock),0) AS disponibles
                     FROM eventos e
                     LEFT JOIN inventario i ON i.evento_id = e.id
                     WHERE e.activo = 1
                     GROUP BY e.id
                     ORDER BY e.fecha IS NULL, e.fecha
                     LIMIT 4'
                );
                foreach ($eventos as $ev) {
                    tarjeta_evento($ev);
                }
                ?>
            </div>

            <div class="seccion-final">
                <a href="eventos.php" class="boton boton-borde">Ver todos los eventos</a>
            </div>

        </div>
    </section>

    <!-- =============== sede, fechas y pago ============================ -->
    <section class="seccion">
        <div class="contenedor">
            <h2 class="seccion-titulo">Información del recinto</h2>
            <p class="seccion-subtitulo">Todo lo que necesitas saber para asistir.</p>
            <div class="rejilla">
                <div class="compra-caja">
                    <h3 class="compra-titulo">Fechas</h3>
                    <p>Eventos del 8 de octubre al 28 de noviembre de 2026.</p>
                    <p><a href="cartelera.php" class="formulario-enlace">Ver cartelera por día y hora</a></p>
                </div>
                <div class="compra-caja">
                    <h3 class="compra-titulo">Sede</h3>
                    <p>Coliseo Centenario</p>
                    <p>Torreón, Coahuila, México</p>
                </div>
                <div class="compra-caja">
                    <h3 class="compra-titulo">Registro y pago</h3>
                    <p>Crea tu cuenta y paga tus boletos en línea.</p>
                    <a href="eventos.php"><img src="../Recursos/paypal.svg" alt="Pagar con PayPal" class="paypal"></a>
                    <a href="registro.php" class="boton boton-borde boton-completo">Crear cuenta</a>
                </div>
            </div>
        </div>
    </section>

    <?php require_once 'pie.php'; ?>

</body>
</html>
