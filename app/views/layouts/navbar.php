<header class="header">
    <nav class="navbar">
        <div class="container navbar-container">
            <a href="<?= url('/') ?>" class="logo"><?= APP_NAME ?></a>
            
            <ul class="nav-menu">
                <li><a href="<?= url('/') ?>">Inicio</a></li>
                <li><a href="<?= url('/catalogo') ?>">Catálogo</a></li>
                
                <?php if (Auth::check()): ?>
                    <li><a href="<?= url('/carrito') ?>">
                        Carrito 
                        <?php if (!empty($_SESSION['carrito'])): ?>
                            <span class="badge"><?= count($_SESSION['carrito']) ?></span>
                        <?php endif; ?>
                    </a></li>
                    <li><a href="<?= url('/pedidos') ?>">Mis Pedidos</a></li>
                    
                    <?php if (Auth::hasRole(['ADMIN', 'GESTOR_PRODUCTOS', 'GESTOR_INVENTARIO', 'DESPACHADOR'])): ?>
                        <li><a href="<?= url('/admin') ?>">Panel Admin</a></li>
                    <?php endif; ?>
                    
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">
                            <?= e(Auth::user()['nombre']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= url('/perfil') ?>">Mi Perfil</a></li>
                            <li><a href="<?= url('/logout') ?>">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="<?= url('/login') ?>">Iniciar Sesión</a></li>
                    <li><a href="<?= url('/register') ?>" class="btn-primary">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>
