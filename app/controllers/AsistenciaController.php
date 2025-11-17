<?php
/**
 * Controlador de Asistencia
 */
class AsistenciaController extends Controller {
    private $attendanceModel;
    private $employeeModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->attendanceModel = new Asistencia();
        $this->employeeModel = new Empleado();
    }

    public function index() {
        $date = $_GET['date'] ?? date('Y-m-d');
        $filters = ['date' => $date];
        
        $attendance = $this->attendanceModel->getAll($filters);
        $employees = $this->employeeModel->getAll();

        if ($this->isAjax()) {
            $this->json(['attendance' => $attendance]);
        } else {
            $this->view('attendance/index', [
                'attendance' => $attendance,
                'employees' => $employees,
                'selectedDate' => $date
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
                'date' => $_POST['date'] ?? date('Y-m-d'),
                'check_in_time' => !empty($_POST['check_in_time']) ? $_POST['check_in_time'] : null,
                'check_out_time' => !empty($_POST['check_out_time']) ? $_POST['check_out_time'] : null,
                'status' => $_POST['status'] ?? 'ausente',
                'notes' => $this->sanitize($_POST['notes'] ?? '')
            ];

            if (empty($data['employee_id'])) {
                $this->json(['error' => 'Empleado requerido'], 400);
                return;
            }

            if ($this->attendanceModel->create($data)) {
                $this->json(['success' => true, 'message' => 'Asistencia registrada exitosamente']);
            } else {
                $this->json(['error' => 'Error al registrar asistencia'], 500);
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
            if (isset($_POST['check_in_time'])) $data['check_in_time'] = $_POST['check_in_time'];
            if (isset($_POST['check_out_time'])) $data['check_out_time'] = $_POST['check_out_time'];
            if (isset($_POST['status'])) $data['status'] = $_POST['status'];
            if (isset($_POST['notes'])) $data['notes'] = $this->sanitize($_POST['notes']);

            if ($this->attendanceModel->update($id, $data)) {
                $this->json(['success' => true, 'message' => 'Asistencia actualizada exitosamente']);
            } else {
                $this->json(['error' => 'Error al actualizar asistencia'], 500);
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

            if ($this->attendanceModel->delete($id)) {
                $this->json(['success' => true, 'message' => 'Asistencia eliminada exitosamente']);
            } else {
                $this->json(['error' => 'Error al eliminar asistencia'], 500);
            }
        }
    }

    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}

