<?php // user_password_change

class user_password_change
{
    private $conexion;
    private $id;
    private $password;
    private $token;

    public function __construct($conexion, $id, $password, $token)
    {
        $this->conexion = $conexion;
        $this->id = $id;
        $this->password = $password;
        $this->token = $token;
    }

    public function passwordChange()
    {
        try {
            // Verificar si el usuario con el ID proporcionado existe en la base de datos
            $query = $this->conexion->prepare('SELECT COUNT(*) FROM users WHERE id_user = :id_user');
            $query->execute([':id_user' => $this->id]);
            $count = $query->fetchColumn();

            if ($count === 0) {
                //devuelve mensaje de error en json
                http_response_code(400);
                echo json_encode('El usuario no existe');
                return;
            }

            if ($this->password === null || $this->password === '') {
                //devuelve mensaje de error en json
                http_response_code(400);
                echo json_encode('El password no puede ser nulo');
                return;
            }

            // Encripto el password con password_hash
            $hashPassword = password_hash($this->password, PASSWORD_DEFAULT);


            try {
                // Actualizo el campo password en la tabla users que concidan con el id y token
                $query = $this->conexion->prepare('UPDATE users SET password = :password WHERE id_user = :id_user AND token = :token');
                $query->execute([':password' => $hashPassword, ':id_user' => $this->id, ':token' => $this->token]);
                $count = $query->rowCount();

                if ($count === 0) {
                    //devuelve mensaje de error en json
                    http_response_code(400);
                    echo json_encode('userId y token no coinciden');
                    return;
                }
            } catch (PDOException $e) {
                //devuelve mensaje de error en json
                http_response_code(500);
                echo json_encode('Error al encontrar el userId y token');
                return;
            }

            //devuelve mensaje de exito en json
            http_response_code(200);
            echo json_encode('Password actualizada');
        } catch (PDOException $e) {
            //devuelve mensaje de error en json
            http_response_code(500);
            echo json_encode('Error al actualizar la contraseña');
        }
    }
}
