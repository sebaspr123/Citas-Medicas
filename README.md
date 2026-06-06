# 🏥 Sistema de Citas Médicas

Sistema web para la gestión de citas médicas desarrollado en PHP puro con MySQL. Permite administrar pacientes, médicos, consultorios, especialidades y empleados, con control de acceso por roles.

---

## 📁 Estructura del Proyecto

```
Citas-Medicas/
├── public/
│   ├── assets/css/estilos.css
│   └── index.html
└── src/
    ├── index.php                  # Dashboard principal
    ├── auth/
    │   ├── auth.php               # Sesión y funciones de rol
    │   ├── login.php
    │   ├── logout.php
    │   └── procesar_login.php
    ├── config/
    │   └── db_connect.php         # Conexión a la base de datos
    ├── includes/
    │   ├── plantilla.php          # Layout base (sidebar + head)
    │   └── footer.php
    ├── citas/
    │   ├── registrar_cita.php
    │   ├── buscar_cita.php
    │   ├── buscar_cita_resultado.php
    │   ├── editar_cita.php
    │   ├── get_citas.php
    │   └── calendario.php
    ├── pacientes/
    │   ├── lista_pacientes.php
    │   ├── registrar_paciente.php
    │   ├── editar_paciente.php
    │   └── registrar_historia.php
    ├── medicos/
    │   ├── lista_medicos.php
    │   └── registrar_medico.php
    ├── admin/                     # Solo accesible por administradores
    │   ├── especialidades/
    │   │   ├── lista_especialidades.php
    │   │   └── registrar_especialidad.php
    │   ├── consultorios/
    │   │   ├── lista_consultorios.php
    │   │   └── registrar_consultorio.php
    │   └── empleados/
    │       ├── lista_empleados.php
    │       └── registrar_empleado.php
    └── utils/
        ├── buscar_paciente_ajax.php
        └── generar_hash.php
```

---

## ⚙️ Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior / MariaDB
- XAMPP, Laragon, o servidor con soporte PHP+MySQL

---

## 🚀 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/Citas-Medicas.git
cd Citas-Medicas
```

### 2. Configurar la base de datos

Crea la base de datos en MySQL e importa el script SQL:

```bash
mysql -u root -p < database/citas_medicas.sql
```

### 3. Configurar la conexión

Copia el archivo de ejemplo y edítalo con tus credenciales:

```bash
cp src/config/db_connect.example.php src/config/db_connect.php
```

```php
// src/config/db_connect.php
$conn = new mysqli("localhost", "tu_usuario", "tu_password", "citas_medicas");
```

### 4. Iniciar el servidor

```bash
cd src
php -S localhost:8000
```

Abre tu navegador en: [http://localhost:8000](http://localhost:8000)

---

## 👤 Roles de Usuario

| Rol | Permisos |
|---|---|
| **Administrador** | Acceso total: gestión de médicos, consultorios, especialidades y usuarios |
| **Empleado** | Registrar y buscar citas, gestionar pacientes |

---

## ✨ Funcionalidades

- 🔐 Autenticación con control de sesión y roles
- 📊 Dashboard con estadísticas en tiempo real
- 📅 Registro, búsqueda y gestión de citas
- 🗓️ Vista de calendario de citas
- 👨‍⚕️ Gestión de médicos y especialidades
- 🧑‍🤝‍🧑 Gestión de pacientes con historial
- 🏥 Gestión de consultorios
- 👥 Gestión de usuarios del sistema (solo admin)
- 🔍 Búsqueda de pacientes por AJAX en tiempo real

---

## 🛡️ Seguridad

- Contraseñas almacenadas con `password_hash()` (bcrypt)
- Validación de sesión en cada página mediante `auth.php`
- Protección de rutas administrativas con `soloAdmin()`
- Uso de `prepared statements` para prevenir SQL Injection
- Sanitización de salidas con `htmlspecialchars()`

---

## 🗃️ Base de Datos

Tablas principales:

- `usuario` — credenciales y roles
- `empleado` — datos del personal
- `medico` — médicos registrados
- `especialidad` — especialidades médicas
- `paciente` — pacientes del sistema
- `cita` — citas agendadas
- `consultorio` — consultorios disponibles

---

## 👨‍💻 Autor

Desarrollado por **Sebastián** — Ingeniería de Sistemas UNICOR, Semestre VI  
Asignatura: Ingeniería de Software — Corte III
