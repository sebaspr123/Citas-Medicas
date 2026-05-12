<?php
header("Content-Type: application/json; charset=UTF-8");
include "db_connect.php";

$sql = "SELECT 
            c.id_cita,
            c.fecha,
            c.hora,
            p.nombre AS paciente,
            m.nombre AS medico
        FROM cita c
        INNER JOIN paciente p ON c.id_paciente = p.id_paciente
        INNER JOIN medico m ON c.id_medico = m.id_medico
        ORDER BY c.fecha, c.hora";

$res = $conn->query($sql);

$citas = [];

while ($row = $res->fetch_assoc()) {

    // Normalizar fecha para que siempre sea yyyy-mm-dd
    $fecha = trim($row["fecha"]);

    // Si trae hora, quitarla
    if (strpos($fecha, " ") !== false) {
        $fecha = explode(" ", $fecha)[0];
    }

    // dd/mm/yyyy → yyyy-mm-dd
    if (strpos($fecha, "/") !== false) {
        list($d, $m, $a) = explode("/", $fecha);
        $fecha = "$a-$m-$d";
    }
    // dd-mm-yyyy → yyyy-mm-dd
    else if (strlen(explode("-", $fecha)[0]) == 2) {
        list($d, $m, $a) = explode("-", $fecha);
        $fecha = "$a-$m-$d";
    }

    // Normalizar a formato estándar
    list($y, $m, $d) = explode("-", $fecha);
    $fecha = "$y-" . str_pad($m, 2, "0", STR_PAD_LEFT) . "-" . str_pad($d, 2, "0", STR_PAD_LEFT);

    $citas[] = [
        "id_cita"  => $row["id_cita"],
        "fecha"    => $fecha,
        "hora"     => $row["hora"],
        "paciente" => $row["paciente"],
        "medico"   => $row["medico"]
    ];
}

echo json_encode($citas, JSON_UNESCAPED_UNICODE);
