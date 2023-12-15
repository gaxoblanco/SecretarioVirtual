<?php
//- Consulta al archivo suscript.php para obtener el num_exp y num_secretary (son el numero maximo de expedientes y secretarios que puede tener un usuario con esa suscripcion)
// - Consulta el numero de expedientes que tiene el usuario en la tabla dispatchlist
// - Si el numero de expedientes es menor al numero de expedientes permitidos, se agrega el expediente a la tabla dispatchlist y se devuelve un mensaje de exito.
// - Si el numero de expedientes es igual al numero de expedientes permitidos, se devuelve un mensaje de error.
require_once './subscription.php';
class add_dispatch
{
  private $id_user;
  private $caseNumber;
  private $caseYear;
  private $conexion;
  private $dispatch;

  public function __construct($conexion, $id_user, $caseNumber, $caseYear, $dispatch)
  {
    $this->conexion = $conexion;
    $this->id_user = $id_user;
    $this->caseNumber = $caseNumber;
    $this->caseYear = $caseYear;
    $this->dispatch = $dispatch;

    $this->id_user = $id_user;
  }

  // Funcion que toma la informacion y consulta si existe un expediente con el mismo numero y año y es del mismo usuario
  public function addDispatch()
  {
    //Obtengo la informacion del tipo de suscripcion del usuario
    $subscription = new subscription($this->conexion, $this->id_user);
    $subscriptionInfo = $subscription->subscriptionUser();
    $Nexp = $subscriptionInfo['num_exp'];

    // trycatch para consultar a la db la cantidad de expedientes que tiene el usuario
    try {
      // verifica cuantos expedientes ya tiene el usuario
      $query = $this->conexion->prepare('SELECT COUNT(*) FROM user_expedients WHERE id_user = :id_user');
      $query->execute([':id_user' => $this->id_user]);
      $expedientsCount = $query->fetchColumn();
    } catch (PDOException $e) {
      // Devolver mensaje de error en json
      echo json_encode([
        'status' => 500,
        'message' => 'Error al consultar el numero de expedientes para crear uno nuevo'
      ]);
    }

    // Si el numero de expedientes es igual al numero de expedientes permitidos, se devuelve un mensaje de error.
    if ($expedientsCount >= $Nexp) {
      // Devolver mensaje de error en json si ya tiene el maximo de expedientes
      echo json_encode([
        'status' => 400,
        'message' => 'El usuario ya tiene el maximo de expedientes. No se puede agregar más.'
      ]);
      return;
    }

    try {
      // Verificar si el expediente con el numero, año y dependencia proporcionado existe para el id_user
      $query = $this->conexion->prepare('SELECT * FROM user_expedients WHERE numero_exp = :numero_exp AND anio_exp = :anio_exp AND dependencia = :dependencia AND id_user = :id_user');
      $query->execute([':numero_exp' => $this->caseNumber, ':anio_exp' => $this->caseYear, ':dependencia' => $this->dispatch, ':id_user' => $this->id_user]);
      $count = $query->rowCount();
      // echo json_encode($count);
      if ($count > 0) {
        // http response de que salio bien pero que no se cargo
        http_response_code(400);
        //retorno un json con el mensaje de expediente ya existe
        echo json_encode(['message' => 'El expediente ya existe.']);
        exit;
      }
    } catch (PDOException $e) {
      // Devolver mensaje de error en json
      echo json_encode([
        'status' => 500,
        'message' => 'Error al consultar el expediente para crear uno nuevo'
      ]);
    }

    try {
      // Crear el expediente
      $query = $this->conexion->prepare('INSERT INTO user_expedients (id_lista_despacho, numero_exp, anio_exp, dependencia, id_user) VALUES (NULL, :numero_exp, :anio_exp, :dependencia, :id_user)');
      $query->execute([':numero_exp' => $this->caseNumber, ':anio_exp' => $this->caseYear, ':dependencia' => $this->dispatch, ':id_user' => $this->id_user]);
      //retorna un json mensaje de exito
      http_response_code(200);
      echo json_encode(['message' => 'Expediente creado con exito.']);
    } catch (PDOException $e) {
      // Devolver una respuesta JSON de error
      http_response_code(500); // Establece el código de estado HTTP adecuado para un error interno del servidor
      echo json_encode(['message' => 'Error al crear el expediente API: ' . $e->getMessage()]);
    }
  }
}
