<?php
// buscar_cita_resultado.php

include 'conexion.php'; // Tu archivo de conexión

if (!isset($_GET['busqueda'])) {
    die("No se recibió ningún valor de búsqueda.");
}

$busqueda = $_GET['busqueda'];

// Si la búsqueda es solo números → buscar por ID
if (ctype_digit($busqueda)) {
    $sql = "
        SELECT c.*, p.nombre AS paciente_nombre
        FROM citas c
        JOIN pacientes p ON c.paciente_id = p.id
        WHERE p.id = $busqueda
    ";
} 
// Si es texto → buscar por nombre
else {
    $sql = "
        SELECT c.*, p.nombre AS paciente_nombre
        FROM citas c
        JOIN pacientes p ON c.paciente_id = p.id
        WHERE p.nombre LIKE '%$busqueda%'
    ";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de la Búsqueda</title>
</head>
<body>

<h2>Resultados para: <?= htmlspecialchars($busqueda) ?></h2>

<?php
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'>";
    echo "<tr>
            <th>ID Cita</th>
            <th>Paciente</th>
            <th>ID Paciente</th>
            <th>ID Médico</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Motivo</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['paciente_nombre']}</td>
                <td>{$row['paciente_id']}</td>
                <td>{$row['medico_id']}</td>
                <td>{$row['fecha']}</td>
                <td>{$row['hora']}</td>
                <td>{$row['motivo']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<p>No se encontraron citas.</p>";
}
?>

</body>
</html>