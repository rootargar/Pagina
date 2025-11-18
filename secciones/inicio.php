<style>
    .welcome-section {
        text-align: center;
        padding: 40px 20px;
    }

    .welcome-title {
        font-size: 2.5em;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
    }

    .welcome-logo {
        height: 80px;
        width: auto;
    }

    .welcome-subtitle {
        font-size: 1.2em;
        color: #7f8c8d;
        margin-bottom: 40px;
    }

    .quick-access {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .access-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        border: 2px solid transparent;
        cursor: pointer;
    }

    .access-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-color: #3498db;
    }

    .access-card-icon {
        font-size: 3em;
        margin-bottom: 15px;
        display: block;
    }

    .access-card-title {
        font-size: 1.4em;
        font-weight: bold;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    .access-card-description {
        font-size: 0.95em;
        color: #7f8c8d;
        line-height: 1.6;
    }

    .card-cotizador {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .card-cotizador .access-card-title,
    .card-cotizador .access-card-description {
        color: white;
    }

    .card-consulta {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .card-consulta .access-card-title,
    .card-consulta .access-card-description {
        color: white;
    }

    .card-refacciones {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .card-refacciones .access-card-title,
    .card-refacciones .access-card-description {
        color: white;
    }

    .card-inventarios {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
    }

    .card-inventarios .access-card-title,
    .card-inventarios .access-card-description {
        color: white;
    }

    .card-servicio {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }

    .card-servicio .access-card-title,
    .card-servicio .access-card-description {
        color: white;
    }

    .card-indicadores {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        color: white;
    }

    .card-indicadores .access-card-title,
    .card-indicadores .access-card-description {
        color: white;
    }

    .stats-section {
        margin-top: 50px;
        padding: 30px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 12px;
    }

    .stats-title {
        font-size: 1.5em;
        color: #2c3e50;
        margin-bottom: 20px;
        text-align: center;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .stat-item {
        background: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .stat-value {
        font-size: 2em;
        font-weight: bold;
        color: #3498db;
        display: block;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.9em;
        color: #7f8c8d;
    }

    @media (max-width: 768px) {
        .welcome-title {
            font-size: 1.8em;
            flex-direction: column;
        }

        .welcome-logo {
            height: 60px;
        }

        .quick-access {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="welcome-section">
    <h1 class="welcome-title">
        <img src="kwdaf.png" alt="Logo KW-DAF" class="welcome-logo">
        Bienvenido al Sistema KW-DAF
    </h1>
    <p class="welcome-subtitle">
        Sistema integral de gestión empresarial
    </p>

    <div class="quick-access">
        <a href="?seccion=cotizador" class="access-card card-cotizador">
            <i class="fas fa-calculator access-card-icon"></i>
            <div class="access-card-title">Cotizador</div>
            <div class="access-card-description">
                Gestión de precios y cotizaciones de refacciones con cálculo automático de márgenes
            </div>
        </a>

        <a href="?seccion=consulta" class="access-card card-consulta">
            <i class="fas fa-users access-card-icon"></i>
            <div class="access-card-title">Consulta Clientes</div>
            <div class="access-card-description">
                Búsqueda y consulta de información de clientes y firmas autorizadas
            </div>
        </a>

        <a href="?seccion=refacciones" class="access-card card-refacciones">
            <i class="fas fa-cogs access-card-icon"></i>
            <div class="access-card-title">Refacciones</div>
            <div class="access-card-description">
                Gestión completa del catálogo de refacciones y repuestos
            </div>
        </a>

        <a href="?seccion=inventarios" class="access-card card-inventarios">
            <i class="fas fa-boxes access-card-icon"></i>
            <div class="access-card-title">Inventarios</div>
            <div class="access-card-description">
                Control de existencias y movimientos de almacén
            </div>
        </a>

        <a href="?seccion=servicio" class="access-card card-servicio">
            <i class="fas fa-tools access-card-icon"></i>
            <div class="access-card-title">Servicio</div>
            <div class="access-card-description">
                Gestión de órdenes de servicio y mantenimiento
            </div>
        </a>

        <a href="?seccion=indi" class="access-card card-indicadores">
            <i class="fas fa-chart-line access-card-icon"></i>
            <div class="access-card-title">Indicadores</div>
            <div class="access-card-description">
                Reportes y análisis de indicadores de rendimiento
            </div>
        </a>
    </div>

    <div class="stats-section">
        <h2 class="stats-title">
            <i class="fas fa-chart-bar"></i> Información del Sistema
        </h2>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-value"><i class="fas fa-database"></i></span>
                <span class="stat-label">Conexión Activa</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?php echo date('d/m/Y'); ?></span>
                <span class="stat-label">Fecha Actual</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?php echo date('H:i'); ?></span>
                <span class="stat-label">Hora del Sistema</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><i class="fas fa-check-circle"></i></span>
                <span class="stat-label">Sistema Operativo</span>
            </div>
        </div>
    </div>
</div>
