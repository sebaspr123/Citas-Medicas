<?php include "../../includes/plantilla.php"; ?>
<?php
soloAdmin(); // Solo administradores pueden gestionar consultorios
include "../../config/db_connect.php";

// ==========================================
// ELIMINAR CONSULTORIO
// ==========================================
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);

    // Verificar si tiene citas activas (Pendiente o Confirmada)
    $check = $conn->prepare("
        SELECT COUNT(*) AS total FROM cita
        WHERE id_consultorio = ? AND estado IN ('Pendiente', 'Confirmada')
    ");
    $check->bind_param("i", $id);
    $check->execute();
    $total = $check->get_result()->fetch_assoc()['total'];
    $check->close();

    if ($total > 0) {
        header("Location: lista_consultorios.php?msg_error=No+se+puede+eliminar:+el+consultorio+tiene+$total+cita(s)+activa(s).");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM consultorio WHERE id_consultorio = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: lista_consultorios.php?msg=Consultorio+eliminado+correctamente.");
    exit;
}

// ==========================================
// LISTAR CONSULTORIOS
// ==========================================
$result = $conn->query("SELECT * FROM consultorio ORDER BY piso, numero");
?>

<div class="card mx-auto" style="max-width: 900px;">
  <div style="padding: 30px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4"
        style="margin: -30px -30px 20px -30px; padding: 20px 30px;">
      <i class="bi bi-door-closed-fill"></i> Consultorios
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
            <th><i class="bi bi-hash"></i> Número</th>
            <th><i class="bi bi-layers"></i> Piso</th>
            <th><i class="bi bi-geo-alt"></i> Ubicación</th>
            <th style="text-align:center;"><i class="bi bi-gear"></i> Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id_consultorio'] ?></td>
            <td><strong><?= htmlspecialchars($row['numero']) ?></strong></td>
            <td><?= $row['piso'] ?></td>
            <td><?= htmlspecialchars($row['ubicacion'] ?? '—') ?></td>
            <td style="text-align:center;">
              <a class="btn btn-sm btn-danger"
                 href="lista_consultorios.php?eliminar=<?= $row['id_consultorio'] ?>"
                 title="Eliminar"
                 onclick="return confirm('¿Eliminar este consultorio? Solo es posible si no tiene citas activas.');">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end mt-4">
      <a href="registrar_consultorio.php" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Agregar Consultorio
      </a>
    </div>
  </div>
</div>

<?php include "../../includes/footer.php"; ?>



