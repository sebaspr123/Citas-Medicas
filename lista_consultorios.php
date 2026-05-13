<?php include "plantilla.php"; ?>
<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once "db_connect.php";

// Eliminar consultorio
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);

    $del = $conn->prepare("DELETE FROM consultorio WHERE id_consultorio = ?");
    $del->bind_param("i", $id);
    $del->execute();
    $del->close();
}

// Consultar consultorios
$result = $conn->query("SELECT * FROM consultorio ORDER BY id_consultorio DESC");
?>

<div class="card mx-auto" style="max-width: 800px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4">
        <i class="bi bi-door-closed"></i> Listado de Consultorios
    </h2>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th><i class="bi bi-hash"></i> ID</th>
                    <th><i class="bi bi-door-closed"></i> Número</th>
                    <th><i class="bi bi-building"></i> Piso</th>
                    <th style="text-align: center;"><i class="bi bi-gear"></i> Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_consultorio'] ?></td>
                            <td><strong><?= htmlspecialchars($row['numero']) ?></strong></td>
                            <td><?= htmlspecialchars($row['piso']) ?></td>
                            <td style="text-align: center;">
                                <a href="?eliminar=<?= $row['id_consultorio'] ?>"
                                   onclick="return confirm('¿Seguro que deseas eliminar este consultorio?')"
                                   class="btn btn-sm btn-danger" title="Eliminar">
                                   <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;" class="py-4">
                        <i class="bi bi-inbox"></i> No hay consultorios registrados.
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
        <a href="registrar_consultorio.php" class="btn btn-success">
            <i class="bi bi-door-closed"></i> Agregar Consultorio
        </a>
    </div>
</div>

<?php include "footer.php"; ?>
