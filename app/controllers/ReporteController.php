<?php
/**
 * Controlador de Reportes
 */
class ReporteController extends Controller {
    private $employeeModel;
    private $payrollModel;
    private $attendanceModel;
    private $evaluationModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->employeeModel = new Empleado();
        $this->payrollModel = new Planilla();
        $this->attendanceModel = new Asistencia();
        $this->evaluationModel = new EvaluacionDesempeno();
    }

    /**
     * Generar reporte de empleados en PDF
     */
    public function employees() {
        try {
            if (!class_exists('\Mpdf\Mpdf')) {
                require_once ROOT_PATH . '/vendor/autoload.php';
            }
            
            $employees = $this->employeeModel->getAll();
            
            if (empty($employees)) {
                $_SESSION['flash_error'] = 'No hay empleados para generar el reporte';
                header("Location: " . BASE_URL . "/employees");
                exit;
            }
            
            $tempDir = sys_get_temp_dir();
            if (!is_writable($tempDir)) {
                $tempDir = ROOT_PATH . '/temp';
                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }
            }
            
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 16,
                'margin_bottom' => 16,
                'tempDir' => $tempDir,
                'default_font' => 'arial',
                'default_font_size' => 10,
                'allow_charset_conversion' => true
            ]);

            $html = $this->generateEmployeesReportHTML($employees);
            
            $mpdf->WriteHTML($html);
            $mpdf->Output('reporte_empleados_' . date('Y-m-d') . '.pdf', 'D');
            exit;
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            error_log("Error generating employees report: " . $errorMsg);
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Mensaje más específico para el usuario
            $userMessage = 'Error al generar el reporte PDF. ';
            if (strpos($errorMsg, 'mbstring') !== false) {
                $userMessage .= 'La extensión mbstring de PHP no está habilitada. Por favor, habilite extension=mbstring en php.ini';
            } elseif (strpos($errorMsg, 'GD') !== false || strpos($errorMsg, 'gd') !== false) {
                $userMessage .= 'La extensión GD de PHP no está habilitada.';
            } elseif (strpos($errorMsg, 'temp') !== false || strpos($errorMsg, 'directory') !== false) {
                $userMessage .= 'Problema con el directorio temporal.';
            } else {
                $userMessage .= 'Detalles: ' . substr($errorMsg, 0, 100);
            }
            
            if (!headers_sent()) {
                $_SESSION['flash_error'] = $userMessage;
                header("Location: " . BASE_URL . "/employees");
            } else {
                echo "Error: " . htmlspecialchars($errorMsg);
            }
            exit;
        }
    }

    /**
     * Generar reporte de planillas en PDF
     */
    public function payroll() {
        try {
            if (!class_exists('\Mpdf\Mpdf')) {
                require_once ROOT_PATH . '/vendor/autoload.php';
            }
            
            $period = $_GET['period'] ?? date('Y-m');
            if (!preg_match('/^\d{4}-\d{2}$/', $period)) {
                $period = date('Y-m');
            }
            
            list($year, $month) = explode('-', $period);
            
            $filters = [
                'period_start' => "$year-$month-01",
                'period_end' => date('Y-m-t', strtotime("$year-$month-01"))
            ];
            
            $payroll = $this->payrollModel->getAll($filters);
            
            if (empty($payroll)) {
                $_SESSION['flash_error'] = 'No hay planillas para el período seleccionado';
                header("Location: " . BASE_URL . "/payroll");
                exit;
            }
            
            $tempDir = sys_get_temp_dir();
            if (!is_writable($tempDir)) {
                $tempDir = ROOT_PATH . '/temp';
                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }
            }
            
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'L', // Landscape para más columnas
                'tempDir' => $tempDir,
                'default_font' => 'arial',
                'default_font_size' => 9
            ]);

            $html = $this->generatePayrollReportHTML($payroll, $period);
            
            $mpdf->WriteHTML($html);
            $mpdf->Output('reporte_planilla_' . $period . '.pdf', 'D');
            exit;
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            error_log("Error generating payroll report: " . $errorMsg);
            error_log("Stack trace: " . $e->getTraceAsString());
            
            $userMessage = 'Error al generar el reporte PDF. ';
            if (strpos($errorMsg, 'mbstring') !== false) {
                $userMessage .= 'La extensión mbstring de PHP no está habilitada. Por favor, habilite extension=mbstring en php.ini';
            } elseif (strpos($errorMsg, 'GD') !== false || strpos($errorMsg, 'gd') !== false) {
                $userMessage .= 'La extensión GD de PHP no está habilitada.';
            } elseif (strpos($errorMsg, 'temp') !== false || strpos($errorMsg, 'directory') !== false) {
                $userMessage .= 'Problema con el directorio temporal.';
            } else {
                $userMessage .= 'Detalles: ' . substr($errorMsg, 0, 100);
            }
            
            if (!headers_sent()) {
                $_SESSION['flash_error'] = $userMessage;
                header("Location: " . BASE_URL . "/payroll");
            } else {
                echo "Error: " . htmlspecialchars($errorMsg);
            }
            exit;
        }
    }

    /**
     * Generar reporte de empleados en Excel (CSV)
     */
    public function employeesExcel() {
        try {
            $employees = $this->employeeModel->getAll();
            
            if (empty($employees)) {
                $_SESSION['flash_error'] = 'No hay empleados para generar el reporte';
                header("Location: " . BASE_URL . "/employees");
                exit;
            }
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="reporte_empleados_' . date('Y-m-d') . '.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $output = fopen('php://output', 'w');
            
            // BOM para UTF-8 (Excel)
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados completos
            fputcsv($output, [
                'ID Empleado', 'Nombre', 'Apellido', 'Email', 'Rol', 
                'Posición', 'Departamento', 'Salario', 'Tipo Contrato',
                'Fecha Contratación', 'Teléfono', 'Dirección', 'Estado'
            ], ';');
            
            // Datos completos
            foreach ($employees as $emp) {
                $contractTypes = [
                    'tiempo_completo' => 'Tiempo Completo',
                    'medio_tiempo' => 'Medio Tiempo',
                    'contratista' => 'Contratista'
                ];
                
                fputcsv($output, [
                    $emp['employee_id'],
                    $emp['first_name'],
                    $emp['last_name'],
                    $emp['email'],
                    ucfirst($emp['role']),
                    $emp['position'],
                    $emp['department_name'],
                    number_format($emp['salary'], 2, '.', ''),
                    $contractTypes[$emp['contract_type']] ?? $emp['contract_type'],
                    date('d/m/Y', strtotime($emp['hire_date'])),
                    $emp['phone'] ?? '-',
                    $emp['address'] ?? '-',
                    $emp['is_active'] ? 'Activo' : 'Inactivo'
                ], ';');
            }
            
            fclose($output);
            exit;
        } catch (\Exception $e) {
            error_log("Error generating Excel report: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Error al generar el reporte Excel: ' . $e->getMessage();
            header("Location: " . BASE_URL . "/employees");
            exit;
        }
    }

    /**
     * Generar reporte de planillas en Excel (CSV)
     */
    public function payrollExcel() {
        try {
            $period = $_GET['period'] ?? date('Y-m');
            if (!preg_match('/^\d{4}-\d{2}$/', $period)) {
                $period = date('Y-m');
            }
            
            list($year, $month) = explode('-', $period);
            
            $filters = [
                'period_start' => "$year-$month-01",
                'period_end' => date('Y-m-t', strtotime("$year-$month-01"))
            ];
            
            $payroll = $this->payrollModel->getAll($filters);
            
            if (empty($payroll)) {
                $_SESSION['flash_error'] = 'No hay planillas para el período seleccionado';
                header("Location: " . BASE_URL . "/payroll");
                exit;
            }
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="reporte_planilla_' . $period . '.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            $statusLabels = [
                'borrador' => 'Borrador',
                'aprobado' => 'Aprobada',
                'pagado' => 'Pagada',
                'cancelado' => 'Cancelada'
            ];
            
            fputcsv($output, [
                'ID Empleado', 'Empleado', 'Período Inicio', 'Período Fin', 
                'Salario Base', 'Bonificaciones', 'Descuentos', 'Impuestos', 
                'Neto', 'Método de Pago', 'Fecha de Pago', 'Estado', 'Notas'
            ], ';');
            
            foreach ($payroll as $pay) {
                $paymentMethods = [
                    'transferencia_bancaria' => 'Transferencia Bancaria',
                    'efectivo' => 'Efectivo',
                    'cheque' => 'Cheque'
                ];
                
                fputcsv($output, [
                    $pay['employee_id'],
                    $pay['first_name'] . ' ' . $pay['last_name'],
                    date('d/m/Y', strtotime($pay['payment_period_start'])),
                    date('d/m/Y', strtotime($pay['payment_period_end'])),
                    number_format($pay['base_salary'], 2, '.', ''),
                    number_format($pay['allowances'], 2, '.', ''),
                    number_format($pay['deductions'], 2, '.', ''),
                    number_format($pay['tax'], 2, '.', ''),
                    number_format($pay['net_salary'], 2, '.', ''),
                    $paymentMethods[$pay['payment_method']] ?? $pay['payment_method'],
                    $pay['payment_date'] ? date('d/m/Y', strtotime($pay['payment_date'])) : '-',
                    $statusLabels[$pay['status']] ?? 'Desconocido',
                    $pay['notes'] ?? '-'
                ], ';');
            }
            
            fclose($output);
            exit;
        } catch (\Exception $e) {
            error_log("Error generating Excel report: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Error al generar el reporte Excel: ' . $e->getMessage();
            header("Location: " . BASE_URL . "/payroll");
            exit;
        }
    }

    /**
     * Generar reporte de asistencia en Excel (CSV)
     */
    public function attendanceExcel() {
        try {
            $date = $_GET['date'] ?? date('Y-m-d');
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $date = date('Y-m-d');
            }
            
            $filters = ['date' => $date];
            $attendance = $this->attendanceModel->getAll($filters);
            
            if (empty($attendance)) {
                $_SESSION['flash_error'] = 'No hay registros de asistencia para la fecha seleccionada';
                header("Location: " . BASE_URL . "/attendance");
                exit;
            }
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="reporte_asistencia_' . $date . '.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            $statusLabels = [
                'presente' => 'Presente',
                'retrasado' => 'Retrasado',
                'ausente' => 'Ausente',
                'remoto' => 'Remoto',
                'salida_temprana' => 'Salida Temprana'
            ];
            
            fputcsv($output, [
                'ID Empleado', 'Empleado', 'Fecha', 'Hora Entrada', 
                'Hora Salida', 'Estado', 'Notas', 'Fecha Registro'
            ], ';');
            
            foreach ($attendance as $att) {
                fputcsv($output, [
                    $att['employee_id'],
                    $att['first_name'] . ' ' . $att['last_name'],
                    date('d/m/Y', strtotime($att['date'])),
                    $att['check_in_time'] ? date('H:i', strtotime($att['check_in_time'])) : '-',
                    $att['check_out_time'] ? date('H:i', strtotime($att['check_out_time'])) : '-',
                    $statusLabels[$att['status']] ?? $att['status'],
                    $att['notes'] ?? '-',
                    date('d/m/Y H:i', strtotime($att['created_at']))
                ], ';');
            }
            
            fclose($output);
            exit;
        } catch (\Exception $e) {
            error_log("Error generating Excel report: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Error al generar el reporte Excel: ' . $e->getMessage();
            header("Location: " . BASE_URL . "/attendance");
            exit;
        }
    }

    /**
     * Generar reporte de asistencia en PDF
     */
    public function attendance() {
        try {
            if (!class_exists('\Mpdf\Mpdf')) {
                require_once ROOT_PATH . '/vendor/autoload.php';
            }
            
            $date = $_GET['date'] ?? date('Y-m-d');
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $date = date('Y-m-d');
            }
            
            $filters = ['date' => $date];
            
            $attendance = $this->attendanceModel->getAll($filters);
            
            if (empty($attendance)) {
                $_SESSION['flash_error'] = 'No hay registros de asistencia para la fecha seleccionada';
                header("Location: " . BASE_URL . "/attendance");
                exit;
            }
            
            $tempDir = sys_get_temp_dir();
            if (!is_writable($tempDir)) {
                $tempDir = ROOT_PATH . '/temp';
                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }
            }
            
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'tempDir' => $tempDir,
                'default_font' => 'arial',
                'default_font_size' => 10
            ]);

            $html = $this->generateAttendanceReportHTML($attendance, $date);
            
            $mpdf->WriteHTML($html);
            $mpdf->Output('reporte_asistencia_' . $date . '.pdf', 'D');
            exit;
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            error_log("Error generating attendance report: " . $errorMsg);
            error_log("Stack trace: " . $e->getTraceAsString());
            
            $userMessage = 'Error al generar el reporte PDF. ';
            if (strpos($errorMsg, 'mbstring') !== false) {
                $userMessage .= 'La extensión mbstring de PHP no está habilitada. Por favor, habilite extension=mbstring en php.ini';
            } elseif (strpos($errorMsg, 'GD') !== false || strpos($errorMsg, 'gd') !== false) {
                $userMessage .= 'La extensión GD de PHP no está habilitada.';
            } elseif (strpos($errorMsg, 'temp') !== false || strpos($errorMsg, 'directory') !== false) {
                $userMessage .= 'Problema con el directorio temporal.';
            } else {
                $userMessage .= 'Detalles: ' . substr($errorMsg, 0, 100);
            }
            
            if (!headers_sent()) {
                $_SESSION['flash_error'] = $userMessage;
                header("Location: " . BASE_URL . "/attendance");
            } else {
                echo "Error: " . htmlspecialchars($errorMsg);
            }
            exit;
        }
    }

    /**
     * Generar HTML para reporte de empleados
     */
    private function generateEmployeesReportHTML($employees) {
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { color: #333; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #3b82f6; color: white; padding: 10px; text-align: left; }
            td { padding: 8px; border-bottom: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .header { text-align: center; margin-bottom: 30px; }
            .footer { margin-top: 30px; text-align: center; color: #666; font-size: 12px; }
        </style>
        <div class="header">
            <h1>Reporte de Empleados</h1>
            <p>Sistema de Recursos Humanos</p>
            <p>Fecha de generación: ' . date('d/m/Y H:i:s') . '</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID Empleado</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Posición</th>
                    <th>Departamento</th>
                    <th>Salario</th>
                    <th>Tipo Contrato</th>
                    <th>Fecha Contratación</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($employees as $emp) {
            $contractTypes = [
                'tiempo_completo' => 'Tiempo Completo',
                'medio_tiempo' => 'Medio Tiempo',
                'contratista' => 'Contratista'
            ];
            
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($emp['employee_id']) . '</td>
                    <td>' . htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) . '</td>
                    <td>' . htmlspecialchars($emp['email']) . '</td>
                    <td>' . htmlspecialchars(ucfirst($emp['role'])) . '</td>
                    <td>' . htmlspecialchars($emp['position']) . '</td>
                    <td>' . htmlspecialchars($emp['department_name']) . '</td>
                    <td>S/ ' . number_format($emp['salary'], 2) . '</td>
                    <td>' . htmlspecialchars($contractTypes[$emp['contract_type']] ?? $emp['contract_type']) . '</td>
                    <td>' . date('d/m/Y', strtotime($emp['hire_date'])) . '</td>
                    <td>' . htmlspecialchars($emp['phone'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($emp['address'] ?? '-') . '</td>
                    <td>' . ($emp['is_active'] ? 'Activo' : 'Inactivo') . '</td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>
        <div class="footer">
            <p>Total de empleados: ' . count($employees) . '</p>
            <p>Generado por: ' . htmlspecialchars($_SESSION['user_name'] ?? 'Sistema') . '</p>
        </div>';

        return $html;
    }

    /**
     * Generar HTML para reporte de planillas
     */
    private function generatePayrollReportHTML($payroll, $period) {
        $total = array_sum(array_column($payroll, 'net_salary'));
        
        $statusLabels = [
            'borrador' => 'Borrador',
            'aprobado' => 'Aprobada',
            'pagado' => 'Pagada',
            'cancelado' => 'Cancelada'
        ];
        
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { color: #333; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10px; }
            th { background-color: #3b82f6; color: white; padding: 8px; text-align: left; }
            td { padding: 6px; border-bottom: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .header { text-align: center; margin-bottom: 30px; }
            .footer { margin-top: 30px; text-align: right; }
            .total { font-weight: bold; background-color: #e3f2fd; }
        </style>
        <div class="header">
            <h1>Reporte de Planillas</h1>
            <p>Período: ' . date('F Y', strtotime($period . '-01')) . '</p>
            <p>Fecha de generación: ' . date('d/m/Y H:i:s') . '</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID Empleado</th>
                    <th>Empleado</th>
                    <th>Período Inicio</th>
                    <th>Período Fin</th>
                    <th>Salario Base</th>
                    <th>Bonificaciones</th>
                    <th>Descuentos</th>
                    <th>Impuestos</th>
                    <th>Neto</th>
                    <th>Método de Pago</th>
                    <th>Fecha de Pago</th>
                    <th>Estado</th>
                    <th>Notas</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($payroll as $pay) {
            $paymentMethods = [
                'transferencia_bancaria' => 'Transferencia Bancaria',
                'efectivo' => 'Efectivo',
                'cheque' => 'Cheque'
            ];
            
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($pay['employee_id']) . '</td>
                    <td>' . htmlspecialchars($pay['first_name'] . ' ' . $pay['last_name']) . '</td>
                    <td>' . date('d/m/Y', strtotime($pay['payment_period_start'])) . '</td>
                    <td>' . date('d/m/Y', strtotime($pay['payment_period_end'])) . '</td>
                    <td>S/ ' . number_format($pay['base_salary'], 2) . '</td>
                    <td>S/ ' . number_format($pay['allowances'], 2) . '</td>
                    <td>S/ ' . number_format($pay['deductions'], 2) . '</td>
                    <td>S/ ' . number_format($pay['tax'], 2) . '</td>
                    <td>S/ ' . number_format($pay['net_salary'], 2) . '</td>
                    <td>' . htmlspecialchars($paymentMethods[$pay['payment_method']] ?? $pay['payment_method']) . '</td>
                    <td>' . ($pay['payment_date'] ? date('d/m/Y', strtotime($pay['payment_date'])) : '-') . '</td>
                    <td>' . htmlspecialchars($statusLabels[$pay['status']] ?? 'Desconocido') . '</td>
                    <td>' . htmlspecialchars($pay['notes'] ?? '-') . '</td>
                </tr>';
        }

        $html .= '
                <tr class="total">
                    <td colspan="8" style="text-align: right;"><strong>TOTAL:</strong></td>
                    <td colspan="5"><strong>S/ ' . number_format($total, 2) . '</strong></td>
                </tr>
            </tbody>
        </table>
        <div class="footer">
            <p>Total de registros: ' . count($payroll) . '</p>
            <p>Generado por: ' . htmlspecialchars($_SESSION['user_name'] ?? 'Sistema') . '</p>
        </div>';

        return $html;
    }

    /**
     * Generar HTML para reporte de asistencia
     */
    private function generateAttendanceReportHTML($attendance, $date) {
        $present = count(array_filter($attendance, fn($a) => in_array($a['status'], ['presente', 'retrasado', 'remoto'])));
        $absent = count(array_filter($attendance, fn($a) => $a['status'] === 'ausente'));
        
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { color: #333; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #3b82f6; color: white; padding: 10px; text-align: left; }
            td { padding: 8px; border-bottom: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .header { text-align: center; margin-bottom: 30px; }
            .stats { margin: 20px 0; padding: 15px; background-color: #f0f0f0; border-radius: 5px; }
            .footer { margin-top: 30px; text-align: center; color: #666; font-size: 12px; }
        </style>
        <div class="header">
            <h1>Reporte de Asistencia</h1>
            <p>Fecha: ' . date('d/m/Y', strtotime($date)) . '</p>
            <p>Fecha de generación: ' . date('d/m/Y H:i:s') . '</p>
        </div>
        <div class="stats">
            <strong>Resumen:</strong> Presentes: ' . $present . ' | Ausentes: ' . $absent . ' | Total: ' . count($attendance) . '
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID Empleado</th>
                    <th>Empleado</th>
                    <th>Fecha</th>
                    <th>Hora Entrada</th>
                    <th>Hora Salida</th>
                    <th>Estado</th>
                    <th>Notas</th>
                    <th>Fecha Registro</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($attendance as $att) {
            $statusLabels = [
                'presente' => 'Presente',
                'retrasado' => 'Retrasado',
                'ausente' => 'Ausente',
                'remoto' => 'Remoto',
                'salida_temprana' => 'Salida Temprana'
            ];
            
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($att['employee_id']) . '</td>
                    <td>' . htmlspecialchars($att['first_name'] . ' ' . $att['last_name']) . '</td>
                    <td>' . date('d/m/Y', strtotime($att['date'])) . '</td>
                    <td>' . ($att['check_in_time'] ? date('H:i', strtotime($att['check_in_time'])) : '-') . '</td>
                    <td>' . ($att['check_out_time'] ? date('H:i', strtotime($att['check_out_time'])) : '-') . '</td>
                    <td>' . htmlspecialchars($statusLabels[$att['status']] ?? $att['status']) . '</td>
                    <td>' . htmlspecialchars($att['notes'] ?? '-') . '</td>
                    <td>' . date('d/m/Y H:i', strtotime($att['created_at'])) . '</td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>
        <div class="footer">
            <p>Generado por: ' . htmlspecialchars($_SESSION['user_name'] ?? 'Sistema') . '</p>
        </div>';

        return $html;
    }
}

