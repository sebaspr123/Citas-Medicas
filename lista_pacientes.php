<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once "db_connect.php";

$info = [];
$errors = [];

// --- Eliminar paciente ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    if ($id > 0) {
        if ($conn->query("DELETE FROM paciente WHERE id_paciente = $id")) {
            header("Location: lista_pacientes.php?ok=1");
            exit;
        } else {
            $errors[] = "Error al eliminar: " . $conn->error;
        }
    }
}

if (isset($_GET['ok'])) {
    $info[] = "Paciente eliminado correctamente.";
}

include "plantilla.php";
?>

<div class="card mx-auto" style="max-width: 1100px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4">
        <i class="bi bi-people"></i> Listado de Pacientes
    </h2>

    <?php foreach ($info as $m): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= htmlspecialchars($m) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>

    <?php foreach ($errors as $m): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($m) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>

    <?php
    $res = $conn->query("SELECT * FROM paciente ORDER BY nombre, apellido");

    if ($res && $res->num_rows > 0):
    ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><i class="bi bi-hash"></i> ID</th>
                        <th><i class="bi bi-person"></i> Nombre</th>
                        <th><i class="bi bi-card-text"></i> Cédula</th>
                        <th><i class="bi bi-telephone"></i> Teléfono</th>
                        <th><i class="bi bi-envelope"></i> Correo</th>
                        <th style="text-align: center;"><i class="bi bi-gear"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($r = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= $r['id_paciente'] ?></td>
                            <td><strong><?= htmlspecialchars($r['nombre'] . " " . $r['apellido']) ?></strong></td>
                            <td><?= htmlspecialchars($r['cedula']) ?></td>
                            <td><?= htmlspecialchars($r['telefono']) ?></td>
                            <td><?= htmlspecialchars($r['correo']) ?></td>
                            <td style="text-align: center;">
                                <a class="btn btn-sm btn-primary" href="editar_paciente.php?id=<?= $r['id_paciente'] ?>" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a class="btn btn-sm btn-danger" href="?delete=<?= $r['id_paciente'] ?>" onclick="return confirm('¿Eliminar paciente?')" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i> No hay pacientes registrados aún.
        </div>
    <?php endif; ?>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
        <a href="registrar_paciente.php" class="btn btn-success">
            <i class="bi bi-person-plus"></i> Agregar Paciente
        </a>
    </div>
</div>

<?php include "footer.php"; ?>