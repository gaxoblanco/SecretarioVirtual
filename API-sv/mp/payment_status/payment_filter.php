<?php

/*
Escript que se encargar de consultar en la tabla mercado_pago el status
con este escript se pausa la funcionalidades de Secretario Virtual
*/

/**** Para Implementarlo ****
    // Valido que el usuario tenga una suscripcion activa usando getPaymentStatus()
    include_once './mp/payment_status/payment_filter.php';
    $paymentFilter = new PaymentFilter($this->conexion, $this->id_user);
    $paymentStatus = $paymentFilter->getPaymentStatus();

    // Si el usuario no tiene una suscripcion activa, se devuelve un mensaje de error.
    if ($paymentStatus != 'approved') {
      // Devolver mensaje de error en json
      http_response_code(400);
      echo json_encode('El usuario no tiene una suscripcion activa.');
      return;
    }
 **/

class PaymentFilter
{
  private $conexion;
  private $user_id;
  public function __construct($conexion, $user_id)
  {
    $this->conexion = $conexion;
    $this->user_id = $user_id;
  }

  // obtengo el status de la tabla mercado_pago con el user_id
  public function getPaymentStatus()
  {
    try {
      $query = $this->conexion->prepare('SELECT status FROM mercado_pago WHERE user_id = :user_id');
      $query->execute([':user_id' => $this->user_id]);
      $status_subscription = $query->fetchColumn();
      return $status_subscription;;
    } catch (\Throwable $th) {
      echo json_encode([
        'status' => 500,
        'message' => 'Error obteniendo el status: ' . $th->getMessage()
      ]);
    }
  }
}
