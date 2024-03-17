<?php

// creo la class getDispatchById que devuelve un mensaje d eexito
class get_dispatch
{
    // creo las variables de la clase
    // creo las variables de la clase
    private $id_exp;
    private $conexion;

    // creo el constructor
    public function __construct($conexion, $id_exp)
    {
        $this->conexion = $conexion;
        $this->id_exp = $id_exp;
    }

    // creo la funcion getDispatchById que devuelve un mensaje de exito
    public function getDispatchById()
    {
        try {
            // Obtengo el expediente con el id_exp de la tabla user_expedients
            $query = $this->conexion->prepare('SELECT * FROM user_expedients WHERE id_exp = :id_exp');
            $query->execute([':id_exp' => $this->id_exp]);
            $dispatch = $query->fetch(PDO::FETCH_ASSOC);

            // Verifico si $dispatch está vacío
            if (empty($dispatch)) {
                http_response_code(204);  // Sin contenido
                echo json_encode(['message' => 'No se encontró el expediente segun el id_exp']);
                return;
            }

            // Obtengo el listado de movimientos que corresponden al expediente con el id_exp en la tabla user_exp_move
            try {
                $query = $this->conexion->prepare('SELECT * FROM user_exp_move WHERE id_exp = :id_exp');
                $query->execute([':id_exp' => $this->id_exp]);
                $moves = $query->fetchAll(PDO::FETCH_ASSOC);
                $dispatch['moves'] = $moves;
            } catch (PDOException $e) {
                http_response_code(500);  // Error interno del servidor
                echo json_encode(['error' => 'Error al obtener los movimientos del expediente segun el id_exp: ' . $e->getMessage()]);
                return;
            }

            // Devuelvo el expediente como respuesta en formato JSON
            http_response_code(200);
            echo json_encode($dispatch);
            return;
        } catch (PDOException $e) {
            http_response_code(500);  // Error interno del servidor
            echo json_encode(['error' => 'Error al obtener el expediente segun el id_exp: ' . $e->getMessage()]);
        }
    }
}
