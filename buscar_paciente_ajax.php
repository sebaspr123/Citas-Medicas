<?php
include "db_connect.php";

header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');
$data = [];

if (strlen($q) >= 2) {
    $q = $conn->real_escape_string($q);

    $sql = "SELECT id_paciente, nombre, apellido, cedula
            FROM paciente
            WHERE nombre LIKE '%$q%'
               OR apellido LIKE '%$q%'
               OR CONCAT(nombre, ' ', apellido) LIKE '%$q%'
               OR cedula LIKE '%$q%'
            ORDER BY nombre, apellido
            LIMIT 10";

    $res = $conn->query($sql);

    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }
    }
}

echo json_encode($data);
exit;