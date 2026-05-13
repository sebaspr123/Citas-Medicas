<?php include "plantilla.php"; ?>
<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once "db_connect.php";

// Eliminar especialidad
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);

    $del = $conn->prepare("DELETE FROM especialidad WHERE id_especialidad = ?");
    $del->bind_param("i", $id);
    $del->execute();
    $del->close();
}

// Consulta de especialidades
$result = $conn->query("SELECT * FROM especialidad ORDER BY id_especialidad DESC");
?>

<div class="card mx-auto" style="max-width: 900px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4">
        <i class="bi bi-tags"></i> Listado de Especialidades
    </h2>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th><i class="bi bi-hash"></i> ID</th>
                    <th><i class="bi bi-tag"></i> Nombre</th>
                    <th><i class="bi bi-file-text"></i> Descripción</th>
                    <th style="text-align: center;"><i class="bi bi-gear"></i> Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_especialidad'] ?></td>
                            <td><strong><?= htmlspecialchars($row['nombre']) ?></strong></td>
                            <td><?= htmlspecialchars(substr($row['descripcion'], 0, 60) . (strlen($row['descripcion']) > 60 ? '...' : '')) ?></td>
                            <td style="text-align: center;">
                                <a href="?eliminar=<?= $row['id_especialidad'] ?>"
                                   onclick="return confirm('¿Seguro que deseas eliminar esta especialidad?')"
                                   class="btn btn-sm btn-danger" title="Eliminar">
                                   <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;" class="py-4">
                        <i class="bi bi-inbox"></i> No hay especialidades registradas.
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
        <a href="registrar_especialidad.php" class="btn btn-success">
            <i class="bi bi-tags"></i> Agregar Especialidad
        </a>
    </div>
</div>

<?php include "footer.php"; ?>
