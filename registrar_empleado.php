<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once "db_connect.php";

$errors = [];
$success = null;

$nombre = $cargo = $telefono = $correo = "";

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $cargo = trim($_POST['cargo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    // Validaciones mínimas
    if ($nombre === '') $errors[] = "El nombre es obligatorio.";
    if ($cargo === '') $errors[] = "El cargo es obligatorio.";

    if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL))
        $errors[] = "Correo inválido.";

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO empleado (nombre, cargo, telefono, correo)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $nombre, $cargo, $telefono, $correo);

        if ($stmt->execute()) {
            $success = "Empleado registrado correctamente.";
            $nombre = $cargo = $telefono = $correo = "";
        } else {
            $errors[] = "Error al guardar: " . $conn->error;
        }

        $stmt->close();
    }
}

include "plantilla.php";
?>

<div class="card mx-auto" style="max-width: 700px;">
    <div style="padding: 30px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4" style="margin: -30px -30px 20px -30px; padding: 20px 30px;">
        <i class="bi bi-briefcase"></i> Registrar Empleado
    </h2>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($e) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>

    <form method="post" class="form">

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label"><i class="bi bi-person"></i> Nombre *</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                       value="<?= htmlspecialchars($nombre) ?>" required>
            </div>

            <div class="col-md-6">
                <label for="cargo" class="form-label"><i class="bi bi-briefcase-fill"></i> Cargo *</label>
                <input type="text" class="form-control" id="cargo" name="cargo"
                       value="<?= htmlspecialchars($cargo) ?>" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="telefono" class="form-label"><i class="bi bi-telephone"></i> Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono"
                       value="<?= htmlspecialchars($telefono) ?>">
            </div>

            <div class="col-md-6">
                <label for="correo" class="form-label"><i class="bi bi-envelope"></i> Correo</label>
                <input type="email" class="form-control" id="correo" name="correo"
                       value="<?= htmlspecialchars($correo) ?>">
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-check-circle"></i> Registrar Empleado
            </button>
            <a href="lista_empleados.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Ver listado
            </a>
        </div>
    </form>
    </div>
</div>


<?php include "footer.php"; ?>
