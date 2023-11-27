<?php


require_once 'getIdExp.php';
require_once 'existExp.php';
require_once 'upDispatch.php';
require_once 'upDispatchMoves.php';
// Incluir la clase write_mail
require_once 'email/send_mail_by_exp.php';

// clase ejecutable desde el add_dispatch una vez cargado el expediente 
class add_dispatch_upData
{
    private $conexion;
    private $userId;
    private $caseNumber;
    private $caseYear;
    private $dispatch;

    public function __construct($conexion, $userId, $caseNumber, $caseYear, $dispatch)
    {
        $this->conexion = $conexion;
        $this->userId = $userId;
        $this->caseNumber = $caseNumber;
        $this->caseYear = $caseYear;
        $this->dispatch = $dispatch;
    }

    public function addDispatchUpData()
    {

        // consulto en la tabla expedientes para saber si ya existe y obtengo los datos del expediente
        $existExp = new existExpediente($this->conexion, $this->caseNumber, $this->caseYear, $this->dispatch);
        $exist = $existExp->existExpediente();
        // echo json_encode($exist);

        // si devuelve != de null 
        if ($exist != false) {
            try {
                // guardo el id del nuevo expediente cargado en la variable $id_exp
                $getIdExp = new getIdExpediente($this->conexion, $this->userId, $this->caseNumber, $this->caseYear, $this->dispatch);
                $id_exp = $getIdExp->getIdExp(); // = id_exp FROM user_expedients
                // echo json_encode(['id_exp' => $id_exp]);

                // actualizo los datos del expediente cargado en la tabla user_expedients
                $upDispatch = new upDispatch($this->conexion, $id_exp, $exist);
                $upDispatch->upDispatch();

                // actualizo los movimientos del expediente en la tabla user_exp_move
                // $upDispatchMoves = new upDispatchMoves($this->conexion, $id_exp, $exist);
                // $upDispatchMoves->upDispatchMoves();

                //--- tomo el $id_exp para enviar el email - desde el email obtengo los correos 
                // envio el mail con la informacion del expediente
                $sendMail = new send_mail_by_exp($this->conexion, $id_exp, $this->userId);
                $sendMail->sendMail();
            } catch (PDOException $e) {
                // Devolver una respuesta JSON de error
                http_response_code(500); // Establece el código de estado HTTP adecuado para un error interno del servidor
                echo json_encode(['message' => 'Error al actualizar el nuevoExpediente API: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['message' => 'No se encontro el expediente' . $exist]);
        }
    }
}
