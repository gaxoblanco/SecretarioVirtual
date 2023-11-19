<?php
// tabla expedientes: id_expediente	id_lista_despacho	numero_expediente	anio_expediente	caratula	reservado	dependencia	tipo_lista
class existExpediente
{
    private $conexion;
    private $caseNumber;
    private $caseYear;
    private $dispatch;

    public function __construct($conexion, $caseNumber, $caseYear, $dispatch)
    {
        $this->conexion = $conexion;
        $this->caseNumber = $caseNumber;
        $this->caseYear = $caseYear;
        $this->dispatch = $dispatch;
    }

    // existExpediente devuelve los datos del expediente si existe
    public function existExpediente()
    {
        try {
            // Consulta en la tabla expedientes si existe un expediente que tenga this->caseNumber = numero_expediente, this->caseYear = anio_expediente y this->dispatch = id_lista_despacho
            $query = "SELECT id_expediente, id_lista_despacho, numero_expediente, anio_expediente, caratula, reservado, dependencia, tipo_lista FROM expedientes WHERE numero_expediente = :caseNumber AND anio_expediente = :caseYear AND dependencia = :dispatch";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(':caseNumber', $this->caseNumber, PDO::PARAM_STR);
            $statement->bindParam(':caseYear', $this->caseYear, PDO::PARAM_STR);
            $statement->bindParam(':dispatch', $this->dispatch, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            // Devolver una respuesta JSON de error
            http_response_code(500); // Establece el código de estado HTTP adecuado para un error interno del servidor
            echo json_encode(['message' => 'Error al obtener el idExp ' . $e->getMessage()]);
        }
    }
}
