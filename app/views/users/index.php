<?php
$title = 'Usuarios - Sistema RH';
ob_start();
?>

<div class="container-fluid px-4 py-4">
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1>Usuarios</h1>
            <p class="text-muted-custom mb-0">Gestión de cuentas de acceso al sistema</p>
        </div>
        <div class="page-actions mt-3 mt-md-0">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openUserModal()">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Usuario
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card card-custom mb-4">
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>/users" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" name="search" placeholder="Buscar por nombre o email..." 
                               value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="role">
                        <option value="all" <?= $role === 'all' ? 'selected' : '' ?>>Todos los roles</option>
                        <option value="administrador" <?= $role === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                        <option value="gerente" <?= $role === 'gerente' ? 'selected' : '' ?>>Gerente</option>
                        <option value="empleado" <?= $role === 'empleado' ? 'selected' : '' ?>>Empleado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="card card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Empleado Asociado</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox text-muted-custom" style="font-size: 2.5rem; opacity: 0.4;"></i>
                                    <p class="text-muted-custom mt-3 mb-0">No se encontraron usuarios</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): 
                                // Verificar si tiene empleado asociado
                                $db = Database::getInstance()->getConnection();
                                $empStmt = $db->prepare("SELECT id, employee_id FROM empleados WHERE user_id = ? AND is_active = 1");
                                $empStmt->execute([$user['id']]);
                                $employee = $empStmt->fetch();
                            ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-white">
                                            <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            <?= htmlspecialchars($user['email']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $roleColors = [
                                            'administrador' => 'danger',
                                            'gerente' => 'warning',
                                            'empleado' => 'primary'
                                        ];
                                        $roleLabels = [
                                            'administrador' => 'Administrador',
                                            'gerente' => 'Gerente',
                                            'empleado' => 'Empleado'
                                        ];
                                        $roleColor = $roleColors[$user['role']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $roleColor ?>">
                                            <?= $roleLabels[$user['role']] ?? ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($employee): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($employee['employee_id']) ?>
                                            </span>
                                            <button class="btn btn-sm btn-link text-danger p-0 ms-1" 
                                                    onclick="disassociateEmployee(<?= $user['id'] ?>)" 
                                                    title="Desasociar empleado"
                                                    style="font-size: 0.75rem;">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="badge bg-secondary me-1">
                                                <i class="bi bi-x-circle me-1"></i>Sin empleado
                                            </span>
                                            <button class="btn btn-sm btn-link text-primary p-0" 
                                                    onclick="associateEmployee(<?= $user['id'] ?>)" 
                                                    title="Asociar empleado"
                                                    style="font-size: 0.75rem;">
                                                <i class="bi bi-plus-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $user['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $user['is_active'] ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted-custom" style="font-size: 0.875rem;">
                                            <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-sm btn-link text-primary p-1" 
                                                    onclick="editUser(<?= htmlspecialchars(json_encode($user)) ?>)" 
                                                    title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <button class="btn btn-sm btn-link text-danger p-1" 
                                                        onclick="deleteUser(<?= $user['id'] ?>)" 
                                                        title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
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

<!-- Modal Usuario -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content card-custom">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="userModalTitle">Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="user_id" name="id">
                    <input type="hidden" name="csrf_token" value="<?= $controller->generateCSRF() ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre *</label>
                            <input type="text" class="form-control" name="first_name" id="user_first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido *</label>
                            <input type="text" class="form-control" name="last_name" id="user_last_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" id="user_email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol *</label>
                            <select class="form-select" name="role" id="user_role" required>
                                <option value="empleado">Empleado</option>
                                <option value="gerente">Gerente</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" id="passwordLabel">Contraseña *</label>
                            <input type="password" class="form-control" name="password" id="user_password" minlength="6">
                            <small class="text-muted-custom">Mínimo 6 caracteres. Dejar en blanco para no cambiar (al editar).</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Empleado Asociado</label>
                            <select class="form-select" name="employee_id" id="user_employee_id">
                                <option value="">Sin empleado asociado</option>
                                <?php 
                                // Obtener empleados sin usuario asociado o disponibles
                                $db = Database::getInstance()->getConnection();
                                $availableEmployeesStmt = $db->query("
                                    SELECT e.id, e.employee_id, e.position, d.name as department_name
                                    FROM empleados e
                                    LEFT JOIN usuarios u ON e.user_id = u.id
                                    INNER JOIN departamentos d ON e.department_id = d.id
                                    WHERE e.is_active = 1 AND (e.user_id IS NULL OR u.is_active = 0)
                                    ORDER BY e.employee_id
                                ");
                                $availableEmployees = $availableEmployeesStmt->fetchAll();
                                
                                // Si estamos editando, incluir el empleado actual si existe
                                if (isset($user) && isset($employee)) {
                                    $currentEmpStmt = $db->prepare("
                                        SELECT e.id, e.employee_id, e.position, d.name as department_name
                                        FROM empleados e
                                        INNER JOIN departamentos d ON e.department_id = d.id
                                        WHERE e.id = ? AND e.is_active = 1
                                    ");
                                    $currentEmpStmt->execute([$employee['id']]);
                                    $currentEmp = $currentEmpStmt->fetch();
                                    if ($currentEmp) {
                                        $availableEmployees[] = $currentEmp;
                                    }
                                }
                                
                                foreach ($availableEmployees as $emp): 
                                    $displayText = $emp['employee_id'] . ' - ' . $emp['position'] . ' (' . $emp['department_name'] . ')';
                                ?>
                                    <option value="<?= $emp['id'] ?>" 
                                            <?= (isset($employee) && $employee['id'] == $emp['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($displayText) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted-custom">Opcional: Asociar este usuario con un empleado existente.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentUser = null;

function openUserModal() {
    currentUser = null;
    document.getElementById('userForm').reset();
    document.getElementById('userModalTitle').textContent = 'Nuevo Usuario';
    document.getElementById('user_id').value = '';
    document.getElementById('passwordLabel').innerHTML = 'Contraseña *';
    document.getElementById('user_password').required = true;
}

function editUser(user) {
    currentUser = user;
    document.getElementById('userModalTitle').textContent = 'Editar Usuario';
    document.getElementById('user_id').value = user.id;
    document.getElementById('user_first_name').value = user.first_name;
    document.getElementById('user_last_name').value = user.last_name;
    document.getElementById('user_email').value = user.email;
    document.getElementById('user_role').value = user.role;
    document.getElementById('passwordLabel').innerHTML = 'Contraseña <small class="text-muted-custom">(opcional)</small>';
    document.getElementById('user_password').required = false;
    document.getElementById('user_password').value = '';
    
    // Cargar empleado asociado si existe
    fetch('<?= BASE_URL ?>/users/get-employee?user_id=' + user.id)
        .then(response => response.json())
        .then(data => {
            if (data.employee_id) {
                document.getElementById('user_employee_id').value = data.employee_id;
            } else {
                document.getElementById('user_employee_id').value = '';
            }
        })
        .catch(() => {
            document.getElementById('user_employee_id').value = '';
        });
    
    new bootstrap.Modal(document.getElementById('userModal')).show();
}

function deleteUser(id) {
    if (!confirm('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.')) return;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('csrf_token', '<?= $controller->generateCSRF() ?>');
    
    fetch('<?= BASE_URL ?>/users/delete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al eliminar usuario');
        }
    });
}

document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = currentUser ? '<?= BASE_URL ?>/users/update' : '<?= BASE_URL ?>/users/create';
    const userId = formData.get('id') || null;
    const employeeId = formData.get('employee_id');
    
    // Primero guardar/actualizar el usuario
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Si hay un employee_id, asociarlo
            if (employeeId && userId) {
                const associateData = new FormData();
                associateData.append('user_id', userId);
                associateData.append('employee_id', employeeId);
                associateData.append('csrf_token', '<?= $controller->generateCSRF() ?>');
                
                return fetch('<?= BASE_URL ?>/users/associate-employee', {
                    method: 'POST',
                    body: associateData
                }).then(response => response.json());
            } else if (!employeeId && userId) {
                // Si no hay employee_id pero hay userId, desasociar
                const associateData = new FormData();
                associateData.append('user_id', userId);
                associateData.append('csrf_token', '<?= $controller->generateCSRF() ?>');
                
                return fetch('<?= BASE_URL ?>/users/associate-employee', {
                    method: 'POST',
                    body: associateData
                }).then(response => response.json());
            } else {
                return { success: true };
            }
        } else {
            throw new Error(data.error || 'Error al guardar usuario');
        }
    })
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
            location.reload();
        } else {
            alert(data.error || 'Error al asociar empleado');
        }
    })
    .catch(error => {
        alert(error.message || 'Error al guardar usuario');
    });
});

function associateEmployee(userId) {
    const employeeId = prompt('Ingrese el ID del empleado a asociar:');
    if (!employeeId) return;
    
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('employee_id', employeeId);
    formData.append('csrf_token', '<?= $controller->generateCSRF() ?>');
    
    fetch('<?= BASE_URL ?>/users/associate-employee', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al asociar empleado');
        }
    });
}

function disassociateEmployee(userId) {
    if (!confirm('¿Desasociar el empleado de este usuario?')) return;
    
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('csrf_token', '<?= $controller->generateCSRF() ?>');
    
    fetch('<?= BASE_URL ?>/users/associate-employee', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al desasociar empleado');
        }
    });
}
</script>

<?php
$content = ob_get_clean();
$showSidebar = true;
include VIEWS_PATH . '/layouts/main.php';
?>
