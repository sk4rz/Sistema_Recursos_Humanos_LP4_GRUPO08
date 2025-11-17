<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $title ?? 'Sistema de Recursos Humanos' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        /* ============================================
           DESIGN SYSTEM - Sistema de Recursos Humanos
           ============================================ */
        
        :root {
            /* Colores Base - Tema Oscuro Profesional */
            --color-bg-primary: #0a0e13;
            --color-bg-secondary: #141b23;
            --color-bg-tertiary: #1a2332;
            --color-bg-elevated: #1f2937;
            
            /* Colores de Superficie */
            --color-surface: #1e2832;
            --color-surface-hover: #252e3a;
            --color-surface-active: #2a3441;
            
            /* Colores de Borde */
            --color-border: #2d3748;
            --color-border-light: #374151;
            --color-border-dark: #1f2937;
            
            /* Colores de Texto */
            --color-text-primary: #f8fafc;
            --color-text-secondary: #cbd5e1;
            --color-text-tertiary: #94a3b8;
            --color-text-disabled: #64748b;
            
            /* Colores Semánticos - Estándar */
            --color-primary: #3b82f6;
            --color-primary-hover: #2563eb;
            --color-primary-light: rgba(59, 130, 246, 0.1);
            
            --color-success: #10b981;
            --color-success-hover: #059669;
            --color-success-light: rgba(16, 185, 129, 0.1);
            
            --color-warning: #f59e0b;
            --color-warning-hover: #d97706;
            --color-warning-light: rgba(245, 158, 11, 0.1);
            
            --color-danger: #ef4444;
            --color-danger-hover: #dc2626;
            --color-danger-light: rgba(239, 68, 68, 0.1);
            
            --color-info: #06b6d4;
            --color-info-hover: #0891b2;
            --color-info-light: rgba(6, 182, 212, 0.1);
            
            /* Sombras */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
            
            /* Espaciado */
            --spacing-xs: 0.375rem;
            --spacing-sm: 0.5rem;
            --spacing-md: 0.875rem;
            --spacing-lg: 1.125rem;
            --spacing-xl: 1.375rem;
            --spacing-2xl: 1.75rem;
            
            /* Tipografía */
            --font-family-base: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            --font-size-xs: 0.8125rem;
            --font-size-sm: 0.875rem;
            --font-size-base: 0.96875rem;
            --font-size-lg: 1.0625rem;
            --font-size-xl: 1.1875rem;
            --font-size-2xl: 1.375rem;
            --font-size-3xl: 1.625rem;
            
            /* Bordes */
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            
            /* Transiciones */
            --transition-fast: 150ms ease;
            --transition-base: 200ms ease;
            --transition-slow: 300ms ease;
        }

        /* ============================================
           RESET Y BASE
           ============================================ */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--color-bg-primary) !important;
            color: var(--color-text-primary) !important;
            font-family: var(--font-family-base);
            font-size: var(--font-size-base);
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Sobrescribir Bootstrap para usar nuestros colores */
        .bg-dark {
            background-color: var(--color-surface) !important;
        }

        .bg-secondary {
            background-color: var(--color-bg-secondary) !important;
        }

        .text-white {
            color: var(--color-text-primary) !important;
        }

        .text-muted {
            color: var(--color-text-tertiary) !important;
        }

        .border-secondary {
            border-color: var(--color-border) !important;
        }

        /* ============================================
           SIDEBAR
           ============================================ */
        
        .sidebar {
            background-color: var(--color-surface);
            border-right: 1px solid var(--color-border);
            height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: width var(--transition-base);
            overflow: hidden;
        }
        
        .sidebar > .d-flex {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed {
            width: 68px;
        }

        .sidebar-header {
            padding: var(--spacing-md) var(--spacing-lg);
            border-bottom: 1px solid var(--color-border);
            flex-shrink: 0;
            overflow: visible;
        }

        .sidebar-header .d-flex {
            align-items: center;
            overflow: visible;
        }

        .sidebar-logo {
            width: 38px;
            height: 38px;
            background-color: var(--color-primary);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-logo i {
            color: white;
            font-size: 1.0625rem;
        }

        .sidebar-title-content {
            flex: 1;
            min-width: 0;
        }

        .sidebar-title-content h6 {
            font-size: 1rem;
            color: var(--color-text-primary);
            margin: 0;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 600;
        }

        .sidebar-title-content small {
            font-size: 0.8125rem;
            color: var(--color-text-tertiary);
            display: block;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-toggle-btn {
            color: var(--color-text-tertiary) !important;
            padding: 0.375rem 0.5rem !important;
            min-width: 32px;
            min-height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-md);
            transition: all var(--transition-base);
        }

        .sidebar-toggle-btn:hover {
            color: var(--color-text-primary) !important;
            background-color: var(--color-primary-light);
        }

        .sidebar-toggle-btn i {
            font-size: 1.125rem;
            line-height: 1;
        }

        .sidebar-user {
            padding: var(--spacing-md) var(--spacing-lg);
            border-bottom: 1px solid var(--color-border);
            flex-shrink: 0;
        }

        .sidebar-user .d-flex {
            align-items: center;
        }

        .sidebar-avatar {
            width: 38px;
            height: 38px;
            background-color: var(--color-primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-avatar i {
            color: var(--color-primary);
            font-size: 0.9375rem;
        }

        .sidebar-user-name {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--color-text-primary);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-role {
            font-size: 0.8125rem;
            color: var(--color-text-tertiary);
            display: block;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-nav {
            flex: 1 1 auto;
            padding: var(--spacing-sm) var(--spacing-md);
            overflow-y: auto;
            overflow-x: hidden;
            min-height: 0;
        }

        .sidebar-nav .nav {
            gap: 0.25rem;
        }

        .sidebar-divider {
            margin-top: var(--spacing-sm);
            padding-top: var(--spacing-sm);
            border-top: 1px solid var(--color-border);
        }

        .sidebar-footer {
            padding: var(--spacing-md) var(--spacing-lg);
            border-top: 1px solid var(--color-border);
            flex-shrink: 0;
            flex-grow: 0;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: var(--spacing-sm) !important;
        }

        .sidebar.collapsed .nav-link i {
            margin: 0;
        }

        .main-content {
            margin-left: 250px;
            transition: margin-left var(--transition-base);
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: 68px;
        }

        /* ============================================
           CARDS
           ============================================ */
        
        .card-custom {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-base);
        }

        .card-custom:hover {
            box-shadow: var(--shadow-md);
        }

        .card-custom .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--color-border);
            padding: var(--spacing-md) var(--spacing-lg);
        }

        .card-custom .card-body {
            padding: var(--spacing-lg);
        }

        .card-custom.h-100 {
            display: flex;
            flex-direction: column;
        }

        .card-custom.h-100 .card-body {
            flex: 1;
        }

        /* ============================================
           STATS CARDS
           ============================================ */
        
        .stat-card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--spacing-lg);
            height: 100%;
            transition: all var(--transition-base);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--color-primary);
            opacity: 0;
            transition: opacity var(--transition-base);
        }

        .stat-card:hover {
            border-color: var(--color-primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card .stat-label {
            font-size: var(--font-size-xs);
            color: var(--color-text-tertiary);
            margin-bottom: var(--spacing-sm);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .stat-value {
            font-size: var(--font-size-2xl);
            font-weight: 700;
            color: var(--color-text-primary);
            margin-bottom: 0;
            line-height: 1.2;
        }

        .stat-card .stat-icon {
            font-size: 1.75rem;
            opacity: 0.6;
            position: absolute;
            top: var(--spacing-md);
            right: var(--spacing-md);
        }

        /* ============================================
           TABLES
           ============================================ */
        
        .table-dark {
            color: var(--color-text-primary);
            background-color: transparent;
        }

        .table-dark thead th {
            background-color: transparent;
            color: var(--color-text-tertiary);
            font-weight: 600;
            padding: var(--spacing-sm) var(--spacing-md);
            border-bottom: 1px solid var(--color-border);
            font-size: var(--font-size-xs);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-dark tbody td {
            padding: var(--spacing-sm) var(--spacing-md);
            border-color: var(--color-border);
            vertical-align: middle;
            color: var(--color-text-primary);
            font-size: var(--font-size-sm);
        }

        .table-dark tbody tr {
            transition: background-color var(--transition-fast);
        }

        .table-dark tbody tr:hover {
            background-color: var(--color-primary-light);
        }

        /* ============================================
           BUTTONS
           ============================================ */
        
        .btn {
            border-radius: var(--radius-md);
            font-weight: 500;
            padding: var(--spacing-sm) var(--spacing-lg);
            transition: all var(--transition-base);
            border: 1px solid transparent;
        }

        .btn-sm {
            padding: var(--spacing-xs) var(--spacing-md);
            font-size: var(--font-size-sm);
        }

        .btn-primary {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--color-primary-hover);
            border-color: var(--color-primary-hover);
            color: white;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline-secondary {
            border-color: var(--color-border);
            color: var(--color-text-secondary);
            background-color: transparent;
        }

        .btn-outline-secondary:hover {
            background-color: var(--color-surface-hover);
            border-color: var(--color-border-light);
            color: var(--color-text-primary);
        }

        .btn-link {
            text-decoration: none;
            color: var(--color-primary);
            border: none;
            background: transparent;
        }

        .btn-link:hover {
            text-decoration: underline;
            color: var(--color-primary-hover);
        }

        /* ============================================
           FORMS
           ============================================ */
        
        .form-control, .form-select {
            background-color: var(--color-bg-elevated) !important;
            border: 1px solid var(--color-border) !important;
            color: var(--color-text-primary) !important;
            border-radius: var(--radius-md);
            padding: var(--spacing-sm) var(--spacing-md);
            transition: all var(--transition-base);
        }

        .form-control::placeholder, .form-select::placeholder {
            color: var(--color-text-tertiary) !important;
            opacity: 0.7;
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--color-bg-elevated) !important;
            border-color: var(--color-primary) !important;
            color: var(--color-text-primary) !important;
            box-shadow: 0 0 0 3px var(--color-primary-light) !important;
            outline: none;
        }

        .input-group-text {
            background-color: var(--color-bg-elevated) !important;
            border-color: var(--color-border) !important;
            color: var(--color-text-secondary) !important;
        }

        .form-label {
            color: var(--color-text-secondary) !important;
            font-weight: 500;
            margin-bottom: var(--spacing-xs);
        }

        /* ============================================
           NAVIGATION
           ============================================ */
        
        .sidebar-link {
            border-radius: var(--radius-md);
            padding: var(--spacing-sm) var(--spacing-md);
            color: var(--color-text-secondary) !important;
            transition: all var(--transition-base);
            position: relative;
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            font-size: var(--font-size-sm);
            text-decoration: none;
            width: 100%;
            margin-bottom: 0.25rem;
        }

        .sidebar-link i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .sidebar-link .sidebar-text {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 500;
        }

        .sidebar-link:hover:not(.active) {
            color: var(--color-text-primary) !important;
            background-color: var(--color-primary-light);
        }

        .sidebar-link.active {
            color: var(--color-primary) !important;
            background-color: var(--color-primary-light) !important;
            font-weight: 600 !important;
            border-left: 3px solid var(--color-primary) !important;
            padding-left: calc(var(--spacing-md) - 3px) !important;
        }

        .sidebar-link.active:hover {
            color: var(--color-primary) !important;
            background-color: var(--color-primary-light) !important;
        }

        .sidebar-link.active i {
            color: var(--color-primary) !important;
        }

        .sidebar-link.active:hover i {
            color: var(--color-primary) !important;
        }

        .sidebar-link.active .sidebar-text {
            color: var(--color-primary) !important;
            font-weight: 600 !important;
        }

        .sidebar-link.active:hover .sidebar-text {
            color: var(--color-primary) !important;
        }

        .sidebar.collapsed .sidebar-link.active::before {
            display: none;
        }

        .sidebar-logout-btn {
            color: var(--color-danger) !important;
            padding: var(--spacing-sm) var(--spacing-md) !important;
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            font-size: var(--font-size-sm);
            text-decoration: none;
            border-radius: var(--radius-md);
            transition: all var(--transition-base);
            width: 100%;
            border: none;
            background: transparent;
            font-weight: 500;
        }

        .sidebar-logout-btn:hover {
            background-color: var(--color-danger-light) !important;
            color: var(--color-danger) !important;
        }

        .sidebar-logout-btn i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .sidebar-logout-btn .sidebar-text {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ============================================
           PAGE HEADER
           ============================================ */
        
        .page-header {
            margin-bottom: var(--spacing-lg);
        }

        .page-header h1 {
            font-size: var(--font-size-xl);
            font-weight: 600;
            margin-bottom: var(--spacing-xs);
            color: var(--color-text-primary);
        }

        .page-header p {
            font-size: var(--font-size-sm);
            color: var(--color-text-tertiary);
            margin-bottom: 0;
        }

        .page-actions {
            display: flex;
            gap: var(--spacing-md);
            align-items: center;
        }

        /* ============================================
           BADGES
           ============================================ */
        
        .badge {
            padding: var(--spacing-xs) var(--spacing-md);
            border-radius: var(--radius-sm);
            font-weight: 500;
            font-size: var(--font-size-xs);
        }

        .badge.bg-secondary {
            background-color: var(--color-bg-secondary) !important;
            color: var(--color-text-primary) !important;
        }

        .badge.bg-success {
            background-color: var(--color-success) !important;
            color: white !important;
        }

        .badge.bg-warning {
            background-color: var(--color-warning) !important;
            color: white !important;
        }

        .badge.bg-danger {
            background-color: var(--color-danger) !important;
            color: white !important;
        }

        .badge.bg-info {
            background-color: var(--color-info) !important;
            color: white !important;
        }

        .badge.bg-primary {
            background-color: var(--color-primary) !important;
            color: white !important;
        }

        /* ============================================
           ALERTS
           ============================================ */
        
        .alert {
            border: none;
            border-radius: var(--radius-md);
            border-left: 4px solid;
            padding: var(--spacing-lg);
        }

        .alert-danger {
            background-color: var(--color-danger-light);
            border-left-color: var(--color-danger);
            color: var(--color-danger);
        }

        .alert-success {
            background-color: var(--color-success-light);
            border-left-color: var(--color-success);
            color: var(--color-success);
        }

        .alert-info {
            background-color: var(--color-info-light);
            border-left-color: var(--color-info);
            color: var(--color-info);
        }

        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* ============================================
           LIST GROUP
           ============================================ */
        
        .list-group-item {
            background-color: transparent !important;
            border-color: var(--color-border) !important;
            color: var(--color-text-primary) !important;
        }

        .list-group-item:last-child {
            border-bottom: none !important;
        }

        .list-group-item:hover {
            background-color: var(--color-surface-hover) !important;
            color: var(--color-text-primary) !important;
        }

        /* ============================================
           UTILITIES
           ============================================ */
        
        .text-muted-custom {
            color: var(--color-text-tertiary) !important;
        }

        .text-primary-custom {
            color: var(--color-primary) !important;
        }

        .text-success-custom {
            color: var(--color-success) !important;
        }

        .text-danger-custom {
            color: var(--color-danger) !important;
        }

        .text-warning-custom {
            color: var(--color-warning) !important;
        }

        /* Asegurar que todos los textos sean legibles */
        h1, h2, h3, h4, h5, h6 {
            color: var(--color-text-primary) !important;
        }

        p, span, div, td, th, li {
            color: inherit;
        }

        small {
            color: var(--color-text-tertiary) !important;
        }

        /* Modal estandarizado */
        .modal-content {
            background-color: var(--color-surface) !important;
            border: 1px solid var(--color-border) !important;
            color: var(--color-text-primary) !important;
        }

        .modal-header {
            border-bottom-color: var(--color-border) !important;
        }

        .modal-header .modal-title {
            color: var(--color-text-primary) !important;
        }

        .modal-footer {
            border-top-color: var(--color-border) !important;
        }

        /* Asegurar contraste en todos los elementos */
        .table-dark {
            --bs-table-bg: transparent;
            --bs-table-color: var(--color-text-primary);
            --bs-table-border-color: var(--color-border);
        }

        .table-dark th,
        .table-dark td {
            color: var(--color-text-primary) !important;
        }

        .table-dark thead th {
            color: var(--color-text-tertiary) !important;
        }

        /* Clases de utilidad para textos */
        .text-primary-color {
            color: var(--color-primary) !important;
        }

        .text-secondary-color {
            color: var(--color-text-secondary) !important;
        }

        .text-tertiary-color {
            color: var(--color-text-tertiary) !important;
        }

        /* Asegurar que los enlaces sean visibles */
        a {
            color: var(--color-primary);
        }

        a:hover {
            color: var(--color-primary-hover);
        }

        .btn-link {
            color: var(--color-primary) !important;
        }

        .btn-link:hover {
            color: var(--color-primary-hover) !important;
        }

        /* Card headers estandarizados */
        .card-header h6 {
            color: var(--color-text-primary) !important;
        }

        /* Asegurar que todos los elementos de tabla usen colores estandarizados */
        .table td, .table th {
            color: inherit;
        }

        /* Inputs y selects siempre legibles */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        textarea,
        select {
            background-color: var(--color-bg-elevated) !important;
            border-color: var(--color-border) !important;
            color: var(--color-text-primary) !important;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        textarea:focus,
        select:focus {
            background-color: var(--color-bg-elevated) !important;
            border-color: var(--color-primary) !important;
            color: var(--color-text-primary) !important;
        }

        /* ============================================
           MOBILE
           ============================================ */
        
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: var(--spacing-lg);
            left: var(--spacing-lg);
            z-index: 1050;
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            color: var(--color-text-primary);
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
        }

        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1040;
        }

        .mobile-overlay.show {
            display: block;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1050;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0 !important;
            }
            .mobile-menu-btn {
                display: block;
            }
        }

        @media (max-width: 576px) {
        .container-fluid {
            padding: var(--spacing-md) var(--spacing-lg) !important;
        }
            .page-header h1 {
                font-size: var(--font-size-2xl);
            }
        }

        /* ============================================
           SCROLLBAR
           ============================================ */
        
        .card-body::-webkit-scrollbar {
            width: 6px;
        }

        .card-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .card-body::-webkit-scrollbar-thumb {
            background: var(--color-border);
            border-radius: 3px;
        }

        .card-body::-webkit-scrollbar-thumb:hover {
            background: var(--color-text-tertiary);
        }
    </style>
</head>
<body>
    <?php if (isset($showSidebar) && $showSidebar): ?>
        <button class="mobile-menu-btn" id="mobileMenuBtn" onclick="toggleMobileSidebar()">
            <i class="bi bi-list"></i>
        </button>
        <div class="mobile-overlay" id="mobileOverlay" onclick="toggleMobileSidebar()"></div>
        <?php include VIEWS_PATH . '/layouts/sidebar.php'; ?>
    <?php endif; ?>
    
    <main class="<?= isset($showSidebar) && $showSidebar ? 'main-content' : '' ?>">
        <?php
        // Mostrar mensajes flash
        if (isset($_SESSION['flash_error'])) {
            echo '<div class="container-fluid px-4 pt-4"><div class="alert alert-danger alert-dismissible fade show" role="alert">';
            echo '<i class="bi bi-exclamation-circle me-2"></i>';
            echo htmlspecialchars($_SESSION['flash_error']);
            echo '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div></div>';
            unset($_SESSION['flash_error']);
        }
        if (isset($_SESSION['flash_success'])) {
            echo '<div class="container-fluid px-4 pt-4"><div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo '<i class="bi bi-check-circle me-2"></i>';
            echo htmlspecialchars($_SESSION['flash_success']);
            echo '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div></div>';
            unset($_SESSION['flash_success']);
        }
        if (isset($_SESSION['flash_info'])) {
            echo '<div class="container-fluid px-4 pt-4"><div class="alert alert-info alert-dismissible fade show" role="alert">';
            echo '<i class="bi bi-info-circle me-2"></i>';
            echo htmlspecialchars($_SESSION['flash_info']);
            echo '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div></div>';
            unset($_SESSION['flash_info']);
        }
        ?>
        <?= $content ?? '' ?>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= ASSETS_URL ?>/js/main.js"></script>
    <script>
        // Toggle mobile sidebar
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (sidebar && overlay) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileBtn = document.getElementById('mobileMenuBtn');
            const overlay = document.getElementById('mobileOverlay');
            
            if (window.innerWidth <= 991.98 && sidebar && overlay) {
                if (!sidebar.contains(event.target) && 
                    event.target !== mobileBtn && 
                    !mobileBtn.contains(event.target) &&
                    sidebar.classList.contains('show')) {
                    toggleMobileSidebar();
                }
            }
        });
    </script>
</body>
</html>
