<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
include "db_connect.php";
include "plantilla.php";

// Validar ID
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "<div class='msg error'>ID inválido.</div>";
    include "footer.php";
    exit;
}

// Cargar médico
$stmt = $conn->prepare("SELECT * FROM medico WHERE id_medico = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$medico = $res->fetch_assoc();
$stmt->close();

if (!$medico) {
    echo "<div class='msg error'>Médico no encontrado.</div>";
    include "footer.php";
    exit;
}

$success = null;
$errors = [];

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $cedula = trim($_POST['cedula']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $especialidad = intval($_POST['especialidad']);

    if ($nombre === '') $errors[] = "El nombre es obligatorio.";
    if ($apellido === '') $errors[] = "El apellido es obligatorio.";
    if ($cedula === '') $errors[] = "La cédula es obligatoria.";
    if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo inválido.";

    if (empty($errors)) {
        $stmt = $conn->prepare("
            UPDATE medico 
            SET nombre=?, apellido=?, cedula=?, telefono=?, correo=?, id_especialidad=?
            WHERE id_medico=?
        ");
        $stmt->bind_param("ssssssi",
            $nombre, $apellido, $cedula, $telefono, $correo, $especialidad, $id
        );

        if ($stmt->execute()) {
            $success = "Datos actualizados correctamente.";
        } else {
            $errors[] = "Error al actualizar: ".$conn->error;
        }

        $stmt->close();
    }

    // actualizar los valores actuales en la interfaz
    $medico['nombre'] = $nombre;
    $medico['apellido'] = $apellido;
    $medico['cedula'] = $cedula;
    $medico['telefono'] = $telefono;
    $medico['correo'] = $correo;
    $medico['id_especialidad'] = $especialidad;
}
?>

<div class="card mx-auto" style="max-width: 700px;">
    <div style="padding: 30px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4" style="margin: -30px -30px 20px -30px; padding: 20px 30px;">
        <i class="bi bi-pencil-square"></i> Editar Médico
    </h2>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php foreach($errors as $e): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($e) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>

    <form method="post">

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label"><i class="bi bi-person"></i> Nombre *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($medico['nombre']) ?>">
            </div>

            <div class="col-md-6">
                <label for="apellido" class="form-label"><i class="bi bi-person"></i> Apellido *</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?= htmlspecialchars($medico['apellido']) ?>">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="cedula" class="form-label"><i class="bi bi-card-text"></i> Cédula *</label>
                <input type="text" class="form-control" id="cedula" name="cedula" value="<?= htmlspecialchars($medico['cedula']) ?>">
            </div>

            <div class="col-md-6">
                <label for="telefono" class="form-label"><i class="bi bi-telephone"></i> Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($medico['telefono']) ?>">
            </div>
        </div>

        <div class="mb-3">
            <label for="correo" class="form-label"><i class="bi bi-envelope"></i> Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars($medico['correo']) ?>">
        </div>

        <div class="mb-3">
            <label for="especialidad" class="form-label"><i class="bi bi-stethoscope"></i> Especialidad</label>
            <select class="form-select" id="especialidad" name="especialidad">
                <option value="0">-- Seleccionar especialidad --</option>
                <?php
                $q = $conn->query("SELECT * FROM especialidad ORDER BY nombre");
                while($row = $q->fetch_assoc()):
                    $sel = ($row['id_especialidad'] == $medico['id_especialidad']) ? 'selected' : '';
                ?>
                    <option value="<?= $row['id_especialidad'] ?>" <?= $sel ?>>
                        <?= htmlspecialchars($row['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-check-circle"></i> Guardar Cambios
            </button>
            <a href="lista_medicos.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </form>
    </div>
</div>

<?php include "footer.php"; ?>