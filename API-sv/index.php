<?php
require_once 'config.php';
// Habilitar CORS
// header('Access-Control-Allow-Origin: https://secretariovirtual.ar/*');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, email, password, token, userId, idExp, caseNumber, caseYear, secreataryId, oldSemail, newSemail, Spass, firstName");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
// Comprueba el método de solicitud y la ruta para determinar la acción
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}

// Eliminar la parte de la URL después de 'Logica' y obtener la ruta relativa
$base = '/API-sv';
$route = explode($base, $request, 2)[1] ?? '';

switch ($route) {
        //user create
    case '/user/create':
        require_once './user/user_create.php';
        if ($method === 'POST') {
            // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
            $data = json_decode(file_get_contents('php://input'), true);
            // Crear un nuevo usuario utilizando los datos recibidos
            $firstName = $data['firstName'];
            $lastName = $data['lastName'];
            $email = $data['email'];
            $password = $data['password'];

            $userCreate = new user_create($conexion, $firstName, $lastName, $email, $password);
            $userCreate->createUser();
        } else {
            // Método no permitido para esta ruta
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;

        // user login
    case '/user/login':
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

        //user get
        //si no se envia id trae todos, servicio para update_dispatch y process_users
    case '/user/get':
        require_once './user/user_get.php';
        require_once './tokenControl.php';
        // verificamos el token
        if (isset($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN']; // Accede al encabezado 'token' enviado en la solicitud
        } else {
            // El encabezado 'token' no se ha proporcionado en la solicitud
            http_response_code(401);
            echo json_encode(['message' => 'Token not provided']);
            exit; // Sale del script
        }

        // echo json_encode(verifyToken($conexion, $token));

        if (!verifyToken($conexion, $token)) {
            // El token no es válido
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            break;
        }
        if ($method === 'GET') {
            // Obtener el id del encabezado de la solicitud
            $id = $_SERVER['HTTP_USERID'];
            $userGetSecretary = new user_get($conexion, $id);
            $userGetSecretary->getUsers();
        } else {
            // Método no permitido para esta ruta
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;


        //user update
    case '/user/update':
        require_once './user/user_update.php';
        require_once './tokenControl.php';
        if (isset($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN']; // Accede al encabezado 'token' enviado en la solicitud
        } else {
            // El encabezado 'token' no se ha proporcionado en la solicitud
            http_response_code(401);
            echo json_encode(['message' => 'Token not provided']);
            exit; // Sale del script
        }


        if (!verifyToken($conexion, $token)) {
            // El token no es válido
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            break;
        }
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
            $password = $data['password'] ?? null;

            // Crear una instancia de la clase user_update y llamar al método updateUser()
            $userUpdate = new user_update($conexion, $id, $firstName, $lastName, $email, $password);
            $userUpdate->updateUser();
        } else {
            // Método no permitido para esta ruta
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;

        // user create secretary
    case '/user/secretary/create':
        require_once './user/user_create_secretary.php';
        require_once './tokenControl.php';
        if (isset($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN']; // Accede al encabezado 'token' enviado en la solicitud
        } else {
            // El encabezado 'token' no se ha proporcionado en la solicitud
            http_response_code(401);
            echo json_encode(['message' => 'Token not provided']);
            exit; // Sale del script
        }


        if (!verifyToken($conexion, $token)) {
            // El token no es válido
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            break;
        }
        if ($method === 'POST') {
            // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
            $data = json_decode(file_get_contents('php://input'), true);

            // Obtener los datos para crear un nuevo secretario
            $userId = $_SERVER['HTTP_USERID'];
            $firstName = $data['name'];
            $email = $data['Semail'];
            $Spass = $data['Spass'];

            $userCreateSecretary = new user_create_secretary($conexion, $userId, $firstName, $email, $Spass);
            $userCreateSecretary->createSecretary();
        } else {
            // Método no permitido para esta ruta
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;

        // user updata secretary
    case '/user/secretary/update':
        require_once './user/user_update_secretary.php';
        require_once './tokenControl.php';
        if (isset($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN']; // Accede al encabezado 'token' enviado en la solicitud
        } else {
            // El encabezado 'token' no se ha proporcionado en la solicitud
            http_response_code(401);
            echo json_encode(['message' => 'Token not provided']);
            exit; // Sale del script
        }


        if (!verifyToken($conexion, $token)) {
            // El token no es válido
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            break;
        }
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
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;

        // user delete secretary
    case '/user/secretary/delete':
        require_once './user/user_delete_secretary.php';
        require_once './tokenControl.php';
        if (isset($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN']; // Accede al encabezado 'token' enviado en la solicitud
        } else {
            // El encabezado 'token' no se ha proporcionado en la solicitud
            http_response_code(401);
            echo json_encode(['message' => 'Token not provided']);
            exit; // Sale del script
        }


        if (!verifyToken($conexion, $token)) {
            // El token no es válido
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            break;
        }
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
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;

        // user get secretary
    case '/user/secretary/get':
        require_once './tokenControl.php';
        if (isset($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN']; // Accede al encabezado 'token' enviado en la solicitud
        } else {
            // El encabezado 'token' no se ha proporcionado en la solicitud
            http_response_code(401);
            echo json_encode(['message' => 'Token not provided']);
            exit; // Sale del script
        }


        if (!verifyToken($conexion, $token)) {
            // El token no es válido
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            break;
        }
        require_once './user/user_get_secretary.php';
        if ($method === 'GET') {
            // Obtener el userId del encabezado de la solicitud
            $userId = $_SERVER['HTTP_USERID'];

            $userGetSecretary = new user_get_secretary($conexion, $userId);
            $userGetSecretary->getSecretaries();
        } else {
            // Método no permitido para esta ruta
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;

        ///-------------------- DISPATCH ------------------------------

        // dispatch get
    case '/dispatch/get':
        require_once './tokenControl.php';
        if (isset($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN']; // Accede al encabezado 'token' enviado en la solicitud
        } else {
            // El encabezado 'token' no se ha proporcionado en la solicitud
            http_response_code(401);
            echo json_encode(['message' => 'Token not provided']);
            exit; // Sale del script
        }


        if (!verifyToken($conexion, $token)) {
            // El token no es válido
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            break;
        }
        require_once './dispatch/get_dispatch.php';
        if ($method === 'GET') {
            // Obtener el userId del encabezado de la solicitud
            $userId = $_SERVER['HTTP_USERID'];

            $getDispatch = new get_dispatch($conexion, $userId);
            $getDispatch->getDispatches();
        } else {
            // Método no permitido para esta ruta
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;

        //dispatch moves
    case '/dispatch/moves':
        require_once './tokenControl.php';
        if (isset($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN']; // Accede al encabezado 'token' enviado en la solicitud
        } else {
            // El encabezado 'token' no se ha proporcionado en la solicitud
            http_response_code(401);
            echo json_encode(['message' => 'Token not provided']);
            exit; // Sale del script
        }


        if (!verifyToken($conexion, $token)) {
            // El token no es válido
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            break;
        }
        require_once './dispatch/get_moves.php';
        if ($method === 'GET') {
            // Obtener el id_exp del encabezado de la solicitud
            $id_exp = $_SERVER['HTTP_IDEXP'];

            // Verificar si $id_exp está definido en los encabezados
            if ($id_exp) {
                $getDispatchMoves = new get_moves($conexion, $id_exp);
                $getDispatchMoves->getMoves();
            } else {
                // Enviar una respuesta de error si 'id_exp' no se encuentra en los encabezados
                http_response_code(400);
                echo json_encode(['message' => 'Missing id_exp in headers']);
            }
        }


        break;

        // dispatch create
    case '/dispatch/create':
        require_once './tokenControl.php';
        require_once './dispatch/add_dispatch.php';
        $token = $_SERVER['HTTP_TOKEN'];
        //guardo el valor del elemento 0 del array
        $token = explode(' ', $token)[0];

        //valido que el token sea valido sino devuelvo un error de credenciales incorrectas
        if (!verifyToken($conexion, $token)) {
            // El token no es válido
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            break;
        }


        if ($method === 'POST') {
            // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
            $data = json_decode(file_get_contents('php://input'), true);

            // Obtener los datos para crear un nuevo expediente
            $Id = $_SERVER['HTTP_USERID'];
            // guardo el valor del elemento 0 del array
            $userId = explode(' ', $Id)[0];
            $caseNumber = $data['fileNumber'];
            $caseYear = $data['yearNumber'];

            $addDispatch = new add_dispatch($conexion, $userId, $caseNumber, $caseYear);
            $addDispatch->addDispatch();
        } else {
            // Método no permitido para esta ruta
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;

        // dispatch delete
    case '/dispatch/delete':
        require_once './dispatch/delete_dispatch.php';
        require_once './tokenControl.php';
        if (isset($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN']; // Accede al encabezado 'token' enviado en la solicitud
        } else {
            // El encabezado 'token' no se ha proporcionado en la solicitud
            http_response_code(401);
            echo json_encode(['message' => 'Token not provided']);
            exit; // Sale del script
        }


        if (!verifyToken($conexion, $token)) {
            // El token no es válido
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            break;
        }
        if ($method === 'POST') {
            // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
            $data = json_decode(file_get_contents('php://input'), true);

            // Obtener los datos para eliminar el expediente
            $userId = $_SERVER['HTTP_USERID'];
            $dispatchId = $data['dispatchId'];

            $deleteDispatch = new delete_dispatch($conexion, $userId, $dispatchId);
            $deleteDispatch->deleteDispatchFromList();
        } else {
            // Método no permitido para esta ruta
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;

        // dispatch re enviar el expediente al usuario
        // case '/dispatch/reload':
        //     require_once './dispatch/update_dispatch_status.php';
        //     require_once './tokenControl.php';
        //     if (!$token) {
        //         // No se envió un token válido
        //         http_response_code(401);
        //         echo json_encode(['message' => 'Unauthorized']);
        //         break;
        //     }

        //     if (!verifyToken($conexion, $token)) {
        //         // El token no es válido
        //         http_response_code(401);
        //         echo json_encode(['message' => 'Invalid token']);
        //         break;
        //     }
        //     if ($method === 'POST') {

        //         // Obtener los datos para actualizar el estado del expediente
        //         $userId = $data['userId'];
        //         $caseNumber = $data['caseNumber'];

        //         $updateDispatchStatus = new UpdateDispatchStatus($userId, $caseNumber);
        //         $updateDispatchStatus->updateStatus();
        //     } else {
        //         // Método no permitido para esta ruta
        //         http_response_code(405);
        //         echo json_encode(['message' => 'Method Not Allowed']);
        //     }
        //     break;

        // actualiza las tablas de expedientes y movimientos
    case '/dispatch/update':
        require_once './scrapper/users_data.php';
        require_once './scrapper/up_user_exp.php';
        require_once './scrapper/write_mail.php';
        require_once './tokenControl.php';
        if (isset($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN']; // Accede al encabezado 'token' enviado en la solicitud
        } else {
            // El encabezado 'token' no se ha proporcionado en la solicitud
            http_response_code(401);
            echo json_encode(['message' => 'Token not provided']);
            exit; // Sale del script
        }


        if (!verifyToken($conexion, $token)) {
            // El token no es válido
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            break;
        }

        //obtengo un array de usuarios con sus expedientes y los movimientos asociados
        $tablesUpdater = new users_data($conexion);
        $oldTableUserExp = $tablesUpdater->getExpedients();

        // echo json_encode($oldTableUserExp);

        // compara las tablas y actualiza los expedientes y movimientos
        $upUserExp = new up_user_exp($conexion, $oldTableUserExp);
        $newsBy = $upUserExp->getExpedient();

        // echo json_encode($newsBy);

        // crear los correos apartir del array de usuario con expediente que tuvieron cambios write_mail
        $writeMail = new write_mail($conexion, $newsBy);
        // $writeMail->write();


        echo json_encode($writeMail->write());

        break;


        ///-------------------- EMAILS ------------------------------


    default:
        // Ruta no encontrada
        http_response_code(404);
        echo json_encode(['message' => 'Not Found']);
        break;
}
