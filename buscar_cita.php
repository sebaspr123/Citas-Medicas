<?php include "plantilla.php"; ?>
<?php include "db_connect.php"; ?>

<?php

$resultado = "";

/* --------------------------------------
   ELIMINAR UNA CITA
----------------------------------------*/
if (isset($_GET['eliminar'])) {
    $id_eliminar = intval($_GET['eliminar']);
    $delete = $conn->query("DELETE FROM cita WHERE id_cita = $id_eliminar");

    if ($delete) {
        $resultado = '<div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Cita eliminada correctamente.
                      </div>';
    } else {
        $resultado = '<div class="alert alert-danger">
                        <i class="bi bi-x-circle-fill me-2"></i>
                        Error al eliminar la cita.
                      </div>';
    }
}

/* --------------------------------------
   BUSCAR CITA
----------------------------------------*/
if (isset($_GET['buscar'])) {
    $busqueda     = strtolower(trim($_GET['busqueda']));
    $busqueda_sql = $conn->real_escape_string($busqueda);

    $sql = "
        SELECT 
            c.id_cita, c.fecha, c.hora, c.estado,
            p.id_paciente,
            p.nombre  AS nombre_paciente,
            p.apellido AS apellido_paciente,
            p.cedula   AS cedula_paciente,
            m.nombre   AS nombre_medico
        FROM cita c
        LEFT JOIN paciente p ON c.id_paciente = p.id_paciente
        LEFT JOIN medico   m ON c.id_medico   = m.id_medico
        WHERE 
            LOWER(p.nombre)  LIKE '%$busqueda_sql%' OR
            LOWER(p.apellido) LIKE '%$busqueda_sql%' OR
            LOWER(CONCAT(p.nombre,' ',p.apellido)) LIKE '%$busqueda_sql%' OR
            p.cedula       LIKE '%$busqueda_sql%' OR
            p.id_paciente  LIKE '%$busqueda_sql%'
    ";

    $res = $conn->query($sql);

    if ($res && $res->num_rows > 0) {
        $resultado .= '
        <div class="table-responsive mt-4">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>Paciente</th>
                <th>Cédula</th>
                <th>Médico</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>';

        while ($f = $res->fetch_assoc()) {
            $estado_badge = match ($f['estado'] ?? 'pendiente') {
                'confirmada' => 'success',
                'cancelada'  => 'danger',
                default      => 'warning'
            };

            $resultado .= "
              <tr>
                <td>{$f['id_cita']}</td>
                <td>
                  {$f['nombre_paciente']} {$f['apellido_paciente']}<br>
                  <small class='text-muted'>ID: {$f['id_paciente']}</small>
                </td>
                <td>{$f['cedula_paciente']}</td>
                <td>{$f['nombre_medico']}</td>
                <td>{$f['fecha']}</td>
                <td>{$f['hora']}</td>
                <td><span class='badge bg-{$estado_badge}'>" . ucfirst($f['estado'] ?? 'pendiente') . "</span></td>
                <td>
                  <a href='editar_cita.php?id={$f['id_cita']}' class='btn btn-info btn-sm btn-acciones'>
                    <i class='bi bi-pencil'></i> Editar
                  </a>
                  <a href='buscar_cita.php?eliminar={$f['id_cita']}' 
                     class='btn btn-danger btn-sm btn-acciones'
                     onclick=\"return confirm('¿Seguro que deseas eliminar esta cita?');\">
                    <i class='bi bi-trash'></i> Eliminar
                  </a>
                </td>
              </tr>";
        }

        $resultado .= '</tbody></table></div>';
    } else {
        $resultado .= '<div class="alert alert-warning mt-3">
                         <i class="bi bi-search me-2"></i>
                         No se encontraron citas con ese criterio.
                       </div>';
    }
}
?>

<!-- Título -->
<h1 class="page-title">
    <i class="bi bi-search me-2"></i> Buscar Cita
</h1>

<!-- Mensaje de eliminación -->
<?= $resultado_eliminacion ?? "" ?>

<!-- Formulario de búsqueda -->
<div class="card mb-4">
    <div class="card-body p-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-9">
                <label class="form-label">
                    <i class="bi bi-person-search me-1"></i> Buscar paciente
                </label>
                <input
                    type="text"
                    name="busqueda"
                    class="form-control"
                    placeholder="Nombre, Apellido, Cédula o ID del Paciente"
                    value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>"
                    required>
            </div>
            <div class="col-md-3">
                <button type="submit" name="buscar" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Resultados -->
<?= $resultado ?>

</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>