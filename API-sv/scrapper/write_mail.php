<?php

require_once './vendor/autoload.php';

class write_mail
{
  private $conexion;
  private $newsBy;
  // Agrega la configuración SMTP aquí
  private $smtpServer = 'c2361340.ferozo.com';
  private $smtpPort = 465;
  private $smtpUsername = 'expedientes@secretariovirtual.ar';
  private $smtpPassword = 'S3cretari@';

  public function __construct($conexion, $newsBy)
  {
    $this->conexion = $conexion;
    $this->newsBy = $newsBy;
  }

  // por el array $newsBy, recorre cada entrada y agrega el array secretaries
  public function write()
  {
    // Crear un array temporal para almacenar el resultado final
    $result = [];

    // Por cada iteración, obtenemos los secretaries y los fusionamos con $news
    foreach ($this->newsBy as $news) {
      $secretaries = $this->getSecretaries($news['id_user']);
      $news['secretaries'] = $secretaries;
      $result[] = $news; // Agregamos $news al resultado final
    }

    // Llama a la funcion createHeader y le pasa el array result como parametro
    $this->createHeader($result);

    echo count($result);
    return $result;
  }

  // trae los Semail del secretaries que tenga el id_users = id_user haciendo una consulta SQL a la tabla secretaries
  private function getSecretaries($id_user)
  {
    try {
      $query = $this->conexion->prepare("SELECT Semail FROM secretaries WHERE id_users = :id_user");
      $query->bindParam(":id_user", $id_user, PDO::PARAM_INT);
      $query->execute();
      $secretaries = $query->fetchAll(PDO::FETCH_ASSOC);

      return $secretaries;
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  }

  // crea el mail donde email es el destinatario, y con copia al array de secretaries

  private function createHeader($result)
  {
    foreach ($result as $news) {
      $email = $news['email'];
      $secretaries = $news['secretaries'];
      $this->sendMail($email, $secretaries, $news);
    }
    echo "header creado";
  }

  // envia el mail con los datos de la notificacion
  private function sendMail($email, $secretaries, $news)
  {
    $subject = "Notificación de expedientes";
    $message = $this->createMessage($news);
    $headers = $this->createHeaders($secretaries);

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
      ->setCc($headers)
      ->setBody($message, 'text/html');

    // Envía el mensaje
    try {
      $result = $mailer->send($messageObj);
      echo "Mail enviado";
      return $result;
    } catch (Exception $e) {
      echo "Error al enviar el correo: " . $e->getMessage();
      return false;
    }
  }

  // crea los headers del mail siendo $email el destinatario y $secretaries[Semail] los destinatarios en copia
  private function createHeaders($secretaries)
  {
    $headers = [];

    foreach ($secretaries as $secretary) {
      $headers[] = $secretary['Semail'];
    }

    echo "con copia creado";
    return $headers;
  }

  // crea el mensaje del mail con los datos de la notificacion estructurandolos en cards para que el email se ve mas prolijo al usuario

  private function createMessage($news)
  {
    $message = "<html><body>";
    $message .= "<h1>Notificación de expedientes</h1>";
    $message .= "<h2>Estimado/a " . $news['name'] . "</h2>";
    $message .= "<p>Se le notifica que los siguientes expedientes tuvieron movimientos:</p>";

    foreach ($news['expedients'] as $expedient) {
      $message .= "<div style='height: 4px; background-color: #37bbed; margin: 30px; border-radius: 10px;'>";
      $message .= "</div>";
      $message .= "<div style='max-width: 80%; border: 1px solid #a0bdcf; padding: 10px; margin: 0 auto; border-top: 20px solid #37bbed; border-radius: 12px;'>";
      $message .= "<h3>Expediente: " . $expedient['numero_exp'] . "/" . $expedient['anio_exp'] . "</h3>";
      $message .= "<div style='padding: 10px; margin: 10px;'>";
      $message .= "<h4>Carátula: " . $expedient['caratula'] . "</h4>";
      $message .= "<h4>Reservado: " . $expedient['reservado'] . "</h4>";
      $message .= "<h4>Dependencia: " . $expedient['dependencia'] . "</h4>";
      $message .= "<h4>Tipo de lista: " . $expedient['tipo_lista'] . "</h4>";
      $message .= "</div>";
      $message .= "<h4 style='font-size: medium;'>Movimientos:</h4>";


      //si $expedient['movimientos'] escribo el expediente aun no tiene movimientos
      if (empty($expedient['movimientos'])) { //si $expedient['movimientos'] esta vacio
        $message .= "<div style='border: 1px solid #a0bdcf; padding: 10px; margin: 10px; border-top: 10px solid #37bbed;'>";
        $message .= "<h5 style='font-weight: bold; font-size:14px;'>El expediente aun no tiene movimientos</h5>";
        $message .= "</div>";
      }

      foreach ($expedient['movimientos'] as $movimiento) {
        $message .= "<div style='border: 1px solid #a0bdcf; margin: 10px; border-radius: 12px;'>";
        $message .= "<div style='border-bottom: 1px solid #a0bdcf; padding: 0 10px;'>";
        $message .= "<h5 style='font-weight: bold; font-size:14px; display: flex;'>Fecha de movimiento: " .
          "<p style='margin:0; margin-left: 10px;'>" . $movimiento['fecha_movimiento'] . "</p>" . "</h5>";
        $message .= "<h5 style='font-weight: bold; font-size:14px; display: flex;'>Estado: " .
          "<p style='margin:0; margin-left: 10px;'>" . $movimiento['estado'] . "</p>" . "</h5>";
        $message .= "</div>";

        $message .= "<h5 style='margin: 10px; font-weight: bold; font-size:14px;'>Texto: " .
          "<p style='margin: 8px; margin-left: 12px;'>" . $movimiento['texto'] . "</p>" . "</h5>";
        $message .= "<h5 style='margin: 10px; font-weight: bold; font-size:14px;'>Título: " .
          "<p style='margin: 8px; margin-left: 12px;>" . $movimiento['titulo'] . "</p>" . "</h5>";
        $message .= "<h5 style='margin: 10px; font-weight: bold; font-size:14px;'>Despacho: " .
          "<p style='margin: 8px; margin-left: 12px;>" . $movimiento['despacho'] . "</p>" . "</h5>";
        $message .= "</div>";
      }

      $message .= "</div>";
    }
    $message .= "</body></html>";

    echo "body creado";

    return $message;
  }
}
