<?php include "plantilla.php"; ?>
<?php include "db_connect.php"; ?>

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

<style>
.form-modern {
    max-width: 700px;
    margin: 25px auto;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px #0002;
}
.form-modern h2 {
    margin-bottom: 18px;
    color: #0a3d62;
}
.form-modern label {
    font-weight: 600;
    margin-top: 12px;
    display: block;
}
.form-modern input,
.form-modern select {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #bbb;
    margin-top: 5px;
}
.btn-save {
    margin-top: 18px;
    background: #0a3d62;
    color: white;
    padding: 12px 20px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
}
.btn-save:hover {
    background: #062a44;
}
.msg.success {
    background: #d4edda;
    padding: 10px;
    border-left: 4px solid #28a745;
    margin-bottom: 10px;
}
.msg.error {
    background: #f8d7da;
    padding: 10px;
    border-left: 4px solid #c0392b;
    margin-bottom: 10px;
}
</style>

<div class="form-modern">

    <h2>Editar Médico</h2>

    <?php if ($success): ?>
        <div class="msg success"><?= $success ?></div>
    <?php endif; ?>

    <?php foreach($errors as $e): ?>
        <div class="msg error"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <form method="post">

        <label>Nombre *</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($medico['nombre']) ?>">

        <label>Apellido *</label>
        <input type="text" name="apellido" value="<?= htmlspecialchars($medico['apellido']) ?>">

        <label>Cédula *</label>
        <input type="text" name="cedula" value="<?= htmlspecialchars($medico['cedula']) ?>">

        <label>Teléfono</label>
        <input type="text" name="telefono" value="<?= htmlspecialchars($medico['telefono']) ?>">

        <label>Correo</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($medico['correo']) ?>">

        <label>Especialidad</label>
        <select name="especialidad">
            <option value="0">--Sin asignar--</option>
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

        <button class="btn-save" type="submit">Guardar Cambios</button>
    </form>

</div>

<?php include "footer.php"; ?>