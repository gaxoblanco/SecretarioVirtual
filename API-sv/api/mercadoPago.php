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
        $updateIdMp = new update_id_mp($conexion);
        $updateIdMp->startIdMp();
      } else {
        // Método no permitido para esta ruta
        http_response_code(405);
        echo json_encode(['message' => 'Method Not /mp/updateIdMp']);
      }
      break;
  }
}
