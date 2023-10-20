<?php
// Importar la clase PJF Lista de Despachos   

// Importar el cliente SQL
require_once 'db.php';

// Importar clase Listas_Despacho
require_once '../pjf-listas-despacho/PJF_Listas_Despacho.php';
// Crear instancia de la clase Listas_Despacho
$pjf = new PJF_Listas_Despacho();

// Definir la fecha desde hasta como la fecha de hoy menos DIAS_ATRAS dias
define('DIAS_ATRAS', 30);
$fecha_fin = date('Y-m-d');
$fecha_inicio = date('Y-m-d', strtotime("-" . DIAS_ATRAS . " days"));

// Leer el archivo tipos_listas_y_dependencias.json y convertirlo a array
$tipos_listas_y_dependencias = json_decode(file_get_contents('tipos_listas_y_dependencias.json'), true);
// Separar los tipos de lista y dependencias en variables
$tipos_listas = $tipos_listas_y_dependencias['tipos_listas'];
$dependencias = $tipos_listas_y_dependencias['dependencias'];

// Iterar por cada dependencia
foreach ($dependencias as $id_dependencia => $nombre_dependencia) {
    echo "Dependencia: {$nombre_dependencia} ({$id_dependencia})\n";
    // Iterar por cada tipo de lista
    foreach ($tipos_listas as $id_tipo_lista => $nombre_tipo_lista) {
        echo "Tipo de Lista: {$nombre_tipo_lista} ({$id_tipo_lista})\n";
        // Obtener Listas de Despacho
        $listas_despacho = $pjf->getListasDespachoPorRangoFechaYTipo($fecha_inicio, $fecha_fin, $id_dependencia, $id_tipo_lista);
        // Iterar por cada Lista de Despacho
        foreach ($listas_despacho as $lista_despacho) {
            // Obtener el número de expediente
            $id_lista_despacho = $lista_despacho['id'];
            $dependencia = $lista_despacho['dependencia'];
            $tipo_lista = $lista_despacho['tipo_lista'];
            // Iterar por cada expediente
            echo "Lista de Despacho: {$id_lista_despacho} Dependencia: {$dependencia} Tipo de Lista: {$tipo_lista}\n";
            foreach ($listas_despacho['expedientes'] as $expediente) {
                $orden = $expediente['orden'];
                $numero_expediente = $expediente['numero'];
                $anio_expediente = $expediente['anio'];
                $caratula = $expediente['caratula'];
                $reservado = $expediente['reservado'];
                $movimientos = $expediente['movimientos'];
                // Existe el expediente en nuestra bd?
                $sql = "SELECT * FROM expedientes WHERE dependencia = :dependencia AND tipo_lista = :tipo_lista AND numero_expediente = :numero_expediente AND anio_expediente = :anio_expediente";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':numero_expediente', $numero_expediente, PDO::PARAM_INT);
                $stmt->bindParam(':anio_expediente', $anio_expediente, PDO::PARAM_INT);
                $stmt->execute();
                // Si no existe, lo insertamos
                if ($stmt->rowCount() == 0) {
                    $sql = "INSERT INTO expedientes (id_lista_despacho, dependencia, tipo_lista, numero_expediente, anio_expediente, caratula, reservado) VALUES (:id_lista_despacho, :dependencia, :tipo_lista, :numero_expediente, :anio_expediente, :caratula, :reservado)";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bindParam(':id_lista_despacho', $id_lista_despacho, PDO::PARAM_INT);
                    $stmt->bindParam(':dependencia', $dependencia, PDO::PARAM_INT);
                    $stmt->bindParam(':tipo_lista', $tipo_lista, PDO::PARAM_INT);
                    $stmt->bindParam(':numero_expediente', $numero_expediente, PDO::PARAM_INT);
                    $stmt->bindParam(':anio_expediente', $anio_expediente, PDO::PARAM_INT);
                    $stmt->bindParam(':caratula', $caratula, PDO::PARAM_STR);
                    $stmt->bindParam(':reservado', $reservado, PDO::PARAM_BOOL);
                    $stmt->execute();
                    echo "Expediente insertado: {$numero_expediente}/{$anio_expediente}\n";
                    // Obtener el id del expediente insertado
                    $id_expediente = $conexion->lastInsertId();
                    // Iterar por cada movimiento
                    foreach ($movimientos as $movimiento) {
                        $fecha = $movimiento['fecha'];
                        $hora = $movimiento['hora'];
                        $descripcion = $movimiento['descripcion'];
                        $sql = "INSERT INTO movimientos (id_expediente, fecha, hora, descripcion) VALUES (:id_expediente, :fecha, :hora, :descripcion)";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bindParam(':id_expediente', $id_expediente, PDO::PARAM_INT);
                        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
                        $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
                        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
                        $stmt->execute();
                        echo "Movimiento insertado: {$fecha} {$hora} {$descripcion}\n";
                    }
                }
                // Si existe, consultamos los movimientos, y verificamos si hubo cambios
                else {
                    $expediente = $stmt->fetch(PDO::FETCH_ASSOC);
                    $id_expediente = $expediente['id'];
                    $sql = "SELECT * FROM movimientos WHERE id_expediente = :id_expediente ORDER BY fecha DESC, hora DESC LIMIT 1";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bindParam(':id_expediente', $id_expediente, PDO::PARAM_INT);
                    $stmt->execute();
                    // Si no hay movimientos, insertamos todos los movimientos
                    if ($stmt->rowCount() == 0) {
                        echo "No hay movimientos para el expediente {$numero_expediente}/{$anio_expediente}\n";
                        foreach ($movimientos as $movimiento) {
                            $fecha = $movimiento['fecha'];
                            $hora = $movimiento['hora'];
                            $descripcion = $movimiento['descripcion'];
                            $sql = "INSERT INTO movimientos (id_expediente, fecha, hora, descripcion) VALUES (:id_expediente, :fecha, :hora, :descripcion)";
                            $stmt = $conexion->prepare($sql);
                            $stmt->bindParam(':id_expediente', $id_expediente, PDO::PARAM_INT);
                            $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
                            $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
                            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
                            $stmt->execute();
                            echo "Movimiento insertado: {$fecha} {$hora} {$descripcion}\n";
                        }
                    }
                    // Si no esta vacio, comparar con el actual e insertar los que falten
                    else {
                        $movimientos_actuales = $stmt->fetchAll();
                        // Verificar que cada elemento de movimientos este en movimientos actuales, sino, insertarlo
                        foreach ($movimientos as $movimiento) {
                            echo "Verificando movimiento: {$movimiento['fecha']} {$movimiento['hora']} {$movimiento['descripcion']}\n";
                            // Validar si existe un $movimiento_actual->fecha cuya fecha coincida con el $movimiento->fecha
                            $existe_movimiento = false;
                            foreach ($movimientos_actuales as $movimiento_actual) {
                                if ($movimiento_actual['fecha'] == $movimiento['fecha']) {
                                    $existe_movimiento = true;
                                    echo "Movimiento ya existe: {$movimiento['fecha']} {$movimiento['hora']} {$movimiento['descripcion']}\n";
                                    break;
                                }
                            }
                            // Si no existe, insertarlo
                            if (!$existe_movimiento) {
                                $fecha = $movimiento['fecha'];
                                $hora = $movimiento['hora'];
                                $descripcion = $movimiento['descripcion'];
                                $sql = "INSERT INTO movimientos (id_expediente, fecha, hora, descripcion) VALUES (:id_expediente, :fecha, :hora, :descripcion)";
                                $stmt = $conexion->prepare($sql);
                                $stmt->bindParam(':id_expediente', $id_expediente, PDO::PARAM_INT);
                                $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
                                $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
                                $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
                                $stmt->execute();
                                echo "Movimiento insertado: {$fecha} {$hora} {$descripcion}\n";
                            }
                        }
                    }
                }
            }
        }
    }
}
