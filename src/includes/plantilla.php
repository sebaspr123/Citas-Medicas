<?php require_once "../auth/auth.php"; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Sistema de Citas Médicas</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #0d6efd;
      --secondary-color: #6c757d;
      --success-color: #198754;
      --danger-color: #dc3545;
      --warning-color: #ffc107;
      --info-color: #0dcaf0;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
      min-height: 100vh;
    }
    .layout { display: flex; min-height: 100vh; }

    /* --- SIDEBAR --- */
    .sidebar {
      width: 260px;
      background: linear-gradient(180deg, #0d6efd 0%, #0a58ca 100%);
      color: white;
      padding: 30px 0;
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      box-shadow: 2px 0 10px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 10px;
      font-size: 22px;
      font-weight: 700;
      letter-spacing: 0.5px;
      padding: 0 20px;
    }
    .menu-section-label {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.2px;
      color: rgba(255,255,255,0.5);
      padding: 16px 20px 4px 20px;
    }
    .menu a {
      display: block;
      padding: 11px 20px;
      color: rgba(255,255,255,0.85);
      text-decoration: none;
      font-size: 14px;
      transition: all 0.3s ease;
      border-left: 3px solid transparent;
      margin: 2px 0;
    }
    .menu a:hover, .menu a.active {
      background: rgba(255,255,255,0.15);
      color: white;
      border-left-color: #fff;
      padding-left: 22px;
    }
    .sidebar-footer {
      padding: 20px;
      border-top: 1px solid rgba(255,255,255,0.2);
      margin-top: auto;
    }
    .sidebar-footer .operador-info {
      font-size: 13px;
      color: rgba(255,255,255,0.75);
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .btn-logout {
      display: block;
      text-align: center;
      padding: 9px 12px;
      background: rgba(220,53,69,0.85);
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      transition: all 0.3s ease;
      border: 1px solid rgba(220,53,69,0.5);
    }
    .btn-logout:hover {
      background: rgba(220,53,69,1);
      color: white;
      transform: translateY(-1px);
    }
    .badge-rol {
      display: inline-block;
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 2px 7px;
      border-radius: 20px;
      margin-top: 2px;
    }
    .badge-admin  { background: rgba(255,193,7,0.25);  color: #ffe08a; border: 1px solid rgba(255,193,7,0.4); }
    .badge-empleado { background: rgba(13,202,240,0.2); color: #8de8f5; border: 1px solid rgba(13,202,240,0.35); }

    /* --- CONTENIDO --- */
    .content {
      flex: 1;
      margin-left: 260px;
      padding: 40px 30px;
      overflow-y: auto;
    }
    .page-title {
      font-size: 32px; font-weight: 700; color: #212529;
      margin-bottom: 30px; padding-bottom: 15px;
      border-bottom: 3px solid #0d6efd;
    }
    .card {
      border: none;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      border-radius: 12px;
      margin-bottom: 20px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
    .card h2 { margin-top: 0; color: #0d6efd; font-weight: 700; }
    .form-label { font-weight: 600; color: #495057; margin-bottom: 8px; }
    .form-control, .form-select {
      border: 1px solid #dee2e6; border-radius: 8px;
      padding: 10px 15px; font-size: 14px;
    }
    .form-control:focus, .form-select:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.25);
    }
    .btn { border-radius: 8px; font-weight: 600; padding: 10px 20px; font-size: 14px; transition: all 0.3s ease; }
    .btn-primary { background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); border: none; }
    .btn-primary:hover {
      background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(13,110,253,0.4);
    }
    .btn-success, .btn-danger, .btn-warning, .btn-info { border: none; }
    .table { border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; }
    .table thead { background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); color: white; font-weight: 600; }
    .table tbody tr:hover { background-color: #f8f9fa; }
    .table th, .table td { padding: 15px; border-color: #e9ecef; vertical-align: middle; }
    .alert { border: none; border-radius: 10px; border-left: 4px solid; }
    .alert-success { border-left-color: #198754; background-color: #f0f9f6; }
    .alert-danger  { border-left-color: #dc3545; background-color: #fdf6f7; }
    .btn-acciones { display: inline-block; margin: 4px; }
    .btn-sm { padding: 6px 12px; font-size: 13px; }
  </style>
</head>
<body>
<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div>
      <h2><i class="bi bi-hospital"></i> Clínica Citas</h2>

      <nav class="menu">

        <!-- ── Módulos disponibles para TODOS los usuarios ── -->
        <div class="menu-section-label">General</div>
        <a href="index.php"><i class="bi bi-house-door me-2"></i> Inicio</a>
        <a href="registrar_cita.php"><i class="bi bi-calendar-plus me-2"></i> Registrar Cita</a>
        <a href="buscar_cita.php"><i class="bi bi-search me-2"></i> Buscar Cita</a>
        <a href="calendario.php"><i class="bi bi-calendar2 me-2"></i> Calendario</a>

        <div class="menu-section-label">Pacientes</div>
        <a href="registrar_paciente.php"><i class="bi bi-person-plus me-2"></i> Registrar Paciente</a>
        <a href="lista_pacientes.php"><i class="bi bi-people me-2"></i> Lista de Pacientes</a>

        <div class="menu-section-label">Médicos</div>
        <a href="lista_medicos.php"><i class="bi bi-person-badge me-2"></i> Lista de Médicos</a>

        <?php if ($_SESSION['rol'] === 'admin'): ?>
        <!-- ── Módulos exclusivos del ADMINISTRADOR ── -->
        <div class="menu-section-label">Administración</div>
        <a href="registrar_medico.php"><i class="bi bi-stethoscope me-2"></i> Registrar Médico</a>
        <a href="lista_especialidades.php"><i class="bi bi-tags me-2"></i> Especialidades</a>
        <a href="lista_consultorios.php"><i class="bi bi-door-closed me-2"></i> Consultorios</a>
        <a href="lista_empleados.php"><i class="bi bi-briefcase me-2"></i> Usuarios del Sistema</a>
        <?php endif; ?>

      </nav>
    </div>

    <!-- Operador activo y cerrar sesión -->
    <div class="sidebar-footer">
      <div class="operador-info">
        <i class="bi bi-person-circle fs-5"></i>
        <div>
          <div class="fw-semibold text-white"><?= htmlspecialchars($_SESSION['nombre']) ?></div>
          <span class="badge-rol badge-<?= htmlspecialchars($_SESSION['rol']) ?>">
            <?= $_SESSION['rol'] === 'admin' ? 'Administrador' : 'Empleado' ?>
          </span>
        </div>
      </div>
      <a href="logout.php" class="btn-logout">
        <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
      </a>
    </div>
  </aside>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="content">

    <?php if (isset($_GET['error']) && $_GET['error'] === 'acceso_denegado'): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
      <i class="bi bi-shield-lock-fill me-2"></i>
      <strong>Acceso denegado.</strong> No tienes permisos para acceder a esa sección.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

