<?php
$title = 'Evaluaciones - Sistema RH';
ob_start();
?>

<div class="container-fluid px-4 py-4">
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1>Evaluaciones</h1>
            <p class="text-muted-custom mb-0">Evaluaciones de desempeño</p>
        </div>
        <div class="page-actions mt-3 mt-md-0">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#evaluationModal" onclick="openEvaluationModal()">
                <i class="bi bi-plus-lg me-1"></i>Nueva
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-3">
        <a href="?status=all" class="btn btn-sm <?= $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' ?>">Todas</a>
        <a href="?status=borrador" class="btn btn-sm <?= $status === 'borrador' ? 'btn-warning' : 'btn-outline-secondary' ?>">Borradores</a>
        <a href="?status=completado" class="btn btn-sm <?= $status === 'completado' ? 'btn-success' : 'btn-outline-secondary' ?>">Completadas</a>
    </div>

    <!-- Table -->
    <div class="card card-custom">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Evaluador</th>
                            <th>Período</th>
                            <th>Calificación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($evaluations)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted-custom py-4">No hay evaluaciones registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($evaluations as $eval): ?>
                                <tr>
                                    <td><?= htmlspecialchars($eval['first_name'] . ' ' . $eval['last_name']) ?></td>
                                    <td><?= htmlspecialchars($eval['evaluator_first_name'] . ' ' . $eval['evaluator_last_name']) ?></td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($eval['evaluation_period_start'])) ?> - 
                                        <?= date('d/m/Y', strtotime($eval['evaluation_period_end'])) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?= $eval['rating'] ?>/5</span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusLabels = [
                                            'borrador' => ['Borrador', 'warning'],
                                            'completado' => ['Completada', 'success'],
                                            'revisado' => ['Revisada', 'info']
                                        ];
                                        $label = $statusLabels[$eval['status']] ?? ['Desconocido', 'secondary'];
                                        ?>
                                        <span class="badge bg-<?= $label[1] ?>"><?= $label[0] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-link text-primary p-1" onclick="editEvaluation(<?= htmlspecialchars(json_encode($eval)) ?>)" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-link text-danger p-1" onclick="deleteEvaluation(<?= $eval['id'] ?>)" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Evaluation Modal -->
<div class="modal fade" id="evaluationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-custom">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="evaluationModalTitle">Nueva Evaluación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="evaluationForm">
                <div class="modal-body">
                    <input type="hidden" id="evaluation_id" name="id">
                    <input type="hidden" name="csrf_token" value="<?= $controller->generateCSRF() ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Empleado *</label>
                            <select class="form-select" name="employee_id" id="evaluation_employee_id" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Evaluador *</label>
                            <select class="form-select" name="evaluator_id" id="evaluation_evaluator_id" required>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= $user['id'] == $_SESSION['user_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Período Inicio *</label>
                            <input type="date" class="form-control" name="evaluation_period_start" id="evaluation_period_start" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Período Fin *</label>
                            <input type="date" class="form-control" name="evaluation_period_end" id="evaluation_period_end" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Calificación (1-5) *</label>
                            <input type="number" min="1" max="5" class="form-control" name="rating" id="evaluation_rating" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="status" id="evaluation_status">
                                <option value="draft">Borrador</option>
                                <option value="completed">Completada</option>
                                <option value="reviewed">Revisada</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Comentarios</label>
                            <textarea class="form-control" name="comments" id="evaluation_comments" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Fortalezas</label>
                            <textarea class="form-control" name="strengths" id="evaluation_strengths" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Áreas de Mejora</label>
                            <textarea class="form-control" name="areas_for_improvement" id="evaluation_areas" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Metas</label>
                            <textarea class="form-control" name="goals" id="evaluation_goals" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentEvaluation = null;

function openEvaluationModal() {
    currentEvaluation = null;
    document.getElementById('evaluationForm').reset();
    document.getElementById('evaluationModalTitle').textContent = 'Nueva Evaluación';
    document.getElementById('evaluation_id').value = '';
    document.getElementById('evaluation_evaluator_id').value = <?= $_SESSION['user_id'] ?>;
}

function editEvaluation(eval) {
    currentEvaluation = eval;
    document.getElementById('evaluationModalTitle').textContent = 'Editar Evaluación';
    document.getElementById('evaluation_id').value = eval.id;
    document.getElementById('evaluation_employee_id').value = eval.employee_id;
    document.getElementById('evaluation_evaluator_id').value = eval.evaluator_id;
    document.getElementById('evaluation_period_start').value = eval.evaluation_period_start;
    document.getElementById('evaluation_period_end').value = eval.evaluation_period_end;
    document.getElementById('evaluation_rating').value = eval.rating;
    document.getElementById('evaluation_status').value = eval.status;
    document.getElementById('evaluation_comments').value = eval.comments || '';
    document.getElementById('evaluation_strengths').value = eval.strengths || '';
    document.getElementById('evaluation_areas').value = eval.areas_for_improvement || '';
    document.getElementById('evaluation_goals').value = eval.goals || '';
    
    new bootstrap.Modal(document.getElementById('evaluationModal')).show();
}

function deleteEvaluation(id) {
    if (!confirm('¿Está seguro de eliminar esta evaluación?')) return;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('csrf_token', '<?= $controller->generateCSRF() ?>');
    
    fetch('<?= BASE_URL ?>/evaluations/delete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al eliminar evaluación');
        }
    });
}

document.getElementById('evaluationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = currentEvaluation ? '<?= BASE_URL ?>/evaluations/update' : '<?= BASE_URL ?>/evaluations/create';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('evaluationModal')).hide();
            location.reload();
        } else {
            alert(data.error || 'Error al guardar evaluación');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
$showSidebar = true;
include VIEWS_PATH . '/layouts/main.php';
?>

