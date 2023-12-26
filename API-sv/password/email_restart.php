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
  private $name;
  // Agrega la configuración SMTP aquí
  private $smtpServer = 'c2361340.ferozo.com';
  private $smtpPort = 465;
  private $smtpUsername = 'expedientes@secretariovirtual.ar';
  private $smtpPassword = 'S3cretari@';

  public function __construct($conexion, $email, $token, $name)
  {
    $this->conexion = $conexion;
    $this->email = $email;
    $this->token = $token;
    $this->name = $name;
  }

  private function buildEmailMessage()
  {
    // $resetLink = 'https://secretariovirtual.ar/reset-password/:token=' . $this->token . '/:email=' . $this->email;
    $resetLink = 'https://secretariovirtual.ar/reset-password/' . ($this->token) . '/' . ($this->email);
    // $message = 'Hola, para restablecer tu contraseña, haz clic en el siguiente enlace: <a href="' . $resetLink . '">' . "secretariovirtual.ar/restablecer" . '</a>';
    $message = "<html><body style='color:#090909;'>";
    $message .= "<h1 style='color:#090909;'>Solicitud para restablecer la contraseña</h1>";
    $message .= "<h2 style='color:#090909;'>Estimado/a " . $this->name . "</h2>";

    $message .= "<div style='height: 4px; background-color: #37bbed; margin: 30px; border-radius: 10px;'></div>";
    $message .= "<div style='max-width: 80%; border: 1px solid #a0bdcf; padding: 10px; margin: 0 auto; border-top: 20px solid #37bbed; border-radius: 12px;'>";
    $message .= "<h3 style='color:#090909;'>Restablecimiento de Contraseña</h3>";
    $message .= "<p style='color:#090909;'>Para restablecer tu contraseña, haz clic en el siguiente enlace:</p>";
    $message .= "<a href='" . $resetLink . "'>secretariovirtual.ar/restablecer</a>";
    $message .= "<p style='color:#090909;'>Si no solicitaste restablecer tu contraseña, puedes ignorar este correo.</p>";
    $message .= "</div>";
    $message .= "</body></html>";

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

      $mail->Subject = '=?UTF-8?B?' . base64_encode('Restablecer contraseña') . '?=';
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
