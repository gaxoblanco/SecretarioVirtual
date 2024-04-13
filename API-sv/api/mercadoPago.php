<?php

function mpRoot($route, $method, $conexion)
{
    switch ($route) {
            //mp/getById
        case 'mp/getById':
            require_once './mp/getById.php';
            if ($method === 'GET') {
                $data = json_decode(file_get_contents('php://input'), true);

                $userId = $_SERVER['HTTP_USERID'];
                // Obtener id_subscription del body que esta como clave valor 
                $id_subscription = $data['id_subscription'];

                $getById = new get_by_id($conexion, $userId, $id_subscription);
                $getById->get_by_id();
            } else {
                // Método no permitido para esta ruta
                http_response_code(405);
                echo json_encode(['message' => 'Method Not /mp/getById']);
            }
            break;
    }
}
