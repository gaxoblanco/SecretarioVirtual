<?php

function secretaryRoot($route, $method, $conexion)
{
  switch ($route) {
    case 'user/secretary/create':
      require_once './user/user_create_secretary.php';
      if ($method === 'POST') {
        // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
        $data = json_decode(file_get_contents('php://input'), true);

        // Obtener los datos para crear un nuevo secretario
        $userId = $_SERVER['HTTP_USERID'];
        $firstName = isset($data['name']) ? $data['name'] : null; // valido que existan
        $email = isset($data['Semail']) ? $data['Semail'] : null;
        $Spass = isset($data['Spass']) ? $data['Spass'] : null;


        if (!empty($userId) && !empty($firstName) && !empty($email) && !empty($Spass)) {
          // Realizar la inserción en la base de datos
          $userCreateSecretary = new user_create_secretary($conexion, $userId, $firstName, $email, $Spass);
          $userCreateSecretary->createSecretary();
        } else {
          // Manejar el caso en el que los valores son nulos
          http_response_code(400);
          echo json_encode(['status' => 400, 'message' => 'Campos obligatorios vacíos']);
        }
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not /secretary/create']);
      }
      break;

      // user updata secretary
    case 'user/secretary/update':
      require_once './user/user_update_secretary.php';
      if ($method === 'POST') {
        // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
        $data = json_decode(file_get_contents('php://input'), true);

        // Obtener los datos para actualizar el secretario
        $userId = $_SERVER['HTTP_USERID'];
        $secreataryId = $data['secreataryId'];
        $oldSemail = $data['oldSemail'];
        $newSemail = $data['newSemail'];
        $Spass = $data['Spass'];
        $firstName = $data['name'];

        $userUpdateSecretary = new user_update_secretary($conexion, $userId, $secreataryId, $oldSemail, $newSemail, $Spass, $firstName);
        $userUpdateSecretary->updateSecretary();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not /secretary/update']);
      }
      break;

      // user delete secretary
    case 'user/secretary/delete':
      require_once './user/user_delete_secretary.php';
      if ($method === 'POST') {
        // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
        $data = json_decode(file_get_contents('php://input'), true);

        // Obtener los datos para eliminar al secretario
        $userId = $_SERVER['HTTP_USERID'];
        $semail = $data['Semail'];

        $userDeleteSecretary = new user_delete_secretary($conexion, $userId, $semail);
        $userDeleteSecretary->deleteSecretary();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not /secretary/delete']);
      }
      break;

      // user get secretary
    case 'user/secretary/get':
      require_once './user/user_get_secretary.php';
      if ($method === 'GET') {
        // Obtener el userId del encabezado de la solicitud
        $userId = $_SERVER['HTTP_USERID'];

        $userGetSecretary = new user_get_secretary($conexion, $userId);
        $userGetSecretary->getSecretaries();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not secretary/get']);
      }
      break;

    default:
      // Ruta no encontrada
      http_response_code(404);
      echo json_encode(['message' => 'Not Found Secretary']);
      break;
  }
}
