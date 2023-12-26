<?php

// Incluir la clase users_data
require_once 'users_data.php';

// Incluir la clase up_user_exp
require_once 'up_user_exp.php';

// Incluir la clase write_mail
require_once './email/write_mail.php';

// Importar el cliente SQL
require_once 'db.php';

echo "Iniciando eMail...\n";

// Obtener los usuarios con paginación
$offset = 0;
$limit = 50; // Número de usuarios por bloque

do {
  //obtengo un array de usuarios con sus expedientes y los movimientos asociados
  $tablesUpdater = new users_data($conexion);
  $oldTableUserExp = $tablesUpdater->userExpedients($offset, $limit);

  // echo json_encode($oldTableUserExp);
  // valido que el array no este vacio
  if (empty($oldTableUserExp)) {
    echo "No hay usuarios con expedientes\n";
    break;
  }

  // compara las tablas y actualiza los expedientes y movimientos
  $upUserExp = new up_user_exp($conexion, $oldTableUserExp);
  $newsBy = $upUserExp->getExpedient($offset, $limit);

  echo json_encode("correos para...\n");

  // valido que el array no este vacio
  if (empty($newsBy)) {
    echo "No hay correos para enviar\n";
    break;
  }

  // crear los correos apartir del array de usuario con expediente que tuvieron cambios write_mail
  $writeMail = new write_mail($conexion, $newsBy);
  $writeMail->write();
  echo json_encode("correo enviado al grupo\n");

  // Incrementar el offset para el siguiente bloque
  $offset += $limit;
} while (!empty($oldTableUserExp));
