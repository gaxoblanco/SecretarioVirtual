<?php
// crea la class upDispatchMoves que consulta en la tabla movimientos por los movimientos que concidan con el id_expediente

class upDispatchMoves
{
    private $conexion;
    private $id_exp;
    private $exist; // id_exp en la tabla expedientes

    public function __construct($conexion, $id_exp, $exist)
    {
        $this->conexion = $conexion;
        $this->id_exp = $id_exp;
        $this->exist = $exist;
    }

    // upDispatchMoves actualiza los movimientos del expediente
    public function upDispatchMoves()
    {
        try {
            $query = "SELECT * FROM movimientos WHERE id_expediente = :exist";
            // preparo la consulta
            $stmt = $this->conexion->prepare($query);
            // ejecuto la consulta
            $stmt->execute([':exist' => $this->exist['id_expediente']]);
            // guardo el resultado de la consulta en la variable $result
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // valido que sea un array y que no este vacio
            if (is_array($result) && !empty($result)) {
                // recorro el array $result y por cada entrada guardo los datos en la tabla user_exp_move
                // la tabla user_exp_move tiene: fecha_movimiento 	estado 	texto 	titulo 	despacho 	id_exp (id_move auto increment)
                foreach ($result as $key => $value) {
                    $query = "INSERT INTO user_exp_move (fecha_movimiento, estado, texto, titulo, despacho, id_exp) VALUES (:fecha_movimiento, :estado, :texto, :titulo, :despacho, :id_exp)";
                    // preparo la consulta
                    $stmt = $this->conexion->prepare($query);
                    // ejecuto la consulta
                    $stmt->execute([
                        ':fecha_movimiento' => $value['fecha_movimiento'],
                        ':estado' => $value['estado'],
                        ':texto' => $value['texto'],
                        ':titulo' => $value['titulo'],
                        ':despacho' => $value['despacho'],
                        ':id_exp' => $this->id_exp
                    ]);
                }

                // Devolver una respuesta JSON de éxito
                // echo json_encode(['message' => 'Movimientos del nuevoExpediente actualizado correctamente']);
            } else {
                // Manejar el caso en que algunas claves no estén presentes en $exist
                http_response_code(200); // Código de estado 400 para solicitud incorrecta
                echo json_encode(['message' => 'No hay movimientos para el expediente ' . $this->exist]);
            }
        } catch (PDOException $e) {
            // Devolver una respuesta JSON de error
            http_response_code(500); // Establece el código de estado HTTP adecuado para un error interno del servidor
            echo json_encode(['message' => 'Error al actualizar los movimientoes del nuevoExpediente ']);
        }
    }
}
