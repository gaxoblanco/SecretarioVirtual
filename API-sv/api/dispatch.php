<?php
function dispatchRoot($route, $method, $conexion)
{
  switch ($route) {
      // dispatch get
    case 'dispatch/get':
      require_once './dispatch/get_dispatch.php';
      if ($method === 'GET') {
        // Obtener el id del encabezado de la solicitud
        $userId = $_SERVER['HTTP_USERID'];
        $getDispatch = new get_dispatch($conexion, $userId);
        $getDispatch->getDispatches();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
      }
      break;

      // dispatch get by id
    case 'dispatch/getById':
      require_once './dispatch/get_dispatchById.php';
      if ($method === 'POST') {
        // Obtener el id_exp del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);
        $idExp = $data['idExp'];
        // Verificar si el id_exp está presente y es válido
        if (isset($idExp)) {
          $getDispatch = new get_dispatch($conexion, $idExp);
          $getDispatch->getDispatchById();
        } else {
          // No se proporcionó un id_exp válido en el cuerpo de la solicitud
          http_response_code(400); // Bad Request
          echo json_encode(['message' => 'Se requiere un id_exp válido en el cuerpo de la solicitud']);
        }
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
      }
      break;

      //dispatch moves
    case 'dispatch/moves':
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
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
      }
      break;

      // dispatch create
    case 'dispatch/create':
      require_once './dispatch/add_dispatch.php';

      if ($method === 'POST') {
        // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
        $data = json_decode(file_get_contents('php://input'), true);

        // Obtener los datos para crear un nuevo expediente
        $Id = $_SERVER['HTTP_USERID'];
        // guardo el valor del elemento 0 del array
        $userId = explode(' ', $Id)[0];
        $caseNumber = $data['fileNumber'];
        $caseYear = $data['yearNumber'];
        $dispatch = $data['dispatch'];

        $addDispatch = new add_dispatch($conexion, $userId, $caseNumber, $caseYear, $dispatch);
        $addDispatch->addDispatch();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
      }
      break;

      // dispatch delete
    case 'dispatch/delete':
      require_once './dispatch/delete_dispatch.php';
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
      // /dispatch/updateNewFile
    case 'dispatch/updateNewFile':
      require_once './dispatch/last-move/last_move.php';
      if ($method === 'POST') {
        // Obtener los datos del cuerpo de la solicitud (por ejemplo, utilizando json_decode())
        $data = json_decode(file_get_contents('php://input'), true);

        // Obtener los datos para actualizar el expediente
        $userId = $_SERVER['HTTP_USERID'];
        $caseNumber = $data['fileNumber'];
        $caseYear = $data['yearNumber'];
        $dispatch = $data['dispatch'];

        $updateNewFile = new last_move($conexion, $userId, $caseNumber, $caseYear, $dispatch);
        $updateNewFile->lastMove();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
      }
      break;

    default:
      // Ruta no encontrada
      http_response_code(404);
      echo json_encode(['message' => 'Not Found Dispatch']);
      break;
  }
}
