<?php
$title = 'Dashboard - Sistema RH';
ob_start();
?>

<div class="container-fluid px-4 py-4">
    <div class="page-header mb-4">
        <h1>Dashboard</h1>
        <p class="text-muted-custom mb-0">Resumen general del sistema</p>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Empleados</div>
                <div class="stat-value"><?= number_format($stats['total_employees']) ?></div>
                <i class="bi bi-people stat-icon text-primary"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Asistencia</div>
                <div class="stat-value"><?= number_format($stats['attendance_rate'], 1) ?>%</div>
                <i class="bi bi-calendar-check stat-icon text-success"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Nómina</div>
                <div class="stat-value">$<?= number_format($stats['monthly_payroll'], 0) ?></div>
                <i class="bi bi-cash-coin stat-icon text-warning"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Desempeño</div>
                <div class="stat-value"><?= number_format($stats['avg_performance'], 1) ?>/5</div>
                <i class="bi bi-star stat-icon text-primary"></i>
            </div>
        </div>
    </div>

    <!-- Primera fila: Gráfico principal y Asistencia de hoy -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card card-custom h-100">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">Asistencia Mensual</h6>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-custom h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">Asistencia de Hoy</h6>
                    <a href="<?= BASE_URL ?>/attendance?date=<?= date('Y-m-d') ?>" class="btn btn-sm btn-link p-0">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($recentData['recent_attendance'])): ?>
                        <div class="text-center py-5 px-3">
                            <i class="bi bi-inbox text-muted-custom" style="font-size: 2.5rem; opacity: 0.4;"></i>
                            <p class="text-muted-custom mt-3 mb-0" style="font-size: 0.875rem;">No hay registros de asistencia para hoy</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach (array_slice($recentData['recent_attendance'], 0, 8) as $att): ?>
                                <div class="list-group-item bg-transparent border-bottom border-secondary d-flex align-items-center justify-content-between py-3 px-3">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold mb-1 text-white" style="font-size: 0.9375rem;">
                                            <?= htmlspecialchars($att['first_name'] . ' ' . $att['last_name']) ?>
                                        </div>
                                        <?php if ($att['check_in_time']): ?>
                                            <div class="d-flex align-items-center text-muted" style="font-size: 0.8125rem;">
                                                <i class="bi bi-clock me-1"></i>
                                                <span><?= date('H:i', strtotime($att['check_in_time'])) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="badge ms-3 <?= 
                                        $att['status'] === 'presente' ? 'bg-success' : 
                                        ($att['status'] === 'retrasado' ? 'bg-warning' : 
                                        ($att['status'] === 'remoto' ? 'bg-info' : 'bg-danger')) 
                                    ?>">
                                        <?= ucfirst($att['status']) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda fila: Empleados Recientes y Solicitudes Pendientes -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">Empleados Recientes</h6>
                    <a href="<?= BASE_URL ?>/employees" class="btn btn-sm btn-link p-0">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentData['recent_employees'])): ?>
                        <div class="text-center py-5 px-3">
                            <i class="bi bi-people text-muted-custom" style="font-size: 2.5rem; opacity: 0.4;"></i>
                            <p class="text-muted-custom mt-3 mb-0" style="font-size: 0.875rem;">No hay empleados registrados</p>
                            <a href="<?= BASE_URL ?>/employees" class="btn btn-sm btn-primary mt-3">
                                <i class="bi bi-plus-lg me-1"></i>Agregar Empleado
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                    <tr>
                                    <th>Empleado</th>
                                    <th>Departamento</th>
                                    <th class="text-end">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentData['recent_employees'] as $emp): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold mb-1 text-white" style="font-size: 0.9375rem;">
                                                    <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                                </div>
                                                <div class="text-muted" style="font-size: 0.8125rem;">
                                                    <?= htmlspecialchars($emp['employee_id']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($emp['department_name']) ?></span>
                                            </td>
                                            <td class="text-end">
                                                <span class="text-muted" style="font-size: 0.875rem;">
                                                    <?= date('d/m/Y', strtotime($emp['created_at'])) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">Solicitudes Pendientes</h6>
                    <a href="<?= BASE_URL ?>/leave-requests?status=pendiente" class="btn btn-sm btn-link p-0">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentData['pending_leaves'])): ?>
                        <div class="text-center py-5 px-3">
                            <i class="bi bi-calendar-x text-muted-custom" style="font-size: 2.5rem; opacity: 0.4;"></i>
                            <p class="text-muted-custom mt-3 mb-0" style="font-size: 0.875rem;">No hay solicitudes pendientes</p>
                            <a href="<?= BASE_URL ?>/leave-requests" class="btn btn-sm btn-primary mt-3">
                                <i class="bi bi-eye me-1"></i>Ver Todas
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                    <tr>
                                    <th>Empleado</th>
                                    <th>Período</th>
                                    <th class="text-end">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($recentData['pending_leaves'], 0, 5) as $leave): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold mb-1 text-white" style="font-size: 0.9375rem;">
                                                    <?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?>
                                                </div>
                                                <div class="text-muted" style="font-size: 0.8125rem;">
                                                    <?= ucfirst(str_replace('_', ' ', $leave['leave_type'])) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted" style="font-size: 0.875rem;">
                                                    <?= date('d/m', strtotime($leave['start_date'])) ?> - <?= date('d/m/Y', strtotime($leave['end_date'])) ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?= BASE_URL ?>/leave-requests" class="btn btn-sm btn-primary">Revisar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tercera fila: Gráficos adicionales -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">Evaluaciones de Desempeño</h6>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">Por Departamento</h6>
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Attendance Chart
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
new Chart(attendanceCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($chartData['attendance'], 'month')) ?>,
        datasets: [
            {
                label: 'Presente',
                data: <?= json_encode(array_column($chartData['attendance'], 'present')) ?>,
                backgroundColor: '#10b981'
            },
            {
                label: 'Retrasado',
                data: <?= json_encode(array_column($chartData['attendance'], 'late')) ?>,
                backgroundColor: '#f59e0b'
            },
            {
                label: 'Ausente',
                data: <?= json_encode(array_column($chartData['attendance'], 'absent')) ?>,
                backgroundColor: '#ef4444'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: { 
                    color: '#cbd5e1',
                    font: { size: 12 },
                    padding: 15
                }
            }
        },
        scales: {
            x: { 
                ticks: { color: '#cbd5e1', font: { size: 12 } }, 
                grid: { color: '#2d3748', display: false } 
            },
            y: { 
                ticks: { color: '#cbd5e1', font: { size: 12 } }, 
                grid: { color: '#2d3748' } 
            }
        }
    }
});

// Performance Chart
const performanceCtx = document.getElementById('performanceChart').getContext('2d');
new Chart(performanceCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($chartData['performance'], 'month')) ?>,
        datasets: [
            {
                label: 'Calificación',
                data: <?= json_encode(array_column($chartData['performance'], 'rating')) ?>,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Meta',
                data: <?= json_encode(array_column($chartData['performance'], 'target')) ?>,
                borderColor: '#9ca3af',
                borderDash: [5, 5],
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: { 
                    color: '#cbd5e1',
                    font: { size: 12 },
                    padding: 15
                }
            }
        },
        scales: {
            x: { 
                ticks: { color: '#cbd5e1', font: { size: 12 } }, 
                grid: { color: '#2d3748', display: false } 
            },
            y: { 
                ticks: { color: '#cbd5e1', font: { size: 12 } }, 
                grid: { color: '#2d3748' }, 
                min: 3, 
                max: 5 
            }
        }
    }
});

// Department Chart
const departmentCtx = document.getElementById('departmentChart').getContext('2d');
new Chart(departmentCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($chartData['departments'], 'name')) ?>,
        datasets: [{
            label: 'Empleados',
            data: <?= json_encode(array_column($chartData['departments'], 'employees')) ?>,
            backgroundColor: '#3b82f6'
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: { 
                ticks: { color: '#cbd5e1', font: { size: 12 } }, 
                grid: { color: '#2d3748' } 
            },
            y: { 
                ticks: { color: '#cbd5e1', font: { size: 12 } }, 
                grid: { color: '#2d3748', display: false } 
            }
        }
    }
});
</script>

<?php
$content = ob_get_clean();
$showSidebar = true;
include VIEWS_PATH . '/layouts/main.php';
?>
