<?php
require_once './config.php';
// clase para consultar el estado de una suscripción obteniendo el id de la suscripción
class get_by_id
{
  private $conexion;
  private $ACCES_TOKEN = 'TEST-5548694823343472-041412-4dd92592ca1e30d38ecfd4053f041c33-1751465896';
  private $user_id;
  private $preapproval_id;
  // creo la funcion status_by_id para hacer get a https://api.mercadopago.com/preapproval/{id} esperando un json

  public function __construct($conexion, $user_id)
  {
    $this->conexion = $conexion;
    $this->user_id = $user_id;
  }

  // consulto en la base de datos si el id_user existe y tiene el mismo preapproval_id
  public function getIdUser()
  {
    try {
      // obtengo de la tabla mercado_pago el preapproval_id del elemento que contenga el mismo user_id
      $query = "SELECT preapproval_id FROM mercado_pago WHERE user_id = :user_id";
      $stmt = $this->conexion->prepare($query);
      $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_STR);
      $stmt->execute();
      // guardo el preapproval_id en la variable preapproval_id
      $this->preapproval_id = $stmt->fetch(PDO::FETCH_ASSOC)['preapproval_id'];

      // valido que preapproval_id no sea null
      if ($this->preapproval_id == null) {
        echo json_encode([
          'status' => 400,
          'message' => 'El preapproval_id no existe'
        ]);
        return;
      }
      // echo json_encode($this->preapproval_id);
    } catch (PDOException $e) {
      //devuelve mensaje de error en json
      echo json_encode([
        'status' => 500,
        'message' => 'Error obteniendo el preapproval_id: ' . $e->getMessage()
      ]);
      return;
    }
  }

  public function mpGetById()
  {
    // obtengo el preapproval_id del usuario en la tabla mercado_pago
    $this->getIdUser();

    // Utilizo searchData para buscar el preapproval_id en la api de mercado pago
    require_once './mp/search_data.php';
    $searchData = new searchData($this->conexion, $this->ACCES_TOKEN);
    $searchData->searchData(50, $this->preapproval_id);

    // Devuelvo el json $searchData
    echo json_encode($searchData);
  }
}
