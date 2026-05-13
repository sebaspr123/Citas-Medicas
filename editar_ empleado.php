<?php include "plantilla.php"; ?>
<?php include "db_connect.php"; ?>

<?php
include "db_connect.php";
include "plantilla.php";

$id = intval($_GET['id']);
$errors = [];
$success = null;

// OBTENER DATOS DEL EMPLEADO
$stmt = $conn->prepare("SELECT * FROM empleado WHERE id_empleado = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$empleado = $stmt->get_result()->fetch_assoc();

if (!$empleado) {
    die("<div class='msg error'>Empleado no encontrado.</div>");
}

// GUARDAR CAMBIOS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre  = trim($_POST['nombre']);
    $cargo   = trim($_POST['cargo']);
    $telefono = trim($_POST['telefono']);
    $correo  = trim($_POST['correo']);

    if ($nombre === "" || $cargo === "") {
        $errors[] = "Nombre y cargo son obligatorios.";
    }

    if (empty($errors)) {
        $stmt2 = $conn->prepare("
            UPDATE empleado SET nombre=?, cargo=?, telefono=?, correo=? 
            WHERE id_empleado=?
        ");
        $stmt2->bind_param("ssssi", $nombre, $cargo, $telefono, $correo, $id);

        if ($stmt2->execute()) {
            $success = "Empleado actualizado correctamente.";
        } else {
            $errors[] = "Error al actualizar: " . $conn->error;
        }
    }
}

?>

<div class="card">
    <h2>Editar Empleado</h2>

    <?php if ($success): ?>
        <div class="msg success"><?= $success ?></div>
    <?php endif; ?>

    <?php foreach ($errors as $e): ?>
        <div class="msg error"><?= $e ?></div>
    <?php endforeach; ?>

    <form method="POST">
        <label>Nombre</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($empleado['nombre']) ?>">

        <label>Cargo</label>
        <input type="text" name="cargo" value="<?= htmlspecialchars($empleado['cargo']) ?>">

        <label>Teléfono</label>
        <input type="text" name="telefono" value="<?= htmlspecialchars($empleado['telefono']) ?>">

        <label>Correo</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($empleado['correo']) ?>">

        <button class="btn" type="submit">Guardar Cambios</button>
    </form>

    <a href="lista_empleados.php" class="btn" style="margin-top:15px;">Volver</a>
</div>

<?php include "footer.php"; ?>
