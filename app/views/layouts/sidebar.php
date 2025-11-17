<aside class="sidebar" id="sidebar">
    <div class="d-flex flex-column h-100">
        <!-- Header -->
        <div class="sidebar-header">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 0;">
                    <div class="sidebar-logo">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <div id="sidebar-title" class="sidebar-title-content">
                        <h6 class="mb-0 fw-semibold">RH System</h6>
                        <small>Recursos Humanos</small>
                    </div>
                </div>
                <button class="btn btn-link p-0 d-none d-lg-block sidebar-toggle-btn flex-shrink-0" 
                        onclick="toggleSidebar()" 
                        title="Colapsar menú">
                    <i class="bi bi-chevron-left" id="sidebar-toggle-icon"></i>
                </button>
            </div>
        </div>

        <!-- User Info -->
        <div class="sidebar-user">
            <div class="d-flex align-items-center gap-2">
                <div class="sidebar-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="flex-grow-1" id="sidebar-user-info">
                    <div class="sidebar-user-name">
                        <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?>
                    </div>
                    <small class="sidebar-user-role">
                        <?php
                        $roleLabels = [
                            'administrador' => 'Administrador',
                            'gerente' => 'Gerente',
                            'empleado' => 'Empleado'
                        ];
                        echo htmlspecialchars($roleLabels[$_SESSION['user_role'] ?? 'empleado'] ?? 'Empleado');
                        ?>
                    </small>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link sidebar-link" href="<?= BASE_URL ?>/dashboard">
                        <i class="bi bi-house-door"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-link" href="<?= BASE_URL ?>/employees">
                        <i class="bi bi-people"></i>
                        <span class="sidebar-text">Empleados</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-link" href="<?= BASE_URL ?>/departments">
                        <i class="bi bi-building"></i>
                        <span class="sidebar-text">Departamentos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-link" href="<?= BASE_URL ?>/attendance">
                        <i class="bi bi-calendar-check"></i>
                        <span class="sidebar-text">Asistencia</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-link" href="<?= BASE_URL ?>/payroll">
                        <i class="bi bi-cash-coin"></i>
                        <span class="sidebar-text">Planillas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-link" href="<?= BASE_URL ?>/leave-requests">
                        <i class="bi bi-calendar-event"></i>
                        <span class="sidebar-text">Vacaciones</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-link" href="<?= BASE_URL ?>/evaluations">
                        <i class="bi bi-star"></i>
                        <span class="sidebar-text">Evaluaciones</span>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'administrador'): ?>
                <li class="nav-item sidebar-divider">
                    <a class="nav-link sidebar-link" href="<?= BASE_URL ?>/users">
                        <i class="bi bi-person-gear"></i>
                        <span class="sidebar-text">Usuarios</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Logout -->
        <div class="sidebar-footer">
            <form method="POST" action="<?= BASE_URL ?>/auth/logout">
                <button type="submit" class="btn btn-link w-100 sidebar-logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="sidebar-text">Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const sidebarTexts = document.querySelectorAll('.sidebar-text');
    const sidebarTitle = document.getElementById('sidebar-title');
    const sidebarUserInfo = document.getElementById('sidebar-user-info');
    const toggleIcon = document.getElementById('sidebar-toggle-icon');
    
    if (window.innerWidth > 991.98) {
        sidebar.classList.toggle('collapsed');
        if (mainContent) {
            mainContent.classList.toggle('expanded');
        }
        
        sidebarTexts.forEach(text => {
            text.style.display = sidebar.classList.contains('collapsed') ? 'none' : 'inline';
        });
        
        if (sidebarTitle) {
            sidebarTitle.style.display = sidebar.classList.contains('collapsed') ? 'none' : 'block';
        }
        
        if (sidebarUserInfo) {
            sidebarUserInfo.style.display = sidebar.classList.contains('collapsed') ? 'none' : 'block';
        }
        
        if (toggleIcon) {
            toggleIcon.className = sidebar.classList.contains('collapsed') ? 'bi bi-chevron-right' : 'bi bi-chevron-left';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    
    // Función para normalizar rutas
    function normalizeRoute(url) {
        let path = url;
        
        // Si es URL completa, extraer solo el pathname
        if (path.includes('://')) {
            try {
                const urlObj = new URL(path);
                path = urlObj.pathname;
            } catch (e) {
                // Si falla, extraer manualmente
                const match = path.match(/\/[^?#]*/);
                path = match ? match[0] : '/';
            }
        }
        
        // Remover cualquier parte antes de '/public' incluyendo '/public'
        // Ejemplos: '/RRHH/public/dashboard' -> '/dashboard', '/public/dashboard' -> '/dashboard'
        const publicIndex = path.indexOf('/public');
        if (publicIndex !== -1) {
            path = path.substring(publicIndex + 7); // 7 = length of '/public'
        }
        
        // Normalizar: remover trailing slash y asegurar que empiece con /
        path = path.replace(/\/+$/, '') || '/';
        if (!path.startsWith('/')) {
            path = '/' + path;
        }
        
        return path.toLowerCase();
    }
    
    // Obtener la ruta actual
    const currentPath = normalizeRoute(window.location.href);
    
    // Mapeo de rutas base a sus variantes
    const routeMap = {
        '/dashboard': ['/dashboard'],
        '/employees': ['/employees'],
        '/departments': ['/departments'],
        '/attendance': ['/attendance'],
        '/payroll': ['/payroll'],
        '/leave-requests': ['/leave-requests', '/leave'],
        '/evaluations': ['/evaluations'],
        '/users': ['/users']
    };
    
    sidebarLinks.forEach(link => {
        const href = link.getAttribute('href');
        const linkPath = normalizeRoute(href);
        
        let isActive = false;
        
        // Comparación exacta
        if (currentPath === linkPath) {
            isActive = true;
        } else {
            // Para rutas que no sean dashboard, verificar si la ruta actual empieza con la ruta del link
            if (linkPath !== '/dashboard' && currentPath.startsWith(linkPath + '/')) {
                isActive = true;
            }
            // También verificar variantes de rutas
            else {
                for (const [baseRoute, variants] of Object.entries(routeMap)) {
                    if (variants.includes(linkPath) && currentPath.startsWith(baseRoute)) {
                        isActive = true;
                        break;
                    }
                }
            }
        }
        
        if (isActive) {
            link.classList.add('active');
        }
        
        // Cerrar sidebar en móvil al hacer click
        link.addEventListener('click', function() {
            if (window.innerWidth <= 991.98) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('mobileOverlay');
                if (sidebar && overlay) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            }
        });
    });
});
</script>
