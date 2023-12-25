<?php
function openRoute($route, $method, $conexion)
{
  switch ($route) {
      //user create
    case 'user/create':
      require_once './user/user_create.php';
      if ($method === 'POST') {
        // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
        $data = json_decode(file_get_contents('php://input'), true);
        // Crear un nuevo usuario utilizando los datos recibidos
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $email = $data['email'];
        $password = $data['password'];
        $id_subscription = $data['id_subscription'];

        $userCreate = new user_create($conexion, $firstName, $lastName, $email, $password, $id_subscription);
        $userCreate->createUser();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
      }
      break;

      // user login
    case 'user/login':
      require_once './user/login.php';
      if ($method === 'POST') {
        // Obtener el email y password proporcionados
        $email = $_SERVER['HTTP_EMAIL'];
        $password = $_SERVER['HTTP_PASSWORD'];

        // Verificar que la variable $conexion sea un objeto PDO válido
        if ($conexion instanceof PDO) {
          // Crear una instancia de la clase Login
          $login = new Login($conexion, $email, $password);
          $login->loginUser();
        } else {
          // No se pudo conectar a la base de datos
          echo json_encode(['message' => 'Error connecting to database']);
        }
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
      }
      break;

      // restart password - user/password-restart
    case 'user/password-restart':
      require_once './password/restart_password.php';
      if ($method === 'POST') {
        // Obtener el email del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'];

        // Verificar que la variable $conexion sea un objeto PDO válido
        if ($conexion instanceof PDO) {
          // Crear una instancia de la clase user_password_restart
          $userPasswordRestart = new user_password_restart($conexion, $email);
          $userPasswordRestart->passwordRestart();
        } else {
          // No se pudo conectar a la base de datos
          echo json_encode(['message' => 'Error connecting to database']);
        }
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
      }
      break;

      // /user/password-reset
    case 'user/password-reset':
      require_once './password/save_new.php';
      if ($method === 'POST') {
        // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
        $data = json_decode(file_get_contents('php://input'), true);
        $token = $data['token'];
        $email = $data['email'];
        $password = $data['password'];

        // Verificar que la variable $conexion sea un objeto PDO válido
        if ($conexion instanceof PDO) {
          // Crear una instancia de la clase save_new_pass
          $saveNewPass = new save_new_pass($conexion, $email, $token, $password);
          $saveNewPass->restart_password();
        } else {
          // No se pudo conectar a la base de datos
          echo json_encode(['message' => 'Error connecting to database']);
        }
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
      }
      break;
    default:
      // Ruta no encontrada
      http_response_code(404);
      echo json_encode(['message' => 'Not Found openRoute' . $route]);
      break;
  }
}
