<?php include "../includes/plantilla.php"; ?>
<?php include "../config/db_connect.php"; ?>

<?php
$medicos = $conn->query("SELECT id_medico, CONCAT(nombre,' ',apellido) AS nombre FROM medico ORDER BY nombre");
$consultorios = $conn->query("SELECT id_consultorio, numero FROM consultorio ORDER BY numero");
$empleados = $conn->query("SELECT id_empleado, nombre FROM empleado ORDER BY nombre");

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_paciente = intval($_POST['id_paciente']);
    $id_medico = intval($_POST['id_medico']);
    $id_consultorio = intval($_POST['id_consultorio']);
    $id_empleado = intval($_POST['id_empleado']);
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $hora = $conn->real_escape_string($_POST['hora']);

    if ($id_paciente <= 0) {
        $mensaje = '<div class="alert alert-danger">
                        <i class="bi bi-x-circle-fill me-2"></i>
                        Debes seleccionar un paciente válido.
                    </div>';
    } else {
        $sql = "INSERT INTO cita (id_paciente, id_medico, id_consultorio, id_empleado, fecha, hora)
                VALUES ($id_paciente, $id_medico, $id_consultorio, $id_empleado, '$fecha', '$hora')";

        if ($conn->query($sql) === TRUE) {
            $mensaje = '<div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Cita registrada correctamente.
                        </div>';
        } else {
            $mensaje = '<div class="alert alert-danger">
                            <i class="bi bi-x-circle-fill me-2"></i>
                            Error: ' . $conn->error . '
                        </div>';
        }
    }
}
?>

<h1 class="page-title">
    <i class="bi bi-calendar-plus me-2"></i> Registrar Cita
</h1>

<?= $mensaje ?>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" novalidate autocomplete="off">
            <div class="row g-3">

                <!-- Paciente -->
                <div class="col-md-6 position-relative">
                    <label class="form-label">
                        <i class="bi bi-person me-1"></i> Buscar paciente
                    </label>

                    <input
                        type="text"
                        id="buscar_paciente"
                        class="form-control"
                        placeholder="Escribe cédula, nombre o apellido"
                    >

                    <input type="hidden" name="id_paciente" id="id_paciente" required>

                    <div id="resultado_pacientes" class="list-group position-absolute w-100 shadow-sm" style="z-index:1000;"></div>

                    <div id="paciente_seleccionado" class="form-text text-success mt-2"></div>
                </div>

                <!-- Médico -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-stethoscope me-1"></i> Médico
                    </label>
                    <select name="id_medico" class="form-select" required>
                        <option value="">-- Seleccione médico --</option>
                        <?php while ($r = $medicos->fetch_assoc()): ?>
                            <option value="<?= $r['id_medico'] ?>">
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
                            <option value="<?= $r['id_consultorio'] ?>">
                                Consultorio <?= htmlspecialchars($r['numero']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Empleado -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-briefcase me-1"></i> Empleado (quien registra)
                    </label>
                    <select name="id_empleado" class="form-select" required>
                        <option value="">-- Seleccione empleado --</option>
                        <?php while ($r = $empleados->fetch_assoc()): ?>
                            <option value="<?= $r['id_empleado'] ?>">
                                <?= htmlspecialchars($r['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Fecha -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-calendar-date me-1"></i> Fecha
                    </label>
                    <input type="date" name="fecha" class="form-control" min="<?= date('Y-m-d') ?>" required>
                </div>

                <!-- Hora -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-clock me-1"></i> Hora
                    </label>
                    <input type="time" name="hora" class="form-control" required>
                </div>

                <!-- Botones -->
                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-calendar-check me-1"></i> Registrar Cita
                    </button>
                    <a href="../index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Cancelar
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
const inputPaciente = document.getElementById('buscar_paciente');
const listaResultados = document.getElementById('resultado_pacientes');
const idPaciente = document.getElementById('id_paciente');
const pacienteSeleccionado = document.getElementById('paciente_seleccionado');

inputPaciente.addEventListener('input', async function () {
    const texto = this.value.trim();

    idPaciente.value = "";
    pacienteSeleccionado.textContent = "";
    listaResultados.innerHTML = "";

    if (texto.length < 2) return;

    try {
        const response = await fetch(`/utils/buscar_paciente_ajax.php?q=${encodeURIComponent(texto)}`);
        const pacientes = await response.json();

        if (!Array.isArray(pacientes) || pacientes.length === 0) {
            listaResultados.innerHTML = '<div class="list-group-item text-muted">No se encontraron pacientes</div>';
            return;
        }

        pacientes.forEach(p => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action';
            item.innerHTML = `<strong>${p.nombre} ${p.apellido}</strong><br><small>Cédula: ${p.cedula}</small>`;

            item.addEventListener('click', function () {
                inputPaciente.value = `${p.nombre} ${p.apellido} - CC ${p.cedula}`;
                idPaciente.value = p.id_paciente;
                pacienteSeleccionado.textContent = `Paciente seleccionado: ${p.nombre} ${p.apellido}`;
                listaResultados.innerHTML = "";
            });

            listaResultados.appendChild(item);
        });
    } catch (error) {
        listaResultados.innerHTML = '<div class="list-group-item text-danger">Error al buscar pacientes</div>';
        console.error(error);
    }
});

document.addEventListener('click', function (e) {
    if (!listaResultados.contains(e.target) && e.target !== inputPaciente) {
        listaResultados.innerHTML = "";
    }
});
</script>

</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

