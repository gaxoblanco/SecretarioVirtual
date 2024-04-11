<?php

// importo el .env para las variables de entorno
require_once 'new-plan/post_plan.php';

// Al crear el usuario se ejecuta create_plan que obtiene el reason === tipo suscripcion
// Obtengo el application_id y lo guardo en la tabla mercado_pago asociandolo con el usuario

class create_plan
{
  private $conexion;
  private $user_id;
  private $subscription;

  public function __construct($conexion, $subscription, $user_id)
  {
    $this->conexion = $conexion;
    $this->subscription = $subscription;
    $this->user_id = $user_id;
  }

  // Envio al usuario a MercadoPago para que realice el pago
  public function create_plan()
  {
    // ejecuto la funcion post_plan pasando el tipo de subscription/reason
    $post_plan = new post_plan();
    $response = $post_plan->post_plan($this->subscription);

    // consulto si el campo application_id existe en la respuesta de tipo json
    $response = json_decode($response, true);
    if (array_key_exists('application_id', $response)) {
      // si existe sanitizo los datos y los guardo en la tabla mercado_pago asociando con el usuario
      $application_id = $response['application_id']; // en este punto asocio mp con el usuario de sv
      $this->user_id = $_SESSION['user_id'];
      $reason = $response['reason'];
      $date_created = $response['date_created'];
      $last_modified = $response['last_modified'];
      // date_created viene "date_created": "2024-04-10T11:00:48.823-04:00" valido que sea una fecha y la paso de string a date para alamacenarla
      $date_created = date('Y-m-d H:i:s', strtotime($date_created));
      $last_modified = date('Y-m-d H:i:s', strtotime($last_modified));
      $init_point = $response['init_point']; // si el usuario no paga en ese momento le muestro un button de pago con esta url

      // preparo la consulta para insertar los datos en la tabla mercado_pago
      try {
        $sql = "INSERT INTO mercado_pago (application_id, user_id, reason, date_created, last_modified, init_point) VALUES (:application_id, :user_id, :reason, :date_created, :last_modified)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':application_id', $application_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':date_created', $date_created);
        $stmt->bindParam(':last_modified', $last_modified);
        $stmt->bindParam(':init_point', $init_point);
        $stmt->execute();
      } catch (PDOException $e) {
        // si falla devuelvo un mensaje de error
        echo json_encode(['message' => 'Error al crear el plan de suscripción']);
      }

      // devuelvo el valor de init_point para enviar al usuario a pagar
      return $init_point;
    } else {
      // si no existe devuelvo un mensaje de error
      echo json_encode(['message' => 'Error al crear el plan de suscripción']);
    }
  }
}
