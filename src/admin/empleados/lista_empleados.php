<?php
// 1. Auth primero
require_once __DIR__ . "/../../auth/auth.php";
soloAdmin();

// 2. BD
require_once __DIR__ . "/../../config/db_connect.php";

// 3. ELIMINAR
if (isset($_GET['eliminar'])) {
  $id = intval($_GET['eliminar']);

  if ($id === intval($_SESSION['id_usuario'])) {
    header("Location: /admin/empleados/lista_empleados.php?msg_error=No+puedes+eliminar+tu+propio+usuario.");
    exit;
  }

  $checkAdmin  = $conn->query("SELECT COUNT(*) AS total FROM usuario WHERE rol = 'admin' AND estado = 1");
  $totalAdmins = $checkAdmin->fetch_assoc()['total'];

  $checkRol = $conn->prepare("SELECT rol FROM usuario WHERE id_usuario = ?");
  $checkRol->bind_param("i", $id);
  $checkRol->execute();
  $rolUsuario = $checkRol->get_result()->fetch_assoc()['rol'] ?? '';
  $checkRol->close();

  if ($rolUsuario === 'admin' && $totalAdmins <= 1) {
    header("Location: /admin/empleados/lista_empleados.php?msg_error=No+puedes+eliminar+el+único+administrador+activo.");
    exit;
  }

  $stmt = $conn->prepare("DELETE FROM usuario WHERE id_usuario = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();

  header("Location: /admin/empleados/lista_empleados.php?msg=Usuario+eliminado+correctamente.");
  exit;
}

// 4. TOGGLE ESTADO
if (isset($_GET['toggle'])) {
  $id = intval($_GET['toggle']);

  if ($id === intval($_SESSION['id_usuario'])) {
    header("Location: /admin/empleados/lista_empleados.php?msg_error=No+puedes+desactivar+tu+propio+usuario.");
    exit;
  }

  $checkAdmin  = $conn->query("SELECT COUNT(*) AS total FROM usuario WHERE rol = 'admin' AND estado = 1");
  $totalAdmins = $checkAdmin->fetch_assoc()['total'];

  $infoQ = $conn->prepare("SELECT rol, estado FROM usuario WHERE id_usuario = ?");
  $infoQ->bind_param("i", $id);
  $infoQ->execute();
  $info = $infoQ->get_result()->fetch_assoc();
  $infoQ->close();

  if ($info['rol'] === 'admin' && $info['estado'] == 1 && $totalAdmins <= 1) {
    header("Location: /admin/empleados/lista_empleados.php?msg_error=No+puedes+desactivar+el+único+administrador+activo.");
    exit;
  }

  $nuevoEstado = $info['estado'] == 1 ? 0 : 1;
  $stmtT = $conn->prepare("UPDATE usuario SET estado = ? WHERE id_usuario = ?");
  $stmtT->bind_param("ii", $nuevoEstado, $id);
  $stmtT->execute();
  $stmtT->close();

  $msg = $nuevoEstado ? 'Usuario+activado.' : 'Usuario+desactivado.';
  header("Location: /admin/empleados/lista_empleados.php?msg=$msg");
  exit;
}

// 5. LISTAR
$result = $conn->query("
    SELECT u.id_usuario, u.nombre, u.username, u.rol, u.estado,
           e.telefono, e.correo
    FROM usuario u
    LEFT JOIN empleado e ON e.nombre COLLATE utf8mb4_0900_ai_ci = u.nombre
    ORDER BY u.id_usuario DESC
");

// 6. HTML al final
include __DIR__ . "/../../includes/plantilla.php";
?>

<div class="card mx-auto" style="max-width: 1100px;">
  <div style="padding: 30px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4"
      style="margin: -30px -30px 20px -30px; padding: 20px 30px;">
      <i class="bi bi-people-fill"></i> Usuarios del Sistema
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
            <th><i class="bi bi-person"></i> Nombre</th>
            <th><i class="bi bi-at"></i> Usuario</th>
            <th><i class="bi bi-shield"></i> Rol</th>
            <th><i class="bi bi-telephone"></i> Teléfono</th>
            <th><i class="bi bi-envelope"></i> Correo</th>
            <th><i class="bi bi-toggles"></i> Estado</th>
            <th style="text-align:center;"><i class="bi bi-gear"></i> Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="<?= $row['estado'] == 0 ? 'table-secondary text-muted' : '' ?>">
              <td><?= $row['id_usuario'] ?></td>
              <td><strong><?= htmlspecialchars($row['nombre']) ?></strong></td>
              <td><code><?= htmlspecialchars($row['username']) ?></code></td>
              <td>
                <?php if ($row['rol'] === 'admin'): ?>
                  <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Administrador</span>
                <?php else: ?>
                  <span class="badge bg-info text-dark"><i class="bi bi-person-fill me-1"></i>Empleado</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($row['telefono'] ?? '—') ?></td>
              <td><?= htmlspecialchars($row['correo']   ?? '—') ?></td>
              <td>
                <?php if ($row['estado'] == 1): ?>
                  <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activo</span>
                <?php else: ?>
                  <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>Inactivo</span>
                <?php endif; ?>
              </td>
              <td style="text-align:center; white-space:nowrap;">
                <!-- Activar / Desactivar -->
                <a class="btn btn-sm <?= $row['estado'] == 1 ? 'btn-warning' : 'btn-success' ?>"
                  href="/admin/empleados/lista_empleados.php?toggle=<?= $row['id_usuario'] ?>"
                  title="<?= $row['estado'] == 1 ? 'Desactivar' : 'Activar' ?>"
                  onclick="return confirm('<?= $row['estado'] == 1 ? '¿Desactivar este usuario?' : '¿Activar este usuario?' ?>');">
                  <i class="bi bi-<?= $row['estado'] == 1 ? 'pause-circle' : 'play-circle' ?>"></i>
                </a>

                <!-- Eliminar -->
                <a class="btn btn-sm btn-danger"
                  href="/admin/empleados/lista_empleados.php?eliminar=<?= $row['id_usuario'] ?>"
                  title="Eliminar"
                  onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end mt-4">
      <a href="../empleados/registrar_empleado.php" class="btn btn-success">
        <i class="bi bi-person-plus-fill"></i> Agregar Usuario
      </a>
    </div>
  </div>
</div>

<?php include "../../includes/footer.php"; ?>