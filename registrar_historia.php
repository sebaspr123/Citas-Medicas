<?php include "plantilla.php"; ?>
<?php include("db_connect.php"); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registrar Historia Clínica</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
<h2>Registrar Historia Clínica</h2>

<form method="POST">

    <select name="id_cita" required>
        <option value="">Seleccione Cita</option>
        <?php
        $result = $conn->query("
            SELECT cita.id_cita, paciente.nombre, paciente.apellido, cita.fecha
            FROM cita
            INNER JOIN paciente ON cita.id_paciente = paciente.id_paciente
        ");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id_cita']}'>Cita #{$row['id_cita']} - {$row['nombre']} {$row['apellido']} - {$row['fecha']}</option>";
        }
        ?>
    </select>

    <textarea name="diagnostico" placeholder="Diagnóstico"></textarea>
    <textarea name="tratamiento" placeholder="Tratamiento"></textarea>
    <textarea name="observaciones" placeholder="Observaciones"></textarea>

    <button type="submit" name="guardar">Guardar</button>
</form>

<?php
if (isset($_POST['guardar'])) {
    $sql = "INSERT INTO historia_clinica(id_cita, diagnostico, tratamiento, observaciones)
            VALUES ('{$_POST['id_cita']}', '{$_POST['diagnostico']}',
                    '{$_POST['tratamiento']}', '{$_POST['observaciones']}')";

    echo $conn->query($sql)
        ? "<p class='success'>Historia clínica registrada correctamente</p>"
        : "<p class='error'>Error: " . $conn->error . "</p>";
}
?>

</div>
</body>
</html>
