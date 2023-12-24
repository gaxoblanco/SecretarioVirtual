<?php
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

    public function email_contructor($conexion, $email, $token)
    {
        $this->conexion = $conexion;
        $this->email = $email;
        $this->token = $token;
    }

    private function buildEmailMessage()
    {
        $resetLink = 'http://tudominio.com/restablecer-contrasena?token=' . $this->token . '&email=' . $this->email;
        $message = 'Hola, para restablecer tu contraseña, haz clic en el siguiente enlace: <a href="' . $resetLink . '">' . $resetLink . '</a>';
        return $message;
    }

    public function write_restart()
    {

        $subject = 'Recuperación de Contraseña';
        $message = $this->buildEmailMessage();
        $headers = "From: " . $this->smtpUsername . "\r\n" .
            "X-Mailer: PHP/" . phpversion();

        // Configuración adicional para utilizar SMTP
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

        // Configura la conexión SMTP
        $transport = new Swift_SmtpTransport($this->smtpServer, $this->smtpPort, 'ssl');
        $transport->setUsername($this->smtpUsername);
        $transport->setPassword($this->smtpPassword);


        // Crea el objeto Swift_Mailer
        $mailer = new Swift_Mailer($transport);
        // Crea el mensaje
        $messageObj = (new Swift_Message($subject))
            ->setFrom(['expedientes@secretariovirtual.ar' => 'Secretario Virtual'])
            ->setTo($this->email)
            ->setCc($headers)
            ->setBody($message, 'text/html');

        // Envía el mensaje


        try {
            $result = $mailer->send($messageObj);
            echo "email enviado";
            return $result;
        } catch (Exception $e) {
            echo "Error al enviar el correo: " . $e->getMessage();
            return false;
        }
    }
}
