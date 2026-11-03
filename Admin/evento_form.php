<?php
require_once '../Conexion/conexion.php';
requiere_admin();
$pagina = 'eventos';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Valores por defecto (evento nuevo)
$ev = [
    'nombre' => '', 'fecha' => '', 'hora' => '', 'lugar' => 'Coliseo Centenario, Torreón',
    'tipo' => 'Concierto', 'imagen' => '', 'descripcion' => '', 'activo' => 1,
];

// Si es edición, cargar el evento
if ($id > 0) {
    $r = consulta('SELECT * FROM eventos WHERE id = ?', 'i', [$id]);
    if (!$r) {
        header('Location: eventos.php');
        exit;
    }
    $ev = $r[0];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ev['nombre']      = trim($_POST['nombre'] ?? '');
    $ev['fecha']       = trim($_POST['fecha'] ?? '');
    $ev['hora']        = trim($_POST['hora'] ?? '');
    $ev['lugar']       = trim($_POST['lugar'] ?? '');
    $ev['tipo']        = $_POST['tipo'] ?? 'Concierto';
    $ev['descripcion'] = trim($_POST['descripcion'] ?? '');
    $ev['activo']      = isset($_POST['activo']) ? 1 : 0;

    if ($ev['nombre'] === '' || $ev['lugar'] === '') {
        $error = 'El nombre y el lugar son obligatorios.';
    } elseif ($ev['fecha'] !== '' && !DateTime::createFromFormat('Y-m-d', $ev['fecha'])) {
        $error = 'La fecha no es válida.';
    }

    // Subir el cartel (imagen)
    if ($error === '' && !empty($_FILES['imagen']['name'])) {
        $archivo = $_FILES['imagen'];
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            $error = 'No se pudo subir la imagen.';
        } elseif (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $error = 'La imagen debe ser JPG, PNG o WEBP.';
        } elseif ($archivo['size'] > 3 * 1024 * 1024) {
            $error = 'La imagen no debe pesar más de 3 MB.';
        } elseif (!getimagesize($archivo['tmp_name'])) {
            $error = 'El archivo no es una imagen válida.';
        } else {
            $nombreArchivo = 'evento_' . time() . '.' . $ext;
            move_uploaded_file($archivo['tmp_name'], '../Recursos/' . $nombreArchivo);
            $ev['imagen'] = $nombreArchivo;
        }
    } elseif ($error === '' && $ev['imagen'] === '') {
        $error = 'Sube el cartel del evento.';
    }

    if ($error === '') {
        $fechaBD = $ev['fecha'] === '' ? null : $ev['fecha'];

        if ($id > 0) {
            ejecutar(
                'UPDATE eventos SET nombre = ?, fecha = ?, hora = ?, lugar = ?, tipo = ?,
                                    imagen = ?, descripcion = ?, activo = ? WHERE id = ?',
                'sssssssii',
                [$ev['nombre'], $fechaBD, $ev['hora'], $ev['lugar'], $ev['tipo'],
                 $ev['imagen'], $ev['descripcion'], $ev['activo'], $id]
            );
            $_SESSION['msg_admin'] = 'Evento actualizado.';
        } else {
            ejecutar(
                'INSERT INTO eventos (nombre, fecha, hora, lugar, tipo, imagen, descripcion, activo)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                'sssssssi',
                [$ev['nombre'], $fechaBD, $ev['hora'], $ev['lugar'], $ev['tipo'],
                 $ev['imagen'], $ev['descripcion'], $ev['activo']]
            );
            $nuevoId = $db->insert_id;

            // Zonas iniciales del evento nuevo (luego editas precio y stock en Productos)
            $zonas = [['VIP', 1200, 100], ['Preferente', 650, 200], ['Palcos', 900, 150],
                      ['General baja', 350, 400], ['General alta', 250, 500]];
            foreach ($zonas as $z) {
                ejecutar('INSERT INTO inventario (evento_id, zona, precio, stock) VALUES (?, ?, ?, ?)',
                         'isdi', [$nuevoId, $z[0], $z[1], $z[2]]);
            }
            $_SESSION['msg_admin'] = 'Evento creado con sus 5 zonas. Ajusta precios y stock en Productos.';
        }
        header('Location: eventos.php');
        exit;
    }
}

$tipos = ['Concierto', 'Lucha libre', 'Deportivo', 'Familiar', 'Conferencia'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id ? 'Editar' : 'Nuevo' ?> evento | Admin Coliseo</title>
    <link rel="stylesheet" href="../Estilos/estilos.css?v=2">
</head>
<body>
    <?php require_once 'encabezado_admin.php'; ?>

    <main class="contenedor pagina-centrada">
        <form class="formulario formulario-ancho" method="post" action="evento_form.php" enctype="multipart/form-data">
            <h1 class="formulario-titulo"><?= $id ? 'Editar evento' : 'Nuevo evento' ?></h1>
            <p class="formulario-subtitulo">Llena los datos que se muestran en el sitio.</p>

            <?php if ($error !== ''): ?>
                <p class="aviso"><?= e($error) ?></p>
            <?php endif; ?>

            <input type="hidden" name="id" value="<?= (int)$id ?>">

            <div class="campo">
                <label class="campo-etiqueta" for="nombre">Nombre del evento</label>
                <input class="campo-entrada" type="text" id="nombre" name="nombre" value="<?= e($ev['nombre']) ?>" required>
            </div>

            <div class="campo">
                <label class="campo-etiqueta" for="fecha">Fecha <span class="campo-opcional">(vacía = por confirmar)</span></label>
                <input class="campo-entrada" type="date" id="fecha" name="fecha" value="<?= e($ev['fecha']) ?>">
            </div>

            <div class="campo">
                <label class="campo-etiqueta" for="hora">Hora</label>
                <input class="campo-entrada" type="text" id="hora" name="hora" placeholder="9:00 PM" value="<?= e($ev['hora']) ?>">
            </div>

            <div class="campo">
                <label class="campo-etiqueta" for="lugar">Lugar</label>
                <input class="campo-entrada" type="text" id="lugar" name="lugar" value="<?= e($ev['lugar']) ?>" required>
            </div>

            <div class="campo">
                <label class="campo-etiqueta" for="tipo">Tipo</label>
                <select class="campo-entrada" id="tipo" name="tipo">
                    <?php foreach ($tipos as $t): ?>
                        <option value="<?= e($t) ?>" <?= $ev['tipo'] === $t ? 'selected' : '' ?>><?= e($t) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label class="campo-etiqueta" for="descripcion">Descripción</label>
                <textarea class="campo-entrada" id="descripcion" name="descripcion" rows="4"><?= e($ev['descripcion']) ?></textarea>
            </div>

            <div class="campo">
                <label class="campo-etiqueta" for="imagen">Cartel <span class="campo-opcional">(JPG, PNG o WEBP, máx. 3 MB)</span></label>
                <?php if ($ev['imagen'] !== ''): ?>
                    <p class="aviso">Cartel actual: <?= e($ev['imagen']) ?>. Sube otro solo si quieres cambiarlo.</p>
                <?php endif; ?>
                <input class="campo-entrada" type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
            </div>

            <label class="formulario-terminos">
                <input class="formulario-casilla" type="checkbox" name="activo" <?= $ev['activo'] ? 'checked' : '' ?>>
                <span>Visible en el sitio</span>
            </label>

            <button type="submit" class="boton boton-completo">Guardar evento</button>
            <a href="eventos.php" class="boton boton-borde boton-completo">Cancelar</a>
        </form>
    </main>
</body>
</html>