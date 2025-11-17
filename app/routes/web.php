<?php
/**
 * Rutas de la aplicación
 */

// Rutas de autenticación
$router->get('/auth/login', 'AutenticacionController@login');
$router->post('/auth/login', 'AutenticacionController@login');
$router->post('/auth/logout', 'AutenticacionController@logout');

// Redirigir raíz a dashboard o login
$router->get('/', function() {
    if (isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "/dashboard");
    } else {
        header("Location: " . BASE_URL . "/auth/login");
    }
    exit;
});

// Rutas protegidas
$router->get('/dashboard', 'TableroController@index');

// Empleados
$router->get('/employees', 'EmpleadoController@index');
$router->post('/employees/create', 'EmpleadoController@create');
$router->post('/employees/update', 'EmpleadoController@update');
$router->post('/employees/delete', 'EmpleadoController@delete');

// Departamentos
$router->get('/departments', 'DepartamentoController@index');
$router->post('/departments/create', 'DepartamentoController@create');
$router->post('/departments/update', 'DepartamentoController@update');
$router->post('/departments/delete', 'DepartamentoController@delete');

// Asistencia
$router->get('/attendance', 'AsistenciaController@index');
$router->post('/attendance/create', 'AsistenciaController@create');
$router->post('/attendance/update', 'AsistenciaController@update');
$router->post('/attendance/delete', 'AsistenciaController@delete');

// Planillas
$router->get('/payroll', 'PlanillaController@index');
$router->post('/payroll/create', 'PlanillaController@create');
$router->post('/payroll/update', 'PlanillaController@update');
$router->post('/payroll/delete', 'PlanillaController@delete');

// Vacaciones
$router->get('/leave-requests', 'VacacionController@index');
$router->post('/leave-requests/create', 'VacacionController@create');
$router->post('/leave-requests/approve', 'VacacionController@approve');
$router->post('/leave-requests/reject', 'VacacionController@reject');
$router->post('/leave-requests/delete', 'VacacionController@delete');

// Evaluaciones
$router->get('/evaluations', 'EvaluacionController@index');
$router->post('/evaluations/create', 'EvaluacionController@create');
$router->post('/evaluations/update', 'EvaluacionController@update');
$router->post('/evaluations/delete', 'EvaluacionController@delete');

// Usuarios (solo administradores)
$router->get('/users', 'UsuarioController@index');
$router->get('/users/get-employee', 'UsuarioController@getEmployee');
$router->post('/users/create', 'UsuarioController@create');
$router->post('/users/update', 'UsuarioController@update');
$router->post('/users/delete', 'UsuarioController@delete');
$router->post('/users/associate-employee', 'UsuarioController@associateEmployee');

// Reportes PDF
$router->get('/reports/employees', 'ReporteController@employees');
$router->get('/reports/payroll', 'ReporteController@payroll');
$router->get('/reports/attendance', 'ReporteController@attendance');

// Reportes Excel
$router->get('/reports/employees/excel', 'ReporteController@employeesExcel');
$router->get('/reports/payroll/excel', 'ReporteController@payrollExcel');
$router->get('/reports/attendance/excel', 'ReporteController@attendanceExcel');

