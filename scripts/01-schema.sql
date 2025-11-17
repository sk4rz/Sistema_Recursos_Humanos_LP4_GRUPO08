-- HR System Database Schema
-- Ejecutar este script en tu base de datos MySQL/MariaDB
-- IMPORTANTE: Asegúrate de que la base de datos use utf8mb4

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET character_set_connection = utf8mb4;

-- Tabla de Usuarios (Admin, Managers, Employees)
CREATE TABLE usuarios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  role ENUM('administrador', 'gerente', 'empleado') DEFAULT 'empleado',
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Departamentos/Áreas
CREATE TABLE departamentos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(150) NOT NULL UNIQUE,
  description TEXT,
  manager_id INT,
  budget DECIMAL(12, 2),
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (manager_id) REFERENCES usuarios(id) ON DELETE SET NULL,
  INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Empleados (Extended User Info)
CREATE TABLE empleados (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL UNIQUE,
  employee_id VARCHAR(50) UNIQUE NOT NULL,
  department_id INT NOT NULL,
  position VARCHAR(150) NOT NULL,
  hire_date DATE NOT NULL,
  salary DECIMAL(12, 2) NOT NULL,
  contract_type ENUM('tiempo_completo', 'medio_tiempo', 'contratista') DEFAULT 'tiempo_completo',
  manager_id INT,
  phone VARCHAR(20),
  address TEXT,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (department_id) REFERENCES departamentos(id) ON DELETE RESTRICT,
  FOREIGN KEY (manager_id) REFERENCES empleados(id) ON DELETE SET NULL,
  INDEX idx_employee_id (employee_id),
  INDEX idx_department_id (department_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Asistencia
CREATE TABLE asistencia (
  id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL,
  date DATE NOT NULL,
  check_in_time TIME,
  check_out_time TIME,
  status ENUM('presente', 'ausente', 'retrasado', 'salida_temprana', 'remoto') DEFAULT 'ausente',
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES empleados(id) ON DELETE CASCADE,
  UNIQUE KEY unique_attendance (employee_id, date),
  INDEX idx_date (date),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Vacaciones
CREATE TABLE solicitudes_vacaciones (
  id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL,
  leave_type ENUM('anual', 'enfermedad', 'sin_pago', 'personal', 'maternidad') NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  reason TEXT,
  status ENUM('pendiente', 'aprobado', 'rechazado', 'cancelado') DEFAULT 'pendiente',
  approved_by INT,
  approval_date DATETIME,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES empleados(id) ON DELETE CASCADE,
  FOREIGN KEY (approved_by) REFERENCES usuarios(id) ON DELETE SET NULL,
  INDEX idx_status (status),
  INDEX idx_employee_id (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Saldos de Vacaciones
CREATE TABLE saldos_vacaciones (
  id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL UNIQUE,
  annual_leave INT DEFAULT 20,
  sick_leave INT DEFAULT 10,
  personal_leave INT DEFAULT 3,
  year INT DEFAULT NULL,
  last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Planillas
CREATE TABLE planillas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL,
  payment_period_start DATE NOT NULL,
  payment_period_end DATE NOT NULL,
  base_salary DECIMAL(12, 2) NOT NULL,
  allowances DECIMAL(12, 2) DEFAULT 0,
  deductions DECIMAL(12, 2) DEFAULT 0,
  tax DECIMAL(12, 2) DEFAULT 0,
  net_salary DECIMAL(12, 2) NOT NULL,
  payment_date DATE,
  payment_method ENUM('transferencia_bancaria', 'efectivo', 'cheque') DEFAULT 'transferencia_bancaria',
  status ENUM('borrador', 'aprobado', 'pagado', 'cancelado') DEFAULT 'aprobado',
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES empleados(id) ON DELETE CASCADE,
  INDEX idx_status (status),
  INDEX idx_payment_period (payment_period_start, payment_period_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Evaluaciones de Desempeño
CREATE TABLE evaluaciones_desempeno (
  id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL,
  evaluator_id INT NOT NULL,
  evaluation_period_start DATE NOT NULL,
  evaluation_period_end DATE NOT NULL,
  rating INT CHECK (rating >= 1 AND rating <= 5),
  comments TEXT,
  strengths TEXT,
  areas_for_improvement TEXT,
  goals TEXT,
  status ENUM('borrador', 'completado', 'revisado') DEFAULT 'completado',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES empleados(id) ON DELETE CASCADE,
  FOREIGN KEY (evaluator_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
  INDEX idx_employee_id (employee_id),
  INDEX idx_period (evaluation_period_start, evaluation_period_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Auditoria (Seguridad)
CREATE TABLE registros_auditoria (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  action VARCHAR(255) NOT NULL,
  entity_type VARCHAR(100),
  entity_id INT,
  old_values JSON,
  new_values JSON,
  ip_address VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE SET NULL,
  INDEX idx_user_id (user_id),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
