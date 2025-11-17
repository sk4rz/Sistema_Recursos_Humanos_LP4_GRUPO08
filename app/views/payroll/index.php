<?php
$title = 'Planillas - Sistema RH';
ob_start();
?>

<div class="container-fluid px-4 py-4">
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1>Planillas</h1>
            <p class="text-muted-custom mb-0">Gestión de nóminas</p>
        </div>
        <div class="page-actions mt-3 mt-md-0">
            <div class="btn-group">
                <a href="<?= BASE_URL ?>/reports/payroll?period=<?= date('Y-m') ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i>
                </a>
                <a href="<?= BASE_URL ?>/reports/payroll/excel?period=<?= date('Y-m') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-file-earmark-excel"></i>
                </a>
            </div>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#payrollModal" onclick="openPayrollModal()">
                <i class="bi bi-plus-lg me-1"></i>Nueva
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">Total</div>
                    <i class="bi bi-cash-coin stat-icon text-primary"></i>
                </div>
                <div class="stat-value">S/ <?= number_format(array_sum(array_column($payroll, 'net_salary')), 0) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">Aprobadas</div>
                    <i class="bi bi-check-circle stat-icon text-success"></i>
                </div>
                <div class="stat-value"><?= count(array_filter($payroll, fn($p) => $p['status'] === 'aprobado')) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">Pagadas</div>
                    <i class="bi bi-check2-circle stat-icon text-primary"></i>
                </div>
                <div class="stat-value"><?= count(array_filter($payroll, fn($p) => $p['status'] === 'pagado')) ?></div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-3">
        <a href="?status=all" class="btn btn-sm <?= $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' ?>">Todas</a>
        <a href="?status=aprobado" class="btn btn-sm <?= $status === 'aprobado' ? 'btn-primary' : 'btn-outline-secondary' ?>">Aprobadas</a>
        <a href="?status=pagado" class="btn btn-sm <?= $status === 'pagado' ? 'btn-success' : 'btn-outline-secondary' ?>">Pagadas</a>
        <a href="?status=cancelado" class="btn btn-sm <?= $status === 'cancelado' ? 'btn-danger' : 'btn-outline-secondary' ?>">Canceladas</a>
    </div>

    <!-- Table -->
    <div class="card card-custom">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Período</th>
                            <th>Salario Base</th>
                            <th>Bonificaciones</th>
                            <th>Descuentos</th>
                            <th>Impuestos</th>
                            <th>Neto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payroll)): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted-custom py-4">No hay planillas registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payroll as $pay): ?>
                                <tr>
                                    <td><?= htmlspecialchars($pay['first_name'] . ' ' . $pay['last_name']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($pay['payment_period_start'])) ?> - <?= date('d/m/Y', strtotime($pay['payment_period_end'])) ?></td>
                                    <td>S/ <?= number_format($pay['base_salary'], 2) ?></td>
                                    <td>S/ <?= number_format($pay['allowances'], 2) ?></td>
                                    <td>S/ <?= number_format($pay['deductions'], 2) ?></td>
                                    <td>S/ <?= number_format($pay['tax'], 2) ?></td>
                                    <td class="fw-bold">S/ <?= number_format($pay['net_salary'], 2) ?></td>
                                    <td>
                                        <?php
                                        $statusLabels = [
                                            'aprobado' => ['Aprobada', 'primary'],
                                            'pagado' => ['Pagada', 'success'],
                                            'cancelado' => ['Cancelada', 'danger']
                                        ];
                                        $label = $statusLabels[$pay['status']] ?? ['Desconocido', 'secondary'];
                                        ?>
                                        <span class="badge bg-<?= $label[1] ?>"><?= $label[0] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-link text-primary p-1" onclick="editPayroll(<?= htmlspecialchars(json_encode($pay)) ?>)" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-link text-danger p-1" onclick="deletePayroll(<?= $pay['id'] ?>)" title="Eliminar">
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

<!-- Payroll Modal -->
<div class="modal fade" id="payrollModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-custom">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="payrollModalTitle">Nueva Planilla</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="payrollForm">
                <div class="modal-body">
                    <input type="hidden" id="payroll_id" name="id">
                    <input type="hidden" name="csrf_token" value="<?= $controller->generateCSRF() ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Empleado *</label>
                            <select class="form-select" name="employee_id" id="payroll_employee_id" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?= $emp['id'] ?>" data-salary="<?= $emp['salary'] ?>">
                                        <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="status" id="payroll_status">
                                <option value="aprobado" selected>Aprobada</option>
                                <option value="pagado">Pagada</option>
                                <option value="cancelado">Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Período Inicio *</label>
                            <input type="date" class="form-control" name="payment_period_start" id="payroll_period_start" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Período Fin *</label>
                            <input type="date" class="form-control" name="payment_period_end" id="payroll_period_end" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Salario Base *</label>
                            <input type="number" step="0.01" class="form-control" name="base_salary" id="payroll_base_salary" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bonificaciones</label>
                            <input type="number" step="0.01" class="form-control" name="allowances" id="payroll_allowances" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descuentos</label>
                            <input type="number" step="0.01" class="form-control" name="deductions" id="payroll_deductions" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Método de Pago</label>
                            <select class="form-select" name="payment_method" id="payroll_payment_method">
                                <option value="transferencia_bancaria">Transferencia Bancaria</option>
                                <option value="efectivo">Efectivo</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea class="form-control" name="notes" id="payroll_notes" rows="2"></textarea>
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
let currentPayroll = null;

function openPayrollModal() {
    currentPayroll = null;
    document.getElementById('payrollForm').reset();
    document.getElementById('payrollModalTitle').textContent = 'Nueva Planilla';
    document.getElementById('payroll_id').value = '';
    
    // Set default period (current month)
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    document.getElementById('payroll_period_start').value = firstDay.toISOString().split('T')[0];
    document.getElementById('payroll_period_end').value = lastDay.toISOString().split('T')[0];
}

// Auto-fill salary when employee is selected
document.getElementById('payroll_employee_id')?.addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const salary = option.getAttribute('data-salary');
    if (salary) {
        document.getElementById('payroll_base_salary').value = salary;
    }
});

function editPayroll(pay) {
    currentPayroll = pay;
    document.getElementById('payrollModalTitle').textContent = 'Editar Planilla';
    document.getElementById('payroll_id').value = pay.id;
    document.getElementById('payroll_employee_id').value = pay.employee_id;
    document.getElementById('payroll_period_start').value = pay.payment_period_start;
    document.getElementById('payroll_period_end').value = pay.payment_period_end;
    document.getElementById('payroll_base_salary').value = pay.base_salary;
    document.getElementById('payroll_allowances').value = pay.allowances;
    document.getElementById('payroll_deductions').value = pay.deductions;
    document.getElementById('payroll_status').value = pay.status;
    document.getElementById('payroll_payment_method').value = pay.payment_method;
    document.getElementById('payroll_notes').value = pay.notes || '';
    
    new bootstrap.Modal(document.getElementById('payrollModal')).show();
}

function deletePayroll(id) {
    if (!confirm('¿Está seguro de eliminar esta planilla?')) return;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('csrf_token', '<?= $controller->generateCSRF() ?>');
    
    fetch('<?= BASE_URL ?>/payroll/delete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al eliminar planilla');
        }
    });
}

document.getElementById('payrollForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = currentPayroll ? '<?= BASE_URL ?>/payroll/update' : '<?= BASE_URL ?>/payroll/create';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('payrollModal')).hide();
            location.reload();
        } else {
            alert(data.error || 'Error al guardar planilla');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
$showSidebar = true;
include VIEWS_PATH . '/layouts/main.php';
?>

