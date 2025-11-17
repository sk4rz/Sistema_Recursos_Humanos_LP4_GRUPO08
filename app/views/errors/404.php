<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f1419;
            color: #e4e6eb;
        }
    </style>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="text-center">
            <h1 class="display-1">404</h1>
            <h2>Página no encontrada</h2>
            <p class="text-muted">La página que buscas no existe.</p>
            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-primary">Volver al Dashboard</a>
        </div>
    </div>
</body>
</html>

