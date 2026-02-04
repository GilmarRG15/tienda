    </main>
    
    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?></p>
            <p>Guayaquil - Ecuador</p>
            <p>Proyecto de base de datos Grupo 1</p>
        </div>
    </footer>

    <script>
    // Control del menú desplegable con click
    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.querySelector('.dropdown');
        const dropdownToggle = document.querySelector('.dropdown-toggle');
        
        if (dropdownToggle) {
            dropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                dropdown.classList.toggle('active');
            });
            
            // Cerrar el menú si se hace click fuera de él
            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });
        }
    });
    </script>
</body>
</html>
<?php clearOldInput(); ?>
