<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Tienda Deportiva' ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>?v=<?= time() ?>">
</head>
<body>
    <?php include VIEWS_PATH . '/layouts/navbar.php'; ?>
    
    <main class="container">
        <?php if (flash('success')): ?>
            <div class="alert alert-success">
                <?= e(flash('success')) ?>
            </div>
        <?php endif; ?>
        
        <?php if (flash('error')): ?>
            <div class="alert alert-error">
                <?= e(flash('error')) ?>
            </div>
        <?php endif; ?>
        
        <?php if (flash('errors')): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach (flash('errors') as $field => $errors): ?>
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
