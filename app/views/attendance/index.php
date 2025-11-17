<?php
$title = 'Asistencia - Sistema RH';
ob_start();
?>

<div class="container-fluid px-4 py-4">
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1>Asistencia</h1>
            <p class="text-muted-custom mb-0">Control de asistencia diaria</p>
        </div>
        <div class="page-actions mt-3 mt-md-0">
            <div class="btn-group">
                <a href="<?= BASE_URL ?>/reports/attendance?date=<?= $selectedDate ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i>
                </a>
                <a href="<?= BASE_URL ?>/reports/attendance/excel?date=<?= $selectedDate ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-file-earmark-excel"></i>
                </a>
            </div>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#attendanceModal" onclick="openAttendanceModal()">
                <i class="bi bi-plus-lg me-1"></i>Registrar
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">Presentes</div>
                    <i class="bi bi-check-circle stat-icon text-success"></i>
                </div>
                <div class="stat-value"><?= count(array_filter($attendance, fn($a) => in_array($a['status'], ['presente', 'retrasado', 'remoto']))) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">Ausentes</div>
                    <i class="bi bi-x-circle stat-icon text-danger"></i>
                </div>
                <div class="stat-value"><?= count(array_filter($attendance, fn($a) => $a['status'] === 'ausente')) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">Retrasados</div>
                    <i class="bi bi-clock stat-icon text-warning"></i>
                </div>
                <div class="stat-value"><?= count(array_filter($attendance, fn($a) => $a['status'] === 'retrasado')) ?></div>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="mb-3">
        <form method="GET" action="<?= BASE_URL ?>/attendance" class="d-flex gap-2">
            <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($selectedDate) ?>" style="max-width: 200px;">
            <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
        </form>
    </div>

    <!-- Table -->
    <div class="card card-custom">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Fecha</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($attendance)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted-custom py-4">No hay registros de asistencia para esta fecha</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($attendance as $att): ?>
                                <tr>
                                    <td><?= htmlspecialchars($att['first_name'] . ' ' . $att['last_name']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($att['date'])) ?></td>
                                    <td><?= $att['check_in_time'] ? date('H:i', strtotime($att['check_in_time'])) : '-' ?></td>
                                    <td><?= $att['check_out_time'] ? date('H:i', strtotime($att['check_out_time'])) : '-' ?></td>
                                    <td>
                                        <?php
                                        $statusLabels = [
                                            'presente' => ['Presente', 'success'],
                                            'retrasado' => ['Retrasado', 'warning'],
                                            'ausente' => ['Ausente', 'danger'],
                                            'remoto' => ['Remoto', 'info'],
                                            'salida_temprana' => ['Salida Temprana', 'warning']
                                        ];
                                        $label = $statusLabels[$att['status']] ?? ['Desconocido', 'secondary'];
                                        ?>
                                        <span class="badge bg-<?= $label[1] ?>"><?= $label[0] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-link text-primary p-1" onclick="editAttendance(<?= htmlspecialchars(json_encode($att)) ?>)" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-link text-danger p-1" onclick="deleteAttendance(<?= $att['id'] ?>)" title="Eliminar">
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

<!-- Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content card-custom">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="attendanceModalTitle">Registrar Asistencia</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="attendanceForm">
                <div class="modal-body">
                    <input type="hidden" id="attendance_id" name="id">
                    <input type="hidden" name="csrf_token" value="<?= $controller->generateCSRF() ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Empleado *</label>
                        <select class="form-select" name="employee_id" id="attendance_employee_id" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha *</label>
                        <input type="date" class="form-control" name="date" id="attendance_date" value="<?= $selectedDate ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hora de Entrada</label>
                            <input type="time" class="form-control" name="check_in_time" id="attendance_check_in_time">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hora de Salida</label>
                            <input type="time" class="form-control" name="check_out_time" id="attendance_check_out_time">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado *</label>
                    <select class="form-select" name="status" id="attendance_status" required>
                        <option value="presente">Presente</option>
                        <option value="retrasado">Retrasado</option>
                        <option value="ausente">Ausente</option>
                        <option value="remoto">Remoto</option>
                        <option value="salida_temprana">Salida Temprana</option>
                    </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea class="form-control" name="notes" id="attendance_notes" rows="2"></textarea>
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
let currentAttendance = null;

function openAttendanceModal() {
    currentAttendance = null;
    document.getElementById('attendanceForm').reset();
    document.getElementById('attendanceModalTitle').textContent = 'Registrar Asistencia';
    document.getElementById('attendance_id').value = '';
    document.getElementById('attendance_date').value = '<?= $selectedDate ?>';
}

function editAttendance(att) {
    currentAttendance = att;
    document.getElementById('attendanceModalTitle').textContent = 'Editar Asistencia';
    document.getElementById('attendance_id').value = att.id;
    document.getElementById('attendance_employee_id').value = att.employee_id;
    document.getElementById('attendance_date').value = att.date;
    document.getElementById('attendance_check_in_time').value = att.check_in_time || '';
    document.getElementById('attendance_check_out_time').value = att.check_out_time || '';
    document.getElementById('attendance_status').value = att.status;
    document.getElementById('attendance_notes').value = att.notes || '';
    
    new bootstrap.Modal(document.getElementById('attendanceModal')).show();
}

function deleteAttendance(id) {
    if (!confirm('¿Está seguro de eliminar este registro?')) return;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('csrf_token', '<?= $controller->generateCSRF() ?>');
    
    fetch('<?= BASE_URL ?>/attendance/delete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al eliminar registro');
        }
    });
}

document.getElementById('attendanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = currentAttendance ? '<?= BASE_URL ?>/attendance/update' : '<?= BASE_URL ?>/attendance/create';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('attendanceModal')).hide();
            location.reload();
        } else {
            alert(data.error || 'Error al guardar asistencia');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
$showSidebar = true;
include VIEWS_PATH . '/layouts/main.php';
?>

