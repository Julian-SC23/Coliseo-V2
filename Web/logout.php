<?php
require_once '../Conexion/conexion.php';

$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;