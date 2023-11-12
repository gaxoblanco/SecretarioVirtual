<?php
// Habilitar CORS
header('Access-Control-Allow-Origin: https://secretariovirtual.ar/');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, email, password, token, userId, idExp, caseNumber, caseYear, secreataryId, oldSemail, newSemail, Spass, firstName");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
require_once 'config.php';

// Comprueba el método de solicitud y la ruta para determinar la acción
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
  die();
}

// crea $route =  elemento que sigue a API-sv en $request y borra lo que sigue
$route = str_replace('/API-sv/', '', $request);
// No Requieren Token
$publicRoutes = [
  '/',
  'user/create',
  'user/login',
  'dispatch/update', // actualiza las tablas de expedientes y envia mails
  // Otras rutas públicas
];

// consulto si la ruta esta en el grupo publicRoutes con un for
// for ($i = 0; $i < count($publicRoutes); $i++) {
//   if ($route == $publicRoutes[$i]) {
//     require_once 'api/openRoute.php';
//     openRoute($route, $method, $conexion);
//   }
// }

// consulto si la ruta NO esta en el grupo publicRoutes
if (!in_array($route, $publicRoutes)) {


  require_once 'tokenControl.php';
  // Obtengo el userId del header
  $userId = $_SERVER['HTTP_USERID'];
  // Obtengo el token del header
  $token = $_SERVER['HTTP_TOKEN'];

  verifyToken($conexion, $token);

  //ahora limpio $route de todo lo que sigue a un \/ para saber a que ruta maestra corresponde
  $root = explode('/', $route);
  // si el primer elemento es vacio, lo elimino
  if ($root[0] == '') {
    array_shift($root);
  }

  // derivo la solicitud a la rama correspondiente
  switch ($root[0]) {
    case 'user':
      if ($root[1] == 'secretary') {
        require_once 'api/secretary.php';
        secretaryRoot($route, $method, $conexion);
      } else {
        require_once 'api/user.php';
        userRoot($route, $method, $conexion);
      }
      break;
    case 'dispatch':
      require_once 'api/dispatch.php';
      dispatchRoot($route, $method, $conexion);
      break;
      // si no se cumple ninguno muestro el valor de $route
    default:
      echo json_encode(['message' => $route]);
      break;
  }
} else {
  require_once 'api/openRoute.php';
  openRoute($route, $method, $conexion);
}
