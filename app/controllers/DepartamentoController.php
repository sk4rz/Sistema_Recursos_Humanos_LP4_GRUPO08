<?php
/**
 * Controlador de Departamentos
 */
class DepartamentoController extends Controller {
    private $departmentModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->departmentModel = new Departamento();
        $this->userModel = new Usuario();
    }

    public function index() {
        $departments = $this->departmentModel->getAll();
        $users = $this->userModel->getAll();

        if ($this->isAjax()) {
            $this->json(['departments' => $departments]);
        } else {
            $this->view('departments/index', [
                'departments' => $departments,
                'users' => $users
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
                'name' => $this->sanitize($_POST['name'] ?? ''),
                'description' => $this->sanitize($_POST['description'] ?? ''),
                'manager_id' => !empty($_POST['manager_id']) ? intval($_POST['manager_id']) : null,
                'budget' => !empty($_POST['budget']) ? floatval($_POST['budget']) : null
            ];

            if (empty($data['name'])) {
                $this->json(['error' => 'Nombre requerido'], 400);
                return;
            }

            if ($this->departmentModel->create($data)) {
                $this->json(['success' => true, 'message' => 'Departamento creado exitosamente']);
            } else {
                $this->json(['error' => 'Error al crear departamento'], 500);
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
            if (isset($_POST['name'])) $data['name'] = $this->sanitize($_POST['name']);
            if (isset($_POST['description'])) $data['description'] = $this->sanitize($_POST['description']);
            if (isset($_POST['manager_id'])) $data['manager_id'] = !empty($_POST['manager_id']) ? intval($_POST['manager_id']) : null;
            if (isset($_POST['budget'])) $data['budget'] = !empty($_POST['budget']) ? floatval($_POST['budget']) : null;

            if ($this->departmentModel->update($id, $data)) {
                $this->json(['success' => true, 'message' => 'Departamento actualizado exitosamente']);
            } else {
                $this->json(['error' => 'Error al actualizar departamento'], 500);
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

            if ($this->departmentModel->delete($id)) {
                $this->json(['success' => true, 'message' => 'Departamento eliminado exitosamente']);
            } else {
                $this->json(['error' => 'Error al eliminar departamento'], 500);
            }
        }
    }

    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}

