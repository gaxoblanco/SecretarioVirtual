<?php
require_once 'add_dispatch-upData.php';
//data base
class add_dispatch
{
  private $userId;
  private $caseNumber;
  private $caseYear;
  private $conexion;
  private $dispatch;

  public function __construct($conexion, $userId, $caseNumber, $caseYear, $dispatch)
  {
    $this->conexion = $conexion;
    $this->userId = $userId;
    $this->caseNumber = $caseNumber;
    $this->caseYear = $caseYear;
    $this->dispatch = $dispatch;

    $this->userId = $userId;
  }

  // Funcion que toma la informacion y consulta si existe un expediente con el mismo numero y año y es del mismo usuario
  public function addDispatch()
  {
    try {
      // Verificar si el expediente con el numero, año y dependencia proporcionado existe y obtiene su id_user
      $query = $this->conexion->prepare('SELECT id_user FROM user_expedients WHERE numero_exp = :numero_exp AND anio_exp = :anio_exp AND dependencia = :dependencia');
      $query->execute([':numero_exp' => $this->caseNumber, ':anio_exp' => $this->caseYear, ':dependencia' => $this->dispatch]); // 
      $count = $query->rowCount(); // $count = 0 si no existe el expediente

      if ($count != 0) {
        //retorno un json con el mensaje de expediente ya existe
        echo json_encode(['message' => 'El expediente ya existe.']);
        exit;
      }

      // Crear el expediente
      $query = $this->conexion->prepare('INSERT INTO user_expedients (id_lista_despacho, numero_exp, anio_exp, dependencia, id_user) VALUES (NULL, :numero_exp, :anio_exp, :dependencia, :id_user)');
      $query->execute([':numero_exp' => $this->caseNumber, ':anio_exp' => $this->caseYear, ':dependencia' => $this->dispatch, ':id_user' => $this->userId]);
      //retorna un json mensaje de exito
      // echo json_encode(['message' => 'Expediente creado con exito.']);

      //----------- Actualiza el expediente en caso de que ya este en la tabla expedientes
      // llamo a add_dispatch-upData para verificar si ya existe en la DB y enviar los datos
      try {
        $addDispatchUpData = new add_dispatch_upData($this->conexion, $this->userId, $this->caseNumber, $this->caseYear, $this->dispatch);
        $addDispatchUpData->addDispatchUpData();
      } catch (PDOException $e) {
        // Devolver una respuesta JSON de error
        http_response_code(500); // Establece el código de estado HTTP adecuado para un error interno del servidor
        echo json_encode(['message' => 'Error al actualizar el expediente API: ']);
      }
      echo json_encode(['message' => 'NuevoExpediente cargado y actualizado con exito.']);
    } catch (PDOException $e) {
      // Devolver una respuesta JSON de error
      http_response_code(500); // Establece el código de estado HTTP adecuado para un error interno del servidor
      echo json_encode(['message' => 'Error al crear el expediente API: ' . $e->getMessage()]);
    }
  }
}
