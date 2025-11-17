<?php
$title = 'Vacaciones - Sistema RH';
ob_start();
?>

<div class="container-fluid px-4 py-4">
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1>Vacaciones</h1>
            <p class="text-muted-custom mb-0">Solicitudes de vacaciones</p>
        </div>
        <div class="page-actions mt-3 mt-md-0">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#leaveModal" onclick="openLeaveModal()">
                <i class="bi bi-plus-lg me-1"></i>Nueva
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-3">
        <a href="?status=all" class="btn btn-sm <?= $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' ?>">Todas</a>
        <a href="?status=pendiente" class="btn btn-sm <?= $status === 'pendiente' ? 'btn-warning' : 'btn-outline-secondary' ?>">Pendientes</a>
        <a href="?status=aprobado" class="btn btn-sm <?= $status === 'aprobado' ? 'btn-success' : 'btn-outline-secondary' ?>">Aprobadas</a>
        <a href="?status=rechazado" class="btn btn-sm <?= $status === 'rechazado' ? 'btn-danger' : 'btn-outline-secondary' ?>">Rechazadas</a>
    </div>

    <!-- Table -->
    <div class="card card-custom">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Tipo</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Días</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leaves)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted-custom py-4">No hay solicitudes de vacaciones</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($leaves as $leave): ?>
                                <tr>
                                    <td><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></td>
                                    <td>
                                        <?php
                                        $types = [
                                            'anual' => 'Anual',
                                            'enfermedad' => 'Enfermedad',
                                            'sin_pago' => 'Sin pago',
                                            'personal' => 'Personal',
                                            'maternidad' => 'Maternidad'
                                        ];
                                        echo $types[$leave['leave_type']] ?? $leave['leave_type'];
                                        ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($leave['start_date'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($leave['end_date'])) ?></td>
                                    <td><?= (strtotime($leave['end_date']) - strtotime($leave['start_date'])) / 86400 + 1 ?></td>
                                    <td>
                                        <?php
                                        $statusLabels = [
                                            'pendiente' => ['Pendiente', 'warning'],
                                            'aprobado' => ['Aprobada', 'success'],
                                            'rechazado' => ['Rechazada', 'danger'],
                                            'cancelado' => ['Cancelada', 'secondary']
                                        ];
                                        $label = $statusLabels[$leave['status']] ?? ['Desconocido', 'secondary'];
                                        ?>
                                        <span class="badge bg-<?= $label[1] ?>"><?= $label[0] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <?php if ($leave['status'] === 'pendiente' && !in_array($_SESSION['user_role'], ['empleado'])): ?>
                                                <form method="POST" action="<?= BASE_URL ?>/leave-requests/approve" style="display: inline;">
                                                    <input type="hidden" name="id" value="<?= $leave['id'] ?>">
                                                    <input type="hidden" name="csrf_token" value="<?= $controller->generateCSRF() ?>">
                                                    <button type="submit" class="btn btn-sm btn-link text-success p-1" title="Aprobar" onclick="return confirm('¿Aprobar esta solicitud?');">
                                                        <i class="bi bi-check-lg" style="font-size: 1.125rem;"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="<?= BASE_URL ?>/leave-requests/reject" style="display: inline;">
                                                    <input type="hidden" name="id" value="<?= $leave['id'] ?>">
                                                    <input type="hidden" name="csrf_token" value="<?= $controller->generateCSRF() ?>">
                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-1" title="Rechazar" onclick="return confirm('¿Rechazar esta solicitud?');">
                                                        <i class="bi bi-x-lg" style="font-size: 1.125rem;"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST" action="<?= BASE_URL ?>/leave-requests/delete" style="display: inline;">
                                                <input type="hidden" name="id" value="<?= $leave['id'] ?>">
                                                <input type="hidden" name="csrf_token" value="<?= $controller->generateCSRF() ?>">
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-1" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta solicitud?');">
                                                    <i class="bi bi-trash" style="font-size: 1.125rem;"></i>
                                                </button>
                                            </form>
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

<!-- Leave Modal -->
<div class="modal fade" id="leaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content card-custom">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Nueva Solicitud de Vacaciones</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/leave-requests/create">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $controller->generateCSRF() ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Empleado *</label>
                        <select class="form-select" name="employee_id" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Permiso *</label>
                        <select class="form-select" name="leave_type" required>
                            <option value="anual">Vacaciones Anuales</option>
                            <option value="enfermedad">Enfermedad</option>
                            <option value="personal">Personal</option>
                            <option value="sin_pago">Sin pago</option>
                            <option value="maternidad">Maternidad</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Inicio *</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Fin *</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Razón</label>
                        <textarea class="form-control" name="reason" rows="3"></textarea>
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
function openLeaveModal() {
    const form = document.querySelector('#leaveModal form');
    if (form) {
        form.reset();
    }
}
</script>

<?php
$content = ob_get_clean();
$showSidebar = true;
include VIEWS_PATH . '/layouts/main.php';
?>

