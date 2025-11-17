-- Datos de prueba para el Sistema de Recursos Humanos
-- Ejecutar después de 01-schema.sql
-- IMPORTANTE: Asegúrate de que la conexión use utf8mb4

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET character_set_connection = utf8mb4;

-- Usuarios de prueba (Solo 3 usuarios)
-- Contraseña para todos: "password123"
-- Hash generado con: password_hash('password123', PASSWORD_DEFAULT)
INSERT INTO usuarios (email, password_hash, first_name, last_name, role, is_active) VALUES
('admin@example.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Admin', 'Sistema', 'administrador', TRUE),
('manager@example.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Manager', 'Demo', 'gerente', TRUE),
('employee@example.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Employee', 'Demo', 'empleado', TRUE);

-- Se recomienda cambiar las contraseñas después de la instalación

-- Departamentos
INSERT INTO departamentos (name, description, manager_id, budget, is_active) VALUES
('Recursos Humanos', 'Gestión de personal, reclutamiento y desarrollo organizacional', 1, 850000.00, TRUE),
('Tecnología de la Información', 'Desarrollo de software, infraestructura y soporte técnico', 2, 2500000.00, TRUE),
('Ventas y Marketing', 'Estrategias comerciales, atención al cliente y publicidad', 2, 1800000.00, TRUE),
('Finanzas y Contabilidad', 'Control financiero, contabilidad y presupuestos', 1, 1200000.00, TRUE),
('Operaciones', 'Logística, producción y cadena de suministro', 2, 1500000.00, TRUE),
('Atención al Cliente', 'Soporte post-venta y resolución de consultas', 2, 600000.00, TRUE);

-- Crear usuarios para empleados (sin acceso al sistema, solo registros)
-- Estos usuarios se usarán para asociar con empleados
-- Contraseña para todos: "password123"
INSERT INTO usuarios (email, password_hash, first_name, last_name, role, is_active) VALUES
('maria.rodriguez@empresa.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'María', 'Rodríguez', 'empleado', TRUE),
('juan.perez@empresa.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Juan', 'Pérez', 'empleado', TRUE),
('carmen.silva@empresa.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Carmen', 'Silva', 'empleado', TRUE),
('roberto.torres@empresa.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Roberto', 'Torres', 'empleado', TRUE),
('lucia.martinez@empresa.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Lucía', 'Martínez', 'empleado', TRUE),
('diego.fernandez@empresa.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Diego', 'Fernández', 'empleado', TRUE),
('patricia.gomez@empresa.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Patricia', 'Gómez', 'empleado', TRUE),
('miguel.castro@empresa.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Miguel', 'Castro', 'empleado', TRUE),
('sofia.ruiz@empresa.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Sofía', 'Ruiz', 'empleado', TRUE),
('jorge.morales@empresa.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Jorge', 'Morales', 'empleado', TRUE),
('elena.vargas@empresa.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Elena', 'Vargas', 'empleado', TRUE),
('ricardo.jimenez@empresa.com', '$2y$10$TiotvwxrcOj1Sb0Bl68Wd.flbod.I0etS2yVotu8e6HN3wV8lxWAu', 'Ricardo', 'Jiménez', 'empleado', TRUE);

-- Empleados
INSERT INTO empleados (user_id, employee_id, department_id, position, hire_date, salary, contract_type, phone, address, is_active) VALUES
(3, 'EMP001', 2, 'Desarrollador Full Stack', '2024-01-15', 5500.00, 'tiempo_completo', '+51 987 654 321', 'Av. Javier Prado 1234, San Isidro, Lima', TRUE),
(4, 'EMP002', 3, 'Ejecutivo de Ventas', '2023-08-20', 4200.00, 'tiempo_completo', '+51 987 654 322', 'Jr. Las Begonias 567, Miraflores, Lima', TRUE),
(5, 'EMP003', 4, 'Contador Senior', '2022-03-10', 4800.00, 'tiempo_completo', '+51 987 654 323', 'Av. Arequipa 890, Lince, Lima', TRUE),
(6, 'EMP004', 1, 'Especialista en Reclutamiento', '2024-02-05', 4500.00, 'tiempo_completo', '+51 987 654 324', 'Calle Las Flores 234, Surco, Lima', TRUE),
(7, 'EMP005', 2, 'Analista de Sistemas', '2023-11-12', 5000.00, 'tiempo_completo', '+51 987 654 325', 'Av. Brasil 1456, Magdalena, Lima', TRUE),
(8, 'EMP006', 3, 'Gerente de Marketing Digital', '2022-06-18', 6500.00, 'tiempo_completo', '+51 987 654 326', 'Av. Larco 789, Miraflores, Lima', TRUE),
(9, 'EMP007', 5, 'Coordinador de Operaciones', '2023-04-22', 5200.00, 'tiempo_completo', '+51 987 654 327', 'Jr. Los Olivos 321, San Borja, Lima', TRUE),
(10, 'EMP008', 2, 'Desarrollador Backend', '2024-01-08', 5800.00, 'tiempo_completo', '+51 987 654 328', 'Av. Salaverry 1122, Jesús María, Lima', TRUE),
(11, 'EMP009', 6, 'Especialista en Atención al Cliente', '2023-09-30', 3800.00, 'tiempo_completo', '+51 987 654 329', 'Calle Los Eucaliptos 456, La Molina, Lima', TRUE),
(12, 'EMP010', 4, 'Asistente Contable', '2024-03-14', 3500.00, 'tiempo_completo', '+51 987 654 330', 'Av. Universitaria 2233, San Miguel, Lima', TRUE),
(13, 'EMP011', 1, 'Analista de Recursos Humanos', '2023-07-25', 4300.00, 'tiempo_completo', '+51 987 654 331', 'Jr. Las Orquídeas 678, Surco, Lima', TRUE),
(14, 'EMP012', 3, 'Ejecutivo Comercial', '2024-02-20', 4000.00, 'tiempo_completo', '+51 987 654 332', 'Av. Angamos 901, Surquillo, Lima', TRUE),
(15, 'EMP013', 2, 'Desarrollador Frontend', '2023-12-01', 5400.00, 'tiempo_completo', '+51 987 654 333', 'Calle Las Gardenias 345, Barranco, Lima', TRUE);

-- Saldos de vacaciones para todos los empleados
INSERT INTO saldos_vacaciones (employee_id, annual_leave, sick_leave, personal_leave, year) VALUES
(1, 20, 10, 3, YEAR(CURDATE())),
(2, 22, 10, 3, YEAR(CURDATE())),
(3, 25, 12, 3, YEAR(CURDATE())),
(4, 20, 10, 3, YEAR(CURDATE())),
(5, 20, 10, 3, YEAR(CURDATE())),
(6, 30, 15, 5, YEAR(CURDATE())),
(7, 20, 10, 3, YEAR(CURDATE())),
(8, 20, 10, 3, YEAR(CURDATE())),
(9, 20, 10, 3, YEAR(CURDATE())),
(10, 20, 10, 3, YEAR(CURDATE())),
(11, 20, 10, 3, YEAR(CURDATE())),
(12, 20, 10, 3, YEAR(CURDATE())),
(13, 20, 10, 3, YEAR(CURDATE()));

-- Asistencia de ejemplo (últimos 15 días para varios empleados)
INSERT INTO asistencia (employee_id, date, check_in_time, check_out_time, status, notes) VALUES
-- Empleado 1 (EMP001)
(1, CURDATE(), '08:00:00', '17:00:00', 'presente', NULL),
(1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '08:00:00', '17:00:00', 'presente', NULL),
(1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '08:15:00', '17:30:00', 'retrasado', 'Llegó 15 minutos tarde por tráfico'),
(1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), NULL, NULL, 'remoto', 'Trabajo remoto'),
(1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:00:00', '17:00:00', 'presente', NULL),
(1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:00:00', '16:45:00', 'salida_temprana', 'Salida temprana por cita médica'),
-- Empleado 2 (EMP002)
(2, CURDATE(), '08:30:00', '18:00:00', 'presente', NULL),
(2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '08:00:00', '17:00:00', 'presente', NULL),
(2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), NULL, NULL, 'ausente', 'Licencia médica'),
(2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '08:00:00', '17:00:00', 'presente', NULL),
-- Empleado 3 (EMP003)
(3, CURDATE(), '08:00:00', '17:00:00', 'presente', NULL),
(3, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '08:00:00', '17:00:00', 'presente', NULL),
(3, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '08:00:00', '17:00:00', 'presente', NULL),
(3, DATE_SUB(CURDATE(), INTERVAL 3 DAY), NULL, NULL, 'remoto', 'Trabajo remoto'),
-- Empleado 4 (EMP004)
(4, CURDATE(), '08:00:00', '17:00:00', 'presente', NULL),
(4, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '08:00:00', '17:00:00', 'presente', NULL),
-- Empleado 5 (EMP005)
(5, CURDATE(), '08:00:00', '17:00:00', 'presente', NULL),
(5, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '08:00:00', '17:00:00', 'presente', NULL),
(5, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '08:20:00', '17:00:00', 'retrasado', 'Problemas de transporte');

-- Solicitudes de vacaciones
INSERT INTO solicitudes_vacaciones (employee_id, leave_type, start_date, end_date, reason, status, approved_by) VALUES
(1, 'anual', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Vacaciones familiares en Cusco', 'pendiente', NULL),
(2, 'enfermedad', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'Gripe y fiebre', 'aprobado', 2),
(3, 'personal', DATE_ADD(CURDATE(), INTERVAL 30 DAY), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'Asuntos personales', 'pendiente', NULL),
(4, 'anual', DATE_ADD(CURDATE(), INTERVAL 45 DAY), DATE_ADD(CURDATE(), INTERVAL 52 DAY), 'Vacaciones de verano', 'aprobado', 2),
(6, 'anual', DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'Vacaciones ya tomadas', 'aprobado', 2);

-- Planillas de ejemplo (últimos 3 meses)
INSERT INTO planillas (employee_id, payment_period_start, payment_period_end, base_salary, allowances, deductions, tax, net_salary, payment_method, status, payment_date) VALUES
-- Mes actual
(1, DATE_FORMAT(CURDATE(), '%Y-%m-01'), LAST_DAY(CURDATE()), 5500.00, 500.00, 200.00, 870.00, 4930.00, 'transferencia_bancaria', 'aprobado', NULL),
(2, DATE_FORMAT(CURDATE(), '%Y-%m-01'), LAST_DAY(CURDATE()), 4200.00, 300.00, 150.00, 652.50, 3697.50, 'transferencia_bancaria', 'aprobado', NULL),
(3, DATE_FORMAT(CURDATE(), '%Y-%m-01'), LAST_DAY(CURDATE()), 4800.00, 400.00, 180.00, 751.00, 4269.00, 'transferencia_bancaria', 'pagado', CURDATE()),
(4, DATE_FORMAT(CURDATE(), '%Y-%m-01'), LAST_DAY(CURDATE()), 4500.00, 350.00, 170.00, 702.00, 3978.00, 'transferencia_bancaria', 'aprobado', NULL),
(5, DATE_FORMAT(CURDATE(), '%Y-%m-01'), LAST_DAY(CURDATE()), 5000.00, 450.00, 190.00, 782.50, 4477.50, 'transferencia_bancaria', 'aprobado', NULL),
-- Mes anterior
(1, DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01'), LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)), 5500.00, 500.00, 200.00, 870.00, 4930.00, 'transferencia_bancaria', 'pagado', DATE_SUB(CURDATE(), INTERVAL 5 DAY)),
(2, DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01'), LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)), 4200.00, 300.00, 150.00, 652.50, 3697.50, 'transferencia_bancaria', 'pagado', DATE_SUB(CURDATE(), INTERVAL 5 DAY)),
(3, DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01'), LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)), 4800.00, 400.00, 180.00, 751.00, 4269.00, 'transferencia_bancaria', 'pagado', DATE_SUB(CURDATE(), INTERVAL 5 DAY)),
(6, DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01'), LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)), 6500.00, 600.00, 250.00, 1037.50, 5812.50, 'transferencia_bancaria', 'pagado', DATE_SUB(CURDATE(), INTERVAL 5 DAY)),
(7, DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01'), LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)), 5200.00, 450.00, 200.00, 813.75, 4636.25, 'transferencia_bancaria', 'pagado', DATE_SUB(CURDATE(), INTERVAL 5 DAY));

-- Evaluaciones de desempeño (últimos 6 meses)
INSERT INTO evaluaciones_desempeno (employee_id, evaluator_id, evaluation_period_start, evaluation_period_end, rating, comments, strengths, areas_for_improvement, goals, status) VALUES
(1, 1, DATE_SUB(CURDATE(), INTERVAL 6 MONTH), DATE_SUB(CURDATE(), INTERVAL 3 MONTH), 4, 'Excelente desempeño en desarrollo de nuevas funcionalidades. Muy proactivo y colaborativo.', 'Técnica sólida, proactividad, trabajo en equipo', 'Mejorar documentación de código', 'Completar certificación en tecnologías cloud', 'completado'),
(2, 2, DATE_SUB(CURDATE(), INTERVAL 6 MONTH), DATE_SUB(CURDATE(), INTERVAL 3 MONTH), 5, 'Superó las metas de ventas del trimestre. Excelente relación con clientes.', 'Comunicación, persistencia, resultados', 'Gestión de tiempo en reportes', 'Aumentar cartera de clientes en 25%', 'completado'),
(3, 1, DATE_SUB(CURDATE(), INTERVAL 6 MONTH), DATE_SUB(CURDATE(), INTERVAL 3 MONTH), 4, 'Muy preciso en el trabajo contable. Cumple con todos los plazos establecidos.', 'Precisión, organización, puntualidad', 'Comunicación interdepartamental', 'Automatizar procesos contables repetitivos', 'completado'),
(4, 1, DATE_SUB(CURDATE(), INTERVAL 3 MONTH), DATE_SUB(CURDATE(), INTERVAL 1 DAY), 4, 'Buen trabajo en reclutamiento. Ha mejorado los tiempos de contratación.', 'Empatía, organización, conocimiento del mercado laboral', 'Uso de herramientas de reclutamiento digital', 'Reducir tiempo de contratación en 20%', 'completado'),
(5, 2, DATE_SUB(CURDATE(), INTERVAL 3 MONTH), DATE_SUB(CURDATE(), INTERVAL 1 DAY), 5, 'Desarrollador excepcional. Ha liderado proyectos importantes exitosamente.', 'Liderazgo técnico, resolución de problemas, mentoría', 'Ninguna', 'Continuar mentoría de desarrolladores junior', 'completado'),
(6, 2, DATE_SUB(CURDATE(), INTERVAL 3 MONTH), DATE_SUB(CURDATE(), INTERVAL 1 DAY), 5, 'Excelente gestión de estrategias de marketing. Campañas muy exitosas.', 'Creatividad, análisis de datos, visión estratégica', 'Ninguna', 'Expandir presencia en redes sociales', 'completado');
