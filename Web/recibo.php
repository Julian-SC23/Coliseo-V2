<?php
require_once '../Conexion/conexion.php';
require_once '../libs/fpdf/fpdf.php';
requiere_login();

// FPDF trabaja en Latin-1: convertimos los textos para que salgan bien los acentos
function t($texto) {
    return iconv('UTF-8', 'windows-1252//TRANSLIT', (string)$texto);
}

$folio = $_GET['folio'] ?? '';

// El administrador puede ver cualquier recibo; los demás, solo los suyos
if ($_SESSION['rol'] === 'administrador') {
    $v = consulta(
        'SELECT v.*, u.nombre, u.correo FROM ventas v
         JOIN usuarios u ON u.id = v.usuario_id WHERE v.folio = ?', 's', [$folio]
    );
} else {
    $v = consulta(
        'SELECT v.*, u.nombre, u.correo FROM ventas v
         JOIN usuarios u ON u.id = v.usuario_id WHERE v.folio = ? AND v.usuario_id = ?',
        'si', [$folio, $_SESSION['usuario_id']]
    );
}
if (!$v) {
    http_response_code(404);
    exit('Recibo no encontrado.');
}
$venta = $v[0];

$detalle = consulta(
    'SELECT d.cantidad, d.precio_unitario, i.zona, e.nombre, e.fecha
     FROM detalle_venta d
     JOIN inventario i ON i.id = d.inventario_id
     JOIN eventos e ON e.id = i.evento_id
     WHERE d.venta_id = ?', 'i', [$venta['id']]
);

// ---------- Armar el PDF ----------
$pdf = new FPDF('P', 'mm', 'Letter');
$pdf->AddPage();
$pdf->SetMargins(20, 20, 20);

// Encabezado
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 10, 'COLISEO CENTENARIO', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 7, t('Recibo de pago'), 0, 1, 'C');
$pdf->Ln(6);

// Datos de la venta
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(35, 7, 'Folio:', 0, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, t($venta['folio']), 0, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(35, 7, 'Fecha:', 0, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, date('d/m/Y H:i', strtotime($venta['creado'])), 0, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(35, 7, 'Cliente:', 0, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, t($venta['nombre']), 0, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(35, 7, 'Correo:', 0, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, t($venta['correo']), 0, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(35, 7, 'Metodo de pago:', 0, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, 'PayPal', 0, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(35, 7, 'ID de PayPal:', 0, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, t($venta['paypal_order_id']), 0, 1);
$pdf->Ln(6);

// Tabla de boletos
$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(78, 8, 'Evento', 1, 0, 'L', true);
$pdf->Cell(30, 8, 'Zona', 1, 0, 'L', true);
$pdf->Cell(15, 8, 'Cant.', 1, 0, 'C', true);
$pdf->Cell(23, 8, 'Precio', 1, 0, 'R', true);
$pdf->Cell(24, 8, 'Importe', 1, 1, 'R', true);

$pdf->SetFont('Arial', '', 10);
foreach ($detalle as $d) {
    $nombre = t($d['nombre']);
    if (strlen($nombre) > 40) {
        $nombre = substr($nombre, 0, 37) . '...';
    }
    $pdf->Cell(78, 8, $nombre, 1, 0);
    $pdf->Cell(30, 8, t($d['zona']), 1, 0);
    $pdf->Cell(15, 8, $d['cantidad'], 1, 0, 'C');
    $pdf->Cell(23, 8, '$' . number_format($d['precio_unitario'], 2), 1, 0, 'R');
    $pdf->Cell(24, 8, '$' . number_format($d['precio_unitario'] * $d['cantidad'], 2), 1, 1, 'R');
}

// Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(146, 10, 'TOTAL (MXN)', 0, 0, 'R');
$pdf->Cell(24, 10, '$' . number_format($venta['total'], 2), 0, 1, 'R');

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 9);
$pdf->MultiCell(0, 5, t('Gracias por tu compra. Presenta este recibo y tu ID de boleto en la entrada del Coliseo Centenario, Torreón, Coahuila.'), 0, 'C');

$pdf->Output('I', 'Recibo-' . $venta['folio'] . '.pdf');