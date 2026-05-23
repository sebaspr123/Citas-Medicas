<?php include "plantilla.php"; ?>
<?php
soloAdmin(); // Solo administradores pueden gestionar especialidades
include "db_connect.php";

// ==========================================
// ELIMINAR ESPECIALIDAD
// ==========================================
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);

    // Verificar si tiene médicos asociados activos
    $check = $conn->prepare("SELECT COUNT(*) AS total FROM medico WHERE id_especialidad = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $total = $check->get_result()->fetch_assoc()['total'];
    $check->close();

    if ($total > 0) {
        header("Location: lista_especialidades.php?msg_error=No+se+puede+eliminar:+hay+$total+médico(s)+asociado(s)+a+esta+especialidad.");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM especialidad WHERE id_especialidad = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: lista_especialidades.php?msg=Especialidad+eliminada+correctamente.");
    exit;
}

// ==========================================
// LISTAR ESPECIALIDADES con conteo de médicos
// ==========================================
$result = $conn->query("
    SELECT e.id_especialidad, e.nombre, e.descripcion,
           COUNT(m.id_medico) AS total_medicos
    FROM especialidad e
    LEFT JOIN medico m ON m.id_especialidad = e.id_especialidad
    GROUP BY e.id_especialidad
    ORDER BY e.nombre
");
?>

<div class="card mx-auto" style="max-width: 950px;">
  <div style="padding: 30px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4"
        style="margin: -30px -30px 20px -30px; padding: 20px 30px;">
      <i class="bi bi-tags-fill"></i> Especialidades
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

    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th><i class="bi bi-tag"></i> Nombre</th>
            <th><i class="bi bi-card-text"></i> Descripción</th>
            <th><i class="bi bi-stethoscope"></i> Médicos</th>
            <th style="text-align:center;"><i class="bi bi-gear"></i> Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id_especialidad'] ?></td>
            <td><strong><?= htmlspecialchars($row['nombre']) ?></strong></td>
            <td><?= htmlspecialchars($row['descripcion'] ?? '—') ?></td>
            <td>
              <span class="badge <?= $row['total_medicos'] > 0 ? 'bg-primary' : 'bg-secondary' ?>">
                <?= $row['total_medicos'] ?>
              </span>
            </td>
            <td style="text-align:center;">
              <?php if ($row['total_medicos'] > 0): ?>
                <button class="btn btn-sm btn-secondary" disabled title="Tiene médicos asociados">
                  <i class="bi bi-trash"></i>
                </button>
              <?php else: ?>
                <a class="btn btn-sm btn-danger"
                   href="lista_especialidades.php?eliminar=<?= $row['id_especialidad'] ?>"
                   title="Eliminar"
                   onclick="return confirm('¿Eliminar esta especialidad?');">
                  <i class="bi bi-trash"></i>
                </a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end mt-4">
      <a href="registrar_especialidad.php" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Agregar Especialidad
      </a>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
