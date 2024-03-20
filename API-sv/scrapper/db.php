<?php
// Información de conexión de Donweb
$host = 'localhost'; // localhost
$nombreDB = 'c2361340_sv'; // c2361340_sv // despachos
$usuarioDB = 'c2361340_sv'; // c2361340_sv // gaston
$contrasenaDB = 'reDI80noba'; // reDI80noba // blanco

$uri = "mysql:host=$host;dbname=$nombreDB";
$opciones = array(
  PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);
try {
  $conexion = new PDO($uri, $usuarioDB, $contrasenaDB, $opciones);
} catch (PDOException $e) {
  echo 'Error conectando con la base de datos: ' . $e->getMessage();
}

// email credential
define('SMTP_SERVER', 'c2361340.ferozo.com');
define('SMTP_PORT', 465);
define('SMTP_USERNAME', 'expedientes@secretariovirtual.ar');
define('SMTP_PASSWORD', 'S3cretari@');
