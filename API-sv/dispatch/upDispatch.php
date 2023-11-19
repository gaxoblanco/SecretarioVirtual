<?php

// crea la clase upDispatch que recibe id_exp y los datos del expediente que va a acutlizar

// $exist tiene: [{"id_expediente":000,"id_lista_despacho":000,"numero_expediente":"0","anio_expediente":0,"caratula":"lorem...","reservado":0,"dependencia":000,"tipo_lista":1}]}[{"id_expediente":1,"id_lista_despacho":1,"numero_expediente":"11","anio_expediente":22,"caratula":"lorem...","reservado":0,"dependencia":000000,"tipo_lista":1}]


class upDispatch
{
    private $conexion;
    private $id_exp;
    private $exist;

    public function __construct($conexion, $id_exp, $exist)
    {
        $this->conexion = $conexion;
        $this->id_exp = $id_exp;
        $this->exist = $exist;
    }

    // upDispatch actualiza los datos del expediente
    public function upDispatch()
    {
        try {
            // valido que existan los datos necesarios
            if (
                array_key_exists('id_lista_despacho', $this->exist) &&
                array_key_exists('numero_expediente', $this->exist) &&
                array_key_exists('anio_expediente', $this->exist) &&
                array_key_exists('caratula', $this->exist) &&
                array_key_exists('reservado', $this->exist) &&
                array_key_exists('dependencia', $this->exist) &&
                array_key_exists('tipo_lista', $this->exist)
            ) {
                // en la tabla user_expedients actualizo el expediente que teniene el $id_exp
                $query = "UPDATE user_expedients SET id_lista_despacho = :id_lista_despacho, numero_exp = :numero_expediente, anio_exp = :anio_expediente, caratula = :caratula, reservado = :reservado, dependencia = :dependencia, tipo_lista = :tipo_lista WHERE id_exp = :id_exp";
                // preparo la consulta
                $stmt = $this->conexion->prepare($query);
                // enlazo los valores de las variables con los parametros de la consulta
                $stmt->bindParam(':id_lista_despacho', $this->exist['id_lista_despacho']);
                $stmt->bindParam(':numero_expediente', $this->exist['numero_expediente']);
                $stmt->bindParam(':anio_expediente', $this->exist['anio_expediente']);
                $stmt->bindParam(':caratula', $this->exist['caratula']);
                $stmt->bindParam(':reservado', $this->exist['reservado']);
                $stmt->bindParam(':dependencia', $this->exist['dependencia']);
                $stmt->bindParam(':tipo_lista', $this->exist['tipo_lista']);
                $stmt->bindParam(':id_exp', $this->id_exp);
                // ejecuto la consulta
                $stmt->execute();
                // Devolver una respuesta JSON de éxito
                // http_response_code(200); // Código de estado 200
                // echo json_encode(['message' => 'Expediente actualizado correctamente']);
            } else {
                // Manejar el caso en que algunas claves no estén presentes en $exist
                http_response_code(200); // Código de estado 400 para solicitud incorrecta
                echo json_encode(['message' => 'Faltan datos necesarios en el array $exist', 'data' => $this->exist]);
            }
        } catch (PDOException $e) {
            // Devolver una respuesta JSON de error
            http_response_code(500); // Establece el código de estado HTTP adecuado para un error interno del servidor
            echo json_encode(['message' => 'Error al actualizar el nuevoExpediente cargado ' . $e->getMessage()]);
        }
    }
}
