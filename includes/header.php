<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema KW-DAF Sinaloense</title>
    <link rel="icon" href="kwdaf.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        /* Header principal */
        .main-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-logo {
            height: 50px;
            width: auto;
            border-radius: 5px;
        }

        .header-title {
            font-size: 1.5em;
            font-weight: bold;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.1);
            padding: 8px 15px;
            border-radius: 25px;
        }

        .user-icon {
            font-size: 1.2em;
        }

        .logout-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.4);
        }

        /* Layout principal */
        .main-layout {
            display: flex;
            min-height: calc(100vh - 80px);
        }

        /* Contenedor de contenido */
        .content-wrapper {
            flex: 1;
            padding: 0;
            overflow-y: auto;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            min-height: calc(100vh - 80px);
        }

        /* Botón de inicio */
        .home-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .home-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-header {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }

            .header-title {
                font-size: 1.2em;
                text-align: center;
            }

            .header-right {
                flex-direction: column;
                width: 100%;
            }

            .logout-btn {
                width: 100%;
                justify-content: center;
            }

            .container {
                padding: 15px;
            }

            .main-layout {
                flex-direction: column;
            }
        }

        /* Estilos para mensajes */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-left">
            <img src="kwdaf.png" alt="Logo KW-DAF" class="header-logo">
            <h1 class="header-title">Sistema KW-DAF Sinaloense</h1>
        </div>
        <div class="header-right">
            <div class="user-info">
                <i class="fas fa-user user-icon"></i>
                <span><?php echo htmlspecialchars($_SESSION['usuario'] ?? 'Usuario'); ?></span>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar Sesión
            </a>
        </div>
    </header>
    <div class="main-layout">
