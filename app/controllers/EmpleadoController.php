<?php
/**
 * Controlador de Empleados
 */
class EmpleadoController extends Controller {
    private $employeeModel;
    private $departmentModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->employeeModel = new Empleado();
        $this->departmentModel = new Departamento();
        $this->userModel = new Usuario();
    }

    public function index() {
        $search = $this->sanitize($_GET['search'] ?? '');
        $employees = $this->employeeModel->getAll();
        
        if ($search) {
            $employees = array_filter($employees, function($emp) use ($search) {
                return stripos($emp['first_name'], $search) !== false ||
                       stripos($emp['last_name'], $search) !== false ||
                       stripos($emp['employee_id'], $search) !== false;
            });
        }

        $departments = $this->departmentModel->getAll();

        if ($this->isAjax()) {
            $this->json(['employees' => array_values($employees)]);
        } else {
            $this->view('employees/index', [
                'employees' => array_values($employees),
                'departments' => $departments,
                'search' => $search
            ]);
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF($_POST['csrf_token'] ?? '')) {
                $this->json(['error' => 'Token CSRF inválido'], 403);
                return;
            }

            $data = [
                'email' => $this->sanitize($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? 'password123',
                'first_name' => $this->sanitize($_POST['first_name'] ?? ''),
                'last_name' => $this->sanitize($_POST['last_name'] ?? ''),
                'employee_id' => $this->sanitize($_POST['employee_id'] ?? ''),
                'department_id' => intval($_POST['department_id'] ?? 0),
                'position' => $this->sanitize($_POST['position'] ?? ''),
                'hire_date' => $_POST['hire_date'] ?? date('Y-m-d'),
                'salary' => floatval($_POST['salary'] ?? 0),
                'contract_type' => $_POST['contract_type'] ?? 'full_time',
                'phone' => $this->sanitize($_POST['phone'] ?? ''),
                'address' => $this->sanitize($_POST['address'] ?? '')
            ];

            // Validaciones
            if (empty($data['email']) || !$this->validateEmail($data['email'])) {
                $this->json(['error' => 'Email inválido'], 400);
                return;
            }

            if (empty($data['employee_id'])) {
                $this->json(['error' => 'ID de empleado requerido'], 400);
                return;
            }

            // Verificar si el employee_id ya existe
            if ($this->employeeModel->findByEmployeeId($data['employee_id'])) {
                $this->json(['error' => 'El ID de empleado ya existe'], 400);
                return;
            }

            if ($this->employeeModel->create($data)) {
                $this->json(['success' => true, 'message' => 'Empleado creado exitosamente']);
            } else {
                $this->json(['error' => 'Error al crear empleado'], 500);
            }
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            
            if (!$id) {
                $this->json(['error' => 'ID inválido'], 400);
                return;
            }

            if (!$this->validateCSRF($_POST['csrf_token'] ?? '')) {
                $this->json(['error' => 'Token CSRF inválido'], 403);
                return;
            }

            $data = [];
            if (isset($_POST['department_id'])) $data['department_id'] = intval($_POST['department_id']);
            if (isset($_POST['position'])) $data['position'] = $this->sanitize($_POST['position']);
            if (isset($_POST['salary'])) $data['salary'] = floatval($_POST['salary']);
            if (isset($_POST['contract_type'])) $data['contract_type'] = $_POST['contract_type'];
            if (isset($_POST['phone'])) $data['phone'] = $this->sanitize($_POST['phone']);
            if (isset($_POST['address'])) $data['address'] = $this->sanitize($_POST['address']);

            if ($this->employeeModel->update($id, $data)) {
                $this->json(['success' => true, 'message' => 'Empleado actualizado exitosamente']);
            } else {
                $this->json(['error' => 'Error al actualizar empleado'], 500);
            }
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF($_POST['csrf_token'] ?? '')) {
                $this->json(['error' => 'Token CSRF inválido'], 403);
                return;
            }

            $id = intval($_POST['id'] ?? 0);
            
            if (!$id) {
                $this->json(['error' => 'ID inválido'], 400);
                return;
            }

            if ($this->employeeModel->delete($id)) {
                $this->json(['success' => true, 'message' => 'Empleado eliminado exitosamente']);
            } else {
                $this->json(['error' => 'Error al eliminar empleado'], 500);
            }
        }
    }

    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}

