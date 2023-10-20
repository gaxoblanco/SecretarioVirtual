<?php

require_once '../vendor/autoload.php';

// Asegúrate de incluir las bibliotecas PHPMailer y Swift Mailer
require("class.phpmailer.php");
require("class.smtp.php");

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

        // Configura PHPMailer
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = $this->smtpServer;
        $mail->Port = $this->smtpPort;
        $mail->Username = $this->smtpUsername;
        $mail->Password = $this->smtpPassword;
        $mail->SMTPSecure = 'ssl';

        // Configura el correo
        $mail->setFrom('expedientes@secretariovirtual.ar', 'Secretario Virtual');
        $mail->addAddress($email);

        foreach ($secretaries as $secretary) {
            $mail->addCC($secretary['Semail']);
        }

        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $message;

        // Envía el mensaje
        if ($mail->send()) {
            echo "Mail enviado";
        } else {
            echo "Error al enviar el correo: " . $mail->ErrorInfo;
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
        $message .= "<h1>" . htmlentities("Notificación", ENT_QUOTES, 'UTF-8') . " de expedientes</h1>";
        $message .= "<h2>Estimado/a " . htmlentities($news['name'], ENT_QUOTES, 'UTF-8') . "</h2>";
        $message .= "<p>Se le notifica que los siguientes expedientes tuvieron movimientos:</p>";

        foreach ($news['expedients'] as $expedient) {
            $message .= "<table style='border-collapse: collapse; width: 100%;'>";
            $message .= "<tr>";
            $message .= "<td colspan='4' style='background-color: #37bbed; color: white; text-align: center; padding: 10px; border-top-left-radius: 20px; border-top-right-radius: 20px; font-size: 18px;'>";
            $message .= "Expediente: " . $expedient['numero_exp'] . "/" . $expedient['anio_exp'];
            $message .= "</td>";
            $message .= "</tr>";

            $message .= "<tr>";
            $message .= "<td style='width: 25%; text-align: center; padding: 10px;'>";
            $message .= "<h4>Carátula:</h4>";
            $message .= "<p>" . htmlentities($expedient['caratula'], ENT_QUOTES, 'UTF-8') . "</p>";
            $message .= "</td>";

            $message .= "<td style='width: 25%; text-align: center; padding: 10px;'>";
            $message .= "<h4>Reservado:</h4>";
            $message .= "<p>" . htmlentities($expedient['reservado'], ENT_QUOTES, 'UTF-8') . "</p>";
            $message .= "</td>";

            $message .= "<td style='width: 25%; text-align: center; padding: 10px;'>";
            $message .= "<h4>Dependencia:</h4>";
            $message .= "<p>" . htmlentities($expedient['dependencia'], ENT_QUOTES, 'UTF-8') . "</p>";
            $message .= "</td>";

            $message .= "<td style='width: 25%; text-align: center; padding: 10px;'>";
            $message .= "<h4>Tipo de lista:</h4>";
            $message .= "<p>" . htmlentities($expedient['tipo_lista'], ENT_QUOTES, 'UTF-8') . "</p>";
            $message .= "</td>";
            $message .= "</tr>";

            $message .= "<tr>";
            $message .= "<td colspan='4' style='padding: 10px; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;'>";
            $message .= "<h4 style='border-top: 4px solid #37bbed;'>Movimientos:</h4>";

            // Si $expedient['movimientos'] está vacío
            if (empty($expedient['movimientos'])) {
                $message .= "<div style='border: 1px solid #4e84a5; padding: 10px; margin: 10px; border-radius: 12px;'>";
                $message .= "<h5 style='font-weight: bold; font-size: 14px;'>El expediente aún no tiene movimientos</h5>";
                $message .= "</div>";
            }

            // Reorganiza $expedient['movimientos'] según la fecha de movimiento más actual
            usort($expedient['movimientos'], function ($a, $b) {
                return $a['fecha_movimiento'] <=> $b['fecha_movimiento'];
            });

            foreach ($expedient['movimientos'] as $movimiento) {
                $message .= "<div style='display: flex; justify-content: space-between; padding: 10px; margin: 10px;'>";
                $message .= "<h5 style='font-weight: bold; font-size: 14px; display: ruby; align-items: center;'><b>Fecha de movimiento: </b>" . htmlentities($movimiento['fecha_movimiento'], ENT_QUOTES, 'UTF-8') . "</h5>";

                // Valida que 'estado' tenga un valor
                $estado = empty($movimiento['estado']) ? "Sin estado" : htmlentities($movimiento['estado'], ENT_QUOTES, 'UTF-8');
                $message .= "<h5 style='font-weight: bold; font-size: 14px; display: ruby; align-items: center;'><b>Estado: </b>" . $estado . "</h5>";

                $message .= "</div>";

                $message .= "<h5 style='font-size: 14px; border-bottom: 4px solid aliceblue; padding-bottom: 10px;'>" . htmlentities($movimiento['texto'], ENT_QUOTES, 'UTF-8') . "</h5>";
                $message .= "<h5 style='font-size: 14px;'><b>Título: </b>" . htmlentities($movimiento['titulo'], ENT_QUOTES, 'UTF-8') . "</h5>";
                $message .= "<h5 style='font-size: 14px; border-bottom: 2px solid #4e84a5; margin-bottom: 60px; padding-bottom: 20px;'><b>Despacho: </b>" . htmlentities($movimiento['despacho'], ENT_QUOTES, 'UTF-8') . "</h5>";
            }

            $message .= "</td>";
            $message .= "</tr>";
            $message .= "</table>";
            $message .= "</div>";
        }
        $message .= "</body></html>";

        echo "body creado";

        return $message;
    }
}
