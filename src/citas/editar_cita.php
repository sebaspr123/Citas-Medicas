<?php include "../includes/plantilla.php"; ?>
<?php include "../config/db_connect.php"; ?>

<?php
include("../includes/plantilla.php");
include("../config/db_connect.php");

$mensaje = "";

// Verificar si viene un ID por URL
if (!isset($_GET['id'])) {
    header("Location: buscar_cita.php");
    exit();
}

$id_cita = intval($_GET['id']);

// Función para consultar la cita
function getCita($conn, $id_cita)
{
    $sql = "SELECT c.id_cita, c.id_paciente, c.id_medico, c.id_consultorio,
                   c.fecha, c.hora, c.estado,
                   p.nombre   AS nombre_paciente,
                   p.apellido AS apellido_paciente,
                   m.nombre   AS nombre_medico
            FROM cita c
            INNER JOIN paciente p ON c.id_paciente = p.id_paciente
            INNER JOIN medico   m ON c.id_medico   = m.id_medico
            WHERE c.id_cita = $id_cita";
    $res = $conn->query($sql);
    return ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
}

$datos = getCita($conn, $id_cita);

if (!$datos) {
    header("Location: buscar_cita.php");
    exit();
}

// Procesar formulario ANTES de cargar los selects
if (isset($_POST['guardar'])) {

    $fecha          = $conn->real_escape_string($_POST['fecha']);
    $hora           = $conn->real_escape_string($_POST['hora']);
    $estado         = $conn->real_escape_string($_POST['estado']);
    $id_medico      = intval($_POST['id_medico']);
    $id_paciente    = intval($_POST['id_paciente']);
    $id_consultorio = intval($_POST['id_consultorio']);

    if ($estado === "Cancelada") {
        if ($conn->query("DELETE FROM cita WHERE id_cita = $id_cita")) {
            header("Location: buscar_cita.php?msg=eliminada");
            exit();
        } else {
            $mensaje = '<div class="alert alert-danger">
                            <i class="bi bi-x-circle-fill me-2"></i>
                            Error al eliminar: ' . $conn->error . '
                        </div>';
        }
    } else {

        if ($id_consultorio <= 0) {
            $mensaje = '<div class="alert alert-danger">
                            <i class="bi bi-x-circle-fill me-2"></i>
                            Debes seleccionar un consultorio válido.
                        </div>';
        } else {
            $updateSQL = "UPDATE cita
                          SET fecha          = '$fecha',
                              hora           = '$hora',
                              estado         = '$estado',
                              id_medico      = $id_medico,
                              id_paciente    = $id_paciente,
                              id_consultorio = $id_consultorio
                          WHERE id_cita      = $id_cita";

            if ($conn->query($updateSQL)) {
                $mensaje = '<div class="alert alert-success">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                Cita actualizada correctamente.
                            </div>';
                // Recargar datos con valores nuevos
                $datos = getCita($conn, $id_cita);
            } else {
                $mensaje = '<div class="alert alert-danger">
                                <i class="bi bi-x-circle-fill me-2"></i>
                                Error al actualizar: ' . $conn->error . '
                            </div>';
            }
        }
    }
}

// Cargar selects DESPUÉS del POST para que usen datos frescos
$pacientes    = $conn->query("SELECT id_paciente, CONCAT(nombre,' ',apellido) AS nombre FROM paciente ORDER BY nombre");
$medicos      = $conn->query("SELECT id_medico, CONCAT(nombre,' ',apellido) AS nombre FROM medico ORDER BY nombre");
$consultorios = $conn->query("SELECT id_consultorio, numero FROM consultorio ORDER BY numero");
?>

<!-- Título -->
<h1 class="page-title">
    <i class="bi bi-pencil-square me-2"></i> Editar Cita
    <small class="fs-6 text-muted ms-2">ID: <?= $id_cita ?></small>
</h1>

<!-- Mensaje -->
<?= $mensaje ?>

<!-- Info del paciente/médico -->
<div class="alert alert-info mb-4">
    <div class="row">
        <div class="col-md-6">
            <i class="bi bi-person-fill me-1"></i>
            <strong>Paciente:</strong>
            <?= htmlspecialchars($datos['nombre_paciente'] . ' ' . $datos['apellido_paciente']) ?>
        </div>
        <div class="col-md-6">
            <i class="bi bi-stethoscope me-1"></i>
            <strong>Médico:</strong>
            <?= htmlspecialchars($datos['nombre_medico']) ?>
        </div>
    </div>
</div>

<!-- Formulario — action mantiene el ?id= en el POST -->
<div class="card">
    <div class="card-body p-4">

        <form method="POST" action="editar_cita.php?id=<?= $id_cita ?>">
            <div class="row g-3">

                <!-- Paciente -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-person me-1"></i> Paciente
                    </label>
                    <select name="id_paciente" class="form-select" required>
                        <option value="">-- Seleccione paciente --</option>
                        <?php while ($r = $pacientes->fetch_assoc()): ?>
                            <option value="<?= (int)$r['id_paciente'] ?>"
                                <?= (int)$r['id_paciente'] === (int)$datos['id_paciente'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Médico -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-stethoscope me-1"></i> Médico
                    </label>
                    <select name="id_medico" class="form-select" required>
                        <option value="">-- Seleccione médico --</option>
                        <?php while ($r = $medicos->fetch_assoc()): ?>
                            <option value="<?= (int)$r['id_medico'] ?>"
                                <?= (int)$r['id_medico'] === (int)$datos['id_medico'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Consultorio -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-door-closed me-1"></i> Consultorio
                    </label>
                    <select name="id_consultorio" class="form-select" required>
                        <option value="">-- Seleccione consultorio --</option>
                        <?php while ($r = $consultorios->fetch_assoc()): ?>
                            <option value="<?= (int)$r['id_consultorio'] ?>"
                                <?= (int)$r['id_consultorio'] === (int)$datos['id_consultorio'] ? 'selected' : '' ?>>
                                Consultorio <?= htmlspecialchars($r['numero']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Estado -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-flag me-1"></i> Estado
                    </label>
                    <select name="estado" class="form-select" required>
                        <option value="Pendiente" <?= $datos['estado'] === 'Pendiente'  ? 'selected' : '' ?>>Pendiente</option>
                        <option value="Completada" <?= $datos['estado'] === 'Completada' ? 'selected' : '' ?>>Completada</option>
                        <option value="Cancelada" <?= $datos['estado'] === 'Cancelada'  ? 'selected' : '' ?>>Cancelada (eliminar cita)</option>
                    </select>
                </div>

                <!-- Fecha -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-calendar-date me-1"></i> Fecha
                    </label>
                    <input type="date"
                        name="fecha"
                        class="form-control"
                        value="<?= htmlspecialchars($datos['fecha']) ?>"
                        required>
                </div>

                <!-- Hora -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-clock me-1"></i> Hora
                    </label>
                    <input type="time"
                        name="hora"
                        class="form-control"
                        value="<?= htmlspecialchars($datos['hora']) ?>"
                        required>
                </div>

                <!-- Botones -->
                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" name="guardar" class="btn btn-primary">
                        <i class="bi bi-floppy me-1"></i> Guardar Cambios
                    </button>
                    <a href="buscar_cita.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Volver
                    </a>
                </div>

            </div>
        </form>

    </div>
</div>

</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

