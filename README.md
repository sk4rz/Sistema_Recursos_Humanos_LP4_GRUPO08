# Sistema_Recursos_Humanos_LP4_GRUPO08

Sistema completo de gestión de recursos humanos desarrollado en **PHP 8.0+** con arquitectura **MVC**, **Bootstrap 5** y **MySQL/MariaDB**.

## Integrantes del Grupo 08 - Lenguaje de Programación IV

1. **Oscar Moreno Acosta**
2. **Adrian Ayato Aniya Saldaña**
3. **Juan Carlos Mitsva Llerena Zavaleta**
4. **Manuel Antonio Iglesias Guevara**

## Instalación Rápida

### Opción 1: Instalación Automática (Recomendada)

1. **Ejecutar el instalador:**
   ```bash
   php install.php
   ```

2. **Seguir las instrucciones en pantalla:**
   - Ingresar datos de la base de datos
   - El script creará todo automáticamente

3. **Listo.** Abre tu navegador en la URL indicada

### Opción 2: Instalación Manual

#### Paso 1: Verificar Requisitos

Asegúrate de tener instalado:
- **PHP 8.0** o superior
- **MySQL 5.7+** o **MariaDB 10.3+**
- Extensiones PHP: `pdo`, `pdo_mysql`, `mbstring`, `json`, `gd`

**Verificar PHP:**
```bash
php -v
```

**Verificar Extensiones:**
```bash
php -m | grep -i pdo
php -m | grep -i mbstring
```

#### Paso 2: Configurar Base de Datos

**Crear la base de datos:**
```sql
CREATE DATABASE rrhh_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Importar el esquema:**
```bash
mysql -u root -p rrhh_system < scripts/01-schema.sql
```

**Importar datos de prueba (opcional):**
```bash
mysql -u root -p rrhh_system < scripts/02-seed-data.sql
```

#### Paso 3: Configurar la Aplicación

Edita el archivo `config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'rrhh_system');
define('DB_USER', 'root');
define('DB_PASS', 'tu_contraseña');
define('BASE_URL', 'http://localhost:82/public');
```

#### Paso 4: Configurar Servidor Web

**Opción A: Servidor PHP Integrado (Recomendado para pruebas)**
```bash
cd public
php -S localhost:8000
```

**Opción B: Apache (XAMPP/WAMP)**
1. Copiar proyecto a `htdocs` (XAMPP) o `www` (WAMP)
2. Verificar que `mod_rewrite` esté habilitado
3. Acceder a `http://localhost/RRHH/public`

## Requisitos del Sistema

- **PHP 8.0** o superior
- **MySQL 5.7+** o **MariaDB 10.3+**
- Extensiones PHP: `pdo`, `pdo_mysql`, `mbstring`, `json`, `gd` (para reportes PDF)
- Servidor web (Apache con mod_rewrite o servidor PHP integrado)

### Habilitar Extensiones PHP

#### Windows (XAMPP/WAMP)

1. Editar `php.ini` (generalmente en `C:\xampp\php\php.ini`)
2. Descomentar las siguientes líneas:
   ```ini
   extension=pdo_mysql
   extension=mbstring
   extension=gd
   extension=json
   ```
3. Reiniciar Apache

#### Linux (Ubuntu/Debian)

```bash
sudo apt-get update
sudo apt-get install php8.2-mysql php8.2-mbstring php8.2-gd php8.2-json
sudo systemctl restart apache2
```

#### macOS (Homebrew)

```bash
brew install php@8.2
pecl install pdo_mysql
```

**Verificar extensiones:**
```bash
php -m | grep -E "pdo_mysql|mbstring|gd|json"
```

## Credenciales de Acceso

Después de la instalación, puedes iniciar sesión con:

- **Administrador**: `admin@example.com` / `password123`
- **Gerente**: `manager@example.com` / `password123`
- **Empleado**: `employee@example.com` / `password123`

**IMPORTANTE**: Cambia estas contraseñas después de la primera instalación.

## Características Principales

### Módulos del Sistema

1. **Dashboard**
   - Métricas en tiempo real
   - Gráficos interactivos (Chart.js)
   - Resumen de actividades recientes

2. **Gestión de Empleados**
   - Registro completo de personal
   - Información laboral y de contacto
   - Historial de empleados

3. **Departamentos**
   - Organización por áreas
   - Asignación de gerentes
   - Presupuestos por departamento

4. **Control de Asistencia**
   - Registro de entrada y salida
   - Estados: presente, ausente, retrasado, remoto
   - Reportes de asistencia

5. **Planillas**
   - Cálculo de nóminas
   - Bonificaciones y deducciones
   - Estados: aprobado, pagado, cancelado

6. **Gestión de Vacaciones**
   - Solicitudes de vacaciones
   - Aprobación/rechazo de solicitudes
   - Control de saldos de vacaciones

7. **Evaluaciones de Desempeño**
   - Calificaciones del personal
   - Comentarios y áreas de mejora
   - Seguimiento de objetivos

8. **Gestión de Usuarios** (Solo Administradores)
   - Crear y gestionar cuentas de acceso
   - Asignar roles (administrador, gerente, empleado)
   - Asociar usuarios con empleados
   - Control de usuarios activos/inactivos

### Reportes

- **PDF**: Reportes de empleados, planillas y asistencia (MPDF)
- **Excel (CSV)**: Exportación de datos para análisis

## Tecnologías Utilizadas

- **Backend**: PHP 8.0+ (Arquitectura MVC)
- **Base de Datos**: MySQL 5.7+ / MariaDB 10.3+ (PDO)
- **Frontend**: Bootstrap 5, JavaScript ES6+
- **Gráficos**: Chart.js
- **Reportes**: MPDF (PDF), CSV (Excel)

## Estructura del Proyecto

```
RRHH/
├── app/
│   ├── controllers/     # Controladores (lógica de negocio)
│   ├── models/          # Modelos (acceso a datos)
│   ├── views/           # Vistas (interfaz de usuario)
│   ├── core/            # Clases base del sistema
│   └── routes/          # Definición de rutas
├── config/              # Configuración del sistema
├── public/              # Punto de entrada (DocumentRoot)
│   ├── assets/          # CSS, JavaScript, imágenes
│   └── index.php        # Front controller
├── scripts/             # Scripts SQL
│   ├── 01-schema.sql    # Esquema de base de datos
│   └── 02-seed-data.sql # Datos de prueba
├── temp/                # Archivos temporales
├── reports/             # Reportes generados
├── install.php          # Instalador automático
└── README.md            # Este archivo
```

## Seguridad

El sistema implementa las siguientes medidas de seguridad:

- **Consultas preparadas con PDO** (previene SQL injection)
- **Sanitización de entradas** (previene XSS)
- **Validación server-side y client-side**
- **Tokens CSRF** para formularios
- **Sesiones seguras** (HttpOnly, SameSite)
- **Control de acceso por roles**
- **Auditoría de acciones** (registros de auditoría)

## Diseño

- **Tema oscuro profesional** con colores estandarizados
- **Diseño responsivo** (funciona en móviles, tablets y desktop)
- **Interfaz intuitiva** y fácil de usar
- **Componentes consistentes** en todo el sistema
- **Estado activo en navegación** (resalta el módulo actual)

## Uso del Sistema

### Para Administradores

1. **Gestionar Usuarios**: Crear cuentas de acceso y asignar roles
2. **Gestionar Empleados**: Registrar nuevo personal
3. **Gestionar Departamentos**: Crear y organizar áreas
4. **Aprobar Solicitudes**: Revisar y aprobar vacaciones
5. **Ver Reportes**: Generar reportes PDF y Excel

### Para Gerentes

1. **Ver Empleados**: Consultar información del personal
2. **Gestionar Asistencia**: Registrar y revisar asistencia
3. **Aprobar Vacaciones**: Aprobar solicitudes de su equipo
4. **Ver Planillas**: Consultar información de nóminas

### Para Empleados

1. **Ver Perfil**: Consultar información personal
2. **Solicitar Vacaciones**: Crear solicitudes de vacaciones
3. **Ver Asistencia**: Consultar registro de asistencia

## Solución de Problemas

### Error: "No se puede conectar a la base de datos"

**Solución:**
- Verifica las credenciales en `config/config.php`
- Asegúrate de que MySQL esté corriendo
- Verifica que la base de datos exista

### Error 404 en todas las rutas

**Solución:**
- Verifica que `mod_rewrite` esté habilitado (Apache)
- Verifica que el archivo `.htaccess` esté presente en `public/`
- Si usas servidor PHP integrado, asegúrate de estar en el directorio `public/`

### Problemas con tildes o caracteres especiales

**Solución:**
- Asegúrate de que la base de datos use `utf8mb4`
- Verifica que `config/config.php` tenga `DB_CHARSET = 'utf8mb4'`
- Los scripts SQL ya están configurados con utf8mb4

### Extensiones PHP faltantes

**Solución:**
- Consulta la sección "Habilitar Extensiones PHP" arriba
- Verifica que las extensiones estén habilitadas: `php -m`
- Reinicia el servidor web después de habilitar extensiones

### Error: "Vista no encontrada"

**Solución:**
- Verifica que el archivo exista en `app/views/`
- Verifica permisos de lectura

### Error al iniciar sesión

**Solución:**
- Verifica que los usuarios existan en la base de datos
- Si usaste el seed data, las credenciales son: `admin@example.com` / `password123`
- Si el problema persiste, ejecuta `scripts/03-update-passwords.sql` para actualizar las contraseñas

## Notas para Desarrolladores

### Agregar un Nuevo Módulo

1. Crear modelo en `app/models/MiModelo.php`
2. Crear controlador en `app/controllers/MiController.php`
3. Crear vista en `app/views/mi-modulo/index.php`
4. Agregar rutas en `app/routes/web.php`

### Estructura de un Controlador

```php
<?php
/**
 * Controlador de Ejemplo
 */
class MiController extends Controller {
    private $modelo;

    public function __construct() {
        parent::__construct();
        $this->requireAuth(); // Requiere autenticación
        $this->requireRole(['administrador']); // Requiere rol específico
        $this->modelo = new MiModelo();
    }

    public function index() {
        $datos = $this->modelo->getAll();
        $this->view('mi-modulo/index', ['datos' => $datos]);
    }
}
```

### Convenciones de Código

- **Nombres de clases**: PascalCase (ej: `EmpleadoController`)
- **Nombres de métodos**: camelCase (ej: `getAll()`)
- **Nombres de variables**: camelCase (ej: `$employeeModel`)
- **Archivos**: Mismo nombre que la clase (ej: `EmpleadoController.php`)
- **Tablas de BD**: Español, plural (ej: `empleados`, `departamentos`)
- **Comentarios**: En español, claros y concisos

## Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

## Versión

**Versión actual**: 1.0.0

---

**Desarrollado para la gestión eficiente de recursos humanos**
