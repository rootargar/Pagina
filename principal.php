<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

// Determinar qué sección mostrar (por defecto, mostrar la página de inicio)
$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'inicio';

// Incluir el header común
include 'includes/header.php';
?>

<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="container">
        <?php if($seccion != 'inicio'): ?>
            <a href="?seccion=inicio" class="home-btn"><i class="fas fa-arrow-left"></i> Volver al Inicio</a>
        <?php endif; ?>
        
        <?php
        // Incluir la sección correspondiente
        switch($seccion) {
            case 'inicio':
                include 'secciones/inicio.php';
                break;
            case 'cotizador':
                include 'secciones/cotizador.php';
                break;
            case 'consulta':
                include 'secciones/consulta.php';
                break;
            case 'refacciones':
                include 'secciones/refacciones.php';
                break;
            case 'inventarios':
                include 'secciones/inventarios.php';
                break;
            case 'servicio':
                include 'secciones/servicio.php';
                break;
            case 'indi':
                include 'secciones/reportes.php';
                break;
            case 'admin':
                include 'secciones/admin.php';
                break;
            case 'RH':
                include 'secciones/rh.php';
                break;
            default:
                include 'secciones/inicio.php';
        }
        ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>