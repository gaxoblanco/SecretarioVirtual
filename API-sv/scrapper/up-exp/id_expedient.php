<?php


//obtengo el id_expediente de la tabla expedientes que coincida con numero_exp y anio_exp
function getIdExpediente($numero_exp, $anio_exp, $dependencia, $conexion)
{
    try {
        $query = $conexion->prepare('SELECT id_expediente FROM expedientes WHERE numero_expediente = :numero_exp AND anio_expediente = :anio_exp AND dependencia = :dependencia');
        $query->bindParam(':numero_exp', $numero_exp, PDO::PARAM_STR);
        $query->bindParam(':anio_exp', $anio_exp, PDO::PARAM_STR);
        $query->bindParam(':dependencia', $dependencia, PDO::PARAM_STR);
        $query->execute();

        $resultado = $query->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $id_expediente = $resultado['id_expediente'];
            return $id_expediente;
        } else {
            return null; // El expediente no se encontró en la base de datos
        }
    } catch (PDOException $e) {
        return null; // Error en la consulta
    }
}
