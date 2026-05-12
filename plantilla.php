<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Sistema de Citas Médicas</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
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

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 30px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .menu a {
            display: block;
            padding: 14px 20px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            margin: 4px 0;
        }

        .menu a:hover,
        .menu a:active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-left-color: #fff;
            padding-left: 22px;
        }

        .content {
            flex: 1;
            margin-left: 260px;
            padding: 40px 30px;
            overflow-y: auto;
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #212529;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #0d6efd;
        }

        .card {
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .card h2 {
            margin-top: 0;
            color: #0d6efd;
            font-weight: 700;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
        }

        .btn-success,
        .btn-danger,
        .btn-warning,
        .btn-info {
            border: none;
        }

        .table {
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            font-weight: 600;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table th,
        .table td {
            padding: 15px;
            border-color: #e9ecef;
            vertical-align: middle;
        }

        .alert {
            border: none;
            border-radius: 10px;
            border-left: 4px solid;
        }

        .alert-success {
            border-left-color: #198754;
            background-color: #f0f9f6;
        }

        .alert-danger {
            border-left-color: #dc3545;
            background-color: #fdf6f7;
        }

        .btn-acciones {
            display: inline-block;
            margin: 4px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
    </style>

</head>

<body>
    <div class="layout">
        <aside class="sidebar">
            <h2><i class="bi bi-hospital"></i> Clínica Citas</h2>
            <nav class="menu">
                <a href="index.php"><i class="bi bi-house-door"></i> Inicio</a>
                <a href="registrar_paciente.php"><i class="bi bi-person-plus"></i> Registrar Paciente</a>
                <a href="lista_pacientes.php"><i class="bi bi-people"></i> Pacientes</a>

                <a href="registrar_medico.php"><i class="bi bi-stethoscope"></i> Registrar Médico</a>
                <a href="lista_medicos.php"><i class="bi bi-person-badge"></i> Médicos</a>

                <a href="registrar_cita.php"><i class="bi bi-calendar-plus"></i> Registrar Cita</a>
                <a href="buscar_cita.php"><i class="bi bi-search"></i> Buscar Cita</a>
                <a href="calendario.php"><i class="bi bi-calendar2"></i> Calendario</a>

                <a href="registrar_especialidad.php"><i class="bi bi-tags"></i> Especialidades</a>
                <a href="registrar_consultorio.php"><i class="bi bi-door-closed"></i> Consultorios</a>
                <a href="registrar_empleado.php"><i class="bi bi-briefcase"></i> Empleados</a>
            </nav>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="content">