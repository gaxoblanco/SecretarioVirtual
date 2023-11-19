<?php
// Este archivo se encarga de obtener el id de un expediente
class getIdExpediente
{
    private $conexion;
    private $userId;
    private $caseNumber;
    private $caseYear;
    private $dispatch;

    public function __construct($conexion, $userId, $caseNumber, $caseYear, $dispatch)
    {
        $this->conexion = $conexion;
        $this->userId = $userId;
        $this->caseNumber = $caseNumber;
        $this->caseYear = $caseYear;
        $this->dispatch = $dispatch;
    }

    public function getIdExp()
    {
        try {
            $query = "SELECT id_exp FROM user_expedients WHERE id_user = :userId AND numero_exp = :caseNumber AND anio_exp = :caseYear AND dependencia = :dispatch";

            $stmt = $this->conexion->prepare($query);
            // ejecuto la consulta
            $stmt->execute([':userId' => $this->userId, ':caseNumber' => $this->caseNumber, ':caseYear' => $this->caseYear, ':dispatch' => $this->dispatch]);
            // guardo el resultado de la consulta en la variable $result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            // valido que el resultado no sea null
            if ($result == null) {
                // Devolver una respuesta JSON de error
                http_response_code(404); // Establece el código de estado HTTP adecuado para un error interno del servidor
                echo json_encode(['message' => 'No se encontro el ID expediente']);
                exit;
            }
            // guardo el valor del id del expediente en la variable $id_exp
            $id_exp = $result['id_exp'];

            // retorno el valor del id del expediente
            return $id_exp;
        } catch (PDOException $e) {
            // Devolver una respuesta JSON de error
            http_response_code(500); // Establece el código de estado HTTP adecuado para un error interno del servidor
            echo json_encode(['message' => 'Error al obtener el idExp ' . $e->getMessage()]);
        }
    }
}
