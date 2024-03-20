<?php
// This script is in charge of processing the step-by-step for sending the emails
// 1 - Get the users with pagination
// 2 - Get expedients from the users
// 3 - Create the emails from the $newsBy, array of users with expedient that had changes

// Incluir la clase users_data
require_once 'users_data.php';

// Incluir la clase up_user_exp
require_once 'up_user_exp.php';

// Incluir la clase write_mail
require_once 'write_mail.php';

// Importar el cliente SQL
require_once 'db.php';

echo "Iniciando eMail...\n";

// Obtener los usuarios con paginación
$offset = 0;
$limit = 50; // Número de usuarios por bloque

do {
  //1 - Get the users with pagination
  $tablesUpdater = new users_data($conexion);
  $oldTableUserExp = $tablesUpdater->userExpedients($offset, $limit);

  // echo json_encode($oldTableUserExp);
  // valido que el array no este vacio
  if (empty($oldTableUserExp)) {
    echo "No hay usuarios con expedientes\n";
    break;
  }

  // 2 - Get expedients from users
  $upUserExp = new up_user_exp($conexion, $oldTableUserExp);
  $newsBy = $upUserExp->getExpedient($offset, $limit); // $newsBy is an array of users with expedient that had changes

  echo json_encode("correos para...\n");

  // valido que el array no este vacio
  if (empty($newsBy)) {
    echo "No hay correos para enviar\n";
    break;
  }

  // 3 - Create the emails from the $newsBy, array of users with expedient that had changes
  $writeMail = new write_mail($conexion, $newsBy);
  $writeMail->write();
  echo json_encode("correo enviado al grupo\n");

  // Incrementar el offset para el siguiente bloque
  $offset += $limit;
} while (!empty($oldTableUserExp));
