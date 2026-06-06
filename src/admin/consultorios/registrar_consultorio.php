<?php include "../../includes/plantilla.php"; ?>
<?php
soloAdmin(); // Solo administradores pueden registrar consultorios

ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "../../config/db_connect.php";

$errors  = [];
$success = null;
$numero  = $piso = $ubicacion = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero    = trim($_POST['numero']    ?? '');
    $piso      = trim($_POST['piso']      ?? '');
    $ubicacion = trim($_POST['ubicacion'] ?? '');

    if ($numero === '') $errors[] = "El número de consultorio es obligatorio.";
    if ($piso   === '') $errors[] = "El piso es obligatorio.";
    if (!is_numeric($piso) || intval($piso) < 1) $errors[] = "El piso debe ser un número mayor a 0.";

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO consultorio (numero, piso, ubicacion) VALUES (?, ?, ?)");
        $pisoInt = intval($piso);
        $stmt->bind_param("sis", $numero, $pisoInt, $ubicacion);
        if ($stmt->execute()) {
            $success = "Consultorio registrado correctamente.";
            $numero = $piso = $ubicacion = "";
        } else {
            $errors[] = "Error al guardar: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<div class="card mx-auto" style="max-width: 600px;">
  <div style="padding: 30px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4"
        style="margin: -30px -30px 20px -30px; padding: 20px 30px;">
      <i class="bi bi-door-closed-fill"></i> Registrar Consultorio
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

    <form method="post">
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="numero" class="form-label">Número de consultorio *</label>
          <input type="text" class="form-control" id="numero" name="numero"
                 value="<?= htmlspecialchars($numero) ?>" placeholder="Ej: 101" required>
        </div>
        <div class="col-md-6">
          <label for="piso" class="form-label">Piso *</label>
          <input type="number" class="form-control" id="piso" name="piso" min="1"
                 value="<?= htmlspecialchars($piso) ?>" required>
        </div>
      </div>
      <div class="mb-3">
        <label for="ubicacion" class="form-label">Ubicación / Sector</label>
        <input type="text" class="form-control" id="ubicacion" name="ubicacion"
               value="<?= htmlspecialchars($ubicacion) ?>" placeholder="Ej: Bloque A">
      </div>

      <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check-circle"></i> Registrar Consultorio
        </button>
        <a href="../consultorios/lista_consultorios.php" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Ver listado
        </a>
      </div>
    </form>
  </div>
</div>

<?php include "../../includes/footer.php"; ?>



