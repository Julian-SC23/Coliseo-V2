<?php
require_once 'paypal.php';
header('Content-Type: application/json');

try {
    if (empty($_SESSION['usuario_id']) || empty($_SESSION['compra'])) {
        throw new Exception('No hay una compra en proceso.');
    }

    $datos   = json_decode(file_get_contents('php://input'), true);
    $ordenId = $datos['orderID'] ?? '';
    if ($ordenId === '' || $ordenId !== ($_SESSION['paypal_orden'] ?? '')) {
        throw new Exception('Orden de PayPal no válida.');
    }

    // 1) Cobrar de verdad en PayPal
    $token = paypal_token();
    $cap   = paypal_peticion('POST', "/v2/checkout/orders/$ordenId/capture", '{}', $token);

    $captura = $cap['purchase_units'][0]['payments']['captures'][0] ?? null;
    if (($cap['status'] ?? '') !== 'COMPLETED' || !$captura || $captura['status'] !== 'COMPLETED') {
        throw new Exception('El pago no se completó.');
    }
    if (abs((float)$captura['amount']['value'] - (float)$_SESSION['paypal_total']) > 0.01) {
        throw new Exception('El monto pagado no coincide.');
    }

    // 2) Guardar la venta y bajar el inventario (todo o nada)
    $c        = $_SESSION['compra'];
    $cantidad = (int)$c['cantidad'];
    $total    = (float)$_SESSION['paypal_total'];

    $db->begin_transaction();
    try {
        $inv = consulta('SELECT stock FROM inventario WHERE id = ? FOR UPDATE', 'i', [$c['inventario_id']]);
        if (!$inv || $inv[0]['stock'] < $cantidad) {
            throw new Exception('Se agotaron los boletos. Guarda tu ID de PayPal para el reembolso: ' . $ordenId);
        }

        ejecutar(
            'INSERT INTO ventas (usuario_id, total, paypal_order_id, paypal_capture_id) VALUES (?, ?, ?, ?)',
            'idss', [$_SESSION['usuario_id'], $total, $ordenId, $captura['id']]
        );
        $ventaId = $db->insert_id;
        $folio   = 'CC-' . str_pad($ventaId, 6, '0', STR_PAD_LEFT);
        ejecutar('UPDATE ventas SET folio = ? WHERE id = ?', 'si', [$folio, $ventaId]);

        ejecutar(
            'INSERT INTO detalle_venta (venta_id, inventario_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)',
            'iiid', [$ventaId, $c['inventario_id'], $cantidad, $total / $cantidad]
        );

        // Disminución de inventario + conteo de ventas
        ejecutar(
            'UPDATE inventario SET stock = stock - ?, vendidos = vendidos + ? WHERE id = ?',
            'iii', [$cantidad, $cantidad, $c['inventario_id']]
        );

        $db->commit();
    } catch (Throwable $ex) {
        $db->rollback();
        throw $ex;
    }

    unset($_SESSION['compra'], $_SESSION['paypal_orden'], $_SESSION['paypal_total']);
    echo json_encode(['ok' => true, 'folio' => $folio]);

} catch (Throwable $ex) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $ex->getMessage()]);
}