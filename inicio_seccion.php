<div class="inicio-content">
    <div class="welcome-banner">
        <h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h2>
        <p>Sistema de consulta de precios y gestión de inventario</p>
    </div>
    
    <h3>Seleccione una opción para comenzar:</h3>
    
    <div class="menu-cards">
        <div class="menu-card">
            <i class="fas fa-cogs"></i>
            <h3>Refacciones</h3>
            <p>Consulta de precios y existencias de refacciones</p>
            <a href="?seccion=refacciones">Acceder</a>
        </div>

        <div class="menu-card">
            <i class="fas fa-boxes"></i>
            <h3>Inventarios</h3>
            <p>Consulta de Inventarios Refacciones - Partes</p>
            <a href="?seccion=inventarios">Acceder</a>
        </div>

        <div class="menu-card">
            <i class="fas fa-tools"></i>
            <h3>Servicio</h3>
            <p>Gestión de servicios y mantenimiento</p>
            <a href="?seccion=servicio">Acceder</a>
        </div>
        
        <div class="menu-card">
            <i class="fas fa-chart-bar"></i>
            <h3>Reportes</h3>
            <p>Reportes Servicio Estadías Graficos</p>