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

      //   // actualiza las tablas de expedientes y movimientos
      // case 'dispatch/update':
      //   require_once './scrapper/users_data.php';
      //   require_once './scrapper/up_user_exp.php';
      //   require_once './scrapper/write_mail.php';

      //   //obtengo un array de usuarios con sus expedientes y los movimientos asociados
      //   $tablesUpdater = new users_data($conexion);
      //   $oldTableUserExp = $tablesUpdater->getUsers();

      //   // echo json_encode($oldTableUserExp);

      //   // compara las tablas y actualiza los expedientes y movimientos
      //   $upUserExp = new up_user_exp($conexion, $oldTableUserExp);
      //   $newsBy = $upUserExp->getExpedient();

      //   echo json_encode($newsBy);

      //   // crear los correos apartir del array de usuario con expediente que tuvieron cambios write_mail
      //   $writeMail = new write_mail($conexion, $newsBy);
      //   $writeMail->write();

      //   // echo json_encode($writeMail->write());
      //   break;

    default:
      // Ruta no encontrada
      http_response_code(404);
      echo json_encode(['message' => 'Not Found openRoute']);
      break;
  }
}
