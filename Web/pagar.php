<?php
require_once '../Conexion/conexion.php';
$pagina = '';
requiere_login();

// Viene de boletos.php: validar y guardar la compra en la sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invId  = (int)($_POST['inventario_id'] ?? 0);
    $cant   = (int)($_POST['cantidad'] ?? 0);
    $evento = (int)($_POST['evento'] ?? 0);
    $r = consulta('SELECT stock FROM inventario WHERE id = ?', 'i', [$invId]);

    if (!$r || $cant < 1 || $cant > 6) {
        $_SESSION['error_compra'] = 'Elige una zona y una cantidad válida.';
        header('Location: boletos.php?evento=' . $evento);
        exit;
    }
    if ($r[0]['stock'] < $cant) {
        $_SESSION['error_compra'] = 'Solo quedan ' . (int)$r[0]['stock'] . ' boletos en esa zona.';
        header('Location: boletos.php?evento=' . $evento);
        exit;
    }
    $_SESSION['compra'] = ['inventario_id' => $invId, 'cantidad' => $cant];
    header('Location: pagar.php');   // así, al recargar, no se reenvía el formulario
    exit;
}

if (empty($_SESSION['compra'])) {
    header('Location: boletos.php');
    exit;
}

$c = $_SESSION['compra'];
$r = consulta(
    'SELECT i.*, e.nombre, e.fecha, e.imagen
     FROM inventario i JOIN eventos e ON e.id = i.evento_id
     WHERE i.id = ?', 'i', [$c['inventario_id']]
);
if (!$r) {
    header('Location: boletos.php');
    exit;
}
$p     = $r[0];
$total = $p['precio'] * $c['cantidad'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar | Coliseo Centenario</title>
    <link rel="stylesheet" href="../Estilos/estilos.css">
</head>
<body>
    <?php require_once 'encabezado.php'; ?>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="seccion-titulo">Confirma y paga</h1>
            <p class="seccion-subtitulo">Revisa tu compra y paga de forma segura con PayPal.</p>

            <div class="compra">
                <section class="compra-caja">
                    <h2 class="compra-titulo">Resumen</h2>
                    <img src="../Recursos/<?= e($p['imagen']) ?>" alt="Cartel" class="detalle-imagen">
                    <p><strong><?= e($p['nombre']) ?></strong></p>
                    <p><?= e(fecha_es($p['fecha'])) ?> · Coliseo Centenario, Torreón</p>
                    <p>Zona <?= e($p['zona']) ?></p>
                    <p><?= (int)$c['cantidad'] ?> boleto(s) × $<?= number_format($p['precio']) ?> MXN</p>
                    <p class="evento-precio">Total: $<?= number_format($total, 2) ?> MXN</p>
                </section>

                <section class="compra-caja">
                    <h2 class="compra-titulo">Pago con PayPal</h2>
                    <p id="mensaje-pago" class="aviso" style="display:none"></p>
                    <div id="paypal-boton"></div>
                    <p><a href="boletos.php" class="formulario-enlace">← Cambiar mi compra</a></p>
                </section>
            </div>
        </div>
    </main>

    <?php require_once 'pie.php'; ?>

    <script src="https://www.paypal.com/sdk/js?client-id=<?= e(PAYPAL_CLIENT_ID) ?>&currency=<?= e(PAYPAL_MONEDA) ?>"></script>
    <script>
    const mensaje = document.getElementById('mensaje-pago');
    function avisar(texto) {
        mensaje.textContent = texto;
        mensaje.style.display = 'block';
    }

    paypal.Buttons({
        // 1) El servidor crea la orden con el precio real
        createOrder: function () {
            return fetch('paypal_crear.php', { method: 'POST' })
                .then(r => r.json())
                .then(d => {
                    if (!d.id) throw new Error(d.error || 'No se pudo crear la orden.');
                    return d.id;
                });
        },
        // 2) El cliente aprobó: el servidor cobra, guarda la venta y baja el inventario
        onApprove: function (data) {
            return fetch('paypal_capturar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ orderID: data.orderID })
            })
            .then(r => r.json())
            .then(d => {
                if (d.ok) {
                    window.location.href = 'confirmacion.php?folio=' + encodeURIComponent(d.folio);
                } else {
                    avisar(d.error || 'No se pudo completar el pago.');
                }
            });
        },
        onError: function (err) {
            avisar('Ocurrió un error con PayPal: ' + err.message);
        }
    }).render('#paypal-boton');
    </script>
</body>
</html>