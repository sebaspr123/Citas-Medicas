<?php include "plantilla.php"; ?>
<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
include "db_connect.php";

$errors = [];
$success = null;

$nombre = "";
$descripcion = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($nombre === '') {
        $errors[] = "El nombre de la especialidad es obligatorio.";
    }

    if (empty($errors)) {

        // verificar duplicado
        $stmt = $conn->prepare("SELECT id_especialidad FROM especialidad WHERE nombre = ?");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Ya existe una especialidad con ese nombre.";
        }

        $stmt->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO especialidad (nombre, descripcion) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $descripcion);

        if ($stmt->execute()) {
            $success = "Especialidad registrada correctamente.";
            $nombre = "";
            $descripcion = "";
        } else {
            $errors[] = "Error al guardar: " . $conn->error;
        }

        $stmt->close();
    }
}

?>

<div class="card mx-auto" style="max-width: 700px;">
    <div style="padding: 30px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4" style="margin: -30px -30px 20px -30px; padding: 20px 30px;">
        <i class="bi bi-tags"></i> Registrar Especialidad
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

    <form method="post" novalidate>
        <div class="mb-3">
            <label for="nombre" class="form-label"><i class="bi bi-tag"></i> Nombre de la especialidad *</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label"><i class="bi bi-file-text"></i> Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($descripcion) ?></textarea>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-check-circle"></i> Guardar Especialidad
            </button>
            <a href="lista_especialidades.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Ver listado
            </a>
        </div>
    </form>
    </div>
</div>

<?php include "footer.php"; ?>