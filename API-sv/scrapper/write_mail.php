<?php

// This script is in charge of processing the step-by-step for sending the emails
// 1 - Get the array of mails to send with copy by user
// 2 - Create the header email with the data from the $result array and user_email
// 3 - Create the message of the email with the data from the $newsBy array
// 4 - Send the email, receive email, with copy array and the news array

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once 'db.php';

class write_mail
{
  private $conexion;
  private $newsBy;
  // Agrega la configuración SMTP aquí
  private $smtpServer;
  private $smtpPort;
  private $smtpUsername;
  private $smtpPassword;

  public function __construct($conexion, $newsBy)
  {
    $this->conexion = $conexion;
    $this->newsBy = $newsBy;
    // email credential
    $this->smtpServer = SMTP_SERVER;
    $this->smtpPort = SMTP_PORT;
    $this->smtpUsername = SMTP_USERNAME;
    $this->smtpPassword = SMTP_PASSWORD;
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

    // echo count($result);
    return $result;
  }

  // 1 - Get the array of mails
  // trae los Semail del secretaries que tenga el id_users = id_user haciendo una consulta SQL a la tabla secretaries
  private function getSecretaries($id_user)
  {
    try {
      $query = $this->conexion->prepare("SELECT Semail FROM secretaries WHERE id_users = :id_user");
      $query->bindParam(":id_user", $id_user, PDO::PARAM_INT);
      $query->execute();
      $secretaries = $query->fetchAll(PDO::FETCH_ASSOC);

      // Validar las direcciones de correo electrónico
      foreach ($secretaries as $key => $secretary) {
        if (!filter_var($secretary['Semail'], FILTER_VALIDATE_EMAIL)) {
          // Eliminar la dirección de correo electrónico no válida del array
          unset($secretaries[$key]);
          // echo "Correo electrónico inválido: " . $secretary['Semail'] . ". Se ha eliminado del array.<br>";
        }
      }

      return $secretaries;
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  }

  // crea el mail donde email es el destinatario, y con copia al array de secretaries
  // 2 - Create the header email
  private function createHeader($result)
  {
    foreach ($result as $news) {
      $email = $news['email'];
      $secretaries = $news['secretaries'];
      $this->sendMail($email, $secretaries, $news);
    }
    // echo "header creado";
  }

  // envia el mail con los datos de la notificacion
  // 4 - Send the email
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
      // echo "Mail enviado";
      return $result;
    } catch (Exception $e) {
      echo "Error al enviar el correo: " . $e->getMessage();
      return false;
    }
  }

  // 2 - Proces the with copy array
  private function createHeaders($secretaries)
  {
    $headers = [];

    foreach ($secretaries as $secretary) {
      $headers[] = $secretary['Semail'];
    }

    // echo "con copia creado";
    return $headers;
  }

  // crea el mensaje del mail con los datos de la notificacion estructurandolos en cards para que el email se ve mas prolijo al usuario
  // 3 - Create the message of the email
  private function createMessage($news)
  {
    $message = "<html><body class='color=black;>";
    $message .= "<h1>Notificación de expedientes</h1>";
    $message .= "<h2>Estimado/a " . $news['name'] . "</h2>";
    $message .= "<p>Se le notifica que los siguientes expedientes tuvieron movimientos:</p>";

    foreach ($news['expedients'] as $expedient) {
      // convierto valor numero de $expedient['dependencia'] a su version en texto con textDependenci
      // /dependencia_mixing.php se encuentra en el directorio principal del proyecto
      require_once __DIR__ . '/../dependencia_mixing.php';
      // valido que textDependencia se pudo importar correctamente
      if (function_exists('textDependencia')) {
        $expedient['dependencia'] = textDependencia($expedient['dependencia']);
      }

      // linea azul para separar los expedientes
      $message .= "<div style='height: 4px; background-color: #37bbed; margin: 30px; border-radius: 10px;'>";
      $message .= "</div>";
      // datos del expediente
      $message .= "<div style='color:black; max-width: 80%; border: 1px solid #a0bdcf; padding: 10px; margin: 0 auto; border-top: 20px solid #37bbed; border-radius: 12px;'>";
      $message .= "<h3>Expediente: " . $expedient['numero_exp'] . "/" . $expedient['anio_exp'] . "</h3>";
      $message .= "<div style='padding: 10px; margin: 10px;'>";
      $message .= "<h4>Carátula: " . $expedient['caratula'] . "</h4>";
      $message .= "<h4>Reservado: " . $expedient['reservado'] . "</h4>";
      // $message .= "<h4>Dependencia: " . $expedient['dependencia'] . "</h4>";
      // agrego dependencia y el valor lo proceso para usar caracteres especiales
      $message .= "<h4>Dependencia: " . htmlentities($expedient['dependencia'], ENT_QUOTES, 'UTF-8') . "</h4>";
      $message .= "<h4>Tipo de lista: " . $expedient['tipo_lista'] . "</h4>";
      $message .= "</div>";
      $message .= "<h4 style='font-size: medium;'>Movimiento:</h4>";


      //si $expedient['movimientos'] escribo el expediente aun no tiene movimientos
      if (!empty($expData['movimientos'])) { //si $expedient['movimientos'] esta vacio
        $message .= "<div style='border: 1px solid #a0bdcf; padding: 10px; margin: 10px; border-top: 10px solid #37bbed; border-radius: 12px;'>";
        $message .= "<h5 style='font-weight: bold; font-size:14px;'>El expediente aun no tiene movimientos</h5>";
        $message .= "</div>";
      } else {

        foreach ($expedient['movimientos'] as $movimiento) {
          $message .= "<div style='border: 1px solid #a0bdcf; margin: 10px; border-radius: 12px;'>";
          $message .= "<div style='border-bottom: 1px solid #a0bdcf; padding: 0 10px;'>";
          $message .= "<h5 style='font-weight: bold; font-size:14px; display: flex;'>Fecha de movimiento: " .
            "<p style='margin:0; margin-left: 10px;'>" . $movimiento['fecha_movimiento'] . "</p>" . "</h5>";
          $message .= "<h5 style='font-weight: bold; font-size:14px; display: flex;'>Estado: " .
            "<p style='margin:0; margin-left: 10px;'>" . $movimiento['estado'] . "</p>" . "</h5>";
          $message .= "</div>";

          $message .= "<h5 style='margin: 10px; font-weight: bold; font-size:14px;'>Texto: " .
            "<p style='padding: 0px 10px;'>" . $movimiento['texto'] . "</p>" . "</h5>";
          $message .= "<h5 style='margin: 10px; font-weight: bold; font-size:14px;'>Título: " .
            "<p style='padding: 0px 10px;'>" . $movimiento['titulo'] . "</p>" . "</h5>";
          // valido que $movimiento['despacho'] no este vacio
          if (!empty($movimiento['despacho'])) {
            $message .= "<h5 style='margin: 10px; font-weight: bold; font-size:14px;'>Despacho: " .
              "<p style='padding: 0px 10px;'>" . $movimiento['despacho'] . "</p>" . "</h5>";
          }
          $message .= "</div>";
        }
      }
      $message .= "</div>";
    }
    $message .= "</body></html>";

    return $message;
  }
}
