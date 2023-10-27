<?php
// Habilitar CORS
// header('Access-Control-Allow-Origin: https://secretariovirtual.ar');
header('Access-Control-Allow-Origin: *');
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

// Eliminar la parte de la URL después de 'Logica' y obtener la ruta relativa
$base = '/API-sv';
$route = explode($base, $request, 2)[1] ?? '';

// No Requieren Token
$publicRoutes = [
  '/',
  '/login',
  '/register',
  // Otras rutas públicas
];

// Obtengo el userId del header
$userId = $_SERVER['HTTP_USERID'];
// Obtengo el token del header
$token = $_SERVER['HTTP_TOKEN'];

// verifico si la ruta NO esta en el grupo publicRoutes (todas las rutas necesitan token salvo que sean publcia)
if (!in_array($route, $publicRoutes)) {
  // verifico si el token es valido
  if (!verifyToken($conexion, $token)) {
    // El token no es válido
    http_response_code(401);
    echo json_encode(['message' => 'Invalid token']);
    exit; // Sale del script
  }

  switch ($route) {
    case 'user/secretary':
      //llamo al archivo api/secretary.php y le paso $route
      require_once 'api/secretary.php';
      secretaryRoot($route);
      break;
    case 'user':
      require_once 'api/user.php';
      userRoot($route);
      break;
    case 'dispatch':
      require_once 'api/dispatch.php';
      dispatchRoot($route);
      break;
  }
}

// Rutas publicas
require_once 'api/openRoute.php';
openRoute($route);
