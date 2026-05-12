<?php
// editar_paciente.php
ini_set('display_errors',1); error_reporting(E_ALL);
include "db_connect.php";

$errors=[]; $success=null;
$id = intval($_GET['id'] ?? 0);
if ($id<=0) { header("Location: lista_pacientes.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $cedula = trim($_POST['cedula'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $fecha_n = trim($_POST['fecha_nacimiento'] ?? '');

    if ($nombre==='') $errors[]="Nombre requerido";
    if ($apellido==='') $errors[]="Apellido requerido";
    if ($cedula==='') $errors[]="Cédula requerida";
    if ($correo!=='' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[]="Correo inválido";

    if (empty($errors)) {
        // verificar cedula no repetida en otros registros
        $stmt = $conn->prepare("SELECT id_paciente FROM paciente WHERE cedula=? AND id_paciente<>?");
        $stmt->bind_param("si",$cedula,$id);
        $stmt->execute(); $stmt->store_result();
        if ($stmt->num_rows>0) $errors[]="Otra persona tiene esa cédula.";
        $stmt->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE paciente SET nombre=?, apellido=?, cedula=?, telefono=?, direccion=?, correo=?, fecha_nacimiento=? WHERE id_paciente=?");
        $stmt->bind_param("sssssssi",$nombre,$apellido,$cedula,$telefono,$direccion,$correo,$fecha_n,$id);
        if ($stmt->execute()) { $success="Paciente actualizado."; }
        else $errors[]="Error: ".$conn->error;
        $stmt->close();
    }
}

// cargar datos actuales
$res = $conn->query("SELECT * FROM paciente WHERE id_paciente = $id");
if (!$res || $res->num_rows==0) { header("Location: lista_pacientes.php"); exit; }
$data = $res->fetch_assoc();

include "plantilla.php";
?>
<div class="card mx-auto" style="max-width: 700px;">
    <div style="padding: 30px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4" style="margin: -30px -30px 20px -30px; padding: 20px 30px;">
        <i class="bi bi-pencil-square"></i> Editar Paciente
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
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?=htmlspecialchars($data['nombre'])?>" required>
            </div>
            <div class="col-md-6">
                <label for="apellido" class="form-label"><i class="bi bi-person"></i> Apellido *</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?=htmlspecialchars($data['apellido'])?>" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="cedula" class="form-label"><i class="bi bi-card-text"></i> Cédula *</label>
                <input type="text" class="form-control" id="cedula" name="cedula" value="<?=htmlspecialchars($data['cedula'])?>" required>
            </div>
            <div class="col-md-6">
                <label for="telefono" class="form-label"><i class="bi bi-telephone"></i> Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?=htmlspecialchars($data['telefono'])?>">
            </div>
        </div>

        <div class="mb-3">
            <label for="direccion" class="form-label"><i class="bi bi-geo-alt"></i> Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="<?=htmlspecialchars($data['direccion'])?>">
        </div>

        <div class="mb-3">
            <label for="correo" class="form-label"><i class="bi bi-envelope"></i> Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" value="<?=htmlspecialchars($data['correo'])?>">
        </div>

        <div class="mb-3">
            <label for="fecha_nacimiento" class="form-label"><i class="bi bi-calendar"></i> Fecha de nacimiento</label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?=htmlspecialchars($data['fecha_nacimiento'])?>">
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-check-circle"></i> Guardar cambios
            </button>
            <a href="lista_pacientes.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </form>
    </div>
</div>
<?php include "footer.php"; ?>