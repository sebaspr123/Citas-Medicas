<?php include "plantilla.php"; ?>
<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
include "db_connect.php";

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitizar datos
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $cedula = trim($_POST['cedula'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $id_especialidad = $_POST['id_especialidad'] ?? '';

    // Convertir especialidad vacía a NULL
    $id_especialidad = ($id_especialidad === "0" || $id_especialidad === "") ? null : intval($id_especialidad);

    // Validaciones
    if ($nombre === '') $errors[] = "Nombre requerido";
    if ($apellido === '') $errors[] = "Apellido requerido";
    if ($cedula === '') $errors[] = "Cédula requerida";

    if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Correo inválido";
    }

    // Validar cédula única
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id_medico FROM medico WHERE cedula = ?");
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors[] = "Ya existe un médico con esa cédula.";
        }
        $stmt->close();
    }

    // Si todo está OK — insertar
    if (empty($errors)) {
        
        if ($id_especialidad === null) {
            // Insert SIN especialidad
            $stmt = $conn->prepare("
                INSERT INTO medico (nombre, apellido, cedula, telefono, correo, id_especialidad)
                VALUES (?, ?, ?, ?, ?, NULL)
            ");
            $stmt->bind_param("sssss", $nombre, $apellido, $cedula, $telefono, $correo);

        } else {
            // Insert CON especialidad
            $stmt = $conn->prepare("
                INSERT INTO medico (nombre, apellido, cedula, telefono, correo, id_especialidad)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssssi", $nombre, $apellido, $cedula, $telefono, $correo, $id_especialidad);
        }

        if ($stmt->execute()) {
            $success = "Médico registrado correctamente.";
            // Limpiar campos después de registrar
            $nombre = $apellido = $cedula = $telefono = $correo = '';
            $id_especialidad = null;
        } else {
            $errors[] = "Error al registrar: " . $stmt->error;
        }

        $stmt->close();
    }
}

?>

<div class="card mx-auto" style="max-width: 700px;">
    <div style="padding: 30px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4" style="margin: -30px -30px 20px -30px; padding: 20px 30px;">
        <i class="bi bi-person-badge"></i> Registrar Médico
    </h2>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($e) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>

    <form method="post" novalidate>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label"><i class="bi bi-person"></i> Nombre *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?=htmlspecialchars($nombre ?? '')?>" required>
            </div>
            <div class="col-md-6">
                <label for="apellido" class="form-label"><i class="bi bi-person"></i> Apellido *</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?=htmlspecialchars($apellido ?? '')?>" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="cedula" class="form-label"><i class="bi bi-card-text"></i> Cédula *</label>
                <input type="text" class="form-control" id="cedula" name="cedula" value="<?=htmlspecialchars($cedula ?? '')?>" required>
            </div>
            <div class="col-md-6">
                <label for="telefono" class="form-label"><i class="bi bi-telephone"></i> Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?=htmlspecialchars($telefono ?? '')?>">
            </div>
        </div>

        <div class="mb-3">
            <label for="correo" class="form-label"><i class="bi bi-envelope"></i> Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" value="<?=htmlspecialchars($correo ?? '')?>">
        </div>

        <div class="mb-3">
            <label for="id_especialidad" class="form-label"><i class="bi bi-stethoscope"></i> Especialidad</label>
            <select class="form-select" id="id_especialidad" name="id_especialidad">
                <option value="0">-- Seleccionar especialidad --</option>
                <?php
                $q = $conn->query("SELECT id_especialidad, nombre FROM especialidad ORDER BY nombre");
                while ($r = $q->fetch_assoc()) {
                    $selected = ($id_especialidad == $r['id_especialidad']) ? 'selected' : '';
                    echo "<option value='{$r['id_especialidad']}' $selected>".htmlspecialchars($r['nombre'])."</option>";
                }
                ?>
            </select>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-check-circle"></i> Registrar Médico
            </button>
            <a href="lista_medicos.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Ver listado
            </a>
        </div>
    </form>
    </div>
</div>

<?php include "footer.php"; ?>