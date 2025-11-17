<?php
/**
 * Controlador de Planillas
 */
class PlanillaController extends Controller {
    private $payrollModel;
    private $employeeModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->payrollModel = new Planilla();
        $this->employeeModel = new Empleado();
    }

    public function index() {
        $status = $_GET['status'] ?? 'all';
        $filters = [];
        
        if ($status !== 'all') {
            $filters['status'] = $status;
        }

        $payroll = $this->payrollModel->getAll($filters);
        $employees = $this->employeeModel->getAll();

        // Calcular totales
        $totals = [
            'aprobado' => $this->payrollModel->getTotalByStatus('aprobado'),
            'pagado' => $this->payrollModel->getTotalByStatus('pagado')
        ];

        if ($this->isAjax()) {
            $this->json(['payroll' => $payroll, 'totals' => $totals]);
        } else {
            $this->view('payroll/index', [
                'payroll' => $payroll,
                'employees' => $employees,
                'status' => $status,
                'totals' => $totals
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
                'employee_id' => intval($_POST['employee_id'] ?? 0),
                'payment_period_start' => $_POST['payment_period_start'] ?? date('Y-m-01'),
                'payment_period_end' => $_POST['payment_period_end'] ?? date('Y-m-t'),
                'base_salary' => floatval($_POST['base_salary'] ?? 0),
                'allowances' => floatval($_POST['allowances'] ?? 0),
                'deductions' => floatval($_POST['deductions'] ?? 0),
                'payment_method' => $_POST['payment_method'] ?? 'transferencia_bancaria',
                'status' => $_POST['status'] ?? 'aprobado',
                'notes' => $this->sanitize($_POST['notes'] ?? '')
            ];

            if (empty($data['employee_id'])) {
                $this->json(['error' => 'Empleado requerido'], 400);
                return;
            }

            if ($this->payrollModel->create($data)) {
                $this->json(['success' => true, 'message' => 'Planilla creada exitosamente']);
            } else {
                $this->json(['error' => 'Error al crear planilla'], 500);
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
            if (isset($_POST['status'])) $data['status'] = $_POST['status'];
            if (isset($_POST['payment_date'])) $data['payment_date'] = $_POST['payment_date'];
            if (isset($_POST['payment_method'])) $data['payment_method'] = $_POST['payment_method'];
            if (isset($_POST['notes'])) $data['notes'] = $this->sanitize($_POST['notes']);

            if ($this->payrollModel->update($id, $data)) {
                $this->json(['success' => true, 'message' => 'Planilla actualizada exitosamente']);
            } else {
                $this->json(['error' => 'Error al actualizar planilla'], 500);
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

            if ($this->payrollModel->delete($id)) {
                $this->json(['success' => true, 'message' => 'Planilla eliminada exitosamente']);
            } else {
                $this->json(['error' => 'Error al eliminar planilla'], 500);
            }
        }
    }

    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}

