<?php
// Definir el directorio base del proyecto
define('BASE_DIR', __DIR__);
// this script is in charge of updating the db with the public data

// Incluir la clase scrapper
// require_once 'scrapper.php';
require_once './steps/step1.php';

// Incluir la clase users_data
require_once 'users_data.php';

// Incluir la clase up_user_exp
require_once 'up_user_exp.php';

// Incluir la clase write_mail
require_once 'write_mail.php';

// Importar el cliente SQL
require_once 'db.php';

require_once BASE_DIR . '/../pjf-listas-despacho/PJF_Listas_Despacho.php';
// Crear instancia de la clase Listas_Despacho
$pjf = new PJF_Listas_Despacho();


echo "Iniciando scrapper...\n";

// Crear una instancia de la clase scrapper
try {
  $scrapper = new TipoListaProcessor($pjf, $conexion);
  $scrapper->startScript(); // Ejecutar el scrapper
} catch (\Throwable $th) {
  echo "Error al iniciar el scrapper\n";
  echo $th->getMessage();
  exit;
}

echo "Scrapper finalizado\n";
