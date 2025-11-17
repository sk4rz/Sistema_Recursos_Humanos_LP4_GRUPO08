<?php
$title = 'Empleados - Sistema RH';
ob_start();
?>

<div class="container-fluid px-4 py-4">
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1>Empleados</h1>
            <p class="text-muted-custom mb-0"><?= count($employees) ?> empleados activos</p>
        </div>
        <div class="page-actions mt-3 mt-md-0">
            <div class="btn-group">
                <a href="<?= BASE_URL ?>/reports/employees" class="btn btn-sm btn-outline-secondary" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i>
                </a>
                <a href="<?= BASE_URL ?>/reports/employees/excel" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-file-earmark-excel"></i>
                </a>
            </div>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#employeeModal" onclick="openEmployeeModal()">
                <i class="bi bi-plus-lg me-1"></i>Nuevo
            </button>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-3">
        <form method="GET" action="<?= BASE_URL ?>/employees" class="d-flex gap-2">
            <div class="input-group flex-grow-1" style="max-width: 400px;">
                <span class="input-group-text bg-transparent border-secondary">
                    <i class="bi bi-search text-muted-custom"></i>
                </span>
                <input type="text" class="form-control" name="search" placeholder="Buscar..." 
                       value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card card-custom">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">ID</th>
                            <th>Nombre</th>
                            <th class="d-none d-lg-table-cell">Email</th>
                            <th>Posición</th>
                            <th class="d-none d-md-table-cell">Departamento</th>
                            <th class="d-none d-lg-table-cell">Salario</th>
                            <th class="d-none d-xl-table-cell">Fecha Contratación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted-custom py-4">No hay empleados registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employees as $emp): ?>
                                <tr>
                                    <td class="d-none d-md-table-cell"><?= htmlspecialchars($emp['employee_id']) ?></td>
                                    <td>
                                        <div class="fw-medium"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></div>
                                        <small class="text-muted d-md-none"><?= htmlspecialchars($emp['employee_id']) ?></small>
                                        <small class="text-muted d-lg-none d-block"><?= htmlspecialchars($emp['email']) ?></small>
                                    </td>
                                    <td class="d-none d-lg-table-cell"><?= htmlspecialchars($emp['email']) ?></td>
                                    <td>
                                        <div><?= htmlspecialchars($emp['position']) ?></div>
                                        <small class="text-muted d-md-none"><?= htmlspecialchars($emp['department_name']) ?></small>
                                    </td>
                                    <td class="d-none d-md-table-cell"><?= htmlspecialchars($emp['department_name']) ?></td>
                                    <td class="d-none d-lg-table-cell">$<?= number_format($emp['salary'], 2) ?></td>
                                    <td class="d-none d-xl-table-cell"><?= date('d/m/Y', strtotime($emp['hire_date'])) ?></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-link text-primary p-1" onclick="editEmployee(<?= htmlspecialchars(json_encode($emp)) ?>)" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-link text-danger p-1" onclick="deleteEmployee(<?= $emp['id'] ?>)" title="Eliminar">
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

<!-- Employee Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-custom">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="employeeModalTitle">Nuevo Empleado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="employeeForm">
                <div class="modal-body">
                    <input type="hidden" id="employee_id" name="id">
                    <input type="hidden" name="csrf_token" value="<?= $controller->generateCSRF() ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" id="employee_email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ID de Empleado *</label>
                            <input type="text" class="form-control" name="employee_id" id="employee_employee_id" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre *</label>
                            <input type="text" class="form-control" name="first_name" id="employee_first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido *</label>
                            <input type="text" class="form-control" name="last_name" id="employee_last_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Departamento *</label>
                            <select class="form-select" name="department_id" id="employee_department_id" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Posición *</label>
                            <input type="text" class="form-control" name="position" id="employee_position" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Contratación *</label>
                            <input type="date" class="form-control" name="hire_date" id="employee_hire_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Salario *</label>
                            <input type="number" step="0.01" class="form-control" name="salary" id="employee_salary" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Contrato</label>
                            <select class="form-select" name="contract_type" id="employee_contract_type">
                                <option value="tiempo_completo">Tiempo Completo</option>
                                <option value="medio_tiempo">Medio Tiempo</option>
                                <option value="contratista">Contratista</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="phone" id="employee_phone">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <textarea class="form-control" name="address" id="employee_address" rows="2"></textarea>
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
let currentEmployee = null;

function openEmployeeModal() {
    currentEmployee = null;
    document.getElementById('employeeForm').reset();
    document.getElementById('employeeModalTitle').textContent = 'Nuevo Empleado';
    document.getElementById('employee_id').value = '';
}

function editEmployee(emp) {
    currentEmployee = emp;
    document.getElementById('employeeModalTitle').textContent = 'Editar Empleado';
    document.getElementById('employee_id').value = emp.id;
    document.getElementById('employee_employee_id').value = emp.employee_id;
    document.getElementById('employee_first_name').value = emp.first_name;
    document.getElementById('employee_last_name').value = emp.last_name;
    document.getElementById('employee_email').value = emp.email;
    document.getElementById('employee_department_id').value = emp.department_id;
    document.getElementById('employee_position').value = emp.position;
    document.getElementById('employee_hire_date').value = emp.hire_date;
    document.getElementById('employee_salary').value = emp.salary;
    document.getElementById('employee_contract_type').value = emp.contract_type;
    document.getElementById('employee_phone').value = emp.phone || '';
    document.getElementById('employee_address').value = emp.address || '';
    
    new bootstrap.Modal(document.getElementById('employeeModal')).show();
}

function deleteEmployee(id) {
    if (!confirm('¿Está seguro de eliminar este empleado?')) return;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('csrf_token', '<?= $controller->generateCSRF() ?>');
    
    fetch('<?= BASE_URL ?>/employees/delete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al eliminar empleado');
        }
    });
}

document.getElementById('employeeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = currentEmployee ? '<?= BASE_URL ?>/employees/update' : '<?= BASE_URL ?>/employees/create';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('employeeModal')).hide();
            location.reload();
        } else {
            alert(data.error || 'Error al guardar empleado');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
$showSidebar = true;
include VIEWS_PATH . '/layouts/main.php';
?>

