<?php
class user_create
{
    private $firstName;
    private $lastName;
    private $email;
    private $password;

    private $conexion;

    public function __construct($conexion, $firstName, $lastName, $email, $password)
    {
        $this->conexion = $conexion;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
    }

    public function createUser()
    {
        // Verificar si el correo electrónico ya existe en la base de datos
        $query = $this->conexion->prepare('SELECT email FROM users WHERE email = :email');
        $query->execute([':email' => $this->email]);
        $existingUser = $query->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            //devuelve mensaje de error en json
            echo json_encode([
                'status' => 400,
                'message' => 'El correo electrónico ya existe'
            ]);
            return;
        }
        // Generar el hash de la contraseña
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        // Guardar el nuevo usuario en la db con los datos recibidos del formulario
        $query = $this->conexion->prepare('INSERT INTO users (firstName, lastName, email, password) VALUES (:firstName, :lastName, :email, :password)');
        $query->execute([
            ':firstName' => $this->firstName,
            ':lastName' => $this->lastName,
            ':email' => $this->email,
            ':password' => $hashedPassword
        ]);

        //devolver un mensaje de éxito en json
        echo json_encode([
            'status' => 200,
            'message' => 'Usuario creado correctamente'
        ]);
        error_log('Mensaje de depuración: Algo sucede aquí', 0);
    }
}
