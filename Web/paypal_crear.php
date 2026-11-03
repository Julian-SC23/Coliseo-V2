<?php
require_once 'paypal.php';
header('Content-Type: application/json');

try {
    if (empty($_SESSION['usuario_id']) || empty($_SESSION['compra'])) {
        throw new Exception('No hay una compra en proceso.');
    }
    $c = $_SESSION['compra'];

    // El precio se toma de la BD, nunca del navegador
    $r = consulta(
        'SELECT i.precio, i.stock, i.zona, e.nombre
         FROM inventario i JOIN eventos e ON e.id = i.evento_id
         WHERE i.id = ?', 'i', [$c['inventario_id']]
    );
    if (!$r) {
        throw new Exception('El boleto ya no existe.');
    }
    $p = $r[0];
    if ($p['stock'] < $c['cantidad']) {
        throw new Exception('Ya no hay suficientes boletos disponibles.');
    }

    $total = number_format($p['precio'] * $c['cantidad'], 2, '.', '');
    $descripcion = mb_substr($c['cantidad'] . ' x ' . $p['nombre'] . ' (' . $p['zona'] . ')', 0, 120);

    $token = paypal_token();
    $orden = paypal_peticion('POST', '/v2/checkout/orders', json_encode([
        'intent' => 'CAPTURE',
        'purchase_units' => [[
            'description' => $descripcion,
            'amount' => ['currency_code' => PAYPAL_MONEDA, 'value' => $total],
        ]],
    ]), $token);

    if (empty($orden['id'])) {
        throw new Exception('PayPal no pudo crear la orden.');
    }

    $_SESSION['paypal_orden'] = $orden['id'];
    $_SESSION['paypal_total'] = $total;
    echo json_encode(['id' => $orden['id']]);

} catch (Throwable $ex) {
    http_response_code(500);
    echo json_encode(['error' => $ex->getMessage()]);
}