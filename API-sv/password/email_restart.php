<?php
require_once './vendor/autoload.php';

// incluir las bibliotecas PHPMailer y Swift Mailer
require("class.phpmailer.php");
require("class.smtp.php");



class email_restart
{
  private $conexion;
  private $email;
  private $token;
  // Agrega la configuración SMTP aquí
  private $smtpServer = 'c2361340.ferozo.com';
  private $smtpPort = 465;
  private $smtpUsername = 'expedientes@secretariovirtual.ar';
  private $smtpPassword = 'S3cretari@';

  public function __construct($conexion, $email, $token)
  {
    $this->conexion = $conexion;
    $this->email = $email;
    $this->token = $token;
  }

  private function buildEmailMessage()
  {
    $resetLink = 'http://secretariovirtual.ar/restablecer-contrasena?token=' . $this->token . '&email=' . $this->email;
    $message = 'Hola, para restablecer tu contraseña, haz clic en el siguiente enlace: <a href="' . $resetLink . '">' . $resetLink . '</a>';
    return $message;
  }

  public function write_restart()
  {
    // Crear un array temporal para almacenar el resultado final
    $mail = new PHPMailer(true);

    // valido que $email tenga un formato de email valido
    if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
      http_response_code(400);
      echo "El email no es valido" . $this->email;
      return;
    }

    try {
      //configuracion del servidor SMTP
      $mail->SMTPDebug = 0; // desactiva la depuración de la salida
      $mail->isSMTP();
      $mail->Host = $this->smtpServer;
      $mail->SMTPAuth = true;
      $mail->Username = $this->smtpUsername;
      $mail->Password = $this->smtpPassword;
      $mail->SMTPSecure = 'ssl';
      $mail->Port = $this->smtpPort;

      //configuracion del mensaje
      $mail->setFrom($this->smtpUsername, 'Secretario Virtual');
      $mail->addAddress($this->email);
      $mail->addReplyTo($this->smtpUsername, 'Secretario Virtual');
      $mail->isHTML(true);

      $mail->Subject = 'Restablecer contraseña';
      $mail->Body = $this->buildEmailMessage();
      $mail->AltBody = 'Este es el cuerpo en texto plano para clientes de correo no HTML';

      $mail->send();

      http_response_code(200);
      echo json_encode('envio correctamente');
    } catch (Exception $e) {
      echo "Error al enviar el correo: " . $e->getMessage();
    }
  }
}
