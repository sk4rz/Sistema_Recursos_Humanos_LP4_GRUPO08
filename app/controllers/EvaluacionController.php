<?php
/**
 * Controlador de Evaluaciones
 */
class EvaluacionController extends Controller {
    private $evaluationModel;
    private $employeeModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->evaluationModel = new EvaluacionDesempeno();
        $this->employeeModel = new Empleado();
        $this->userModel = new Usuario();
    }

    public function index() {
        $status = $_GET['status'] ?? 'all';
        $filters = [];
        
        if ($status !== 'all') {
            $filters['status'] = $status;
        }

        $evaluations = $this->evaluationModel->getAll($filters);
        $employees = $this->employeeModel->getAll();
        $users = $this->userModel->getAll();

        if ($this->isAjax()) {
            $this->json(['evaluations' => $evaluations]);
        } else {
            $this->view('evaluations/index', [
                'evaluations' => $evaluations,
                'employees' => $employees,
                'users' => $users,
                'status' => $status
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
                'evaluator_id' => intval($_POST['evaluator_id'] ?? $_SESSION['user_id']),
                'evaluation_period_start' => $_POST['evaluation_period_start'] ?? '',
                'evaluation_period_end' => $_POST['evaluation_period_end'] ?? '',
                'rating' => intval($_POST['rating'] ?? 3),
                'comments' => $this->sanitize($_POST['comments'] ?? ''),
                'strengths' => $this->sanitize($_POST['strengths'] ?? ''),
                'areas_for_improvement' => $this->sanitize($_POST['areas_for_improvement'] ?? ''),
                'goals' => $this->sanitize($_POST['goals'] ?? ''),
                'status' => $_POST['status'] ?? 'completado'
            ];

            if (empty($data['employee_id']) || empty($data['evaluation_period_start']) || empty($data['evaluation_period_end'])) {
                $this->json(['error' => 'Datos incompletos'], 400);
                return;
            }

            if ($data['rating'] < 1 || $data['rating'] > 5) {
                $this->json(['error' => 'Rating debe estar entre 1 y 5'], 400);
                return;
            }

            if ($this->evaluationModel->create($data)) {
                $this->json(['success' => true, 'message' => 'Evaluación creada exitosamente']);
            } else {
                $this->json(['error' => 'Error al crear evaluación'], 500);
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
            if (isset($_POST['rating'])) {
                $rating = intval($_POST['rating']);
                if ($rating >= 1 && $rating <= 5) {
                    $data['rating'] = $rating;
                }
            }
            if (isset($_POST['comments'])) $data['comments'] = $this->sanitize($_POST['comments']);
            if (isset($_POST['strengths'])) $data['strengths'] = $this->sanitize($_POST['strengths']);
            if (isset($_POST['areas_for_improvement'])) $data['areas_for_improvement'] = $this->sanitize($_POST['areas_for_improvement']);
            if (isset($_POST['goals'])) $data['goals'] = $this->sanitize($_POST['goals']);
            if (isset($_POST['status'])) $data['status'] = $_POST['status'];

            if ($this->evaluationModel->update($id, $data)) {
                $this->json(['success' => true, 'message' => 'Evaluación actualizada exitosamente']);
            } else {
                $this->json(['error' => 'Error al actualizar evaluación'], 500);
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

            if ($this->evaluationModel->delete($id)) {
                $this->json(['success' => true, 'message' => 'Evaluación eliminada exitosamente']);
            } else {
                $this->json(['error' => 'Error al eliminar evaluación'], 500);
            }
        }
    }

    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}

