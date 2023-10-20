<?php
class user_update
{
    private $conexion;
    private $id;
    private $firstName;
    private $lastName;
    private $email;
    private $password;


    public function __construct($conexion, $id, $firstName = null, $lastName = null, $email = null, $password = null)
    {
        $this->conexion = $conexion;
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
    }

    public function updateUser()
    {
        try {
            // Verificar si el usuario con el ID proporcionado existe en la base de datos
            $query = $this->conexion->prepare('SELECT COUNT(*) FROM users WHERE id_user = :id_user');
            $query->execute([':id_user' => $this->id]);
            $count = $query->fetchColumn();

            if ($count === 0) {
                //devuelve mensaje de error en json
                echo json_encode([
                    'status' => 400,
                    'message' => 'El usuario no existe'
                ]);
                return;
            }

            // Actualizar los datos del usuario en la base de datos según los campos proporcionados
            $updateQuery = 'UPDATE users SET';
            $updateData = [];

            if (!is_null($this->firstName)) {
                $updateQuery .= ' firstName = :firstName,';
                $updateData[':firstName'] = $this->firstName;
            }
            if (!is_null($this->lastName)) {
                $updateQuery .= ' lastName = :lastName,';
                $updateData[':lastName'] = $this->lastName;
            }
            if (!is_null($this->email)) {
                // Verificar si el nuevo email ya existe en la base de datos para otro usuario
                $existingQuery = $this->conexion->prepare('SELECT id_user FROM users WHERE email = :email AND id_user <> :id_user');
                $existingQuery->execute([':email' => $this->email, ':id_user' => $this->id]);
                $existingUser = $existingQuery->fetch(PDO::FETCH_ASSOC);

                if ($existingUser) {
                    //devuelve mensaje de error en json
                    echo json_encode([
                        'status' => 400,
                        'message' => 'El correo electrónico ya existe'
                    ]);
                    return;
                    return;
                }

                $updateQuery .= ' email = :email,';
                $updateData[':email'] = $this->email;
            }
            if (!is_null($this->password)) {
                $updateQuery .= ' password = :password,';
                $updateData[':password'] = $this->password;
            }

            // Eliminar la coma final de la consulta de actualización
            $updateQuery = rtrim($updateQuery, ',');

            // Agregar la condición WHERE para actualizar solo el usuario con el ID correspondiente
            $updateQuery .= ' WHERE id_user = :id_user';
            $updateData[':id_user'] = $this->id;

            // Ejecutar la consulta de actualización
            $updateStatement = $this->conexion->prepare($updateQuery);
            $updateStatement->execute($updateData);

            //devolver un mensaje de éxito en json
            echo json_encode([
                'status' => 200,
                'message' => 'Usuario actualizado correctamente'
            ]);
        } catch (PDOException $e) {
            //devuelve mensaje de error en json
            echo json_encode([
                'status' => 500,
                'message' => 'Error actualizando el usuario: ' . $e->getMessage()
            ]);
        }
    }
}
