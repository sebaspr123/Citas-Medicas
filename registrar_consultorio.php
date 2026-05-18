<?php include "plantilla.php"; ?>
<?php include "db_connect.php";

$info = []; 
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = trim($_POST['numero']);
    $piso = trim($_POST['piso']);

    if ($numero === "" || $piso === "") {
        $errors[] = "Todos los campos son obligatorios.";
    } else {
        $stmt = $conn->prepare("INSERT INTO consultorio (numero, piso) VALUES (?, ?)");
        $stmt->bind_param("ss", $numero, $piso);

        if ($stmt->execute()) {
            $info[] = "Consultorio registrado correctamente.";
        } else {
            $errors[] = "Error al registrar: " . $conn->error;
        }
    }
}

include "plantilla.php";
?>

<div class="card mx-auto" style="max-width: 600px;">
    <div style="padding: 30px;">
    <h2 class="card-header bg-primary text-white pb-3 mb-4" style="margin: -30px -30px 20px -30px; padding: 20px 30px;">
        <i class="bi bi-door-closed"></i> Registrar Consultorio
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

    <form method="POST">
        <div class="mb-3">
            <label for="numero" class="form-label"><i class="bi bi-door-closed"></i> Número del Consultorio</label>
            <input type="text" class="form-control" id="numero" name="numero" placeholder="Ej: 201" required>
        </div>

        <div class="mb-3">
            <label for="piso" class="form-label"><i class="bi bi-building"></i> Piso</label>
            <input type="number" class="form-control" id="piso" name="piso" placeholder="Ej: 2" required>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-check-circle"></i> Guardar
            </button>
            <a href="lista_consultorios.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Ver listado
            </a>
        </div>
    </form>
    </div>
</div>

<?php include "footer.php"; ?>
