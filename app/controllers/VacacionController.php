<?php
/**
 * Controlador de Vacaciones
 */
class VacacionController extends Controller {
    private $leaveModel;
    private $employeeModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->leaveModel = new SolicitudVacacion();
        $this->employeeModel = new Empleado();
    }

    public function index() {
        $status = $_GET['status'] ?? 'all';
        $filters = [];
        
        if ($status !== 'all') {
            $filters['status'] = $status;
        }

        $leaves = $this->leaveModel->getAll($filters);
        $employees = $this->employeeModel->getAll();

        if ($this->isAjax()) {
            $this->json(['leaves' => $leaves]);
        } else {
            $this->view('leave/index', [
                'leaves' => $leaves,
                'employees' => $employees,
                'status' => $status
            ]);
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF($_POST['csrf_token'] ?? '')) {
                $_SESSION['flash_error'] = 'Token CSRF inválido';
                $this->redirect('/leave-requests');
                return;
            }

            $data = [
                'employee_id' => intval($_POST['employee_id'] ?? 0),
                'leave_type' => $_POST['leave_type'] ?? 'anual',
                'start_date' => $_POST['start_date'] ?? '',
                'end_date' => $_POST['end_date'] ?? '',
                'reason' => $this->sanitize($_POST['reason'] ?? '')
            ];

            if (empty($data['employee_id']) || empty($data['start_date']) || empty($data['end_date'])) {
                $_SESSION['flash_error'] = 'Por favor complete todos los campos requeridos';
                $this->redirect('/leave-requests');
                return;
            }

            if ($this->leaveModel->create($data)) {
                $_SESSION['flash_success'] = 'Solicitud de vacaciones creada exitosamente';
            } else {
                $_SESSION['flash_error'] = 'Error al crear solicitud';
            }
            
            $this->redirect('/leave-requests');
        }
    }

    public function approve() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF($_POST['csrf_token'] ?? '')) {
                $_SESSION['flash_error'] = 'Token CSRF inválido';
                $this->redirect('/leave-requests');
                return;
            }

            $id = intval($_POST['id'] ?? 0);
            
            if (!$id) {
                $_SESSION['flash_error'] = 'ID inválido';
                $this->redirect('/leave-requests');
                return;
            }

            if ($this->leaveModel->updateStatus($id, 'aprobado', $_SESSION['user_id'])) {
                $_SESSION['flash_success'] = 'Solicitud aprobada exitosamente';
            } else {
                $_SESSION['flash_error'] = 'Error al aprobar solicitud';
            }
            
            $this->redirect('/leave-requests');
        }
    }

    public function reject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF($_POST['csrf_token'] ?? '')) {
                $_SESSION['flash_error'] = 'Token CSRF inválido';
                $this->redirect('/leave-requests');
                return;
            }

            $id = intval($_POST['id'] ?? 0);
            
            if (!$id) {
                $_SESSION['flash_error'] = 'ID inválido';
                $this->redirect('/leave-requests');
                return;
            }

            if ($this->leaveModel->updateStatus($id, 'rechazado', $_SESSION['user_id'])) {
                $_SESSION['flash_success'] = 'Solicitud rechazada exitosamente';
            } else {
                $_SESSION['flash_error'] = 'Error al rechazar solicitud';
            }
            
            $this->redirect('/leave-requests');
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF($_POST['csrf_token'] ?? '')) {
                $_SESSION['flash_error'] = 'Token CSRF inválido';
                $this->redirect('/leave-requests');
                return;
            }

            $id = intval($_POST['id'] ?? 0);
            
            if (!$id) {
                $_SESSION['flash_error'] = 'ID inválido';
                $this->redirect('/leave-requests');
                return;
            }

            if ($this->leaveModel->delete($id)) {
                $_SESSION['flash_success'] = 'Solicitud eliminada exitosamente';
            } else {
                $_SESSION['flash_error'] = 'Error al eliminar solicitud';
            }
            
            $this->redirect('/leave-requests');
        }
    }

    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}

