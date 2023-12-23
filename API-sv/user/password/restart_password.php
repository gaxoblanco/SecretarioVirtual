<?php
// Con el correo electronico actualizo el token del usuario a un token que caduca en 24hs

class user_password_restart
{
    private $conexion;
    private $email;
    private $token;
    private $token_expiration;

    public function __construct($conexion, $email)
    {
        $this->conexion = $conexion;
        $this->email = $email;
        $this->token = bin2hex(random_bytes(16));
        $this->token_expiration = date('Y-m-d H:i:s', strtotime('+1 day'));
    }

    public function passwordRestart()
    {
        // genero un has para el token
        $token_hash = password_hash($this->token, PASSWORD_DEFAULT);

        try {
            // Verificar que el correo electrónico exista en la base de datos
            $sql = "SELECT id_user FROM users WHERE email = :email";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            //$user existe y tiene el id_user
            if ($user) {
                try {
                    // actualizo en la tabla users el token y la fecha de expiracion
                    $sql = "UPDATE users SET token = :token, token_expiration = :token_expiration WHERE id_user = :id_user";
                    $stmt = $this->conexion->prepare($sql);
                    $stmt->bindParam(':token', $token_hash);
                    $stmt->bindParam(':token_expiration', $this->token_expiration);
                    $stmt->bindParam(':id_user', $user['id_user']);
                    $stmt->execute();

                    // envio el correo electronico con el token
                    echo json_encode(['message' => 'Email sent' . $token_hash . $this->token_expiration . $user['id_user']]);
                } catch (PDOException $e) {
                    echo json_encode(['message' => 'Error en el token']);
                }
            }


            if (!$user) {
                echo json_encode(['message' => 'Error el correo del usuario no existe en la base de datos']);
            }

            // consulto en la talba secretaries si el correo existe
            $sql = "SELECT secretaryId FROM secretaries WHERE Semail = :Semail";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':Semail', $this->email);
            $stmt->execute();
            $secretary = $stmt->fetch(PDO::FETCH_ASSOC);

            try {
                // si el Semail existe actualizo el token y la fecha de expiracion
                $sql = "UPDATE secretaries SET token = :token, token_expiration = :token_expiration WHERE secretaryId = :secretaryId";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bindParam(':token', $token_hash);
                $stmt->bindParam(':token_expiration', $this->token_expiration);
                $stmt->bindParam(':secretaryId', $secretary['secretaryId']);
                $stmt->execute();

                // envio el correo electronico con el token
                // echo json_encode($stmt->rowCount() . 'Email sent' . $token_hash . $this->token_expiration . $secretary['secretaryId']);

                // cuerpo email
                $body = '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Restablecer contraseña</title>
                </head>
                <body>
                    <p>Estimado usuario,</p>
                    <p>Para restablecer su contraseña, haga clic en el siguiente enlace:</p>
                    <a href="http://localhost:8080/secretary/reset-password.php?token=' . $this->token . '&email=' . $this->email . '">Restablecer contraseña</a>
                    <p>Si no solicitó restablecer su contraseña, ignore este correo electrónico.</p>
                    <p>Saludos cordiales,</p>
                    <p>El equipo de soporte</p>
                </body>
                </html>';

                // cabecera email
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                // envio email
                mail($this->email, 'Restablecer contraseña', $body, $headers);
            } catch (PDOException $e) {
                echo json_encode(['message' => 'Error en el token de secretaries']);
            }
        } catch (PDOException $e) {
            echo json_encode(['message' => 'Error el correo no existe en la base de datos']);
        }
    }
}
