<?php

// Incluir la clase scrapper
require_once 'scrapper.php';

// Incluir la clase users_data
require_once 'users_data.php';

// Incluir la clase up_user_exp
require_once 'up_user_exp.php';

// Incluir la clase write_mail
require_once './mail/write_mail.php';

// Importar el cliente SQL
require_once 'db.php';

require_once '../pjf-listas-despacho/PJF_Listas_Despacho.php';
// Crear instancia de la clase Listas_Despacho
$pjf = new PJF_Listas_Despacho();

echo "Iniciando scrapper...\n";

// Crear una instancia de la clase scrapper
$scrapper = new TipoListaProcessor($pjf, $conexion);
$scrapper->startScript(); // Ejecutar el scrapper

echo "Scrapper finalizado\n";

//obtengo un array de usuarios con sus expedientes y los movimientos asociados
$tablesUpdater = new users_data($conexion);
$oldTableUserExp = $tablesUpdater->getExpedients();

echo "Actualizando base de datos...\n";

// compara las tablas y actualiza los expedientes y movimientos
$upUserExp = new up_user_exp($conexion, $oldTableUserExp);
$newsBy = $upUserExp->getExpedient();

echo "Base de datos actualizada\n";

// crear los correos apartir del array de usuario con expediente que tuvieron cambios write_mail
$writeMail = new write_mail($conexion, $newsBy);
$writeBy = $writeMail->write();

echo "Correos enviados: ";
