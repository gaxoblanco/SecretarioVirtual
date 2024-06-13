<?php

function mpRoot($route, $method, $conexion)
{
  switch ($route) {
      //mp/getById
    case 'mp/getById':
      require_once './mp/update_id_mp/get_by_id.php';
      if ($method === 'GET') {
        // $data = json_decode(file_get_contents('php://input'), true);

        $userId = $_SERVER['HTTP_USERID'];

        $getById = new get_by_id($conexion, $userId);
        $getById->mpGetById();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not /mp/getById']);
      }
      break;
      // script para actualizar el id_mp
    case 'mp/updateIdMp':
      require_once './mp/update_id_mp/updating_id_mp.php';
      if ($method === 'GET') {
        $updateIdMp = new updateing_id_mp($conexion);
        $updateIdMp->startIdMp();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not /mp/updateIdMp']);
      }
      break;
      // Metodo para obtener el init_point y continuar con el pago
    case 'mp/getInitPoint':
      require_once './mp/new-plan/get_init_point.php';
      if ($method === 'GET') {
        $userId = $_SERVER['HTTP_USERID'];

        $getInitPoint = new get_init_point($conexion, $userId);
        $getInitPoint->getInitPoint();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not /mp/getInitPoint']);
      }
      break;
      // Metodo para actualizar el status del pago
    case 'mp/updateStatus':
      require_once './mp/payment_status/updating_status.php';
      if ($method === 'GET') {
        $updateStatus = new updating_status($conexion);
        $updateStatus->startStatus();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not /mp/updateStatus']);
      }
      break;
    default:
      // Ruta no encontrada
      http_response_code(404);
      echo json_encode(['message' => 'Route Not Found']);
      break;
  }
}
