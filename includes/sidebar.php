<style>
    /* Sidebar */
    .sidebar {
        width: 280px;
        background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 0;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        overflow-y: auto;
    }

    .sidebar-header {
        padding: 20px;
        background: rgba(0,0,0,0.2);
        border-bottom: 2px solid rgba(255,255,255,0.1);
    }

    .sidebar-header h3 {
        font-size: 1.1em;
        margin-bottom: 5px;
    }

    .sidebar-header p {
        font-size: 0.85em;
        color: #bdc3c7;
    }

    .menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .menu-item {
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .menu-item a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px 20px;
        color: #ecf0f1;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
    }

    .menu-item a:hover {
        background: rgba(52, 152, 219, 0.2);
        padding-left: 25px;
    }

    .menu-item a.active {
        background: linear-gradient(90deg, #3498db, #2980b9);
        border-left: 4px solid #fff;
        font-weight: bold;
    }

    .menu-item a.active::before {
        content: '';
        position: absolute;
        right: 15px;
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
    }

    .menu-icon {
        font-size: 1.2em;
        width: 25px;
        text-align: center;
    }

    .menu-text {
        flex: 1;
    }

    /* Sección especial para nuevas opciones */
    .menu-section {
        padding: 15px 20px 10px;
        font-size: 0.75em;
        text-transform: uppercase;
        color: #95a5a6;
        font-weight: bold;
        letter-spacing: 1px;
        margin-top: 10px;
    }

    .menu-badge {
        background: #e74c3c;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75em;
        font-weight: bold;
    }

    /* Responsive sidebar */
    .sidebar-toggle {
        display: none;
        position: fixed;
        bottom: 20px;
        left: 20px;
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        z-index: 1001;
        font-size: 1.2em;
    }

    @media (max-width: 768px) {
        .sidebar {
            position: fixed;
            left: -280px;
            top: 0;
            height: 100vh;
            z-index: 1000;
            transition: left 0.3s ease;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar-toggle {
            display: block;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }
    }
</style>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-bars"></i> Menú Principal</h3>
        <p>Navegación del Sistema</p>
    </div>
    <nav>
        <ul class="menu">
            <li class="menu-item">
                <a href="?seccion=inicio" class="<?php echo (!isset($_GET['seccion']) || $_GET['seccion'] == 'inicio') ? 'active' : ''; ?>">
                    <i class="fas fa-home menu-icon"></i>
                    <span class="menu-text">Inicio</span>
                </a>
            </li>

            <li class="menu-section">Operaciones</li>

            <li class="menu-item">
                <a href="?seccion=cotizador" class="<?php echo (isset($_GET['seccion']) && $_GET['seccion'] == 'cotizador') ? 'active' : ''; ?>">
                    <i class="fas fa-calculator menu-icon"></i>
                    <span class="menu-text">Cotizador</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="?seccion=consulta" class="<?php echo (isset($_GET['seccion']) && $_GET['seccion'] == 'consulta') ? 'active' : ''; ?>">
                    <i class="fas fa-users menu-icon"></i>
                    <span class="menu-text">Consulta Clientes</span>
                </a>
            </li>

            <li class="menu-section">Módulos</li>

            <li class="menu-item">
                <a href="?seccion=refacciones" class="<?php echo (isset($_GET['seccion']) && $_GET['seccion'] == 'refacciones') ? 'active' : ''; ?>">
                    <i class="fas fa-cogs menu-icon"></i>
                    <span class="menu-text">Refacciones</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="?seccion=inventarios" class="<?php echo (isset($_GET['seccion']) && $_GET['seccion'] == 'inventarios') ? 'active' : ''; ?>">
                    <i class="fas fa-boxes menu-icon"></i>
                    <span class="menu-text">Inventarios</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="?seccion=servicio" class="<?php echo (isset($_GET['seccion']) && $_GET['seccion'] == 'servicio') ? 'active' : ''; ?>">
                    <i class="fas fa-tools menu-icon"></i>
                    <span class="menu-text">Servicio</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="?seccion=indi" class="<?php echo (isset($_GET['seccion']) && $_GET['seccion'] == 'indi') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line menu-icon"></i>
                    <span class="menu-text">Indicadores</span>
                </a>
            </li>

            <li class="menu-section">Administración</li>

            <li class="menu-item">
                <a href="?seccion=admin" class="<?php echo (isset($_GET['seccion']) && $_GET['seccion'] == 'admin') ? 'active' : ''; ?>">
                    <i class="fas fa-user-shield menu-icon"></i>
                    <span class="menu-text">Administración</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="?seccion=RH" class="<?php echo (isset($_GET['seccion']) && $_GET['seccion'] == 'RH') ? 'active' : ''; ?>">
                    <i class="fas fa-user-tie menu-icon"></i>
                    <span class="menu-text">Recursos Humanos</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<script>
    // Toggle sidebar en móviles
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', toggleSidebar);
    }
</script>
