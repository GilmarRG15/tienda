<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<!-- ===== HERO ===== -->
<section class="hero">
  <div class="hero-bg show"></div>
  <div class="hero-bg hide"></div>

  <div class="hero-content">
    <h1><?= APP_NAME ?></h1>
    <h2>Los mejores artículos deportivos al mejor precio</h2>
    <h2>
      <a href="<?= url('/catalogo') ?>" class="btn-large">Ver Catálogo</a>
    </h2>
  </div>
</section>


<!-- ===== CATEGORÍAS ===== -->
<section class="categorias-section">
    <h2>Categorías</h2>
    <div class="categorias-grid">
        <?php foreach ($categorias as $cat): ?>
            <a href="<?= url('/catalogo?categoria=' . $cat['id']) ?>" class="categoria-card">
                <h3><?= e($cat['nombre']) ?></h3>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- ===== PRODUCTOS DESTACADOS ===== -->
<section class="productos-destacados">
    <h2>Productos Destacados</h2>
    <div class="productos-grid">
        <?php foreach ($productos as $prod): ?>
            <div class="producto-card">
                <?php if ($prod['imagen_url']): ?>
                    <img src="<?= url($prod['imagen_url']) ?>" alt="<?= e($prod['nombre']) ?>">
                <?php else: ?>
                    <div class="no-image">Sin imagen</div>
                <?php endif; ?>

                <h3><?= e($prod['nombre']) ?></h3>
                <p class="marca"><?= e($prod['marca']) ?></p>
                <p class="categoria"><?= e($prod['categoria_nombre']) ?></p>

                <a href="<?= url('/producto/' . $prod['id']) ?>" class="btn">
                    Ver Detalles
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ===== SCRIPT HERO SLIDER (FADE SUAVE) ===== -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const bgs = document.querySelectorAll('.hero-bg');

  const images = [
    "<?= url('/assets/img/productos/banner.jpg') ?>",
    "<?= url('/assets/img/productos/banner2.jpg') ?>"
  ];

  let index = 0;
  let active = 0;

  // imagen inicial
  bgs[0].style.backgroundImage = `url('${images[0]}')`;

  setInterval(() => {
    const next = 1 - active;
    index = (index + 1) % images.length;

    bgs[next].style.backgroundImage = `url('${images[index]}')`;

    bgs[active].classList.replace('show', 'hide');
    bgs[next].classList.replace('hide', 'show');

    active = next;
  }, 6000);
});
</script>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
