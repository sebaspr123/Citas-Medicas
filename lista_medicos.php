<?php include "plantilla.php"; ?>
<?php
// Lista de médicos: visible para todos.
// Editar y eliminar: solo admin (controlado en UI y en servidor).
include "db_connect.php";

// ==========================================
// ELIMINAR MÉDICO — solo admin
// ==========================================
if (isset($_GET['eliminar'])) {
    if ($_SESSION['rol'] !== 'admin') {
        header("Location: lista_medicos.php?msg_error=Acción+no+permitida.");
        exit;
    }
    $id = intval($_GET['eliminar']);

    $check = $conn->prepare("
        SELECT COUNT(*) AS total FROM cita
        WHERE id_medico = ? AND estado IN ('Pendiente', 'Confirmada')
    ");
    $check->bind_param("i", $id);
    $check->execute();
    $total = $check->get_result()->fetch_assoc()['total'];
    $check->close();

    if ($total > 0) {
        header("Location: lista_medicos.php?msg_error=No+se+puede+eliminar:+el+médico+tiene+$total+cita(s)+activa(s).");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM medico WHERE id_medico = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: lista_medicos.php?msg=Médico+eliminado+correctamente.");
    exit;
}

// ==========================================
// GUARDAR EDICIÓN — solo admin
// ==========================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["actualizar"])) {
    if ($_SESSION['rol'] !== 'admin') {
        header("Location: lista_medicos.php?msg_error=Acción+no+permitida.");
        exit;
    }
    $id              = intval($_POST['id_medico']);
    $nombre          = trim($_POST['nombre']);
    $apellido        = trim($_POST['apellido']);
    $cedula          = trim($_POST['cedula']);
    $telefono        = trim($_POST['telefono']);
    $correo          = trim($_POST['correo']);
    $id_especialidad = $_POST['id_especialidad'] !== '' ? intval($_POST['id_especialidad']) : null;

    $stmt = $conn->prepare("
        UPDATE medico SET nombre=?, apellido=?, cedula=?, telefono=?, correo=?, id_especialidad=?
        WHERE id_medico=?
    ");
    $stmt->bind_param("sssssii", $nombre, $apellido, $cedula, $telefono, $correo, $id_especialidad, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: lista_medicos.php?msg=Médico+actualizado+correctamente.");
    exit;
}

// ==========================================
// EDITAR — cargar datos
// ==========================================
$editando = false;
$medico   = null;
if (isset($_GET['editar']) && $_SESSION['rol'] === 'admin') {
    $editando = true;
    $id       = intval($_GET['editar']);
    $q        = $conn->prepare("SELECT * FROM medico WHERE id_medico = ?");
    $q->bind_param("i", $id);
    $q->execute();
    $medico = $q->get_result()->fetch_assoc();
    $q->close();
}

// ==========================================
// LISTAR MÉDICOS
// ==========================================
$result = $conn->query("
    SELECT m.id_medico, m.nombre, m.apellido, m.cedula, m.telefono, m.correo,
           e.nombre AS especialidad
    FROM medico m
    LEFT JOIN especialidad e ON e.id_especialidad = m.id_especialidad
    ORDER BY m.apellido, m.nombre
");

$especialidades = $conn->query("SELECT id_especialidad, nombre FROM especialidad ORDER BY nombre");
?>

<div class="card mx-auto" style="max-width: 1100px;">
  <div style="padding: 30px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4"
        style="margin: -30px -30px 20px -30px; padding: 20px 30px;">
      <i class="bi bi-person-badge-fill"></i> Médicos
    </h2>

    <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_GET['msg']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['msg_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($_GET['msg_error']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- FORMULARIO DE EDICIÓN (solo admin) -->
    <?php if ($editando && $medico): ?>
    <div class="alert alert-info mb-4">
      <h5><i class="bi bi-pencil-square"></i> Editar Médico</h5>
      <form method="POST" class="mt-3">
        <input type="hidden" name="id_medico" value="<?= $medico['id_medico'] ?>">
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($medico['nombre']) ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Apellido</label>
            <input type="text" class="form-control" name="apellido" value="<?= htmlspecialchars($medico['apellido']) ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Cédula</label>
            <input type="text" class="form-control" name="cedula" value="<?= htmlspecialchars($medico['cedula']) ?>" required>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($medico['telefono']) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Correo</label>
            <input type="email" class="form-control" name="correo" value="<?= htmlspecialchars($medico['correo']) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Especialidad</label>
            <select class="form-select" name="id_especialidad">
              <option value="">— Sin asignar —</option>
              <?php while ($esp = $especialidades->fetch_assoc()): ?>
              <option value="<?= $esp['id_especialidad'] ?>"
                <?= $medico['id_especialidad'] == $esp['id_especialidad'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($esp['nombre']) ?>
              </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>
        <button class="btn btn-success" name="actualizar">
          <i class="bi bi-check-circle"></i> Actualizar
        </button>
        <a href="lista_medicos.php" class="btn btn-secondary ms-2">Cancelar</a>
      </form>
    </div>
    <?php endif; ?>

    <!-- TABLA -->
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Cédula</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Especialidad</th>
            <?php if ($_SESSION['rol'] === 'admin'): ?>
            <th style="text-align:center;">Acciones</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id_medico'] ?></td>
            <td><strong><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) ?></strong></td>
            <td><?= htmlspecialchars($row['cedula']) ?></td>
            <td><?= htmlspecialchars($row['telefono'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['correo']   ?? '—') ?></td>
            <td>
              <?php if ($row['especialidad']): ?>
                <span class="badge bg-primary"><?= htmlspecialchars($row['especialidad']) ?></span>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <?php if ($_SESSION['rol'] === 'admin'): ?>
            <td style="text-align:center; white-space:nowrap;">
              <a class="btn btn-sm btn-primary"
                 href="lista_medicos.php?editar=<?= $row['id_medico'] ?>" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              <a class="btn btn-sm btn-danger"
                 href="lista_medicos.php?eliminar=<?= $row['id_medico'] ?>"
                 title="Eliminar"
                 onclick="return confirm('¿Eliminar este médico? Solo es posible si no tiene citas activas.');">
                <i class="bi bi-trash"></i>
              </a>
            </td>
            <?php endif; ?>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <?php if ($_SESSION['rol'] === 'admin'): ?>
    <div class="d-flex justify-content-end mt-4">
      <a href="registrar_medico.php" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Agregar Médico
      </a>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include "footer.php"; ?>
