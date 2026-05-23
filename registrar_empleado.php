<?php include "plantilla.php"; ?>
<?php
soloAdmin(); // Solo administradores pueden registrar usuarios del sistema

ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "db_connect.php";

$errors  = [];
$success = null;
$nombre  = $username = $password = $rol = $telefono = $correo = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre   = trim($_POST['nombre']   ?? '');
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');
  $rol      = trim($_POST['rol']      ?? '');
  $telefono = trim($_POST['telefono'] ?? '');
  $correo   = trim($_POST['correo']   ?? '');

  // Validaciones
  if ($nombre   === '') $errors[] = "El nombre es obligatorio.";
  if ($username === '') $errors[] = "El nombre de usuario es obligatorio.";
  if ($password === '') $errors[] = "La contraseña es obligatoria.";
  elseif (strlen($password) < 6) $errors[] = "La contraseña debe tener al menos 6 caracteres.";
  if (!in_array($rol, ['admin', 'empleado'])) $errors[] = "El rol es obligatorio.";
  if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo inválido.";

  // Verificar que el username no esté ya en uso
  if (empty($errors)) {
    $stmtCheck = $conn->prepare("SELECT id_usuario FROM usuario WHERE username = ?");
    $stmtCheck->bind_param("s", $username);
    $stmtCheck->execute();
    $stmtCheck->store_result();
    if ($stmtCheck->num_rows > 0) {
      $errors[] = "El nombre de usuario '$username' ya está en uso.";
    }
    $stmtCheck->close();
  }

  if (empty($errors)) {
    $conn->begin_transaction();
    try {
      // 1. Crear el usuario del sistema
      $hash = password_hash($password, PASSWORD_BCRYPT);
      $stmtU = $conn->prepare("INSERT INTO usuario (nombre, username, password, rol, estado) VALUES (?, ?, ?, ?, 1)");

      if (!$stmtU) {
        throw new Exception("Prepare error: " . $conn->error);
      }

      $stmtU->bind_param("ssss", $nombre, $username, $hash, $rol);

      if (!$stmtU->execute()) {
        throw new Exception("Execute error: " . $stmtU->error);
      }

      $id_usuario = $conn->insert_id;
      $stmtU->close();

      // 2. Registrar en empleado
      $stmtE = $conn->prepare("INSERT INTO empleado (nombre, cargo, telefono, correo, id_usuario) VALUES (?, NULL, ?, ?, ?)");

      if (!$stmtE) {
        throw new Exception("Prepare error: " . $conn->error);
      }

      $stmtE->bind_param("sssi", $nombre, $telefono, $correo, $id_usuario);

      if (!$stmtE->execute()) {
        throw new Exception("Execute error: " . $stmtE->error);
      }

      $stmtE->close();

      $conn->commit();
      $success = "Usuario registrado correctamente. Ya puede iniciar sesión con el usuario '$username'.";
      $nombre = $username = $password = $rol = $telefono = $correo = "";
    } catch (Exception $e) {
      $conn->rollback();
      $errors[] = "Error al guardar: " . $e->getMessage();
    }
  }
}
?>

<div class="card mx-auto" style="max-width: 700px;">
  <div style="padding: 30px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4"
      style="margin: -30px -30px 20px -30px; padding: 20px 30px;">
      <i class="bi bi-person-plus-fill"></i> Registrar Usuario del Sistema
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

      <!-- Datos personales -->
      <h6 class="text-muted fw-bold mb-3"><i class="bi bi-person me-1"></i> Datos personales</h6>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="nombre" class="form-label">Nombre completo *</label>
          <input type="text" class="form-control" id="nombre" name="nombre"
            value="<?= htmlspecialchars($nombre) ?>" required>
        </div>
        <div class="col-md-6">
          <label for="telefono" class="form-label"><i class="bi bi-telephone"></i> Teléfono</label>
          <input type="text" class="form-control" id="telefono" name="telefono"
            value="<?= htmlspecialchars($telefono) ?>">
        </div>
      </div>
      <div class="mb-3">
        <label for="correo" class="form-label"><i class="bi bi-envelope"></i> Correo electrónico</label>
        <input type="email" class="form-control" id="correo" name="correo"
          value="<?= htmlspecialchars($correo) ?>">
      </div>

      <hr class="my-4">

      <!-- Credenciales de acceso -->
      <h6 class="text-muted fw-bold mb-3"><i class="bi bi-shield-lock me-1"></i> Credenciales de acceso</h6>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="username" class="form-label">Usuario (login) *</label>
          <input type="text" class="form-control" id="username" name="username"
            value="<?= htmlspecialchars($username) ?>" required autocomplete="off">
        </div>
        <div class="col-md-6">
          <label for="password" class="form-label">Contraseña *</label>
          <div class="input-group">
            <input type="password" class="form-control" id="password" name="password"
              value="<?= htmlspecialchars($password) ?>" required autocomplete="new-password" minlength="6">
            <button type="button" class="btn btn-outline-secondary" id="togglePwd" title="Mostrar/ocultar">
              <i class="bi bi-eye" id="eyeIcon"></i>
            </button>
          </div>
          <div class="form-text">Mínimo 6 caracteres.</div>
        </div>
      </div>
      <div class="mb-3">
        <label for="rol" class="form-label">Rol de acceso *</label>
        <select class="form-select" id="rol" name="rol" required>
          <option value="" disabled <?= $rol === '' ? 'selected' : '' ?>>Selecciona un rol...</option>
          <option value="empleado" <?= $rol === 'empleado' ? 'selected' : '' ?>>
            Empleado — puede registrar citas y gestionar pacientes
          </option>
          <option value="admin" <?= $rol === 'admin' ? 'selected' : '' ?>>
            Administrador — acceso completo al sistema
          </option>
        </select>
      </div>

      <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check-circle"></i> Registrar Usuario
        </button>
        <a href="lista_empleados.php" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Ver listado
        </a>
      </div>

    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('togglePwd').addEventListener('click', function() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
  });
</script>

<?php include "footer.php"; ?>