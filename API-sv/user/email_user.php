<?php

class email_user
{
    private $conexion;
    private $email;

    public function __constructor($conexion, $email)
    {
        $this->conexion = $conexion;
        $this->email = $email;
    }

    public function email_user()
    {
        // valido que el correo sea una cadena de caracteres
        if (!is_string($this->email)) {
            echo json_encode(['message' => 'El correo no es una cadena de caracteres' . $this->email]);
            exit;
        }

        try {
            // consulto en la talba secretaries si el correo existe
            $sql = "SELECT id_user FROM users WHERE email = :email";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user;
        } catch (PDOException $e) {
            echo json_encode(['message' => 'Error en el user-email']);
        }
    }
}
