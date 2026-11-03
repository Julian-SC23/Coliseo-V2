# Coliseo Centenario

Sistema web de venta de boletos (cliente y administrador) con PHP, MySQL y PayPal Sandbox.

## Cómo correrlo
1. Instala XAMPP y pon la carpeta del proyecto en `htdocs`.
2. En phpMyAdmin, importa `BD/coliseo.sql`.
3. Copia `Conexion/config.ejemplo.php` como `Conexion/config.local.php` y llena tus datos (PayPal Sandbox).
4. Abre `http://localhost/NOMBRE-CARPETA/Web/index.php`.

Administrador de prueba: `admin@coliseo.com` / `admin1234`

==== SOLUCION DE ERRORES ====
- si en mysql para xapm tien un pueto diferente del 3307 y la pagina no corre agrege lo siguiente:
en el archivo 
config.local.php agregar:
define('DB_PORT', 3307); 

y en la line 15 del conexion.php remplazar por:
new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, defined('DB_PORT') ? DB_PORT : 3306);