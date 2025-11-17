<?php
/**
 * Controlador del Dashboard
 */
class TableroController extends Controller {
    private $employeeModel;
    private $attendanceModel;
    private $payrollModel;
    private $evaluationModel;
    private $leaveModel;
    private $departmentModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->employeeModel = new Empleado();
        $this->attendanceModel = new Asistencia();
        $this->payrollModel = new Planilla();
        $this->evaluationModel = new EvaluacionDesempeno();
        $this->leaveModel = new SolicitudVacacion();
        $this->departmentModel = new Departamento();
    }

    public function index() {
        // Obtener estadísticas
        $stats = [
            'total_employees' => $this->employeeModel->getTotalCount(),
            'attendance_rate' => $this->getAttendanceRate(),
            'monthly_payroll' => $this->getMonthlyPayroll(),
            'avg_performance' => $this->evaluationModel->getAverageRating(),
            'pending_leaves' => $this->leaveModel->getPendingCount(),
            'incomplete_evaluations' => $this->evaluationModel->getIncompleteCount()
        ];

        // Datos para gráficos
        $chartData = [
            'attendance' => $this->getAttendanceChartData(),
            'payroll' => $this->getPayrollChartData(),
            'performance' => $this->getPerformanceChartData(),
            'departments' => $this->getDepartmentChartData()
        ];

        // Datos recientes para widgets
        $recentData = [
            'recent_employees' => $this->getRecentEmployees(5),
            'pending_leaves' => $this->getPendingLeaves(5),
            'recent_attendance' => $this->getRecentAttendance(10),
            'upcoming_evaluations' => $this->getUpcomingEvaluations(5)
        ];

        $this->view('dashboard/index', [
            'stats' => $stats,
            'chartData' => $chartData,
            'recentData' => $recentData
        ]);
    }

    private function getAttendanceRate() {
        $currentMonth = date('n');
        $currentYear = date('Y');
        return $this->attendanceModel->getAttendanceRate($currentMonth, $currentYear);
    }

    private function getMonthlyPayroll() {
        $currentMonth = date('n');
        $currentYear = date('Y');
        return $this->payrollModel->getMonthlyTotal($currentMonth, $currentYear);
    }

    private function getAttendanceChartData() {
        $data = [];
        for ($i = 4; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $month = date('n', strtotime("-$i months"));
            $year = date('Y', strtotime("-$i months"));
            
            $stats = $this->attendanceModel->getMonthlyStats($month, $year);
            $present = 0;
            $absent = 0;
            $late = 0;

            foreach ($stats as $stat) {
                if ($stat['status'] === 'presente' || $stat['status'] === 'remoto') {
                    $present += $stat['count'];
                } elseif ($stat['status'] === 'ausente') {
                    $absent += $stat['count'];
                } elseif ($stat['status'] === 'retrasado') {
                    $late += $stat['count'];
                }
            }

            $data[] = [
                'month' => date('M', strtotime("-$i months")),
                'present' => $present,
                'absent' => $absent,
                'late' => $late
            ];
        }
        return $data;
    }

    private function getPayrollChartData() {
        // Datos simplificados para el gráfico de pastel
        $borrador = $this->payrollModel->getTotalByStatus('borrador');
        $aprobado = $this->payrollModel->getTotalByStatus('aprobado');
        $pagado = $this->payrollModel->getTotalByStatus('pagado');
        
        return [
            ['name' => 'Salarios', 'value' => 45],
            ['name' => 'Beneficios', 'value' => 30],
            ['name' => 'Deducciones', 'value' => 25]
        ];
    }

    private function getPerformanceChartData() {
        // Datos simplificados - en producción se calcularían desde la BD
        $data = [];
        for ($i = 4; $i >= 0; $i--) {
            $data[] = [
                'month' => date('M', strtotime("-$i months")),
                'rating' => round(3.5 + ($i * 0.15), 1),
                'target' => 4.0
            ];
        }
        return $data;
    }

    private function getDepartmentChartData() {
        $departments = $this->departmentModel->getAll();
        $data = [];
        
        foreach ($departments as $dept) {
            $employees = $this->employeeModel->getByDepartment($dept['id']);
            $totalSalary = 0;
            foreach ($employees as $emp) {
                $totalSalary += $emp['salary'] ?? 0;
            }
            
            $data[] = [
                'name' => $dept['name'],
                'employees' => count($employees),
                'salary' => $totalSalary
            ];
        }
        
        return $data;
    }

    private function getRecentEmployees($limit = 5) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT 
                e.*,
                u.email,
                u.first_name,
                u.last_name,
                d.name as department_name
            FROM empleados e
            INNER JOIN usuarios u ON e.user_id = u.id
            INNER JOIN departamentos d ON e.department_id = d.id
            WHERE e.is_active = 1
            ORDER BY e.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function getPendingLeaves($limit = 5) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT 
                lr.*,
                e.employee_id,
                u.first_name,
                u.last_name
            FROM solicitudes_vacaciones lr
            INNER JOIN empleados e ON lr.employee_id = e.id
            INNER JOIN usuarios u ON e.user_id = u.id
            WHERE lr.status = 'pendiente'
            ORDER BY lr.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function getRecentAttendance($limit = 10) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT 
                a.*,
                e.employee_id,
                u.first_name,
                u.last_name
            FROM asistencia a
            INNER JOIN empleados e ON a.employee_id = e.id
            INNER JOIN usuarios u ON e.user_id = u.id
            WHERE a.date = CURDATE()
            ORDER BY a.check_in_time DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function getUpcomingEvaluations($limit = 5) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT 
                pe.*,
                e.employee_id,
                u.first_name,
                u.last_name
            FROM evaluaciones_desempeno pe
            INNER JOIN empleados e ON pe.employee_id = e.id
            INNER JOIN usuarios u ON e.user_id = u.id
            WHERE pe.status = 'borrador' OR pe.evaluation_period_end >= CURDATE()
            ORDER BY pe.evaluation_period_end ASC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}

