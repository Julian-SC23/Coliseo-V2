<?php
require_once '../Conexion/conexion.php';

// Hace una petición a la API de PayPal con cURL y regresa la respuesta como arreglo
function paypal_peticion($metodo, $ruta, $cuerpo = null, $token = null, $basic = false) {
    $ch = curl_init(PAYPAL_API . $ruta);
    $headers = ['Accept: application/json'];

    if ($basic) {                       // para pedir el token (usa Client ID y Secret)
        curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ':' . PAYPAL_SECRET);
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    } else {                            // para todo lo demás (usa el token)
        $headers[] = 'Authorization: Bearer ' . $token;
        $headers[] = 'Content-Type: application/json';
    }

    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $metodo,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_SSL_VERIFYPEER => PAYPAL_VERIFICAR_SSL,
    ]);
    if ($cuerpo !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $cuerpo);
    }

    $respuesta = curl_exec($ch);
    if ($respuesta === false) {
        $msg = curl_error($ch);
        curl_close($ch);
        throw new Exception('Error de conexión con PayPal: ' . $msg);
    }
    curl_close($ch);
    return json_decode($respuesta, true);
}

function paypal_token() {
    $r = paypal_peticion('POST', '/v1/oauth2/token', 'grant_type=client_credentials', null, true);
    if (empty($r['access_token'])) {
        throw new Exception('No se pudo autenticar con PayPal. Revisa tu Client ID y Secret.');
    }
    return $r['access_token'];
}