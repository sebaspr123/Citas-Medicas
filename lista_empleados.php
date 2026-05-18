<?php include "plantilla.php"; ?>
<?php include "db_connect.php";

// ==========================================
// ELIMINAR EMPLEADO
// ==========================================
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM empleado WHERE id_empleado = $id");
    header("Location: lista_empleados.php?msg=Empleado+eliminado");
    exit;
}

// ==========================================
// EDITAR EMPLEADO
// ==========================================
$editando = false;
$empleado = null;

if (isset($_GET['editar'])) {
    $editando = true;
    $id = intval($_GET['editar']);
    $q = $conn->query("SELECT * FROM empleado WHERE id_empleado = $id");
    $empleado = $q->fetch_assoc();
}

// ==========================================
// GUARDAR ACTUALIZACIÓN
// ==========================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["actualizar"])) {
    $id = intval($_POST['id_empleado']);
    $nombre = trim($_POST['nombre']);
    $cargo = trim($_POST['cargo']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);

    $stmt = $conn->prepare("UPDATE empleado SET nombre=?, cargo=?, telefono=?, correo=? WHERE id_empleado=?");
    $stmt->bind_param("ssssi", $nombre, $cargo, $telefono, $correo, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: lista_empleados.php?msg=Empleado+actualizado");
    exit;
}

// ==========================================
// LISTAR EMPLEADOS
// ==========================================
$result = $conn->query("SELECT * FROM empleado ORDER BY id_empleado DESC");
?>

<div class="card mx-auto" style="max-width: 1100px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4">
        <i class="bi bi-briefcase"></i> Listado de Empleados
    </h2>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_GET['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- FORMULARIO PARA EDITAR -->
    <?php if ($editando): ?>
        <div class="alert alert-info mb-4">
            <h5><i class="bi bi-pencil-square"></i> Editar Empleado</h5>

            <form method="POST" class="mt-3">
                <input type="hidden" name="id_empleado" value="<?= $empleado['id_empleado'] ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($empleado['nombre']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="cargo" class="form-label">Cargo</label>
                        <input type="text" class="form-control" id="cargo" name="cargo" value="<?= htmlspecialchars($empleado['cargo']) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($empleado['telefono']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="text" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars($empleado['correo']) ?>">
                    </div>
                </div>

                <button class="btn btn-success" name="actualizar">
                    <i class="bi bi-check-circle"></i> Actualizar
                </button>
            </form>
        </div>
    <?php endif; ?>

    <!-- TABLA -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th><i class="bi bi-hash"></i> ID</th>
                    <th><i class="bi bi-person"></i> Nombre</th>
                    <th><i class="bi bi-briefcase-fill"></i> Cargo</th>
                    <th><i class="bi bi-telephone"></i> Teléfono</th>
                    <th><i class="bi bi-envelope"></i> Correo</th>
                    <th style="text-align: center;"><i class="bi bi-gear"></i> Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_empleado'] ?></td>
                    <td><strong><?= htmlspecialchars($row['nombre']) ?></strong></td>
                    <td><?= htmlspecialchars($row['cargo']) ?></td>
                    <td><?= htmlspecialchars($row['telefono']) ?></td>
                    <td><?= htmlspecialchars($row['correo']) ?></td>
                    <td style="text-align: center;">
                        <a class="btn btn-sm btn-primary" href="lista_empleados.php?editar=<?= $row['id_empleado'] ?>" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Seguro que deseas eliminar este empleado?');"
                           href="lista_empleados.php?eliminar=<?= $row['id_empleado'] ?>" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
        <a href="registrar_empleado.php" class="btn btn-success">
            <i class="bi bi-person-plus"></i> Agregar Empleado
        </a>
    </div>
</div>

<?php include "footer.php"; ?>



