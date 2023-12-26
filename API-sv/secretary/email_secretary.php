<?php

class email_scretary
{
    private $conexion;
    private $email;

    public function __constructor($conexion, $email)
    {
        $this->conexion = $conexion;
        $this->email = $email;
    }

    public function email_secretary()
    {

        try {
            // consulto en la talba secretaries si el correo existe
            $sql = "SELECT secretaryId FROM secretaries WHERE Semail = :Semail";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':Semail', $this->email);
            $stmt->execute();
            $secretary = $stmt->fetch(PDO::FETCH_ASSOC);

            return $secretary;
        } catch (PDOException $e) {
            echo json_encode(['message' => 'Error en el secretary-email']);
        }
    }
}
