<?php
// Una vez cargado el expediente procedo a actualizarlo y si existe en la base de datos le envio la actualizacion

// Start en function lastMove
// Valido que el expediente exista en la tabla expedientes
// Si existe llamo a la function lastMoveUserExpediente y obtengo los movimientos del expediente
// Actualizo con function upExpAndMove tanto el expediente como los movimientos en las tablas asociadas al usuario

include_once __DIR__ . '/../../services/clean_and_encode.php';
include_once __DIR__ . '/../last-move/last_move_user_exp.php';
include_once __DIR__ . '/../last-move/up_exp_and_move.php';
include_once __DIR__ . '/../last-move/send_mail_last_move.php';

class last_move
{
  private $conexion;
  private $id_user;
  private $caseNumber;
  private $caseYear;
  private $dispatch;
  private $lastMove;
  private $expediente;
  private $lastMoveUserExp;

  public function __construct($conexion, $id_user, $caseNumber, $caseYear, $dispatch)
  {
    $this->conexion = $conexion;
    $this->id_user = $id_user;
    $this->caseNumber = $caseNumber;
    $this->caseYear = $caseYear;
    $this->dispatch = $dispatch;
  }

  public function lastMove()
  {
    // echo json_encode(['message' => 'Class LastMove']);
    try {

      try {
        // consulto en la tabla expedientes si el expediente ya existe
        $query = $this->conexion->prepare('SELECT * FROM expedientes WHERE numero_expediente = :numero_expediente AND anio_expediente = :anio_expediente AND dependencia = :dependencia');
        $query->execute([':numero_expediente' => $this->caseNumber, ':anio_expediente' => $this->caseYear, ':dependencia' => $this->dispatch]);
        $count = $query->rowCount();
        // si el expediente no existe en la tabla expedientes
        if ($count == 0) {
          // Devolver mensaje de "error" en json
          http_response_code(200);
          echo json_encode('El expediente aun no existe, no se envia correo con la ultima actualizacion');
          return;
        }
        // si el expediente existe en la tabla expedientes obtengo el id_expediente y se lo envio a la funcion lastMoveUserExpediente
        $this->expediente = $query->fetch();
        $id_expediente = $this->expediente['id_expediente'];
        $this->lastMoveUserExp = lastMoveUserExpediente($this->conexion, $this->lastMove, $id_expediente);
        // imprimo lastMoveUserExp
        // echo json_encode($this->lastMoveUserExp);
      } catch (\Throwable $th) {
        // Devolver mensaje de error en json
        http_response_code(500);
        echo json_encode('Error al consultar el expediente');
      }
      try {
        // upExpAndMove
        upExpAndMove($this->conexion, $this->id_user, $this->expediente);
      } catch (\Throwable $th) {
        // Devolver mensaje de error en json
        http_response_code(500);
        echo json_encode('Error al actualizar el expediente y enviar el correo con la ultima actualizacion');
      }

      try {
        // envio un correo con el ultimo movimiento del expediente recien cargado y actualizado 
        sendMailLastMove($this->conexion, $this->id_user, $this->expediente, $this->lastMoveUserExp);
      } catch (\Throwable $th) {
        // Devolver mensaje de error en json
        http_response_code(500);
        echo json_encode('Error al enviar el correo con la ultima actualizacion');
      }


      // Devolver mensaje de "exito" en json
      // http_response_code(200);
      echo json_encode('El expediente fue actualizado y se envio un correo con la ultima actualizacion');
    } catch (\Throwable $th) {
      // Devolver mensaje de error en json
      http_response_code(500);
      echo json_encode('Error al actualizar el expediente y enviar el correo con la ultima actualizacion');
    }
  }
}
