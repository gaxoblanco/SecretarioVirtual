<?php

// Incluir la clase users_data
require_once 'users_data.php';

// Incluir la clase up_user_exp
require_once 'up_user_exp.php';

// Incluir la clase write_mail
require_once './mail/write_mail.php';

// Importar el cliente SQL
require_once 'db.php';

echo "Iniciando eMail...\n";

//obtengo un array de usuarios con sus expedientes y los movimientos asociados
$tablesUpdater = new users_data($conexion);
$oldTableUserExp = $tablesUpdater->getExpedients();

// echo json_encode($oldTableUserExp);

// compara las tablas y actualiza los expedientes y movimientos
$upUserExp = new up_user_exp($conexion, $oldTableUserExp);
$newsBy = $upUserExp->getExpedient();

echo json_encode("correos para...\n");

// crear los correos apartir del array de usuario con expediente que tuvieron cambios write_mail
$writeMail = new write_mail($conexion, $newsBy);
$writeMail->write();

// echo json_encode($writeMail->write());
