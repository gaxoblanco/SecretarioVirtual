
<?php
// Este archivo se encarga de obtener los movimientos de los expedientes segun id_exp
// trabaja en la tabla user_exp_move:  	id_move 	id_expediente 	fecha_movimiento 	estado 	texto 	titulo 	despacho 	id_exp

// devuelve un array con los movimientos de un expediente
class get_moves
{
    private $conexion;
    private $id_exp;

    public function __construct($conexion, $id_exp)
    {
        $this->conexion = $conexion;
        $this->id_exp = $id_exp;
    }

    // consulto en la tabla user_exp_move los movimientos que concidan con el id_exp
    public function getMoves()
    {
        try {
            $query = $this->conexion->prepare('SELECT * FROM user_exp_move WHERE id_exp = :id_exp');
            $query->execute([':id_exp' => $this->id_exp]);
            $moves = $query->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($moves);
            return $moves;
        } catch (PDOException $e) {
            echo 'Error al obtener los movimientos: ' . $e->getMessage();
        }
    }
}
