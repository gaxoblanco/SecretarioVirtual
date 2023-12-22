<?php
function userRoot($route, $method, $conexion)
{
  switch ($route) {
    case 'user/get':
      require_once './user/user_get.php';
      if ($method === 'GET') {
        // Obtener el id del encabezado de la solicitud
        $id = $_SERVER['HTTP_USERID'];
        $userGetSecretary = new user_get($conexion, $id);
        $userGetSecretary->getUsers();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not /user/get']);
      }
      break;

      //user update
    case 'user/update':
      require_once './user/user_update.php';
      if ($method === 'POST') {
        // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
        $data = json_decode(file_get_contents('php://input'), true);

        // Obtener los datos del usuario a actualizar
        // $id = $data['id'];
        // el id viaja en el header como userId
        $id = $_SERVER['HTTP_USERID'];

        $firstName = $data['firstName'] ?? null;
        $lastName = $data['lastName'] ?? null;
        $email = $data['email'] ?? null;
        // la nueva $password llega por el header
        $password = $_SERVER['HTTP_PASSWORD'] ?? null;

        // Crear una instancia de la clase user_update y llamar al método updateUser()
        $userUpdate = new user_update($conexion, $id, $firstName, $lastName, $email, $password);
        $userUpdate->updateUser();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not /user/update']);
      }
      break;

      // user/password-chenge
    case 'user/password-change':
      require_once './user/password/user_password_change.php';
      if ($method === 'POST') {
        // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
        $data = json_decode(file_get_contents('php://input'), true);

        // Obtener los datos del usuario a actualizar
        $id = $_SERVER['HTTP_USERID'];
        // Obtengo el token del header
        $token = $_SERVER['HTTP_TOKEN'];

        // la nueva $password llega por el header
        $password = $_SERVER['HTTP_PASSWORD'];

        $userPasswordChange = new user_password_change($conexion, $id, $password, $token);
        $userPasswordChange->passwordChange();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not /user/password-change']);
      }
      break;
    default:
      // Ruta no encontrada
      http_response_code(404);
      echo json_encode(['message' => 'Not Found User']);
      break;
  }
}
