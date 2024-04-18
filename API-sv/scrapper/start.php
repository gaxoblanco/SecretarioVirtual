<?php
// this script is in charge of updating the db with the public data

// Incluyo el steps 1
require_once __DIR__ . '/steps/step1.php';

// Importar el cliente SQL
require_once 'db.php';

// require_once '../pjf-listas-despacho/PJF_Listas_Despacho.php';
require_once __DIR__ . '/../pjf-listas-despacho/PJF_Listas_Despacho.php';

// Crear instancia de la clase Listas_Despacho
$pjf = new PJF_Listas_Despacho();

echo "Iniciando scrapper...\n";

// Crear una instancia de la clase scrapper
$scrapper = new TipoListaProcessor($pjf, $conexion);
$scrapper->startScript(); // Ejecutar el scrapper

echo "Scrapper finalizado\n";
