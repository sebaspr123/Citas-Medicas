<?php include "plantilla.php"; ?>
<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
include "db_connect.php";

// FILTRO
$busqueda = trim($_GET['busqueda'] ?? '');

$sql = "SELECT m.id_medico, m.nombre, m.apellido, m.cedula, m.telefono, e.nombre AS especialidad
        FROM medico m
        LEFT JOIN especialidad e ON m.id_especialidad = e.id_especialidad";

if ($busqueda !== '') {
    $sql .= " WHERE m.nombre LIKE '%$busqueda%' 
              OR m.apellido LIKE '%$busqueda%' 
              OR m.cedula LIKE '%$busqueda%'";
}

$sql .= " ORDER BY m.nombre";

$res = $conn->query($sql);
?>

<div class="card mx-auto" style="max-width: 1100px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4">
        <i class="bi bi-person-badge"></i> Listado de Médicos
    </h2>

    <form method="get" class="mb-4">
        <div class="input-group input-group-lg">
            <input type="text" class="form-control" name="busqueda" placeholder="Buscar por nombre, apellido o cédula"
                   value="<?=htmlspecialchars($busqueda)?>">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-search"></i> Buscar
            </button>
            <?php if ($busqueda !== ''): ?>
                <a href="lista_medicos.php" class="btn btn-secondary">
                    <i class="bi bi-x"></i> Limpiar
                </a>
            <?php endif; ?>
        </div>
    </form>

    <?php if ($res && $res->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><i class="bi bi-hash"></i> ID</th>
                        <th><i class="bi bi-person"></i> Nombre</th>
                        <th><i class="bi bi-card-text"></i> Cédula</th>
                        <th><i class="bi bi-stethoscope"></i> Especialidad</th>
                        <th><i class="bi bi-telephone"></i> Teléfono</th>
                        <th style="text-align: center;"><i class="bi bi-gear"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($m = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= $m['id_medico'] ?></td>
                            <td><strong><?= htmlspecialchars($m['nombre']." ".$m['apellido']) ?></strong></td>
                            <td><?= htmlspecialchars($m['cedula']) ?></td>
                            <td>
                                <span class="badge bg-info">
                                    <?= htmlspecialchars($m['especialidad'] ?? 'No asignada') ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($m['telefono']) ?></td>
                            <td style="text-align: center;">
                                <a class="btn btn-sm btn-primary" href="editar_medico.php?id=<?= $m['id_medico'] ?>" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a class="btn btn-sm btn-danger" href="eliminar_medico.php?id=<?= $m['id_medico'] ?>"
                                   onclick="return confirm('¿Seguro de eliminar?')" title="Eliminar">
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
            <i class="bi bi-info-circle"></i> No hay médicos registrados aún.
        </div>
    <?php endif; ?>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
        <a href="registrar_medico.php" class="btn btn-success">
            <i class="bi bi-person-plus"></i> Agregar Médico
        </a>
    </div>
</div>

<?php include "footer.php"; ?>