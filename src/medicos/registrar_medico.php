<?php include "../includes/plantilla.php"; ?>
<?php
soloAdmin(); // Solo administradores pueden registrar médicos

ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "../config/db_connect.php";

$errors   = [];
$success  = null;
$nombre   = $apellido = $cedula = $telefono = $correo = $id_especialidad = "";

// Cargar especialidades para el select
$especialidades = $conn->query("SELECT id_especialidad, nombre FROM especialidad ORDER BY nombre");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre          = trim($_POST['nombre']          ?? '');
    $apellido        = trim($_POST['apellido']        ?? '');
    $cedula          = trim($_POST['cedula']          ?? '');
    $telefono        = trim($_POST['telefono']        ?? '');
    $correo          = trim($_POST['correo']          ?? '');
    $id_especialidad = trim($_POST['id_especialidad'] ?? '');

    if ($nombre   === '') $errors[] = "El nombre es obligatorio.";
    if ($apellido === '') $errors[] = "El apellido es obligatorio.";
    if ($cedula   === '') $errors[] = "La cédula es obligatoria.";
    if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo inválido.";

    // Cédula duplicada
    if (empty($errors)) {
        $stmtCheck = $conn->prepare("SELECT id_medico FROM medico WHERE cedula = ?");
        $stmtCheck->bind_param("s", $cedula);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) $errors[] = "Ya existe un médico con esa cédula.";
        $stmtCheck->close();
    }

    if (empty($errors)) {
        $espId = $id_especialidad !== '' ? intval($id_especialidad) : null;
        $stmt  = $conn->prepare("INSERT INTO medico (nombre, apellido, cedula, telefono, correo, id_especialidad) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $nombre, $apellido, $cedula, $telefono, $correo, $espId);
        if ($stmt->execute()) {
            $success = "Médico registrado correctamente.";
            $nombre = $apellido = $cedula = $telefono = $correo = $id_especialidad = "";
        } else {
            $errors[] = "Error al guardar: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<div class="card mx-auto" style="max-width: 750px;">
  <div style="padding: 30px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4"
        style="margin: -30px -30px 20px -30px; padding: 20px 30px;">
      <i class="bi bi-stethoscope"></i> Registrar Médico
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
          <label for="nombre" class="form-label">Nombre *</label>
          <input type="text" class="form-control" id="nombre" name="nombre"
                 value="<?= htmlspecialchars($nombre) ?>" required>
        </div>
        <div class="col-md-6">
          <label for="apellido" class="form-label">Apellido *</label>
          <input type="text" class="form-control" id="apellido" name="apellido"
                 value="<?= htmlspecialchars($apellido) ?>" required>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="cedula" class="form-label">Cédula *</label>
          <input type="text" class="form-control" id="cedula" name="cedula"
                 value="<?= htmlspecialchars($cedula) ?>" required>
        </div>
        <div class="col-md-6">
          <label for="telefono" class="form-label">Teléfono</label>
          <input type="text" class="form-control" id="telefono" name="telefono"
                 value="<?= htmlspecialchars($telefono) ?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="correo" class="form-label">Correo</label>
          <input type="email" class="form-control" id="correo" name="correo"
                 value="<?= htmlspecialchars($correo) ?>">
        </div>
        <div class="col-md-6">
          <label for="id_especialidad" class="form-label">Especialidad</label>
          <select class="form-select" id="id_especialidad" name="id_especialidad">
            <option value="">— Sin asignar —</option>
            <?php while ($esp = $especialidades->fetch_assoc()): ?>
            <option value="<?= $esp['id_especialidad'] ?>"
              <?= $id_especialidad == $esp['id_especialidad'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($esp['nombre']) ?>
            </option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>

      <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check-circle"></i> Registrar Médico
        </button>
        <a href="/medicos/lista_medicos.php" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Ver listado
        </a>
      </div>
    </form>
  </div>
</div>

<?php include "../includes/footer.php"; ?>


