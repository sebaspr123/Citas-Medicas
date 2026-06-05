<?php
// Router central para la aplicación
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = dirname($_SERVER['SCRIPT_NAME']);

// Remover la ruta base de la URI
$route = str_replace($basePath, '', $requestUri);
$route = rtrim($route, '/') ?: '/';

// Mapear rutas
switch ($route) {
    case '/':
        // Redirigir a public/index.html o al dashboard
        header('Location: public/index.html');
        exit;
    
    case '/dashboard':
    case '/admin':
        include 'src/index.php';
        exit;
    
    // Rutas de autenticación
    case '/login':
        include 'src/auth/login.php';
        exit;
    case '/logout':
        include 'src/auth/logout.php';
        exit;
    case '/procesar_login':
        include 'src/auth/procesar_login.php';
        exit;
    
    // Rutas de pacientes
    case '/pacientes':
        include 'src/pacientes/lista_pacientes.php';
        exit;
    case '/pacientes/registrar':
        include 'src/pacientes/registrar_paciente.php';
        exit;
    case '/pacientes/editar':
        include 'src/pacientes/editar_paciente.php';
        exit;
    
    // Rutas de médicos
    case '/medicos':
        include 'src/medicos/lista_medicos.php';
        exit;
    case '/medicos/registrar':
        include 'src/medicos/registrar_medico.php';
        exit;
    case '/medicos/editar':
        include 'src/medicos/editar_medico.php';
        exit;
    case '/medicos/historia':
        include 'src/medicos/registrar_historia.php';
        exit;
    
    // Rutas de citas
    case '/citas':
        include 'src/citas/calendario.php';
        exit;
    case '/citas/registrar':
        include 'src/citas/registrar_cita.php';
        exit;
    case '/citas/editar':
        include 'src/citas/editar_cita.php';
        exit;
    case '/citas/buscar':
        include 'src/citas/buscar_cita.php';
        exit;
    case '/citas/buscar_resultado':
        include 'src/citas/buscar_cita_resultado.php';
        exit;
    case '/citas/get':
        include 'src/citas/get_citas.php';
        exit;
    
    // Rutas de admin
    case '/admin/especialidades':
        include 'src/admin/especialidades/lista_especialidades.php';
        exit;
    case '/admin/especialidades/registrar':
        include 'src/admin/especialidades/registrar_especialidad.php';
        exit;
    
    case '/admin/consultorios':
        include 'src/admin/consultorios/lista_consultorios.php';
        exit;
    case '/admin/consultorios/registrar':
        include 'src/admin/consultorios/registrar_consultorio.php';
        exit;
    
    case '/admin/empleados':
        include 'src/admin/empleados/lista_empleados.php';
        exit;
    case '/admin/empleados/registrar':
        include 'src/admin/empleados/registrar_empleado.php';
        exit;
    case '/admin/empleados/editar':
        include 'src/admin/empleados/editar_ empleado.php';
        exit;
    
    // Rutas de API/utilidades
    case '/api/buscar_paciente':
        include 'src/utils/buscar_paciente_ajax.php';
        exit;
    case '/utils/generar_hash':
        include 'src/utils/generar_hash.php';
        exit;
    
    // Servir archivos estáticos
    default:
        if (preg_match('/\.(css|js|jpg|jpeg|png|gif|svg|woff|woff2|ttf|eot)$/', $route)) {
            $file = __DIR__ . '/public' . $route;
            if (file_exists($file)) {
                // Determinar tipo de contenido
                $mime_types = [
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'svg' => 'image/svg+xml',
                    'woff' => 'font/woff',
                    'woff2' => 'font/woff2',
                    'ttf' => 'font/ttf',
                    'eot' => 'application/vnd.ms-fontobject'
                ];
                
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $mime = $mime_types[$ext] ?? 'application/octet-stream';
                
                header("Content-Type: $mime");
                readfile($file);
                exit;
            }
        }
        
        // No encontrado
        http_response_code(404);
        echo "404 - Página no encontrada: $route";
        exit;
}
