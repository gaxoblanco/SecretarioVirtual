<?php
require_once './config.php';
// clase para consultar el estado de una suscripción obteniendo el id de la suscripción
class get_by_id
{
  private $conexion;
  private $id_subscription;
  private $ACCES_TOKEN = 'TEST-5763954744698204-040908-f7c5b76430483ea6f5f3ef24a640493c-1751465896';
  private $id_user;
  // creo la funcion status_by_id para hacer get a https://api.mercadopago.com/preapproval/{id} esperando un json

  public function __construct($conexion, $id_subscription, $id_user)
  {
    $this->conexion = $conexion;
    $this->id_subscription = $id_subscription;
    $this->id_user = $id_user;
  }

  // consulto en la base de datos si el id_user existe y tiene el mismo id_subscription
  public function get_by_id()
  {
    try {
      // Verificar en la tabla mercado_pago que el user_id y el id_subscription coincidan y obtengo los datos
      $query = $this->conexion->prepare('SELECT COUNT(*) FROM mercado_pago WHERE user_id = :user_id AND id_subscription = :id_subscription');
      $query->execute([':user_id' => $this->id_user, ':id_subscription' => $this->id_subscription]);
      $count = $query->fetchColumn();


      if ($count === 0) {
        //devuelve mensaje de error en json
        echo json_encode([
          'status' => 400,
          'message' => 'El usuario no existe', $count
        ]);
        return;
      }
    } catch (PDOException $e) {
      //devuelve mensaje de error en json
      echo json_encode([
        'status' => 500,
        'message' => 'Error obteniendo el usuario: ' . $e->getMessage()
      ]);
    }

    // apuntamos a la url: https://api.mercadopago.com/preapproval/{id}
    $url = 'https://api.mercadopago.com/preapproval/' . $this->id_subscription;
    // preparamos el header para enviar un .json con el ACCES_TOKEN de .env
    $header = array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $this->ACCES_TOKEN
    );

    // hacemos el GET con curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // devolvemos el json
    echo json_encode($response);
    return;
  }
}
