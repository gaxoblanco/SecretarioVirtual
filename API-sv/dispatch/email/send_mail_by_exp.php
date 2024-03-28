<?php
require_once './vendor/autoload.php';

require("class.phpmailer.php");
require("class.smtp.php");
// crea la class sendMailByExp que obtiene el id_exp y user_id para enviar el email
class send_mail_by_exp
{
  private $conexion;
  private $id_exp;
  private $user_id;
  private $smtpServer = 'c2361340.ferozo.com';
  private $smtpPort = 465;
  private $smtpUsername = 'expedientes@secretariovirtual.ar';
  private $smtpPassword = 'S3cretari@';
  private $expData;

  public function __construct($conexion, $id_exp, $user_id)
  {
    $this->conexion = $conexion;
    $this->id_exp = $id_exp;
    $this->user_id = $user_id;
  }

  // sendMail envia el mail con la informacion del expediente
  public function sendMail()
  {
    $email = $this->getUserEmail();
    $secretariesEmail = $this->getSecretariesEmail($this->user_id);
    $expData = $this->getExpediente($this->id_exp);

    //uso el email del usuario para enviar el mail y los secreatriesEmail para enviar con copia
    $subject = "Notificacion de Expediente con historial";
    $message = $this->createMessage($expData);
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    // Comprobación si hay secretarios antes de agregar CC
    if (!empty($secretariesEmail)) {
      $headers .= "CC: " . $secretariesEmail . "\r\n";
    }

    // Configura la conexión SMTP
    $transport = new Swift_SmtpTransport($this->smtpServer, $this->smtpPort, 'ssl');
    $transport->setUsername($this->smtpUsername);
    $transport->setPassword($this->smtpPassword);


    // Crea el objeto Swift_Mailer
    $mailer = new Swift_Mailer($transport);

    // Crea el mensaje
    $messageObj = (new Swift_Message($subject))
      ->setFrom(['expedientes@secretariovirtual.ar' => 'Secretario Virtual'])
      ->setTo([$email])
      ->setCc($secretariesEmail)  // Añade los destinatarios con copia directamente aquí
      ->setBody($message, 'text/html');

    // try {
    //     $result = $mailer->send($messageObj);
    //     // envio un mensaje de exito json_encode
    //     echo json_encode(['message' => 'Email del expediente nuevo enviado']);
    //     return $result;
    // } catch (Exception $e) {
    //     // Manejo de la excepción
    //     http_response_code(500); // Establece el código de estado HTTP adecuado para un error interno del servidor
    //     echo json_encode(['error' => 'Error interno del servidor al enviar el correo', 'details' => $e->getMessage()]);
    // }
    // devuelve un array con los valores usador para armar el email
    $this->expData = [
      'numero_exp' => $expData['numero_exp'],
      'anio_exp' => $expData['anio_exp'],
      'caratula' => $expData['caratula'],
      'reservado' => $expData['reservado'],
      'dependencia' => $expData['dependencia'],
      'tipo_lista' => $expData['tipo_lista'],
      'movimientos' => $expData['movimientos']
    ];
  }

  // funcion para obener el email del usuario
  public function getUserEmail()
  {
    try {
      $query = "SELECT email FROM users WHERE id_user = :user_id";
      // preparo la consulta
      $stmt = $this->conexion->prepare($query);
      // ejecuto la consulta
      $stmt->execute([':user_id' => $this->user_id]);
      // guardo el resultado de la consulta en la variable $result
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      // valido que el resultado no sea null
      if ($result == null) {
        // Devolver una respuesta JSON de error
        http_response_code(404); // Establece el código de estado HTTP adecuado para un error interno del servidor
        echo json_encode(['message' => 'No se encontro el usuario']);
        exit;
      }

      // guardo el email del usuario en la variable $email
      $email = $result['email'];
      // devuelvo el valor de result en json_encode
      echo json_encode("getUserEmail", $result);
      // retorno el valor del email
      return $email;
    } catch (PDOException $e) {
      // Devolver una respuesta JSON de error
      http_response_code(500); // Establece el código de estado HTTP adecuado para un error interno del servidor
      echo json_encode(['message' => 'Error al obtener el email del usuario 1' . $e->getMessage()]);
    }
  }
  // funcion para obtener el email de los los secretarios con el id_users en la tabla secreatries (puede ser mas de 1 o ninguno)
  public function getSecretariesEmail($user_id)
  {
    try {
      $query = "SELECT Semail FROM secretaries WHERE id_users = :user_id";
      // preparo la consulta
      $stmt = $this->conexion->prepare($query);
      // ejecuto la consulta
      $stmt->execute([':user_id' => $user_id]);
      // guardo el resultado de la consulta en la variable $result
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // valido que el resultado no sea null
      if ($result == null) {
        // Devolver una respuesta JSON de error
        http_response_code(404); // Establece el código de estado HTTP adecuado para un error interno del servidor
        echo json_encode(['message' => 'No se encontraron secretarios']);
        exit;
      }

      // guardo el email del usuario en la variable $email
      $emails = array_column($result, 'email');
      $email = implode(', ', $emails);

      // devuelvo el valor de result en json_encode
      echo json_encode("getSecretariesEmail", $result);

      // retorno el valor del email
      return $email;
    } catch (PDOException $e) {
      // Devolver una respuesta JSON de error
      http_response_code(500); // Establece el código de estado HTTP adecuado para un error interno del servidor
      echo json_encode(['message' => 'Error al obtener el Semail del secretario ' . $e->getMessage()]);
    }
  }

  // funcion para obtener los modivimientos en la tabla user_exp_move segun id_exp
  public function getMoves($id_exp)
  {
    try {
      $query = "SELECT * FROM user_exp_move WHERE id_exp = :id_exp";
      // preparo la consulta
      $stmt = $this->conexion->prepare($query);
      // ejecuto la consulta
      $stmt->execute([':id_exp' => $id_exp]);
      // guardo el resultado de la consulta en la variable $result
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // valido que el resultado no sea null
      if ($result == null) {
        // Devolver una respuesta JSON de error
        http_response_code(404); // Establece el código de estado HTTP adecuado para un error interno del servidor
        echo json_encode(['message' => 'No se encontraron movimientos']);
        exit;
      }
      // creo el campo movimientos en el $expData con el array de movimientos
      $this->expData['movimientos'] = $result;

      // devuelvo el valor de result en json_encode
      echo json_encode("getMoves", $result);
      // retorno el valor del email
      return $result;
    } catch (PDOException $e) {
      // Devolver una respuesta JSON de error
      echo json_encode(['message' => 'Error al obtener los movimientos del expediente ' . $e->getMessage()]);
    }
  }

  // funcion para obtener toda la informacion del expediente en la tabla user_expedients
  public function getExpediente($id_exp)
  {
    try {
      $query = "SELECT * FROM user_expedients WHERE id_exp = :id_exp";
      // preparo la consulta
      $stmt = $this->conexion->prepare($query);
      // ejecuto la consulta
      $stmt->execute([':id_exp' => $id_exp]);
      // guardo el resultado de la consulta en la variable $result
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      // valido que el resultado no sea null
      if ($result == null) {
        // Devolver una respuesta JSON de error
        http_response_code(404); // Establece el código de estado HTTP adecuado para un error interno del servidor
        echo json_encode(['message' => 'No se encontro el expediente para enviar el email']);
        exit;
      }

      // retorno los datos del exp
      return $result;
    } catch (PDOException $e) {
      // Devolver una respuesta JSON de error
      http_response_code(500); // Establece el código de estado HTTP adecuado para un error interno del servidor
      echo json_encode(['message' => 'Error al obtener el expediente ' . $e->getMessage()]);
    }
  }

  private function createMessage($expData)
  {
    $message = "<html><body>";
    $message .= "<h1>Notificación de expedientes</h1>";
    $message .= "<p>Tenemos informacion sobre el expediente cargado.</p>";
    // linea azul para separar los expedientes
    $message .= "<div style='height: 4px; background-color: #37bbed; margin: 30px; border-radius: 10px;'>";
    $message .= "</div>";
    // div con los datos del expediente
    $message .= "<div style='max-width: 80%; border: 1px solid #a0bdcf; padding: 10px; margin: 0 auto; border-top: 20px solid #37bbed; border-radius: 12px;'>";
    $message .= "<h3>Expediente: " . $expData['numero_exp'] . "/" . $expData['anio_exp'] . "</h3>";
    $message .= "<div style='padding: 10px; margin: 10px;'>";
    $message .= "<h4>Carátula: " . $expData['caratula'] . "</h4>";
    $message .= "<h4>Reservado: " . $expData['reservado'] . "</h4>";
    $message .= "<h4>Dependencia: " . $expData['dependencia'] . "</h4>";
    $message .= "<h4>Tipo de lista: " . $expData['tipo_lista'] . "</h4>";
    $message .= "</div>";
    $message .= "<h4 style='font-size: medium;'>Movimientos:</h4>";


    //si $expedient['movimientos'] escribo el expediente aun no tiene movimientos
    if (empty($expData['movimientos'])) { //si $expedient['movimientos'] esta vacio
      $message .= "<div style='border: 1px solid #a0bdcf; padding: 10px; margin: 10px; border-top: 10px solid #37bbed; border-radius: 10px;'>";
      $message .= "<h5 style='font-weight: bold; font-size:14px;'>El expediente aun no tiene movimientos</h5>";
      $message .= "</div>";
    }

    // itero sobre el array de movimientos obtenido de getMoves($this->$id_exp)
    if (!empty($expData['movimientos'])) {
      foreach ($expData['movimientos'] as $movimiento) {

        // foreach  as $movimiento) {
        $message .= "<div style='border: 1px solid #a0bdcf; margin: 10px; border-radius: 12px;'>";
        $message .= "<div style='border-bottom: 1px solid #a0bdcf; padding: 0 10px;'>";
        $message .= "<h5 style='font-weight: bold; font-size:14px; display: flex;'>Fecha de movimiento: " .
          "<p style='margin:0; margin-left: 10px;'>" . $movimiento['fecha_movimiento'] . "</p>" . "</h5>";
        $message .= "<h5 style='font-weight: bold; font-size:14px; display: flex;'>Estado: " .
          "<p style='margin:0; margin-left: 10px;'>" . $movimiento['estado'] . "</p>" . "</h5>";
        $message .= "</div>";

        $message .= "<h5 style='margin: 10px; font-weight: bold; font-size:14px;'>Texto: " .
          "<p>" . $movimiento['texto'] . "</p>" . "</h5>";
        $message .= "<h5 style='margin: 10px; font-weight: bold; font-size:14px;'>Título: " .
          "<p>" . $movimiento['titulo'] . "</p>" . "</h5>";
        $message .= "<h5 style='margin: 10px; font-weight: bold; font-size:14px;'>Despacho: " .
          "<p>" . $movimiento['despacho'] . "</p>" . "</h5>";
        $message .= "</div>";
      }
    }
    $message .= "</div>";
    $message .= "</body></html>";

    return $message;
  }
}
