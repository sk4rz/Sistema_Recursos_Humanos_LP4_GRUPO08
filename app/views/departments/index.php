<?php
$title = 'Departamentos - Sistema RH';
ob_start();
?>

<div class="container-fluid px-4 py-4">
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1>Departamentos</h1>
            <p class="text-muted-custom mb-0"><?= count($departments) ?> departamentos</p>
        </div>
        <div class="page-actions mt-3 mt-md-0">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#departmentModal" onclick="openDepartmentModal()">
                <i class="bi bi-plus-lg me-1"></i>Nuevo
            </button>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($departments as $dept): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card card-custom h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($dept['name']) ?></h5>
                        <?php if ($dept['description']): ?>
                            <p class="text-muted-custom small"><?= htmlspecialchars($dept['description']) ?></p>
                        <?php endif; ?>
                        <div class="mt-3">
                            <small class="text-muted-custom d-block">
                                <i class="bi bi-people me-1"></i>
                                <?= $dept['employee_count'] ?? 0 ?> empleados
                            </small>
                            <?php if ($dept['budget']): ?>
                                <small class="text-muted-custom d-block mt-1">
                                    <i class="bi bi-cash-coin me-1"></i>
                                    $<?= number_format($dept['budget'], 2) ?>
                                </small>
                            <?php endif; ?>
                            <?php if ($dept['manager_first_name']): ?>
                                <small class="text-muted-custom d-block mt-1">
                                    <i class="bi bi-person-badge me-1"></i>
                                    <?= htmlspecialchars($dept['manager_first_name'] . ' ' . $dept['manager_last_name']) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-primary" onclick="editDepartment(<?= htmlspecialchars(json_encode($dept)) ?>)">
                                <i class="bi bi-pencil me-1"></i>Editar
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteDepartment(<?= $dept['id'] ?>)">
                                <i class="bi bi-trash me-1"></i>Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Department Modal -->
<div class="modal fade" id="departmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content card-custom">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="departmentModalTitle">Nuevo Departamento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="departmentForm">
                <div class="modal-body">
                    <input type="hidden" id="department_id" name="id">
                    <input type="hidden" name="csrf_token" value="<?= $controller->generateCSRF() ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" class="form-control" name="name" id="department_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" id="department_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gerente</label>
                        <select class="form-select" name="manager_id" id="department_manager_id">
                            <option value="">Sin gerente</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Presupuesto</label>
                        <input type="number" step="0.01" class="form-control" name="budget" id="department_budget">
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
let currentDepartment = null;

function openDepartmentModal() {
    currentDepartment = null;
    document.getElementById('departmentForm').reset();
    document.getElementById('departmentModalTitle').textContent = 'Nuevo Departamento';
    document.getElementById('department_id').value = '';
}

function editDepartment(dept) {
    currentDepartment = dept;
    document.getElementById('departmentModalTitle').textContent = 'Editar Departamento';
    document.getElementById('department_id').value = dept.id;
    document.getElementById('department_name').value = dept.name;
    document.getElementById('department_description').value = dept.description || '';
    document.getElementById('department_manager_id').value = dept.manager_id || '';
    document.getElementById('department_budget').value = dept.budget || '';
    
    new bootstrap.Modal(document.getElementById('departmentModal')).show();
}

function deleteDepartment(id) {
    if (!confirm('¿Está seguro de eliminar este departamento?')) return;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('csrf_token', '<?= $controller->generateCSRF() ?>');
    
    fetch('<?= BASE_URL ?>/departments/delete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al eliminar departamento');
        }
    });
}

document.getElementById('departmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = currentDepartment ? '<?= BASE_URL ?>/departments/update' : '<?= BASE_URL ?>/departments/create';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('departmentModal')).hide();
            location.reload();
        } else {
            alert(data.error || 'Error al guardar departamento');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
$showSidebar = true;
include VIEWS_PATH . '/layouts/main.php';
?>

