<?php
$title = 'Iniciar Sesión - Sistema RH';
ob_start();
?>

<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background-color: var(--color-bg-primary);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card card-custom shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary rounded p-3 d-inline-block mb-3">
                                <span class="text-white fw-bold fs-4">RH</span>
                            </div>
                            <h2 class="fw-bold mb-2">Sistema RH</h2>
                            <p class="text-muted-custom">Gestión de Recursos Humanos</p>
                        </div>

                        <?php if (isset($error) && $error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= BASE_URL ?>/auth/login" id="loginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       placeholder="tu@correo.com" autocomplete="email">
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required 
                                       placeholder="••••••••" autocomplete="current-password">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <span id="loginBtnText">Iniciar Sesión</span>
                                <span id="loginBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </form>

                        <p class="text-center text-muted-custom mt-4 small">
                            Demo: admin@example.com / password123
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = this.querySelector('button[type="submit"]');
    const btnText = document.getElementById('loginBtnText');
    const btnSpinner = document.getElementById('loginBtnSpinner');
    
    btn.disabled = true;
    btnText.textContent = 'Iniciando sesión...';
    btnSpinner.classList.remove('d-none');
});
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?>

