<?php include __DIR__ . "/includes/plantilla.php"; ?>
<?php include __DIR__ . "/config/db_connect.php"; ?>
<?php
$totalPacientes = $conn->query("SELECT COUNT(*) AS c FROM paciente")->fetch_assoc()['c'];
$totalMedicos   = $conn->query("SELECT COUNT(*) AS c FROM medico")->fetch_assoc()['c'];
$totalCitas     = $conn->query("SELECT COUNT(*) AS c FROM cita")->fetch_assoc()['c'];
$totalHoy       = $conn->query("SELECT COUNT(*) AS c FROM cita WHERE fecha = CURDATE()")->fetch_assoc()['c'];
?>

<style>
    .dash-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .dash-card {
        padding: 30px 25px;
        border-radius: 14px;
        color: white;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .dash-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .dash-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .dash-card.card-blue {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }

    .dash-card.card-green {
        background: linear-gradient(135deg, #198754 0%, #157347 100%);
    }

    .dash-card.card-orange {
        background: linear-gradient(135deg, #fd7e14 0%, #f8690e 100%);
    }

    .dash-card.card-purple {
        background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
    }

    .dash-number {
        font-size: 48px;
        font-weight: 700;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .dash-label {
        font-size: 15px;
        opacity: 0.95;
        font-weight: 500;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .quick-btn {
        padding: 18px 25px;
        border-radius: 10px;
        font-size: 15px;
        text-decoration: none;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .quick-btn.primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        color: white;
    }

    .quick-btn.secondary {
        background: white;
        color: #0d6efd;
        border: 2px solid #0d6efd;
    }

    .quick-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .quick-btn.secondary:hover {
        background: #0d6efd;
        color: white;
    }
</style>

<div class="container-fluid">
    <h1 class="page-title mb-4"><i class="bi bi-speedometer2"></i> Panel General</h1>

    <!-- TARJETAS SUPERIORES -->
    <div class="dash-cards">
        <div class="dash-card card-blue">
            <div class="dash-number"><i class="bi bi-people"></i><?= $totalPacientes ?></div>
            <div class="dash-label">Pacientes Registrados</div>
        </div>

        <div class="dash-card card-green">
            <div class="dash-number"><i class="bi bi-stethoscope"></i><?= $totalMedicos ?></div>
            <div class="dash-label">Médicos Activos</div>
        </div>

        <div class="dash-card card-orange">
            <div class="dash-number"><i class="bi bi-calendar2-check"></i><?= $totalCitas ?></div>
            <div class="dash-label">Citas Totales</div>
        </div>

        <div class="dash-card card-purple">
            <div class="dash-number"><i class="bi bi-clock-history"></i><?= $totalHoy ?></div>
            <div class="dash-label">Citas Hoy</div>
        </div>
    </div>

    <!-- ACCESOS RÁPIDOS -->
    <h2 class="mt-5 mb-4" style="font-size: 24px; font-weight: 700; color: #212529;">
        <i class="bi bi-lightning"></i> Accesos Rápidos
    </h2>

    <div class="quick-actions">
        <a href="/citas/registrar_cita.php" class="quick-btn primary">
            <i class="bi bi-calendar-plus"></i> Registrar Cita
        </a>
        <a href="/pacientes/lista_pacientes.php" class="quick-btn secondary">
            <i class="bi bi-people"></i> Ver Pacientes
        </a>
        <a href="/medicos/lista_medicos.php" class="quick-btn secondary">
            <i class="bi bi-person-badge"></i> Ver Médicos
        </a>
        <a href="/citas/calendario.php" class="quick-btn secondary">
            <i class="bi bi-calendar2"></i> Calendario
        </a>
    </div>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>