<?php
// Información de conexión de Donweb
$host = 'localhost';
$nombreDB = 'despachos'; // c2361340_sv
$usuarioDB = 'gaston'; // c2361340_sv
$contrasenaDB = 'blanco'; // reDI80noba

$uri = "mysql:host=$host;dbname=$nombreDB";
$opciones = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);
try {
    $conexion = new PDO($uri, $usuarioDB, $contrasenaDB, $opciones);
} catch (PDOException $e) {
    echo 'Error conectando con la base de datos: ' . $e->getMessage();
}
