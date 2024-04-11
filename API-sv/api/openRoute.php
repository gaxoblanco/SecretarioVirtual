<?php
function openRoute($route, $method, $conexion)
{
  switch ($route) {
      //user create
    case 'user/create':
      require_once './user/user_create.php';
      if ($method === 'POST') {

        // obtengo el id de la suscripción en mp
        include_once './mp/create_plan.php';
        $createPlan = new post_plan();
        $response = $createPlan->post_plan("secretariovirtual");

        // Decodificar la respuesta JSON
        $responseData = json_decode($response, true);

        // Verificar si se pudo decodificar la respuesta correctamente
        if ($responseData && isset($responseData['id'])) {
          //$id_subscription = $responseData;
          // echo json_encode($id_subscription);
          //$init_point = $responseData['init_point'];

          // guardo $responseData en formato json en $mp_data
          $mp_data = $responseData;
          // Verificar si la decodificación fue exitosa
          if ($mp_data === null) {
            // Manejar el caso en el que no se pudo decodificar el JSON correctamente
            echo json_encode([
              'status' => 500,
              'message' => 'Error al decodificar los datos de Mercado Pago.'
            ]);
            return;
          }

          // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
          $data = json_decode(file_get_contents('php://input'), true);
          // Crear un nuevo usuario utilizando los datos recibidos
          $firstName = $data['firstName'];
          $lastName = $data['lastName'];
          $email = $data['email'];
          $password = $data['password'];

          $userCreate = new user_create($conexion, $firstName, $lastName, $email, $password, $mp_data);
          $userCreate->createUser();

          echo json_encode($userCreate);
        } else {
          // No se pudo decodificar la respuesta JSON
          http_response_code(500);
          echo json_encode(['message' => 'Error geting subscription id from MercadoPago API']);
        }
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
