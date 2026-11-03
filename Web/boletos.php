<?php
require_once '../Conexion/conexion.php';
$pagina = '';

// Lista de eventos activos para el select
$eventos = consulta('SELECT id, nombre, fecha FROM eventos WHERE activo = 1 ORDER BY fecha IS NULL, fecha');
if (!$eventos) {
    header('Location: eventos.php');
    exit;
}

// Evento elegido (por defecto el primero)
$idEvento = (int)($_GET['evento'] ?? $eventos[0]['id']);
$existe = false;
foreach ($eventos as $ev) {
    if ($ev['id'] == $idEvento) { $existe = true; }
}
if (!$existe) { $idEvento = $eventos[0]['id']; }

$zonas = consulta('SELECT * FROM inventario WHERE evento_id = ? ORDER BY precio DESC', 'i', [$idEvento]);

$colores = [
    'VIP'          => '#c2185b',
    'Preferente'   => '#f5b21b',
    'Palcos'       => '#e6d4a0',
    'General baja' => '#9aa0a8',
    'General alta' => '#2fa595',
];

$error = $_SESSION['error_compra'] ?? '';
unset($_SESSION['error_compra']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprar boletos | Coliseo Centenario</title>
    <link rel="stylesheet" href="../Estilos/estilos.css">
</head>
<body>
    <?php require_once 'encabezado.php'; ?>

    <main class="seccion">
        <div class="contenedor">

            <h1 class="seccion-titulo">Selecciona tus boletos</h1>
            <p class="seccion-subtitulo">Elige el evento, la zona y la cantidad de boletos.</p>

            <div class="compra">

                <!-- ===== Mapa del recinto ===== -->
                <section class="compra-caja">
                    <h2 class="compra-titulo">Mapa del Coliseo</h2>

                    <svg class="mapa" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 340 340" role="img" aria-label="Mapa de zonas del Coliseo Centenario">
                        <circle cx="170" cy="170" r="150" fill="none" stroke="#e6d4a0" stroke-width="26"><title>Palcos</title></circle>
                        <circle cx="170" cy="170" r="122" fill="none" stroke="#2fa595" stroke-width="22"><title>General alta</title></circle>
                        <circle cx="170" cy="170" r="96" fill="none" stroke="#9aa0a8" stroke-width="20"><title>General baja</title></circle>

                        <rect x="115" y="82" width="110" height="22" rx="3" fill="#000000"/>
                        <text x="170" y="97" text-anchor="middle" font-family="Arial" font-size="10" font-weight="bold" fill="#ffffff">ESCENARIO</text>

                        <g fill="#c2185b">
                            <rect x="117" y="112" width="25" height="50"><title>VIP</title></rect>
                            <rect x="145" y="112" width="25" height="50"><title>VIP</title></rect>
                            <rect x="173" y="112" width="25" height="50"><title>VIP</title></rect>
                            <rect x="201" y="112" width="22" height="50"><title>VIP</title></rect>
                        </g>
                        <g fill="#f5b21b">
                            <rect x="117" y="168" width="52" height="28"><title>Preferente</title></rect>
                            <rect x="173" y="168" width="50" height="28"><title>Preferente</title></rect>
                        </g>
                    </svg>

                    <table class="tabla-zonas">
                        <tr>
                            <th>Zona</th>
                            <th>Precio por boleto</th>
                            <th>Disponibles</th>
                        </tr>
                        <?php foreach ($zonas as $z): ?>
                        <tr>
                            <td><span class="punto" style="background:<?= $colores[$z['zona']] ?? '#888' ?>"></span><?= e($z['zona']) ?></td>
                            <td>$<?= number_format($z['precio']) ?> MXN</td>
                            <td><?= $z['stock'] > 0 ? (int)$z['stock'] : 'Agotado' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </section>

                <!-- ===== Formulario de compra ===== -->
                <section class="compra-caja">
                    <h2 class="compra-titulo">Tu compra</h2>

                    <?php if ($error !== ''): ?>
                        <p class="aviso"><?= e($error) ?></p>
                    <?php endif; ?>

                    <!-- 1) Elegir evento (recarga la página con ese evento) -->
                    <form action="boletos.php" method="get">
                        <div class="campo">
                            <label class="campo-etiqueta" for="evento">Evento</label>
                            <select class="campo-entrada" id="evento" name="evento" onchange="this.form.submit()">
                                <?php foreach ($eventos as $ev): ?>
                                    <option value="<?= (int)$ev['id'] ?>" <?= $ev['id'] == $idEvento ? 'selected' : '' ?>>
                                        <?= e($ev['nombre']) ?> · <?= e(fecha_es($ev['fecha'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>

                    <!-- 2) Elegir zona y cantidad -->
                    <form action="pagar.php" method="post">
                        <input type="hidden" name="evento" value="<?= (int)$idEvento ?>">

                        <div class="campo">
                            <label class="campo-etiqueta" for="zona">Zona</label>
                            <select class="campo-entrada" id="zona" name="inventario_id" required>
                                <option value="">Selecciona una zona</option>
                                <?php foreach ($zonas as $z): ?>
                                    <option value="<?= (int)$z['id'] ?>" <?= $z['stock'] <= 0 ? 'disabled' : '' ?>>
                                        <?= e($z['zona']) ?> · $<?= number_format($z['precio']) ?> MXN<?= $z['stock'] <= 0 ? ' (agotado)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo-etiqueta" for="cantidad">Cantidad de boletos</label>
                            <select class="campo-entrada" id="cantidad" name="cantidad">
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?> boleto<?= $i > 1 ? 's' : '' ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <?php if (empty($_SESSION['usuario_id'])): ?>
                            <p class="aviso">Para comprar necesitas una cuenta. Crea una o inicia sesión para continuar.</p>
                            <a href="registro.php" class="boton boton-completo">Crear cuenta</a>
                            <div class="fila-boton">
                                <a href="Iniciar.php" class="boton boton-borde boton-completo">Ya tengo cuenta</a>
                                <img src="../Recursos/paypal.svg" alt="PayPal" class="paypal-mini">
                            </div>
                        <?php else: ?>
                            <button type="submit" class="boton boton-completo">Continuar al pago</button>
                            <div class="fila-boton">
                                <img src="../Recursos/paypal.svg" alt="PayPal" class="paypal-mini">
                            </div>
                        <?php endif; ?>
                    </form>
                </section>

            </div>
        </div>
    </main>

    <?php require_once 'pie.php'; ?>
</body>
</html>