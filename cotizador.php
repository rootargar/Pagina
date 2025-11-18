<?php
// Incluir archivo de conexión
include 'conexion.php';

// Función para calcular nuevo precio con margen
function calcularNuevoPrecio($costo, $porcentaje) {
    if ($porcentaje <= 9 || $porcentaje >= 100) {
        return $costo;
    }
    return $costo / (1 - ($porcentaje / 100));
}

// Función para mostrar resultados
function mostrarResultados($conn, $query, $params, $nuevosPrecios) {
    $result = sqlsrv_query($conn, $query, $params);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($result)) {
        echo "<form method='POST' action='' id='formularioPrincipal'>";
        echo "<input type='hidden' name='id_almacen' value='" . ($_GET['id_almacen'] ?? '') . "'>";
        echo "<input type='hidden' name='num_parte' value='" . ($_GET['num_parte'] ?? '') . "'>";
        echo "<input type='hidden' name='descripcion' value='" . ($_GET['descripcion'] ?? '') . "'>";
        echo "<table>";
        echo "<tr><th>Parte</th><th>Nombre</th><th>Existencia</th><th>Categoría</th><th>Precio Mínimo</th><th>% Categoría</th><th>% Usuario</th><th>Nuevo Precio</th></tr>";


while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $id = $row['IdArticulo'];
    $costo = $row['Costo'];
    $precioMinimo = $row['PrecioMinimo'];
    $nuevoPrecio = $nuevosPrecios[$id] ?? '';

    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['NumArticulo']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Nombre']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Existencia']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Categoria']) . "</td>";
    echo "<td>$" . number_format($row['PrecioMinimo'], 2) . "</td>";
    echo "<td>" . htmlspecialchars($row['PorcMargenUtilMin']) . "%</td>";
    echo "<td>
        <input type='hidden' name='costo[$id]' value='$costo' class='costo-hidden'>
        <input type='hidden' name='precio_minimo[$id]' value='$precioMinimo' class='precio-minimo-hidden'>
        <input type='number' 
               name='porcentaje[$id]' 
               placeholder='%' 
               step='0.01' 
               min='10' 
               max='80' 
               value='" . ($_POST['porcentaje'][$id] ?? '') . "'
               class='porcentaje-input'
               data-costo='$costo'
               data-precio-minimo='$precioMinimo'
               data-id='$id'
               onblur='calcularPrecioAutomatico(this)'>
    </td>";
    
    // Determinar el color basado en la comparación con precio mínimo
    $colorClass = '';
    $textoAdicional = '';
    if ($nuevoPrecio) {
        if ($nuevoPrecio < $precioMinimo) {
            $colorClass = 'precio-bajo-minimo';
            $textoAdicional = ' (Precio bajo)';
        } else {
            $colorClass = 'precio-sobre-minimo';
        }
    }
    
    echo "<td><span id='precio_$id' class='precio-resultado $colorClass'>" . 
         ($nuevoPrecio ? "$" . number_format($nuevoPrecio, 2) . $textoAdicional : "") . 
         "</span></td>";
    echo "</tr>";
}
 echo "</table>";
        // Removido el botón "Calcular Precio"
        echo "</form>";
    } else {
        echo "<p class='message error'><i class='fas fa-exclamation-triangle'></i> No se encontraron resultados para los criterios especificados.</p>";
    }
    
    sqlsrv_free_stmt($result);
}


// Procesar cálculo de precios (mantenido para compatibilidad)
$nuevosPrecios = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calcular_precio'])) {
    foreach ($_POST['costo'] as $id => $costo) {
        $porcentaje = $_POST['porcentaje'][$id] ?? 0;
        if ($porcentaje > 0) {
            $nuevosPrecios[$id] = calcularNuevoPrecio($costo, $porcentaje);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
     <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizador</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h3 {
            color: #34495e;
            margin-bottom: 15px;
        }

        /* Estilos para el contenedor del encabezado con botón */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Estilos para el botón de consulta */
        .btn-consulta {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .btn-consulta:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            background: linear-gradient(135deg, #229954, #1e8449);
        }

        .btn-consulta i {
            margin-right: 8px;
        }

        /* Estilos para selector de almacén */
        .almacen-selector {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .select-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 300px;
        }

        select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            background-color: white;
            cursor: pointer;
        }

        select:focus {
            outline: none;
            border-color: #3498db;
        }

        /* Información del almacén */
        .almacen-info {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .almacen-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
        }

        /* Contenedor de búsqueda */
        .search-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .search-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .search-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }

        .search-form input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .search-form input[type="text"]:focus {
            outline: none;
            border-color: #3498db;
        }

        .button {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .button:hover {
            background: linear-gradient(135deg, #2980b9, #1f4e79);
            transform: translateY(-2px);
        }

        /* Estilos para resultados */
        #resultados {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        input[type="number"] {
            width: 80px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
            text-align: center;
            transition: border-color 0.3s ease;
        }

        input[type="number"]:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }

        /* Estilo para el precio calculado */
        .precio-resultado {
            font-weight: bold;
            color: #27ae60;
            font-size: 14px;
        }

        /* Animación para el cálculo */
        .calculando {
            background-color: #fff3cd;
            border-color: #ffeaa7;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .search-container {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 14px;
                overflow-x: auto;
                display: block;
                white-space: nowrap;
            }

            th, td {
                padding: 8px;
            }

            input[type="number"] {
                width: 60px;
            }
        }

        /* Mensajes */
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        h2 {
            display: flex;
            align-items: center; /* Alinea verticalmente imagen y texto */
            gap: 10px; /* Espacio entre la imagen y el texto */
        }

        h2 img.logo {
            width: 100px;  /* Ajusta el tamaño del logo */
            height: auto;
            border-radius: 2px;
        }
        /* Estilos para precios según validación */
        .precio-bajo-minimo {
            color: #e74c3c !important; /* Rojo para precios bajos */
            font-weight: bold;
            background-color: #ffebee;
            padding: 2px 5px;
            border-radius: 3px;
            border: 1px solid #ffcdd2;
        }

        .precio-sobre-minimo {
            color: #27ae60 !important; /* Verde para precios correctos */
            font-weight: bold;
            background-color: #e8f5e8;
            padding: 2px 5px;
            border-radius: 3px;
            border: 1px solid #c8e6c9;
        }

        .precio-resultado {
            transition: all 0.3s ease;
        }

        /* Animación de alerta para precios bajos */
        .precio-bajo-minimo {
            animation: pulseRed 1s ease-in-out;
        }

        @keyframes pulseRed {
            0% { background-color: #ffebee; }
            50% { background-color: #ffcdd2; }
            100% { background-color: #ffebee; }
        }

    </style>
</head>
<body>
    <div class="container">
        <section id="Refacciones">
           
            <div class="header-container">
                <h2> 
                    <img src="kwdaf.png" alt="Logo" class="logo">  Cotizador KW-DAF Sinaloense
                </h2>
                <a href="http://localhost/kwdaf/clientes/consulta.php" class="btn-consulta">
                    <i class="fas fa-users"></i> Consulta Clientes
                </a>
            </div>
             
            <!-- Selector de almacén -->
            <div class="almacen-selector">
                <h3>Seleccione Sucursal:</h3>
                <div class="select-wrapper">
                    <select id="almacenSelect" onchange="seleccionarAlmacen()">
                        <option value="0">Seleccione un almacén...</option>
                        <option value="1" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 1) echo 'selected'; ?>>Matriz</option>
                        <option value="2" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 2) echo 'selected'; ?>>Mazatlán</option>
                        <option value="3" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 3) echo 'selected'; ?>>Mochis</option>
                        <option value="4" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 4) echo 'selected'; ?>>Guasave</option>
                        <option value="13" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 13) echo 'selected'; ?>>Taller Servicio</option>
                        <option value="1209" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 1209) echo 'selected'; ?>>Taller Inn House Casa Ley</option>
                        <option value="1215" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 1215) echo 'selected'; ?>>Guamuchil</option>
                        <option value="1216" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 1216) echo 'selected'; ?>>TRP Mazatlán</option>
                        <option value="16" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 16) echo 'selected'; ?>>Exhibicion Matriz</option>
                        <option value="104" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 104) echo 'selected'; ?>>Exhibicion Mazatlan</option>
                        <option value="1183" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 1183) echo 'selected'; ?>>Exhibicion Mochis</option>
                        <option value="1199" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 1199) echo 'selected'; ?>>Exhibicion Guasave</option>
                    </select>
                </div>
            </div>

            <!-- Información del almacén seleccionado -->
            <div id="almacenInfo" class="almacen-info" style="display: <?php echo isset($_GET['id_almacen']) && $_GET['id_almacen'] != '0' ? 'block' : 'none'; ?>">
                <div class="almacen-badge">
                    <i class="fas fa-warehouse"></i>
                    <span>Almacén actual: <strong><?php
                        if(isset($_GET['id_almacen'])) {
                            $almacen_id = $_GET['id_almacen'];
                            $almacenes = array(
                                '1' => 'KWS Matriz',
                                '2' => 'KWS Mazatlán',
                                '3' => 'KWS Mochis',
                                '4' => 'KWS Guasave',
                                '13' => 'Consignación de Taller Servicio',
                                '1209' => 'Consignación Taller Inn House Casa Ley',
                                '1215' => 'KWS Guamuchil',
                                '1216' => 'KWS TRP Mazatlán',
                                '16' => 'Exhibicion Matriz',
                                '104' => 'Exhibicion Mazatlan',
                                '1183' => 'Exhibicion Mochis',
                                '1199' => 'Exhibicion Guasave'
                            );
                            echo isset($almacenes[$almacen_id]) ? htmlspecialchars($almacenes[$almacen_id]) : 'Almacén no encontrado';
                        }
                    ?></strong></span>
                </div>
            </div>

            <!-- Formularios de búsqueda -->
            <div class="search-container">
                <form method="GET" action="" class="search-form">
                    <input type="hidden" name="id_almacen" value="<?php echo isset($_GET['id_almacen']) ? $_GET['id_almacen'] : ''; ?>">
                    <label for="num_parte">Número de Parte:</label>
                    <input type="text" id="num_parte" name="num_parte" placeholder="Ingrese el número de parte" value="<?php echo isset($_GET['num_parte']) ? htmlspecialchars($_GET['num_parte']) : ''; ?>">
                    <button class="button" name="buscar_parte">Buscar por Parte</button>
                </form>

                <form method="GET" action="" class="search-form">
                    <input type="hidden" name="id_almacen" value="<?php echo isset($_GET['id_almacen']) ? $_GET['id_almacen'] : ''; ?>">
                    <label for="descripcion">Descripción:</label>
                    <input type="text" id="descripcion" name="descripcion" placeholder="Ingrese la descripción" value="<?php echo isset($_GET['descripcion']) ? htmlspecialchars($_GET['descripcion']) : ''; ?>">
                    <button class="button" name="buscar_descripcion">Buscar por Descripción</button>
                </form>
            </div>
            
            <!-- Resultados -->
            <section id="resultados">
                <h2><i class="fas fa-calculator"></i> Resultados</h2>
                
                <?php if (!empty($nuevosPrecios)): ?>
                    <div class="message success">
                        <i class="fas fa-check-circle"></i>
                        Precios calculados exitosamente con los márgenes especificados.
                    </div>
                <?php endif; ?>

                <?php
                // Validar que se haya seleccionado un almacén
                if (!isset($_GET['id_almacen']) || $_GET['id_almacen'] == '0') {
                    echo "<div class='message info'><i class='fas fa-info-circle'></i> Por favor seleccione un almacén para realizar búsquedas.</div>";
                } else {
                    // Búsqueda por número de parte
                    if (isset($_GET['buscar_parte']) && !empty($_GET['num_parte'])) {
                        $num_parte = $_GET['num_parte'];
                        $id_almacen = $_GET['id_almacen'];

                        $query = "SELECT a.IdArticulo, a.NumArticulo, a.Nombre, e.ExistenciaActual as Existencia, 
                                ct.Nombre as Categoria, ct.PorcMargenUtilMin, 
                                ((c.Costo / (1 - (ct.PorcMargenUtilMin / 100))) - c.Costo + c.Costo) as PrecioMinimo, 
                                c.Costo 
                        FROM InvArticulos a 
                        INNER JOIN invArticulosCostos c ON a.IdArticulo = c.IdArticulo 
                        INNER JOIN invArticulosCat ct ON a.IdArticuloCat = ct.IdArticuloCat 
                        INNER JOIN InvArticulosExistencias e ON a.IdArticulo = e.IdArticulo 
                        WHERE c.IdAlmacen = ? AND e.IdAlmacen = ? 
                        AND a.IdTipoInventario = 1 
                        AND a.Activo = 1 
                        AND e.ExistenciaActual > 0 
                        AND c.ExistenciaActual > 0 
                        AND c.FechaUltimaEntrada = (SELECT MAX(cs.FechaUltimaEntrada) FROM invArticulosCostos cs WHERE cs.IdArticulo = c.IdArticulo AND cs.ExistenciaActual > 0 AND cs.IdAlmacen = ?) 
                        AND a.NumArticulo LIKE ?";

                        $params = array($id_almacen, $id_almacen, $id_almacen, "%$num_parte%");
                        mostrarResultados($conn, $query, $params, $nuevosPrecios);
                    }
                    
                    // Búsqueda por descripción
                    elseif (isset($_GET['buscar_descripcion']) && !empty($_GET['descripcion'])) {
                        $descripcion = $_GET['descripcion'];
                        $id_almacen = $_GET['id_almacen'];

                        $query = "SELECT a.IdArticulo, a.NumArticulo, a.Nombre, e.ExistenciaActual as Existencia, 
                                ct.Nombre as Categoria, ct.PorcMargenUtilMin, 
                                ((c.Costo / (1 - (ct.PorcMargenUtilMin / 100))) - c.Costo + c.Costo) as PrecioMinimo, 
                                c.Costo 
                        FROM InvArticulos a 
                        INNER JOIN invArticulosCostos c ON a.IdArticulo = c.IdArticulo 
                        INNER JOIN invArticulosCat ct ON a.IdArticuloCat = ct.IdArticuloCat 
                        INNER JOIN InvArticulosExistencias e ON a.IdArticulo = e.IdArticulo 
                        WHERE c.IdAlmacen = ? AND e.IdAlmacen = ? 
                        AND a.IdTipoInventario = 1 
                        AND a.Activo = 1 
                        AND e.ExistenciaActual > 0 
                        AND c.ExistenciaActual > 0 
                        AND c.FechaUltimaEntrada = (SELECT MAX(cs.FechaUltimaEntrada) FROM invArticulosCostos cs WHERE cs.IdArticulo = c.IdArticulo AND cs.ExistenciaActual > 0 AND cs.IdAlmacen = ?) 
                        AND a.Nombre LIKE ?";

                        $params = array($id_almacen, $id_almacen, $id_almacen, "%$descripcion%");
                        mostrarResultados($conn, $query, $params, $nuevosPrecios);
                    }
                    
                    // Si se calcularon precios, volver a mostrar los resultados
                    elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calcular_precio'])) {
                        // Determinar qué tipo de búsqueda se realizó anteriormente
                        if (!empty($_POST['num_parte'])) {
                            $num_parte = $_POST['num_parte'];
                            $id_almacen = $_POST['id_almacen'];

                            $query = "SELECT a.IdArticulo, a.NumArticulo, a.Nombre, e.ExistenciaActual as Existencia, 
                                    ct.Nombre as Categoria, ct.PorcMargenUtilMin, 
                                    ((c.Costo / (1 - (ct.PorcMargenUtilMin / 100))) - c.Costo + c.Costo) as PrecioMinimo, 
                                    c.Costo 
                            FROM InvArticulos a 
                            INNER JOIN invArticulosCostos c ON a.IdArticulo = c.IdArticulo 
                            INNER JOIN invArticulosCat ct ON a.IdArticuloCat = ct.IdArticuloCat 
                            INNER JOIN InvArticulosExistencias e ON a.IdArticulo = e.IdArticulo 
                            WHERE c.IdAlmacen = ? AND e.IdAlmacen = ? 
                            AND a.IdTipoInventario = 1 
                            AND a.Activo = 1 
                            AND e.ExistenciaActual > 0 
                            AND c.ExistenciaActual > 0 
                            AND c.FechaUltimaEntrada = (SELECT MAX(cs.FechaUltimaEntrada) FROM invArticulosCostos cs WHERE cs.IdArticulo = c.IdArticulo AND cs.ExistenciaActual > 0 AND cs.IdAlmacen = ?) 
                            AND a.NumArticulo LIKE ?";

                            $params = array($id_almacen, $id_almacen, $id_almacen, "%$num_parte%");
                            mostrarResultados($conn, $query, $params, $nuevosPrecios);
                        } elseif (!empty($_POST['descripcion'])) {
                            $descripcion = $_POST['descripcion'];
                            $id_almacen = $_POST['id_almacen'];

                            $query = "SELECT a.IdArticulo, a.NumArticulo, a.Nombre, e.ExistenciaActual as Existencia, 
                                    ct.Nombre as Categoria, ct.PorcMargenUtilMin, 
                                    ((c.Costo / (1 - (ct.PorcMargenUtilMin / 100))) - c.Costo + c.Costo) as PrecioMinimo, 
                                    c.Costo 
                            FROM InvArticulos a 
                            INNER JOIN invArticulosCostos c ON a.IdArticulo = c.IdArticulo 
                            INNER JOIN invArticulosCat ct ON a.IdArticuloCat = ct.IdArticuloCat 
                            INNER JOIN InvArticulosExistencias e ON a.IdArticulo = e.IdArticulo 
                            WHERE c.IdAlmacen = ? AND e.IdAlmacen = ? 
                            AND a.IdTipoInventario = 1 
                            AND a.Activo = 1 
                            AND e.ExistenciaActual > 0 
                            AND c.ExistenciaActual > 0 
                            AND c.FechaUltimaEntrada = (SELECT MAX(cs.FechaUltimaEntrada) FROM invArticulosCostos cs WHERE cs.IdArticulo = c.IdArticulo AND cs.ExistenciaActual > 0 AND cs.IdAlmacen = ?) 
                            AND a.Nombre LIKE ?";

                            $params = array($id_almacen, $id_almacen, $id_almacen, "%$descripcion%");
                            mostrarResultados($conn, $query, $params, $nuevosPrecios);
                        }
                    } else {
                        echo "<div class='message info'><i class='fas fa-search'></i> Utilice uno de los formularios de búsqueda para encontrar refacciones.</div>";
                    }
                }
                ?>
            </section>
        </section>
    </div>

    <script>
        function seleccionarAlmacen() {
            const select = document.getElementById('almacenSelect');
            const idAlmacen = select.value;
            
            if (idAlmacen != '0') {
                // Mostrar información del almacén
                document.getElementById('almacenInfo').style.display = 'block';
                
                // Actualizar URL para mantener el almacén seleccionado
                const url = new URL(window.location.href);
                url.searchParams.set('id_almacen', idAlmacen);
                window.history.pushState({}, '', url);
                
                // Recargar la página para aplicar el cambio de almacén
                window.location.href = url;
            } else {
                document.getElementById('almacenInfo').style.display = 'none';
            }
        }

        // Función para calcular precio automáticamente cuando el campo pierde el foco
       
// Función JavaScript actualizada para calcular precio automáticamente
function calcularPrecioAutomatico(input) {
    const costo = parseFloat(input.getAttribute('data-costo'));
    const precioMinimo = parseFloat(input.getAttribute('data-precio-minimo'));
    const porcentaje = parseFloat(input.value);
    const id = input.getAttribute('data-id');
    const precioSpan = document.getElementById('precio_' + id);
    
    // Añadir clase de cálculo para efectos visuales
    input.classList.add('calculando');
    
    setTimeout(() => {
        input.classList.remove('calculando');
        
        if (porcentaje > 9 && porcentaje < 100 && !isNaN(costo) && !isNaN(porcentaje)) {
            // Aplicar la misma fórmula que en PHP
            const nuevoPrecio = costo / (1 - (porcentaje / 100));
            
            // Formatear el precio con separadores de miles y 2 decimales
            const precioFormateado = '$' + nuevoPrecio.toLocaleString('es-MX', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            
            // Limpiar clases anteriores
            precioSpan.className = 'precio-resultado';
            
            // Determinar color y mensaje según comparación con precio mínimo
            if (nuevoPrecio < precioMinimo) {
                precioSpan.className += ' precio-bajo-minimo';
                precioSpan.textContent = precioFormateado + ' -MVMC';
            } else {
                precioSpan.className += ' precio-sobre-minimo';
                precioSpan.textContent = precioFormateado;
            }
            
        } else {
            precioSpan.textContent = '';
            precioSpan.className = 'precio-resultado';
            
            // Mostrar mensaje de error si el porcentaje no es válido
            if (porcentaje <= 9 || porcentaje >= 100) {
                precioSpan.textContent = 'Porcentaje inválido';
                precioSpan.style.color = '#e74c3c';
            }
        }
    }, 200);
}
        // Validar formularios antes de enviar
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.search-form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const idAlmacen = document.getElementById('almacenSelect').value;
                    
                    if (idAlmacen == '0') {
                        e.preventDefault();
                        alert('Por favor seleccione un almacén antes de realizar la búsqueda.');
                        return false;
                    }
                    
                    const input = this.querySelector('input[type="text"]');
                    if (!input.value.trim()) {
                        e.preventDefault();
                        alert('Por favor ingrese un criterio de búsqueda.');
                        return false;
                    }
                });
            });

            // Añadir evento de teclado para cálculo en tiempo real (opcional)
            document.addEventListener('keyup', function(e) {
                if (e.target.classList.contains('porcentaje-input')) {
                    // Pequeño delay para evitar cálculos excesivos mientras se escribe
                    clearTimeout(e.target.timeout);
                    e.target.timeout = setTimeout(() => {
                        calcularPrecioAutomatico(e.target);
                    }, 300);
                }
            });
        });
    </script>
    <?php
        // Registrar función para cerrar al final del script
       register_shutdown_function(function() use ($conn) {
              
            if (isset($conn) && $conn !== false) {
                sqlsrv_close($conn);
            }
            
        });
        ?>
</body>
</html>